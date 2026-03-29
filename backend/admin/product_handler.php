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
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(100, 999);
        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
        $categoryId = (int)$_POST['category_id'];
        $brand = clean($_POST['brand']);
        $sku = clean($_POST['sku']);

        try {
            $pdo->beginTransaction();

            // Insert Product
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, discount_price, category_id, brand, sku, status) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$name, $slug, $description, $price, $discountPrice, $categoryId, $brand, $sku]);
            $productId = $pdo->lastInsertId();

            // Handle Variants
            if (isset($_POST['variants'])) {
                foreach ($_POST['variants'] as $v) {
                    if (!empty($v['size']) || !empty($v['color'])) {
                        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, size, color, sku, stock_quantity) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$productId, clean($v['size']), clean($v['color']), clean($v['sku']), (int)$v['stock']]);
                    }
                }
            }

            // Handle Images (Simplified for now - just saving filenames if any)
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $imageName) {
                    $tmpName = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;
                    $newName = uniqid() . '.' . $ext;
                    $target = '../../assets/images/' . $newName;

                    if (move_uploaded_file($tmpName, $target)) {
                        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image, is_main) VALUES (?, ?, ?)");
                        $status = ($key == 0) ? 1 : 0;
                        $stmt->execute([$productId, $newName, $status]);
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "Product added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }

    if ($action == 'edit') {
        $id = (int)$_POST['id'];
        $name = clean($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(100, 999);
        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
        $categoryId = (int)$_POST['category_id'];
        $brand = clean($_POST['brand']);
        $sku = clean($_POST['sku']);

        try {
            $pdo->beginTransaction();

            // Update Product
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, discount_price = ?, category_id = ?, brand = ?, sku = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $price, $discountPrice, $categoryId, $brand, $sku, $id]);

            // Delete existing variants and re-insert
            $stmt = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
            $stmt->execute([$id]);

            // Handle Variants
            if (isset($_POST['variants'])) {
                foreach ($_POST['variants'] as $v) {
                    if (!empty($v['size']) || !empty($v['color'])) {
                        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, size, color, sku, stock_quantity) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$id, clean($v['size']), clean($v['color']), clean($v['sku']), (int)$v['stock']]);
                    }
                }
            }

            // Handle New Images
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $imageName) {
                    $tmpName = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;
                    $newName = uniqid() . '.' . $ext;
                    $target = '../../assets/images/' . $newName;

                    if (move_uploaded_file($tmpName, $target)) {
                        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image, is_main) VALUES (?, ?, ?)");
                        $isMain = ($key == 0 && $pdo->query("SELECT COUNT(*) FROM product_images WHERE product_id = $id")->fetchColumn() == 0) ? 1 : 0;
                        $stmt->execute([$id, $newName, $isMain]);
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "Product updated successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }

    if ($action == 'delete') {
        $id = (int)$_POST['id'];
        try {
            $pdo->beginTransaction();
            // Delete product images from disk
            $stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ?");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($images as $img) {
                $imgPath = '../../assets/images/' . $img;
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }
            // Delete product images from DB
            $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ?");
            $stmt->execute([$id]);
            // Delete product variants from DB
            $stmt = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
            $stmt->execute([$id]);
            // Delete product itself
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $pdo->commit();
            $_SESSION['success'] = "Product deleted!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
}

header("Location: index.php?page=products");
exit;
?>
