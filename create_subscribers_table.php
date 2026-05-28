<?php
$pdo = new PDO('mysql:host=localhost;dbname=blackhead', 'root', '');

// Create subscribers table
$sql = "CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    $pdo->exec($sql);
    echo "SUCCESS: 'subscribers' table created.\n";
} catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
