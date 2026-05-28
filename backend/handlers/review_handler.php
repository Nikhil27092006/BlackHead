<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to post a review.";
    header("Location: ../../index.php?page=login");
    exit;
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: ../../index.php?page=product&id=" . ($_POST['product_id'] ?? ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $rating = (int)$_POST['rating'];
    $comment = clean($_POST['comment']);

    // Simple validation
    if ($rating < 1 || $rating > 5 || empty($comment)) {
        $_SESSION['error'] = "Please provide a valid rating and comment.";
        header("Location: ../../index.php?page=product&id=$productId");
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, user_name, rating, comment, status) VALUES (?, ?, ?, ?, ?, 'active')");
        if ($stmt->execute([$productId, $userId, $userName, $rating, $comment])) {
            $_SESSION['success'] = "Thank you! Your review has been posted.";
        } else {
            $_SESSION['error'] = "Failed to post review. Please try again.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: ../../index.php?page=product&id=$productId#reviews");
    exit;
} else {
    header("Location: ../../index.php");
    exit;
}
?>
