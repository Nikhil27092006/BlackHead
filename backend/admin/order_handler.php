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

    // Strict allowlist validation for order status
    $allowed_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'exchanged'];
    if (!in_array($status, $allowed_statuses)) {
        $_SESSION['error'] = "Invalid order status.";
        header("Location: index.php?page=orders");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    if ($stmt->execute([$status, $orderId])) {
        // --- UPDATED IRONCLAD RESTOCK LOGIC ---
        $checkStmt = $pdo->prepare("SELECT inventory_deducted, payment_status, payment_method FROM orders WHERE id = ?");
        $checkStmt->execute([$orderId]);
        $orderInfo = $checkStmt->fetch();

        // SCENARIO 1: Moving to Cancelled/Returned
        if (in_array($status, ['cancelled', 'returned'])) {
            // ONLY restock if it was actually deducted in the first place
            if ($orderInfo['inventory_deducted'] == 1) {
                $itemsStmt = $pdo->prepare("SELECT variant_id, quantity FROM order_items WHERE order_id = ?");
                $itemsStmt->execute([$orderId]);
                foreach ($itemsStmt->fetchAll() as $item) {
                    if (!empty($item['variant_id'])) {
                        $pdo->prepare("UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?")
                            ->execute([$item['quantity'], $item['variant_id']]);
                    }
                }
                // Mark as NOT deducted now
                $pdo->prepare("UPDATE orders SET inventory_deducted = 0, is_restocked = 1 WHERE id = ?")->execute([$orderId]);
            }
        }
        // SCENARIO 2: Moving BACK to an active state
        elseif (in_array($status, ['confirmed', 'processing', 'shipped', 'delivered'])) {
            // ONLY deduct if it's currently NOT deducted BUT should be (Paid or COD)
            if ($orderInfo['inventory_deducted'] == 0) {
                $isPaid = ($orderInfo['payment_status'] == 'paid');
                $isCOD = ($orderInfo['payment_method'] == 'cod');

                if ($isPaid || $isCOD) {
                    $itemsStmt = $pdo->prepare("SELECT variant_id, quantity FROM order_items WHERE order_id = ?");
                    $itemsStmt->execute([$orderId]);
                    foreach ($itemsStmt->fetchAll() as $item) {
                        if (!empty($item['variant_id'])) {
                            $pdo->prepare("UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ?")
                                ->execute([$item['quantity'], $item['variant_id']]);
                        }
                    }
                    // Mark as deducted again
                    $pdo->prepare("UPDATE orders SET inventory_deducted = 1, is_restocked = 0 WHERE id = ?")->execute([$orderId]);
                }
            }
        }
        // ------------------------------------

        $_SESSION['success'] = "Order status updated to $status and inventory synchronized.";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }
}

header("Location: index.php?page=orders");
exit;
?>
