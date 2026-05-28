<?php
session_start();
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(clean($_POST['code'] ?? ''));
    $subtotal = (float)($_POST['subtotal'] ?? 0);

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a coupon code.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active'");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();

    if ($coupon) {
        $discount_percent = (float)$coupon['discount_percent'];

        // Enforce bounds: discount_percent must be 0-100
        if ($discount_percent < 0 || $discount_percent > 100) {
            echo json_encode(['success' => false, 'message' => 'Invalid coupon configuration.']);
            exit;
        }

        $discount_amount = ($subtotal * $discount_percent) / 100;
        
        // Store in session for final checkout verification
        $_SESSION['applied_coupon'] = [
            'code' => $code,
            'percent' => $discount_percent,
            'amount' => $discount_amount
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Coupon applied! You saved ' . formatPrice($discount_amount),
            'discount_amount' => $discount_amount,
            'discount_percent' => $discount_percent,
            'new_total_formatted' => formatPrice($subtotal - $discount_amount) // Assuming shipping handled in JS
        ]);
    } else {
        unset($_SESSION['applied_coupon']);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
