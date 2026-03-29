<?php
include '../../backend/core/config.php';
include '../../backend/core/functions.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$action = $_POST['action'] ?? '';
$type = $_POST['type'] ?? '';
$id = $_POST['id'] ?? null;
$text = clean($_POST['text'] ?? '');

if ($action === 'add' && $type && $text) {
    $stmt = $pdo->prepare("INSERT INTO highlights (type, text) VALUES (?, ?)");
    $stmt->execute([$type, $text]);
    $_SESSION['success'] = 'Highlight added!';
} elseif ($action === 'edit' && $id && $text) {
    $stmt = $pdo->prepare("UPDATE highlights SET text = ? WHERE id = ?");
    $stmt->execute([$text, $id]);
    $_SESSION['success'] = 'Highlight updated!';
} elseif ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM highlights WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Highlight deleted!';
}

header('Location: index.php?page=products#highlights');
exit;
