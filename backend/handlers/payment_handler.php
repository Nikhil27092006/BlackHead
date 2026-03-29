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
    $stmt = $pdo->prepare("SELECT id, final_amount, payment_method FROM orders WHERE id = ? AND user_id = ? AND payment_status = 'pending'");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if ($order) {
        try {
            $pdo->beginTransaction();

            // 1. Update order status and payment status
            $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed' WHERE id = ?");
            $stmt->execute([$orderId]);

            // 2. Create a mock transaction record (simulating real gateway response)
            $txnId = 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 10));
            
            $gateway = 'UPI_APP_REDIRECT';
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

            // 3. Clear the shopping cart now that payment is confirmed
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);

            $pdo->commit();

            $_SESSION['success'] = "Payment successful! Your order has been placed. Transaction ID: $txnId";
            header("Location: " . SITE_URL . "index.php?page=order_success");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Payment processing error: " . $e->getMessage();
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
