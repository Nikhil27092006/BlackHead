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

    if ($action == 'update_profile') {
        $name = clean($_POST['name']);
        $email = clean($_POST['email']);
        
        if (empty($name) || empty($email)) {
            $_SESSION['error'] = "Name and email are required.";
            header("Location: " . SITE_URL . "index.php?page=profile");
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $userId]);
            
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['success'] = "Profile updated successfully.";
            header("Location: " . SITE_URL . "index.php?page=profile");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Could not update profile. Email might be in use.";
            header("Location: " . SITE_URL . "index.php?page=profile");
            exit;
        }
    }

    if ($action == 'change_password') {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if ($new !== $confirm) {
            $_SESSION['error'] = "New passwords do not match.";
            header("Location: " . SITE_URL . "index.php?page=profile");
            exit;
        }

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (password_verify($current, $user['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $userId]);
            
            $_SESSION['success'] = "Password changed successfully.";
            header("Location: " . SITE_URL . "index.php?page=profile");
        } else {
            $_SESSION['error'] = "Current password is incorrect.";
            header("Location: " . SITE_URL . "index.php?page=profile");
        }
        exit;
    }
}
header("Location: " . SITE_URL . "index.php?page=account");
exit;
