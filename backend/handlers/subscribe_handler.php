<?php
require_once '../core/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    try {
        // Self-Healing: Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            status ENUM('active', 'unsubscribed') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Check if already subscribed
        $check = $pdo->prepare("SELECT id FROM subscribers WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'This email is already in our circle.']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->execute([$email]);

            // Alert for Admin
            $pdo->prepare("INSERT INTO admin_notifications (type, title, message, link) VALUES ('info', '🚀 NEW INNER CIRCLE MEMBER', ?, 'index.php?page=newsletter')")
                ->execute([$email]);

            echo json_encode(['success' => true, 'message' => 'Welcome to the Inner Circle.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'System error. Please try later.']);
    }
}
?>
