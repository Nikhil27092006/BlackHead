<?php
session_start();
require_once __DIR__ . '/../core/config.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? 'fetch';

if ($action === 'mark_read') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'mark_all_read') {
    $pdo->exec("UPDATE admin_notifications SET is_read = 1");
    echo json_encode(['ok' => true]);
    exit;
}

// Fetch latest 10 notifications
$stmt = $pdo->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 10");
$notifications = $stmt->fetchAll();

echo json_encode($notifications);
