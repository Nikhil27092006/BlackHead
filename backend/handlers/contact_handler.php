<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? 'contact';
    $email = clean($_POST['email']);

    if ($action == 'newsletter') {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please provide a valid email address.";
            header("Location: " . SITE_URL . "index.php?page=home");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            
            $_SESSION['success'] = "Welcome to the Inner Circle! You've successfully subscribed.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Something went wrong. Please try again.";
        }

        header("Location: " . SITE_URL . "index.php?page=home");
        exit;
    }
    
    // Default: Contact Form
    $name = clean($_POST['name']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);
    
    // 0. Self-Healing: Create messages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(255),
        subject VARCHAR(255),
        message TEXT,
        status ENUM('new', 'replied', 'archived') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 1. Save Message Permanently
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    // 2. Auto-subscribe to Inner Circle
    $pdo->prepare("INSERT IGNORE INTO subscribers (email) VALUES (?)")->execute([$email]);

    // 3. Alert for Admin
    $pdo->prepare("INSERT INTO admin_notifications (type, title, message, link) VALUES ('exchange', '📩 NEW CONTACT MESSAGE', ?, 'index.php?page=messages')")
        ->execute(["From: $name ($email)\nSubject: $subject"]);
    
    $_SESSION['success'] = "Thank you, $name. Your message has been sent!";
    header("Location: " . SITE_URL . "index.php?page=contact");
    exit;
} else {
    header("Location: " . SITE_URL . "index.php");
    exit;
}
?>
