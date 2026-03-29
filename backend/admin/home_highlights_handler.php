<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categories_title_1 = clean($_POST['categories_title_1'] ?? '');
    $categories_title_2 = clean($_POST['categories_title_2'] ?? '');
    $categories_subtitle = clean($_POST['categories_subtitle'] ?? '');
    $trending_title_1 = clean($_POST['trending_title_1'] ?? '');
    $trending_title_2 = clean($_POST['trending_title_2'] ?? '');
    $trending_subtitle = clean($_POST['trending_subtitle'] ?? '');
    $offer_bar_text = clean($_POST['offer_bar_text'] ?? 'Join the Blackhead Club & get 20% off your first order | Free shipping over ₹999');

    try {
        $pdo->beginTransaction();

        $settings_to_update = [
            'categories_title_1' => $categories_title_1,
            'categories_title_2' => $categories_title_2,
            'categories_subtitle' => $categories_subtitle,
            'trending_title_1' => $trending_title_1,
            'trending_title_2' => $trending_title_2,
            'trending_subtitle' => $trending_subtitle,
            'offer_bar_text' => $offer_bar_text
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value = ?");

        foreach ($settings_to_update as $key => $value) {
            $stmt->execute([$key, $value, $value]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Home highlights updated successfully.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating highlights: " . $e->getMessage();
    }

    header("Location: index.php?page=home_highlights");
    exit;
} else {
    header("Location: index.php?page=home_highlights");
    exit;
}
?>
