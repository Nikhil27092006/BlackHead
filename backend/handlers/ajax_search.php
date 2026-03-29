<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT p.id, p.name, p.price, pi.image as product_image 
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
            WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
            LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$query%", "%$query%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = [];
    foreach ($products as $p) {
        $results[] = [
            'id' => $p['id'],
            'name' => htmlspecialchars($p['name']),
            'price' => formatPrice($p['price']),
            'image' => $p['product_image'] ? 'assets/images/' . $p['product_image'] : 'assets/images/placeholder.jpg',
            'url' => 'index.php?page=product&id=' . $p['id']
        ];
    }

    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
