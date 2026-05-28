<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // CSRF validation
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        header("Location: index.php?page=categories");
        exit;
    }

    if ($action == 'add') {
        $name = clean($_POST['name']);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = clean($_POST['description']);
        
        $baseSlug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
        $slug = $baseSlug;
        $counter = 1;
        while(true) {
            $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $check->execute([$slug]);
            if($check->rowCount() == 0) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = 'cat_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target = ROOT_PATH . UPLOAD_DIR . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        }

        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, parent_id, description, image) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $slug, $parentId, $description, $imageName]);
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
        
        $baseSlug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
        $slug = $baseSlug;
        $counter = 1;
        while(true) {
            $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
            $check->execute([$slug, $id]);
            if($check->rowCount() == 0) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $imageSql = "";
        $params = [$name, $slug, $parentId, $description];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = 'cat_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target = ROOT_PATH . UPLOAD_DIR . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imageSql = ", image = ?";
                $params[] = $imageName;
            }
        }
        $params[] = $id;

        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, parent_id = ?, description = ? $imageSql WHERE id = ?");
        try {
            $stmt->execute($params);
            $_SESSION['success'] = "Category updated!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    if ($action == 'delete') {
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error'] = "Invalid request. Please try again.";
            header("Location: index.php?page=categories");
            exit;
        }
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Category deleted!";
    }
}

header("Location: index.php?page=categories");
exit;
?>
