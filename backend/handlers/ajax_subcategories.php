<?php
require_once '../core/config.php';
header('Content-Type: application/json');

$parentId = (int)($_GET['parent_id'] ?? 0);
if (!$parentId) { echo json_encode([]); exit; }

$stmt = $pdo->prepare("SELECT id, name FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY name");
$stmt->execute([$parentId]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
