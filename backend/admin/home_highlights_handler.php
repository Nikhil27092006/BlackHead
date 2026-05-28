<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?page=home_highlights");
    exit;
}

// Raw sanitizer: strips tags + whitespace but does NOT htmlspecialchars
// (values are stored raw; encoding happens on output with htmlspecialchars())
function rawSanitize($value) {
    return strip_tags(trim(stripslashes($value ?? '')));
}

// ── 0. SELF-HEALING: DYNAMIC SECTIONS TABLES ─────────────────────────
// (Moved outside transaction because DDL causes implicit commit in MySQL)
$pdo->exec("CREATE TABLE IF NOT EXISTS homepage_custom_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    eyebrow VARCHAR(255),
    title_1 VARCHAR(255),
    title_2 VARCHAR(255),
    status ENUM('active', 'hidden') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS homepage_custom_section_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT,
    product_id INT,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

try {
    $pdo->beginTransaction();

    // ── 1. STANDARD HIGHLIGHT SETTINGS ─────────────────────────────────────
    $settings_to_update = [
        'offer_bar_text'      => rawSanitize($_POST['offer_bar_text']   ?? ''),
        'hero_eyebrow'        => rawSanitize($_POST['hero_eyebrow']     ?? ''),
        'hero_title_1'        => rawSanitize($_POST['hero_title_1']     ?? ''),
        'hero_title_2'        => rawSanitize($_POST['hero_title_2']     ?? ''),
        'hero_subtitle'       => rawSanitize($_POST['hero_subtitle']    ?? ''),
        'hero_cta1_text'      => rawSanitize($_POST['hero_cta1_text']   ?? ''),
        'hero_cta2_text'      => rawSanitize($_POST['hero_cta2_text']   ?? ''),
        // Marquee: allow commas — just strip tags and trim
        'marquee_items'       => strip_tags(trim(stripslashes($_POST['marquee_items'] ?? ''))),
        'stat1_number'        => rawSanitize($_POST['stat1_number'] ?? ''),
        'stat1_suffix'        => rawSanitize($_POST['stat1_suffix'] ?? ''),
        'stat1_label'         => rawSanitize($_POST['stat1_label']  ?? ''),
        'stat2_number'        => rawSanitize($_POST['stat2_number'] ?? ''),
        'stat2_suffix'        => rawSanitize($_POST['stat2_suffix'] ?? ''),
        'stat2_label'         => rawSanitize($_POST['stat2_label']  ?? ''),
        'stat3_number'        => rawSanitize($_POST['stat3_number'] ?? ''),
        'stat3_suffix'        => rawSanitize($_POST['stat3_suffix'] ?? ''),
        'stat3_label'         => rawSanitize($_POST['stat3_label']  ?? ''),
        'stat4_number'        => rawSanitize($_POST['stat4_number'] ?? ''),
        'stat4_suffix'        => rawSanitize($_POST['stat4_suffix'] ?? ''),
        'stat4_label'         => rawSanitize($_POST['stat4_label']  ?? ''),
        'categories_title_1'  => rawSanitize($_POST['categories_title_1']  ?? ''),
        'categories_title_2'  => rawSanitize($_POST['categories_title_2']  ?? ''),
        'categories_subtitle' => rawSanitize($_POST['categories_subtitle'] ?? ''),
        'trending_title_1'    => rawSanitize($_POST['trending_title_1']    ?? ''),
        'trending_title_2'    => rawSanitize($_POST['trending_title_2']    ?? ''),
        'trending_subtitle'   => rawSanitize($_POST['trending_subtitle']   ?? ''),
    ];

    $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value = ?");
    foreach ($settings_to_update as $key => $value) {
        $stmt->execute([$key, $value, $value]);
    }

    // ── 2. DYNAMIC SECTION ACTIONS ─────────────────────────────────────────
    $actionType = $_POST['action_type'] ?? '';

    if ($actionType === 'create_section') {
        $eb = rawSanitize($_POST['new_sec_eyebrow']);
        $t1 = rawSanitize($_POST['new_sec_title1']);
        $t2 = rawSanitize($_POST['new_sec_title2']);
        if (!empty($t1)) {
            $pdo->prepare("INSERT INTO homepage_custom_sections (eyebrow, title_1, title_2) VALUES (?, ?, ?)")
                ->execute([$eb, $t1, $t2]);
        }
    }

    if ($actionType === 'add_to_section') {
        foreach ($_POST['add_to_sec'] as $sid => $pid) {
            if (!empty($pid)) {
                $check = $pdo->prepare("SELECT id FROM homepage_custom_section_products WHERE section_id = ? AND product_id = ?");
                $check->execute([$sid, $pid]);
                if (!$check->fetch()) {
                    $pdo->prepare("INSERT INTO homepage_custom_section_products (section_id, product_id) VALUES (?, ?)")
                        ->execute([$sid, $pid]);
                }
            }
        }
    }

    if (!empty($_POST['delete_section'])) {
        $sid = (int)$_POST['delete_section'];
        $pdo->prepare("DELETE FROM homepage_custom_sections WHERE id = ?")->execute([$sid]);
        $pdo->prepare("DELETE FROM homepage_custom_section_products WHERE section_id = ?")->execute([$sid]);
    }

    if (!empty($_POST['remove_from_sec'])) {
        list($sid, $pid) = explode('-', $_POST['remove_from_sec']);
        $pdo->prepare("DELETE FROM homepage_custom_section_products WHERE section_id = ? AND product_id = ?")
            ->execute([(int)$sid, (int)$pid]);
    }

    // ── 2. CURRENTLY HOT — DNA LABEL BULK UPDATE ───────────────────────────
    if (!empty($_POST['dna_tech']) && is_array($_POST['dna_tech'])) {
        $updateDna = $pdo->prepare("UPDATE products SET dna_tech = ?, dna_fit = ? WHERE id = ?");
        foreach ($_POST['dna_tech'] as $pid => $tech) {
            $fit = $_POST['dna_fit'][$pid] ?? '';
            $updateDna->execute([rawSanitize($tech), rawSanitize($fit), (int)$pid]);
        }
    }

    // ── 3. CURRENTLY HOT — REMOVE ITEMS ────────────────────────────────────
    if (!empty($_POST['remove_hot']) && is_array($_POST['remove_hot'])) {
        $removeStmt = $pdo->prepare("UPDATE products SET is_featured = 0, is_currently_hot = 0 WHERE id = ?");
        foreach ($_POST['remove_hot'] as $pid) {
            $removeStmt->execute([(int)$pid]);
        }
    }

    // ── 4. CURRENTLY HOT — ADD NEW ITEM ────────────────────────────────────
    if (!empty($_POST['new_hot_product'])) {
        $addStmt = $pdo->prepare("UPDATE products SET is_featured = 1 WHERE id = ?");
        $addStmt->execute([(int)$_POST['new_hot_product']]);
    }

    $pdo->commit();
    $_SESSION['success'] = "Homepage settings updated successfully!";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = "Error saving settings: " . $e->getMessage();
}

header("Location: index.php?page=home_highlights");
exit;
?>
