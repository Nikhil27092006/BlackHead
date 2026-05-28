<?php
// Get setting value from database (statically cached per-request to prevent N+1 queries)
function getSetting($key, $default = '') {
    global $pdo;
    static $cache = [];
    if (array_key_exists($key, $cache)) {
        return $cache[$key] !== null ? $cache[$key] : $default;
    }
    try {
        $stmt = $pdo->prepare("SELECT key_value FROM settings WHERE key_name = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        $cache[$key] = $result ? $result['key_value'] : null;
        return $cache[$key] !== null ? $cache[$key] : $default;
    } catch (Exception $e) {
        $cache[$key] = null;
        return $default;
    }
}
// CSRF Token generation and validation
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

// Clean input
function clean($data) {
    if ($data === null) return '';
    return stripslashes(trim($data));
}

// Format price
function formatPrice($price) {
    return '₹' . number_format($price, 0);
}

// Get Featured Products
function getFeaturedProducts($pdo, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as cat_name, pi.image as product_image, 
               (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id) as total_stock
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
        WHERE p.status = 'active' AND (p.is_featured = 1 OR p.is_currently_hot = 1) 
        GROUP BY p.id
        ORDER BY p.created_at DESC LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get Categories for Nav
function getNavCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order");
    return $stmt->fetchAll();
}

// Auth check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get Cart Count
function getCartCount($pdo) {
    $userId = $_SESSION['user_id'] ?? null;
    $sessionId = session_id();
    
    $stmt = $pdo->prepare("SELECT SUM(cart.quantity) as count FROM cart JOIN products ON cart.product_id = products.id WHERE (cart.user_id = ? OR (cart.user_id IS NULL AND cart.session_id = ?))");
    $stmt->execute([$userId, $sessionId]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

// Check if product is in wishlist
function isInWishlist($pdo, $userId, $productId) {
    if (!$userId) return false;
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    return $stmt->fetch() ? true : false;
}

// Generate unique order number
function generateOrderNumber() {
    return 'BH-' . strtoupper(substr(uniqid(), -8));
}

// Add to Cart Logic
function addToCart($pdo, $productId, $quantity, $variantId, $userId, $sessionId, $size = null) {
    // Robustness: 0 should be treated as null for foreign keys
    if ($variantId === 0 || $variantId === '0') $variantId = null;
    
    // Check if already in cart with same size/variant
    if ($userId) {
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL)) AND (size = ? OR (size IS NULL AND ? IS NULL))");
        $stmt->execute([$userId, $productId, $variantId, $variantId, $size, $size]);
    } else {
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL)) AND (size = ? OR (size IS NULL AND ? IS NULL))");
        $stmt->execute([$sessionId, $productId, $variantId, $variantId, $size, $size]);
    }
    
    $existing = $stmt->fetch();
    
    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        return $stmt->execute([$newQty, $existing['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, session_id, product_id, variant_id, size, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $sessionId, $productId, $variantId, $size, $quantity]);
    }
}

// Robust redirection with JS fallback
function redirect_to($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    }

    // JS Fallback
    $jsonUrl = json_encode($url);
    echo '<script type="text/javascript">';
    echo 'window.location.href=' . $jsonUrl . ';';
    echo '</script>';
    echo '<noscript>';
    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo '<meta http-equiv="refresh" content="0;url=' . $safeUrl . '" />';
    echo '</noscript>';
    exit;
}

// Migrate guest cart items to user account
function migrateCart($pdo, $sessionId, $userId) {
    if (!$userId) return false;
    
    // Find all guest items
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE session_id = ? AND user_id IS NULL");
    $stmt->execute([$sessionId]);
    $guestItems = $stmt->fetchAll();
    
    foreach ($guestItems as $item) {
        // Use existing addToCart logic which handles merging
        addToCart(
            $pdo, 
            $item['product_id'], 
            $item['quantity'], 
            $item['variant_id'], 
            $userId, 
            $sessionId, 
            $item['size']
        );
        
        // Remove the guest version of this specific record
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$item['id']]);
    }
    return true;
}

// Update setting value in database
function updateSetting($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value = ?");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}
