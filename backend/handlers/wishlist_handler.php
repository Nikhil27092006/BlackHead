<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    header("Location: " . SITE_URL . "index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'];
    $productId = (int)$_POST['product_id'];

    if ($action == 'toggle') {
        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        
        if ($stmt->fetch()) {
            // Remove
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $_SESSION['success'] = "Removed from wishlist.";
        } else {
            // Add
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            $_SESSION['success'] = "Added to wishlist!";
        }
    }
}

// Redirect back to referring page or wishlist
$referrer = $_SERVER['HTTP_REFERER'] ?? (SITE_URL . 'index.php?page=wishlist');
header("Location: $referrer");
exit;
?>
