<?php
require_once 'backend/core/config.php';

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS dna_tech VARCHAR(255) DEFAULT 'High-Density Breathable Luxury Fabric'");
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS dna_fit VARCHAR(255) DEFAULT 'Precision Engineered Modern Silhouette'");
    echo "Columns added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
