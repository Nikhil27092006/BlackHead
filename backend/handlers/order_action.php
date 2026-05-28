<?php
session_start();
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';

if (!isLoggedIn()) {
    header("Location: ../../index.php?page=login");
    exit;
}

$orderId = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;
$reason = $_GET['reason'] ?? '';
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

if (!$orderId || !$type) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../index.php?page=order_history");
    exit;
}

try {
    // Check if order exists and belongs to user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if (!$order) {
        $_SESSION['error'] = "Order not found.";
        header("Location: ../../index.php?page=order_history");
        exit;
    }

    $newStatus = '';
    $notificationTitle = '';
    $notificationMessage = '';
    $columnToUpdate = '';

    error_log("Order Action: Processing type $type for order $orderId");

    if ($type === 'cancel' && in_array($order['order_status'], ['pending', 'confirmed'])) {
        $newStatus = 'cancelled';
        $columnToUpdate = 'cancel_reason';
        $notificationTitle = "Order Cancelled: #{$order['order_number']}";
        $notificationMessage = "Customer {$userName} has cancelled their order. Reason: {$reason}";
    } elseif ($type === 'return' && $order['order_status'] === 'delivered') {
        $newStatus = 'returned';
        $columnToUpdate = 'return_reason';
        $notificationTitle = "Return Request: #{$order['order_number']}";
        $notificationMessage = "Customer {$userName} requested a return. Reason: {$reason}";
    } elseif ($type === 'exchange' && $order['order_status'] === 'delivered') {
        $newStatus = 'exchanged';
        $columnToUpdate = 'exchange_reason';
        $notificationTitle = "Exchange Request: #{$order['order_number']}";
        $notificationMessage = "Customer {$userName} requested an exchange. Reason: {$reason}";
    } else {
        error_log("Order Action: Action $type not allowed for status " . $order['order_status']);
        $_SESSION['error'] = "You cannot perform this action on this order.";
        header("Location: ../../index.php?page=order_detail&id=" . $orderId);
        exit;
    }

    error_log("Order Action: Updating DB and inserting notification: $notificationTitle");

    // Update order status and reason
    $sql = "UPDATE orders SET order_status = ?, {$columnToUpdate} = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newStatus, $reason, $orderId]);

    // Create Admin Notification
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_notifications (title, message, type, link) VALUES (?, ?, ?, ?)");
        $link = "index.php?page=orders&id=" . $orderId; 
        $stmt->execute([$notificationTitle, $notificationMessage, $type, $link]);
        error_log("Order Action: Notification inserted successfully.");
    } catch (Exception $e) {
        error_log("Order Action: Notification insert FAILED: " . $e->getMessage());
    }

    $_SESSION['success'] = "Order status updated successfully.";
    header("Location: ../../index.php?page=order_detail&id=" . $orderId);
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: ../../index.php?page=order_detail&id=" . $orderId);
}
