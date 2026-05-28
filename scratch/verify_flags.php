<?php
require_once 'backend/core/config.php';
$stmt = $pdo->prepare("SELECT id, name, is_featured, is_new, is_currently_hot FROM products WHERE id = 1");
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Product ID: " . $product['id'] . "\n";
echo "Name: " . $product['name'] . "\n";
echo "is_featured: " . $product['is_featured'] . "\n";
echo "is_new: " . $product['is_new'] . "\n";
echo "is_currently_hot: " . $product['is_currently_hot'] . "\n";
?>
