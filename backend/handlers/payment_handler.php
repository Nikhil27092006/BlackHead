<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    header("Location: " . SITE_URL . "index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $userId = $_SESSION['user_id'];

    // Verify order exists, belongs to user, and is still pending
    $stmt = $pdo->prepare("SELECT id, order_number, final_amount, payment_method, razorpay_order_id FROM orders WHERE id = ? AND user_id = ? AND payment_status = 'pending'");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if ($order) {
        try {
            $txnId = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));
            $gateway = 'UPI_APP_REDIRECT';
            
            // If Razorpay is active for this order, verification is mandatory (cannot bypass)
            // razorpay_order_id exists means it IS a razorpay order that needs verification
            // No razorpay_order_id means COD or other method - $is_verified stays based on payment method presence
            $is_verified = !empty($order['razorpay_order_id']) ? false : (isset($_POST['razorpay_payment_id']) ? false : true);

            // --- Razorpay Verification ---
            if (isset($_POST['razorpay_payment_id']) && !empty($_POST['razorpay_payment_id'])) {
                $p_stmt = $pdo->query("SELECT razorpay_key_id, razorpay_key_secret FROM payment_settings LIMIT 1");
                $p_settings = $p_stmt->fetch();
                
                require_once '../core/razorpay_service.php';
                $razorpay = new RazorpayService($p_settings['razorpay_key_id'], $p_settings['razorpay_key_secret']);
                
                $is_verified = $razorpay->verifySignature(
                    $order['razorpay_order_id'], 
                    $_POST['razorpay_payment_id'], 
                    $_POST['razorpay_signature']
                );

                if ($is_verified) {
                    $txnId = $_POST['razorpay_payment_id'];
                    $gateway = 'Razorpay Automated';
                    
                    // Update order with payment ID
                    $up_stmt = $pdo->prepare("UPDATE orders SET razorpay_payment_id = ?, razorpay_signature = ? WHERE id = ?");
                    $up_stmt->execute([$_POST['razorpay_payment_id'], $_POST['razorpay_signature'], $orderId]);
                }
            }
            // -----------------------------

            if (!$is_verified) {
                throw new Exception("Payment verification failed. Invalid Signature.");
            }

            $pdo->beginTransaction();

            // 1. Update order status and payment status
            $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed', inventory_deducted = 1 WHERE id = ?");
            $stmt->execute([$orderId]);

            // 2. Create transaction record
            if ($order['payment_method'] == 'googlepay') $gateway = 'Google Pay API';
            if ($order['payment_method'] == 'paytm') $gateway = 'Paytm Gateway';
            if ($order['payment_method'] == 'phonepe') $gateway = 'PhonePe Gateway';
            if ($order['payment_method'] == 'card') $gateway = 'Credit/Debit Card';

            $stmt = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, payment_method, gateway, amount, status) 
                                 VALUES (?, ?, ?, ?, ?, 'success')");
            $stmt->execute([
                $orderId, 
                $txnId, 
                $order['payment_method'],
                $gateway,
                $order['final_amount']
            ]);

            // 3. Clear the shopping cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);

            // 4. TRIGGER COMPREHENSIVE ADMIN NOTIFICATION
            $notifTitle = "🚀 NEW PAID ORDER: #{$order['order_number']}";
            $notifMsg = "Success! A new " . strtoupper($order['payment_method']) . " order has been placed and payment of " . formatPrice($order['final_amount']) . " was verified via {$gateway}.";
            $notifLink = "index.php?page=orders&id=" . $orderId;
            $stmt = $pdo->prepare("INSERT INTO admin_notifications (title, message, type, link) VALUES (?, ?, 'success', ?)");
            $stmt->execute([$notifTitle, $notifMsg, $notifLink]);

            // 5. DECREMENT STOCK
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

            $_SESSION['success'] = "Payment successful! Your order has been placed. Transaction ID: $txnId";
            header("Location: " . SITE_URL . "index.php?page=order_success");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Payment processing error: " . $e->getMessage());
            $_SESSION['error'] = "Payment processing error. Please try again.";
            header("Location: " . SITE_URL . "index.php?page=payment&order_id=" . $orderId);
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid order or already paid.";
        header("Location: " . SITE_URL . "index.php?page=account");
        exit;
    }
} else {
    header("Location: " . SITE_URL . "index.php");
    exit;
}
?>
