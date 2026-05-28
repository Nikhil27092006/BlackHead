<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'] ?? null;
    $sessionId = session_id();

    // CSRF validation for update and remove actions (add uses AJAX from product pages)
    if (in_array($action, ['update', 'remove'])) {
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error'] = "Invalid request. Please try again.";
            session_write_close();
            redirect_to(SITE_URL . "index.php?page=cart");
        }
    }

    if ($action == 'add') {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'] ?: 1;
        $variantId = (isset($_POST['variant_id']) && $_POST['variant_id'] !== '' && $_POST['variant_id'] != 0) ? (int)$_POST['variant_id'] : null;
        $size = (isset($_POST['size']) && $_POST['size'] !== '') ? clean($_POST['size']) : null;

        if (addToCart($pdo, $productId, $quantity, $variantId, $userId, $sessionId, $size)) {
            $_SESSION['success'] = "Added to cart!";
        } else {
            $_SESSION['error'] = "Could not add to cart.";
        }
        session_write_close();
        redirect_to(SITE_URL . "index.php?page=cart");
    }

    if ($action == 'update') {
        $cartId = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];

        // Verify ownership before updating
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND (user_id = ? OR (user_id IS NULL AND session_id = ?))");
        $stmt->execute([$cartId, $userId, $sessionId]);
        if (!$stmt->fetch()) {
            $_SESSION['error'] = "Invalid cart item.";
            session_write_close();
            redirect_to(SITE_URL . "index.php?page=cart");
        }

        if ($quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND (user_id = ? OR (user_id IS NULL AND session_id = ?))");
            $stmt->execute([$cartId, $userId, $sessionId]);
        } else {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND (user_id = ? OR (user_id IS NULL AND session_id = ?))");
            $stmt->execute([$quantity, $cartId, $userId, $sessionId]);
        }
        session_write_close();
        redirect_to(SITE_URL . "index.php?page=cart");
    }

    if ($action == 'remove') {
        $cartId = (int)$_POST['cart_id'];

        // Verify ownership before removing
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND (user_id = ? OR (user_id IS NULL AND session_id = ?))");
        $stmt->execute([$cartId, $userId, $sessionId]);
        if (!$stmt->fetch()) {
            $_SESSION['error'] = "Invalid cart item.";
            session_write_close();
            redirect_to(SITE_URL . "index.php?page=cart");
        }

        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND (user_id = ? OR (user_id IS NULL AND session_id = ?))");
        $stmt->execute([$cartId, $userId, $sessionId]);
        session_write_close();
        redirect_to(SITE_URL . "index.php?page=cart");
    }
} else {
    redirect_to(SITE_URL . "index.php");
}
?>
