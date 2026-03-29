<style>
/* -- Products Page Premium Styling -- */
.products-page-container {
    padding-top: 40px;
    padding-bottom: 100px;
    background: #050505;
    min-height: 100vh;
}

/* Page Header */
.page-intro-banner {
    position: relative;
    padding: 80px 0 60px;
    overflow: hidden;
    margin-bottom: 40px;
}
.page-bg-text {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18vw;
    font-weight: 900;
    color: rgba(255,255,255,0.02);
    white-space: nowrap;
    z-index: 0;
    pointer-events: none;
    font-family: 'Syne', sans-serif;
    text-transform: uppercase;
}
.page-title-wrap {
    position: relative;
    z-index: 1;
    text-align: center;
}
.page-eyebrow {
    display: block;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #8b5cf6;
    margin-bottom: 16px;
}
.page-main-title {
    font-family: 'Syne', sans-serif;
    font-size: clamp(40px, 5vw, 70px);
    font-weight: 800;
    text-transform: uppercase;
    line-height: 1;
}

/* Layout */
.products-grid-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 50px;
}

/* Sidebar */
.filters-glass-sidebar {
    position: sticky;
    top: 120px;
    height: fit-content;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 24px;
}
.filter-block {
    margin-bottom: 35px;
}
.filter-block:last-child { margin-bottom: 0; }
.filter-block-title {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: rgba(255,255,255,0.4);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.filter-block-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.08);
}

.cat-pill-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.cat-pill-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: rgba(255,255,255,0.02);
    border: 1px solid transparent;
    border-radius: 12px;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.cat-pill-link:hover, .cat-pill-link.active {
    background: rgba(139, 92, 246, 0.1);
    border-color: rgba(139, 92, 246, 0.3);
    color: #8b5cf6;
}
.cat-count {
    font-size: 10px;
    font-weight: 700;
    opacity: 0.5;
}

/* Sidebar Search Live Preview */
.sidebar-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(10,10,10,0.95);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    z-index: 100;
    display: none;
    flex-direction: column;
    margin-top: 5px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    max-height: 400px;
    max-height: 400px;
    overflow-y: auto;
}

/* Control Bar */
.products-control-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 5px;
}
.results-count {
    font-size: 14px;
    color: rgba(255,255,255,0.5);
}
.sort-wrap {
    display: flex;
    align-items: center;
    gap: 15px;
}
.sort-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.4);
}
.sort-custom-select {
    background: #111;
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    outline: none;
    cursor: pointer;
}

/* Responsive */
@media (max-width: 1100px) {
    .products-grid-layout { grid-template-columns: 1fr; }
    .filters-glass-sidebar { position: static; margin-bottom: 40px; }
}
.product-grid-new {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}
@media (max-width: 768px) {
    .product-grid-new {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }
}
@media (max-width: 480px) {
    .product-grid-new {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarSearch = document.getElementById('sidebarSearchInput');
    const sidebarResults = document.getElementById('sidebarSearchResults');
    let dbTimer;

    if (sidebarSearch) {
        sidebarSearch.addEventListener('input', function() {
            clearTimeout(dbTimer);
            const val = this.value.trim();
            if (val.length < 2) {
                sidebarResults.style.display = 'none';
                return;
            }

            dbTimer = setTimeout(() => {
                fetch(`backend/handlers/ajax_search.php?q=${encodeURIComponent(val)}`)
                    .then(r => r.json())
                    .then(data => {
                        sidebarResults.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(p => {
                                const div = document.createElement('a');
                                div.href = p.url;
                                div.style.cssText = 'display:flex; align-items:center; gap:10px; padding:10px; text-decoration:none; transition:0.2s; border-radius:8px;';
                                div.onmouseover = () => { div.style.background = 'rgba(255,255,255,0.05)'; };
                                div.onmouseout = () => { div.style.background = 'transparent'; };
                                div.innerHTML = `
                                    <div style="width:36px; height:36px; border-radius:6px; overflow:hidden; background:#111;">
                                        <img src="${p.image}" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="color:#fff; font-size:12px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${p.name}</div>
                                        <div style="color:#8b5cf6; font-size:10px; font-weight:800;">${p.price}</div>
                                    </div>
                                `;
                                sidebarResults.appendChild(div);
                            });
                        } else {
                            sidebarResults.innerHTML = '<div style="padding:15px; text-align:center; color:rgba(255,255,255,0.3); font-size:11px;">No matches found</div>';
                        }
                        sidebarResults.style.display = 'flex';
                    });
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!sidebarSearch.contains(e.target) && !sidebarResults.contains(e.target)) sidebarResults.style.display = 'none';
        });
    }
});
</script>


