<?php
require_once '../core/config.php';
require_once '../core/functions.php';
require_once '../core/razorpay_service.php';

// 1. Receive the Webhook Data
$webhookBody = file_get_contents('php://input');
$webhookSignature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// 2. Fetch Razorpay Settings
$p_stmt = $pdo->query("SELECT razorpay_key_secret FROM payment_settings LIMIT 1");
$p_settings = $p_stmt->fetch();
$webhookSecret = $p_settings['razorpay_key_secret']; // By default, matching key_secret. Best practice is a separate webhook secret, but we handle standard here.

// 3. Verify the Webhook Signature
$expectedSignature = hash_hmac('sha256', $webhookBody, $webhookSecret);

if (hash_equals($expectedSignature, $webhookSignature)) {
    $event = json_decode($webhookBody, true);
    
    // 4. Handle "Payment Captured" Event
    if ($event['event'] === 'payment.captured') {
        $paymentData = $event['payload']['payment']['entity'];
        $razorpayPaymentId = $paymentData['id'];
        $razorpayOrderId = $paymentData['order_id'];
        $amountReceived = $paymentData['amount'] / 100; // Convert to rupees

        // Find the pending order
        $stmt = $pdo->prepare("SELECT id, order_number, user_id, final_amount FROM orders WHERE razorpay_order_id = ? AND payment_status = 'pending'");
        $stmt->execute([$razorpayOrderId]);
        $order = $stmt->fetch();

        if ($order) {
            // Verify amount matches order's final_amount to prevent tampering
            if (abs($amountReceived - (float)$order['final_amount']) > 0.01) {
                error_log("Webhook amount mismatch for order $orderId: expected {$order['final_amount']}, got $amountReceived");
                http_response_code(200);
                echo 'Amount mismatch.';
                exit;
            }

            // Found a pending order! The user probably lost connection during redirect.
            $orderId = $order['id'];
            $userId = $order['user_id'];
            
            $pdo->beginTransaction();

            try {
                // Update Order Status
                $uStmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed', razorpay_payment_id = ? WHERE id = ?");
                $uStmt->execute([$razorpayPaymentId, $orderId]);

                // Create Transaction Record
                $tStmt = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, payment_method, gateway, amount, status) 
                                     VALUES (?, ?, 'razorpay_webhook', 'Razorpay Webhook (Auto)', ?, 'success')");
                $tStmt->execute([$orderId, $razorpayPaymentId, $amountReceived]);
                
                // Clear User Cart
                $cStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $cStmt->execute([$userId]);

                // Trigger Admin Notification
                $notifTitle = "🚀 WEBHOOK ORDER: #{$order['order_number']}";
                $notifMsg = "A background order of " . formatPrice($amountReceived) . " was successfully completed via Razorpay Webhook.";
                $notifLink = "index.php?page=orders&id=" . $orderId;
                $nStmt = $pdo->prepare("INSERT INTO admin_notifications (title, message, type, link) VALUES (?, ?, 'success', ?)");
                $nStmt->execute([$notifTitle, $notifMsg, $notifLink]);

                // Update Stock
                $itemsStmt = $pdo->prepare("SELECT variant_id, quantity FROM order_items WHERE order_id = ?");
                $itemsStmt->execute([$orderId]);
                $orderItems = $itemsStmt->fetchAll();

                foreach ($orderItems as $item) {
                    if (!empty($item['variant_id'])) {
                        $stockStmt = $pdo->prepare("UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ?");
                        $stockStmt->execute([$item['quantity'], $item['variant_id']]);
                    }
                }

                $pdo->commit();
                http_response_code(200); // Tell Razorpay we succeeded
                echo 'Webhook processed successfully.';
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Webhook processing error for order $orderId: " . $e->getMessage());
                http_response_code(500);
            }
        } else {
            // Order was likely already marked as paid via frontend redirect
            http_response_code(200);
            echo 'Order already processed.';
        }
    } else {
        // Event not supported yet (like payment.failed)
        http_response_code(200);
        echo 'Event unhandled.';
    }
} else {
    // Signature mismatch - ignore to prevent spoofing
    http_response_code(400);
    echo 'Invalid signature.';
}
?>
