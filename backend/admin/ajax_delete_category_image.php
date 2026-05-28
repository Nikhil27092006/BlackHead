<?php
// ajax_delete_category_image.php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// CSRF validation
$csrfToken = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validate_csrf_token($csrfToken)) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$categoryId = isset($data['category_id']) ? (int)$data['category_id'] : 0;

if ($categoryId > 0) {
    // Get image filename
    $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $image = $stmt->fetchColumn();

    if ($image) {
        // Delete file from disk
        $filePath = ROOT_PATH . UPLOAD_DIR . $image;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Clear from DB
        $stmt = $pdo->prepare("UPDATE categories SET image = NULL WHERE id = ?");
        if ($stmt->execute([$categoryId])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'error' => 'Category not found or delete failed']);
?>