<?php
$categoryId = $_GET['category'] ?? null;
$sortBy = $_GET['sort'] ?? 'newest';
$query = $_GET['q'] ?? '';

// Build Query
$sql = "SELECT p.*, pi.image as product_image,
        (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id) as total_stock
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
        WHERE p.status = 'active'";

$params = [];
if ($categoryId) {
    if (is_numeric($categoryId)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $categoryId;
    } else {
        $sql .= " AND p.category_id IN (SELECT id FROM categories WHERE slug = ?)";
        $params[] = $categoryId;
    }
}

if ($query) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}

switch($sortBy) {
    case 'price_low': $sql .= " ORDER BY p.price ASC"; break;
    case 'price_high': $sql .= " ORDER BY p.price DESC"; break;
    default: $sql .= " ORDER BY p.created_at DESC"; break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(id) FROM products WHERE category_id = c.id AND status = 'active') as p_count FROM categories c WHERE status = 'active'")->fetchAll();
?>

<div class="products-page-container">
    <!-- Intro Banner -->
    <div class="page-intro-banner">
        <div class="page-bg-text">COLLECTION</div>
        <div class="container">
            <div class="page-title-wrap">
                <span class="page-eyebrow">The Laboratory</span>
                <h1 class="page-main-title">
                    <?php 
                    if ($categoryId) {
                        foreach($categories as $cat) if($cat['slug'] == $categoryId) echo htmlspecialchars($cat['name']);
                    } else {
                        echo "ALL DROPS";
                    }
                    ?>
                </h1>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="products-grid-layout">
            <!-- Professional Sidebar -->
            <aside class="filters-sidebar-wrap">
                <div class="filters-glass-sidebar">
                    <div class="sidebar-search-wrap" style="position:relative;">
                        <form action="index.php" method="GET">
                            <input type="hidden" name="page" value="products">
                            <i class="fa-solid fa-magnifying-glass sidebar-search-icon" style="position: absolute; left: 18px; top: 18px; color: rgba(255,255,255,0.3); font-size:14px;"></i>
                            <input type="text" name="q" id="sidebarSearchInput" class="sidebar-search-input" placeholder="Search the lab..." value="<?php echo htmlspecialchars($query); ?>" autocomplete="off" style="width: 100%; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); padding: 14px 20px 14px 45px; border-radius: 12px; color: #fff; font-size: 14px; outline: none; transition: all 0.3s ease;" onfocus="this.style.borderColor='#8b5cf6'; this.style.background='rgba(255,255,255,0.05)';" onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.02)';">
                        </form>
                        <div id="sidebarSearchResults" class="sidebar-search-results"></div>
                    </div>

                    <div class="filter-block">
                        <h3 class="filter-block-title">Categories</h3>
                        <div class="cat-pill-list">
                            <a href="index.php?page=products" class="cat-pill-link <?php echo !$categoryId ? 'active' : ''; ?>">
                                <span>All Collections</span>
                                <span class="cat-count"><?php echo array_sum(array_column($categories, 'p_count')); ?></span>
                            </a>
                            <?php foreach($categories as $cat): ?>
                                <a href="index.php?page=products&category=<?php echo $cat['slug']; ?>" class="cat-pill-link <?php echo $categoryId == $cat['slug'] ? 'active' : ''; ?>">
                                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                    <span class="cat-count"><?php echo $cat['p_count']; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="filter-block">
                        <h3 class="filter-block-title">Refinement</h3>
                        <div style="padding: 10px 0;">
                            <p style="font-size: 13px; color: rgba(255,255,255,0.4); line-height: 1.6;">Filtered by current drop standards. Only active inventory items are shown.</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Stream -->
            <main>
                <div class="products-control-bar">
                    <div class="results-count">Showing <strong><?php echo count($products); ?></strong> exclusive pieces</div>
                    <div class="sort-wrap">
                        <span class="sort-label">ORDER BY:</span>
                        <select class="sort-custom-select" onchange="location.href='index.php?page=products&category=<?php echo $categoryId; ?>&q=<?php echo $query; ?>&sort='+this.value">
                            <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>LATEST DROPS</option>
                            <option value="price_low" <?php echo $sortBy == 'price_low' ? 'selected' : ''; ?>>PRICE: LOWEST</option>
                            <option value="price_high" <?php echo $sortBy == 'price_high' ? 'selected' : ''; ?>>PRICE: HIGHEST</option>
                        </select>
                    </div>
                </div>

                <div class="product-grid-new">
                    <?php if(empty($products)): ?>
                        <div class="empty-state-premium" style="grid-column: 1/-1; text-align: center; padding: 100px 0;">
                            <div style="font-size: 80px; margin-bottom: 20px; opacity: 0.1;"><i class="fa-solid fa-microchip"></i></div>
                            <h2 style="font-family: 'Syne', sans-serif; letter-spacing: 2px;">ZERO RESULTS FOUND</h2>
                            <p style="color: rgba(255,255,255,0.4);">No gear matches your current search parameters.</p>
                            <a href="index.php?page=products" class="btn-outline-glow" style="margin-top: 30px;">Clear Filters</a>
                        </div>
                    <?php else: ?>
                        <?php foreach($products as $p): 
                            $image = $p['product_image'] ?: 'placeholder.jpg';
                        ?>
                        <div class="premium-product-card reveal-product">
                            <div class="product-image-container">
                                <?php if(isset($p['total_stock']) && $p['total_stock'] > 0 && $p['total_stock'] <= 3): ?>
                                    <div class="product-badge-premium" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">V. LOW STOCK</div>
                                <?php endif; ?>
                                <a href="index.php?page=product&id=<?php echo $p['id']; ?>">
                                    <img src="assets/images/<?php echo $image; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                                </a>
                                <form action="backend/handlers/wishlist_handler.php" method="POST">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="product-wishlist-btn">
                                        <i class="<?php echo isLoggedIn() && isInWishlist($pdo, $_SESSION['user_id'], $p['id']) ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="premium-product-info">
                                <div>
                                    <div class="premium-product-cat">Laboratroy Order #<?php echo 1000 + $p['id']; ?></div>
                                    <a href="index.php?page=product&id=<?php echo $p['id']; ?>" class="premium-product-name"><?php echo htmlspecialchars($p['name']); ?></a>
                                    
                                    <div class="premium-product-desc" style="font-size: 13px; color: rgba(255,255,255,0.45); line-height: 1.6; margin-bottom: 24px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3.2em;">
                                        <?php echo htmlspecialchars($p['description']); ?>
                                    </div>

                                    <div class="product-dna">
                                        <div class="dna-item"><i class="fa-solid fa-microchip"></i><span><span class="dna-badge">TECH</span> HIGH-DEN FABRIC</span></div>
                                        <div class="dna-item"><i class="fa-solid fa-ruler-combined"></i><span><span class="dna-badge">FIT</span> MODERN CUT</span></div>
                                    </div>
                                </div>
                                <div style="margin-top: auto;">
                                    <div class="premium-product-price" style="margin-bottom: 16px;"><?php echo formatPrice($p['price']); ?></div>
                                    <a href="index.php?page=product&id=<?php echo $p['id']; ?>" class="btn-product-shop">
                                        BUY NOW <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gsap !== 'undefined') {
        gsap.from('.page-bg-text', {
            y: 50, opacity: 0, duration: 1.5, ease: 'power4.out', delay: 0.2
        });
        gsap.from('.page-title-wrap > *', {
            y: 30, opacity: 0, duration: 1, stagger: 0.1, ease: 'power3.out', delay: 0.4
        });
        gsap.from('.filters-glass-sidebar', {
            x: -50, opacity: 0, duration: 1, ease: 'power3.out', delay: 0.6
        });
        gsap.from('.reveal-product', {
            y: 60, opacity: 0, duration: 1.2, stagger: 0.1, ease: 'expo.out', delay: 0.8
        });
    }
});
</script>
