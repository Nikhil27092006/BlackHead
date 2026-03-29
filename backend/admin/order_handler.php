<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $status = clean($_POST['status']);

    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    if ($stmt->execute([$status, $orderId])) {
        $_SESSION['success'] = "Order status updated to $status.";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }
}

header("Location: index.php?page=orders");
exit;
?>
