<?php
require_once 'backend/core/config.php';

// Simulate turning off 'is_featured' for product 1
$id = 1;
$isFeatured = 0; // Simulate UNCHECKED
$isNew = 1;
$isCurrentlyHot = 1;

try {
    $stmt = $pdo->prepare("UPDATE products SET is_featured = ?, is_new = ?, is_currently_hot = ? WHERE id = ?");
    $stmt->execute([$isFeatured, $isNew, $isCurrentlyHot, $id]);
    echo "Update successful. is_featured set to 0.\n";
} catch (Exception $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
}

// Verify
$stmt = $pdo->prepare("SELECT is_featured FROM products WHERE id = 1");
$stmt->execute();
$val = $stmt->fetchColumn();
echo "New is_featured value in DB: " . $val . "\n";

// Reset back to 1 for the user
$pdo->prepare("UPDATE products SET is_featured = 1 WHERE id = 1")->execute();
echo "Reset is_featured to 1.\n";
?>
