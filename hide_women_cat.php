<?php
require_once 'backend/core/config.php';
try {
    $stmt = $pdo->prepare("UPDATE categories SET status = 'hidden' WHERE name LIKE '%Women%'");
    $stmt->execute();
    echo "Women's category hidden successfully.";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
