<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

// Check Admin Logged In
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // CSRF validation
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        header("Location: index.php?page=coupons");
        exit;
    }

    if ($action === 'add') {
        $code = strtoupper(clean($_POST['code']));
        $discount = (float)$_POST['discount'];

        // Validate discount is within bounds (0-100)
        if ($discount < 0 || $discount > 100) {
            $_SESSION['error'] = "Discount must be between 0 and 100 percent.";
            header("Location: index.php?page=coupons");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_percent) VALUES (?, ?)");
            $stmt->execute([$code, $discount]);
            $_SESSION['success'] = "Coupon '$code' generated successfully!";
        } catch (Exception $e) {
            error_log("Coupon creation error: " . $e->getMessage());
            $_SESSION['error'] = "Error generating coupon. Please try again.";
        }
    } 
    elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            // First get the code for the success message
            $stmt = $pdo->prepare("SELECT code FROM coupons WHERE id = ?");
            $stmt->execute([$id]);
            $couponCode = $stmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
            $stmt->execute([$id]);
            
            $_SESSION['success'] = "Coupon '$couponCode' expunged successfully!";
        } catch (Exception $e) {
            error_log("Coupon deletion error: " . $e->getMessage());
            $_SESSION['error'] = "Error deleting coupon. Please try again.";
        }
    }

    header("Location: index.php?page=coupons");
    exit;
}

// If accessed directly without POST
header("Location: index.php?page=dashboard");
exit;
?>
