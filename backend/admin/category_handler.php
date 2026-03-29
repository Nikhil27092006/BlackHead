<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add') {
        $name = clean($_POST['name']);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = clean($_POST['description']);
        $slug = strtolower(str_replace(' ', '-', $name));

        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, parent_id, description) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $slug, $parentId, $description]);
            $_SESSION['success'] = "Category added!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    if ($action == 'edit') {
        $id = (int)$_POST['id'];
        $name = clean($_POST['name']);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = clean($_POST['description']);
        $slug = strtolower(str_replace(' ', '-', $name));

        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, parent_id = ?, description = ? WHERE id = ?");
        try {
            $stmt->execute([$name, $slug, $parentId, $description, $id]);
            $_SESSION['success'] = "Category updated!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    if ($action == 'edit') {
        $id = (int)$_POST['id'];
        $name = clean($_POST['name']);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = clean($_POST['description']);
        $slug = strtolower(str_replace(' ', '-', $name));

        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, parent_id = ?, description = ? WHERE id = ?");
        try {
            $stmt->execute([$name, $slug, $parentId, $description, $id]);
            $_SESSION['success'] = "Category updated!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }

    if ($action == 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Category deleted!";
    }
}

header("Location: index.php?page=categories");
exit;
?>
