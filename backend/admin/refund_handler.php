<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';
require_once '../../backend/core/razorpay_service.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];

    // 1. Fetch Order & Razorpay Payment ID
    $stmt = $pdo->prepare("SELECT id, order_number, razorpay_payment_id, final_amount, payment_status FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order && $order['payment_status'] == 'paid' && !empty($order['razorpay_payment_id'])) {
        try {
            // 2. Fetch Razorpay Settings
            $p_stmt = $pdo->query("SELECT razorpay_key_id, razorpay_key_secret FROM payment_settings LIMIT 1");
            $p_settings = $p_stmt->fetch();

            if (!$p_settings || empty($p_settings['razorpay_key_id'])) {
                throw new Exception("Razorpay keys not configured.");
            }

            // 3. Initialize Razorpay Service
            $razorpay = new RazorpayService($p_settings['razorpay_key_id'], $p_settings['razorpay_key_secret']);
            
            // 4. Call Refund API
            // Note: In our simple RazorpayService, we need to add a refund component.
            // For now, I'll use a direct internal curl call within the handler or update the service.
            
            $url = "https://api.razorpay.com/v1/payments/" . $order['razorpay_payment_id'] . "/refund";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $p_settings['razorpay_key_id'] . ":" . $p_settings['razorpay_key_secret']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['amount' => $order['final_amount'] * 100]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                // 5. Success! Update database
                $pdo->beginTransaction();
                
                $upStmt = $pdo->prepare("UPDATE orders SET payment_status = 'refunded', order_status = 'cancelled' WHERE id = ?");
                $upStmt->execute([$orderId]);

                // Update payments table record
                $payStmt = $pdo->prepare("UPDATE payments SET status = 'online_refund' WHERE order_id = ? AND transaction_id = ?");
                $payStmt->execute([$orderId, $order['razorpay_payment_id']]);

                // Create a notification for record
                $notifTitle = "Refund Processed: #{$order['order_number']}";
                $notifMsg = "A refund of " . formatPrice($order['final_amount']) . " was successfully issued via Razorpay.";
                $nStmt = $pdo->prepare("INSERT INTO admin_notifications (title, message, type, link) VALUES (?, ?, 'info', ?)");
                $nStmt->execute([$notifTitle, $notifMsg, "index.php?page=order_detail&id=" . $orderId]);

                $pdo->commit();

                $_SESSION['success'] = "Refund issued successfully! The customer will receive their money in 5-7 working days.";
            } else {
                $errorData = json_decode($response, true);
                throw new Exception("Razorpay Error: " . ($errorData['error']['description'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $_SESSION['error'] = "Refund Failed: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Order not eligible for refund or payment ID missing.";
    }
    
    header("Location: index.php?page=order_detail&id=" . $orderId);
    exit;
} else {
    header("Location: index.php?page=orders");
    exit;
}
?>
