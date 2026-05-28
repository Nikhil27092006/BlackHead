<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect_to(SITE_URL . "index.php?page=login");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        redirect_to(SITE_URL . "index.php?page=checkout");
    }

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
    // Coupon Logic
    $couponCode = strtoupper(clean($_POST['coupon_code'] ?? ''));
    $discountAmount = 0;
    
    if (!empty($couponCode)) {
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active'");
        $stmt->execute([$couponCode]);
        $coupon = $stmt->fetch();
        if ($coupon) {
            $discountAmount = ($subtotal * (float)$coupon['discount_percent']) / 100;
        } else {
            $couponCode = null; // Invalid coupon
        }
    } else {
        $couponCode = null;
    }

    $total = $subtotal + $shipping - $discountAmount;
    
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
        $invDeducted = ($paymentMethod == 'cod') ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, total_amount, shipping_amount, final_amount, payment_method, shipping_address, order_status, coupon_code, discount_amount, inventory_deducted) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderNumber, $userId, $subtotal, $shipping, $total, $paymentMethod, $addressJson, $orderStatus, $couponCode, $discountAmount, $invDeducted]);
        $orderId = $pdo->lastInsertId();

        // Insert Order Items
        foreach($cartItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, variant_id, size, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['product_id'], $item['variant_id'], $item['size'], $item['quantity'], $item['price'], $item['price'] * $item['quantity']]);
        }

        // Clear Cart & Reduce Stock ONLY for COD. 
        if ($paymentMethod == 'cod') {
            // 1. Clear Cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);

            // 2. Reduce Stock
            foreach($cartItems as $item) {
                if (!empty($item['variant_id'])) {
                    $stCheck = $pdo->prepare("UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ?");
                    $stCheck->execute([$item['quantity'], $item['variant_id']]);
                }
            }
        }

        // Notify Admin (ONLY FOR COD - Online will notify after payment verification)
        if ($paymentMethod === 'cod') {
            $notifTitle = "New " . strtoupper($paymentMethod) . " Order: #{$orderNumber}";
            $notifMsg = "A new " . strtoupper($paymentMethod) . " order has been placed by " . ($_SESSION['user_name'] ?? 'Customer') . ". Total: " . formatPrice($total);
            $notifLink = "index.php?page=orders&id=" . $orderId;
            $stmt = $pdo->prepare("INSERT INTO admin_notifications (title, message, type, link) VALUES (?, ?, 'info', ?)");
            $stmt->execute([$notifTitle, $notifMsg, $notifLink]);
        }

        $pdo->commit();

        // --- Razorpay Integration ---
        if ($paymentMethod != 'cod') {
            $p_stmt = $pdo->query("SELECT razorpay_key_id, razorpay_key_secret, razorpay_active FROM payment_settings LIMIT 1");
            $p_settings = $p_stmt->fetch();

            if ($p_settings && $p_settings['razorpay_active']) {
                require_once '../core/razorpay_service.php';
                $razorpay = new RazorpayService($p_settings['razorpay_key_id'], $p_settings['razorpay_key_secret']);
                
                $rpOrder = $razorpay->createOrder($total, $orderNumber);
                if ($rpOrder && isset($rpOrder['id'])) {
                    $u_stmt = $pdo->prepare("UPDATE orders SET razorpay_order_id = ? WHERE id = ?");
                    $u_stmt->execute([$rpOrder['id'], $orderId]);
                }
            }
        }
        // ----------------------------

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
