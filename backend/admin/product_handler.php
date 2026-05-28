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
        header("Location: index.php?page=products");
        exit;
    }

    if ($action == 'add') {
        $name = clean($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(100, 999);
        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
        $categoryId = (int)$_POST['category_id'];
        $subcategoryId = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
        $brand = clean($_POST['brand']);
        $sku = clean($_POST['sku']);
        if (empty($sku)) {
            $sku = 'BH-' . strtoupper(substr(md5(time() . $name), 0, 8));
        }

        $isFeatured    = isset($_POST['is_featured'])     ? 1 : 0;
        $isNew         = isset($_POST['is_new'])          ? 1 : 0;
        $isCurrentlyHot = isset($_POST['is_currently_hot']) ? 1 : 0;
        $dnaTech       = clean($_POST['dna_tech'] ?? 'High-Density Breathable Luxury Fabric');
        $dnaFit        = clean($_POST['dna_fit']  ?? 'Precision Engineered Modern Silhouette');

        try {
            $pdo->beginTransaction();

            // Insert Product
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, discount_price, category_id, subcategory_id, brand, sku, status, is_featured, is_new, is_currently_hot, dna_tech, dna_fit) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $price, $discountPrice, $categoryId, $subcategoryId, $brand, $sku, $isFeatured, $isNew, $isCurrentlyHot, $dnaTech, $dnaFit]);
            $productId = $pdo->lastInsertId();

            // Handle Variants
            if (isset($_POST['variants'])) {
                foreach ($_POST['variants'] as $v) {
                    if (!empty($v['size']) || !empty($v['color'])) {
                        $v_sku = clean($v['sku']);
                        if (empty($v_sku)) {
                            $v_sku = $sku . '-' . strtoupper(substr(md5(rand()), 0, 4));
                        }
                        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, size, color, sku, stock_quantity) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$productId, clean($v['size']), clean($v['color']), $v_sku, (int)$v['stock']]);
                    }
                }
            }

            // Handle Images (Simplified for now - just saving filenames if any)
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $imageName) {
                    $tmpName = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;
                    $newName = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $target = ROOT_PATH . UPLOAD_DIR . $newName;

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
        
        // Only regenerate slug if name changed or slug is empty
        $existing = $pdo->prepare("SELECT name, slug FROM products WHERE id = ?");
        $existing->execute([$id]);
        $oldProduct = $existing->fetch();
        
        if ($oldProduct && $oldProduct['name'] !== $name) {
            $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(100, 999);
        } else {
            $slug = $oldProduct['slug'] ?? (strtolower(str_replace(' ', '-', $name)) . '-' . rand(100, 999));
        }

        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
        $categoryId = (int)$_POST['category_id'];
        $subcategoryId = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
        $brand = clean($_POST['brand']);
        $sku = clean($_POST['sku']);
        if (empty($sku)) {
            $sku = 'BH-' . strtoupper(substr(md5(time() . $name), 0, 8));
        }

        $isFeatured    = isset($_POST['is_featured'])     ? 1 : 0;
        $isNew         = isset($_POST['is_new'])          ? 1 : 0;
        $isCurrentlyHot = isset($_POST['is_currently_hot']) ? 1 : 0;
        $dnaTech       = clean($_POST['dna_tech'] ?? 'High-Density Breathable Luxury Fabric');
        $dnaFit        = clean($_POST['dna_fit']  ?? 'Precision Engineered Modern Silhouette');

        try {
            $pdo->beginTransaction();

            // Update Product
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, discount_price = ?, category_id = ?, subcategory_id = ?, brand = ?, sku = ?, is_featured = ?, is_new = ?, is_currently_hot = ?, dna_tech = ?, dna_fit = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $price, $discountPrice, $categoryId, $subcategoryId, $brand, $sku, $isFeatured, $isNew, $isCurrentlyHot, $dnaTech, $dnaFit, $id]);

            // Delete existing variants and re-insert
            $stmt = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
            $stmt->execute([$id]);

            // Handle Variants
            if (isset($_POST['variants'])) {
                foreach ($_POST['variants'] as $v) {
                    if (!empty($v['size']) || !empty($v['color'])) {
                        $v_sku = clean($v['sku']);
                        if (empty($v_sku)) {
                            $v_sku = $sku . '-' . strtoupper(substr(md5(rand() . time()), 0, 4));
                        }
                        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, size, color, sku, stock_quantity) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$id, clean($v['size']), clean($v['color']), $v_sku, (int)$v['stock']]);
                    }
                }
            }

            // Handle New Images
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $imageName) {
                    $tmpName = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;
                    $newName = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $target = ROOT_PATH . UPLOAD_DIR . $newName;

                    if (move_uploaded_file($tmpName, $target)) {
                        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image, is_main) VALUES (?, ?, ?)");
                        $isMain = ($key == 0 && $pdo->query("SELECT COUNT(*) FROM product_images WHERE product_id = " . (int)$id)->fetchColumn() == 0) ? 1 : 0;
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
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error'] = "Invalid request. Please try again.";
            header("Location: index.php?page=products");
            exit;
        }
        $id = (int)$_POST['id'];
        try {
            $pdo->beginTransaction();
            // Delete product images from disk
            $stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ?");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($images as $img) {
                $imgPath = ROOT_PATH . UPLOAD_DIR . $img;
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

            // Delete from Homepage Custom Sections
            $stmt = $pdo->prepare("DELETE FROM homepage_custom_section_products WHERE product_id = ?");
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
