<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect_to(SITE_URL . "index.php?page=login");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $addressId = (int)$_POST['address_id'] ?? 0;
    $paymentMethod = clean($_POST['payment_method']);

    if (empty($addressId)) {
        $_SESSION['error'] = "Please select a shipping address.";
        redirect_to(SITE_URL . "index.php?page=checkout");
    }

    if (empty($paymentMethod)) {
        $_SESSION['error'] = "Please select a payment method.";
        redirect_to(SITE_URL . "index.php?page=checkout");
    }

    // Get Cart Items
    $stmt = $pdo->prepare("SELECT c.*, p.price, p.name FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll();

    if (count($cartItems) == 0) {
        redirect_to(SITE_URL . "index.php?page=cart");
    }

    // Calculate Totals
    $subtotal = 0;
    foreach($cartItems as $item) $subtotal += $item['price'] * $item['quantity'];
    $shipping = (count($cartItems) > 0 && $subtotal < 1000) ? 99 : 0;
    $total = $subtotal + $shipping;

    // Get selected address as JSON for the order
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE id = ?");
    $stmt->execute([$addressId]);
    $address = $stmt->fetch();
    $addressJson = json_encode($address);
    $_SESSION['last_total'] = formatPrice($total);

    // Create Order
    $orderNumber = generateOrderNumber();
    
    try {
        $pdo->beginTransaction();
        $orderStatus = ($paymentMethod == 'cod') ? 'confirmed' : 'pending';

        $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, total_amount, shipping_amount, final_amount, payment_method, shipping_address, order_status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderNumber, $userId, $subtotal, $shipping, $total, $paymentMethod, $addressJson, $orderStatus]);
        $orderId = $pdo->lastInsertId();

        // Insert Order Items
        foreach($cartItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, variant_id, size, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['product_id'], $item['variant_id'], $item['size'], $item['quantity'], $item['price'], $item['price'] * $item['quantity']]);
        }

        // Clear Cart ONLY for COD. For online payments, it will be cleared after successful transaction in payment_handler.php
        if ($paymentMethod == 'cod') {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
        }

        $pdo->commit();

        $_SESSION['last_order_id'] = $orderId;
        $_SESSION['last_order_number'] = $orderNumber;

        session_write_close();
        // Redirect based on payment method
        if ($paymentMethod == 'cod') {
            redirect_to(SITE_URL . "index.php?page=order_success");
        } else {
            redirect_to(SITE_URL . "index.php?page=payment&order_id=" . $orderId);
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Order failed: " . $e->getMessage();
        redirect_to(SITE_URL . "index.php?page=checkout");
    }
} else {
    redirect_to(SITE_URL . "index.php");
}
?>
