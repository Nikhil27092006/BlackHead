<?php
$query = isset($_GET['q']) ? $_GET['q'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$categoryId = isset($_GET['category']) ? $_GET['category'] : null;

// Build the Base SQL
$sql = "SELECT p.*, c.name as cat_name, pi.image as product_image,
        (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id) as total_stock
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
        WHERE 1=1";

$params = [];
if ($query) {
    $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
    $params[] = "%$query%";
}

if ($categoryId) {
    $sql .= " AND p.category_id = ?";
    $params[] = $categoryId;
}

// Handle Sorting
switch($sort) {
    case 'price_low': $sql .= " ORDER BY p.price ASC"; break;
    case 'price_high': $sql .= " ORDER BY p.price DESC"; break;
    case 'stock_low': $sql .= " ORDER BY total_stock ASC"; break;
    default: $sql .= " ORDER BY p.id DESC"; break;
}

$prods = $pdo->prepare($sql);
$prods->execute($params);
$prods = $prods->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!-- Action Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
    <div>
        <h3 style="margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -1px; font-family: 'Syne', sans-serif; text-transform: uppercase;">Product Catalog</h3>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--admin-text-muted);">Manage your active drops and inventory</p>
    </div>
    <a href="index.php?page=add_product" class="btn btn-primary" style="padding: 14px 28px; border-radius: 12px; font-size: 12px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-plus"></i> NEW PRODUCT
    </a>
</div>

<!-- Main Catalog Layout -->
<div class="products-grid-layout">
    <!-- Filters Sidebar -->
    <aside class="filters-sidebar-wrap">
        <div class="filters-glass-sidebar">
            <div style="margin-bottom: 30px;">
                <h4 style="margin: 0 0 20px 0; font-size: 11px; font-weight: 800; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1.5px;">Categories</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="index.php?page=products&q=<?php echo urlencode($query); ?>&sort=<?php echo $sort; ?>" 
                       style="padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; color: <?php echo !$categoryId ? '#fff' : 'rgba(255,255,255,0.4)'; ?>; background: <?php echo !$categoryId ? 'rgba(139,92,246,0.15)' : 'transparent'; ?>; transition: all 0.3s ease; text-decoration: none; border: 1px solid <?php echo !$categoryId ? 'rgba(139,92,246,0.2)' : 'transparent'; ?>;">
                       All Collections
                    </a>
                    <?php foreach($categories as $cat): ?>
                        <a href="index.php?page=products&category=<?php echo $cat['id']; ?>&q=<?php echo urlencode($query); ?>&sort=<?php echo $sort; ?>" 
                           style="padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; color: <?php echo $categoryId == $cat['id'] ? '#fff' : 'rgba(255,255,255,0.4)'; ?>; background: <?php echo $categoryId == $cat['id'] ? 'rgba(139,92,246,0.15)' : 'transparent'; ?>; transition: all 0.3s ease; text-decoration: none; border: 1px solid <?php echo $categoryId == $cat['id'] ? 'rgba(139,92,246,0.2)' : 'transparent'; ?>;">
                           <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="padding-top: 25px; border-top: 1px solid var(--admin-border);">
                <h4 style="margin: 0 0 20px 0; font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 1.5px;">Inventory Alert</h4>
                <div style="font-size: 12px; line-height: 1.6; color: rgba(255,255,255,0.3);">
                    Displays red indicator for units under <strong>5</strong>. All experimental subjects are currently set to <strong>Active</strong>.
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Stream -->
    <div class="products-content-stream">
        <!-- Search & Sort Controls -->
        <div class="products-control-bar">
            <div class="results-count">
                Showing <strong><?php echo count($prods); ?></strong> experimental subjects
            </div>
            
            <div style="display: flex; gap: 20px; align-items: center;">
                <form method="GET" action="index.php" style="border:none; padding:0; margin:0; background:transparent; display: flex;">
                    <input type="hidden" name="page" value="products">
                    <?php if($categoryId): ?><input type="hidden" name="category" value="<?php echo $categoryId; ?>"><?php endif; ?>
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-size: 13px;"></i>
                        <input type="text" name="q" class="admin-search-input" placeholder="Search the lab..." value="<?php echo htmlspecialchars($query); ?>" style="padding-left: 45px; background: #111118; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; color: #fff; font-size: 13px; height: 44px; min-width: 250px; transition: all 0.3s ease;" onfocus="this.style.borderColor='var(--admin-accent)'; this.style.boxShadow='0 0 0 4px rgba(139,92,246,0.1)';" onblur="this.style.borderColor='rgba(255,255,255,0.08)'; this.style.boxShadow='none';">
                    </div>
                </form>

                <div class="sort-wrap">
                    <span class="sort-label">ORDER BY:</span>
                    <select class="sort-custom-select" onchange="window.location.href='index.php?page=products&category=<?php echo $categoryId; ?>&q=<?php echo urlencode($query); ?>&sort='+this.value">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>LATEST DROPS</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>PRICE: LOWEST</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>PRICE: HIGHEST</option>
                        <option value="stock_low" <?php echo $sort == 'stock_low' ? 'selected' : ''; ?>>STOCK: ALERT</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="product-grid-new">
            <?php if(empty($prods)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 100px 0; background: var(--admin-card-bg); border-radius: 20px; border: 1px dashed var(--admin-border);">
                    <i class="fa-solid fa-box-open" style="font-size: 48px; color: var(--admin-text-muted); margin-bottom: 20px;"></i>
                    <h4 style="margin: 0; color: #fff;">No products found</h4>
                    <p style="color: var(--admin-text-muted); margin-top: 10px;">Try adjusting your filters or search query</p>
                </div>
            <?php else: ?>
                <?php foreach($prods as $p): ?>
                <div class="admin-product-card">
                    <div class="admin-product-thumb" style="position: relative;">
                        <img src="../../assets/images/<?php echo $p['product_image'] ?: 'placeholder.jpg'; ?>" alt="">
                        <div style="position: absolute; top: 15px; right: 15px; display: flex; gap: 8px; z-index: 10;">
                            <a href="index.php?page=edit_product&id=<?php echo $p['id']; ?>" class="action-btn-sleek" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px);"><i class="fa-solid fa-pen"></i></a>
                            <form action="product_handler.php" method="POST" onsubmit="return confirm('Delete this experimental subject?');" style="display:inline; padding:0; border:none; background:transparent;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="action-btn-sleek action-btn-delete" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); border: none; padding:0;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                        <?php if($p['total_stock'] <= 5): ?>
                            <span style="position: absolute; bottom: 15px; left: 15px; background: #ef4444; color: #fff; font-size: 9px; font-weight: 800; padding: 4px 10px; border-radius: 100px; text-transform: uppercase;">Low Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="admin-product-info">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 800; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;"><?php echo $p['cat_name']; ?></span>
                            <span style="font-size: 10px; font-weight: 700; color: #10b981; text-transform: uppercase; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 4px;">Active</span>
                        </div>
                        <h4 style="margin: 5px 0;"><?php echo htmlspecialchars($p['name']); ?></h4>
                        <div class="admin-product-sku">SKU: <?php echo htmlspecialchars($p['sku']); ?></div>
                    </div>

                    <div class="admin-product-meta" style="margin-top: auto; padding-top: 15px; border-top: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                        <div class="admin-product-price" style="font-weight: 800; color: var(--admin-accent); font-size: 16px;"><?php echo formatPrice($p['price']); ?></div>
                        <div class="admin-product-stock">
                            <span style="opacity: 0.5; font-size: 10px; text-transform: uppercase; margin-right: 5px;">Inv:</span>
                            <span style="<?php echo $p['total_stock'] <= 5 ? 'color: #ef4444;' : 'color: #fff;'; ?>"><?php echo (int)$p['total_stock']; ?> units</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Home Highlights Manager -->
<div id="highlights" class="admin-card-premium" style="margin-top: 60px; padding: 35px;">
    <div style="margin-bottom: 25px;">
        <h4 style="margin: 0; font-size: 18px; font-weight: 900; color: var(--admin-accent); font-family: 'Syne', sans-serif; text-transform: uppercase;">Home Highlights</h4>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--admin-text-muted);">Manage sections like "Currently Hot" and "Find Your Style"</p>
    </div>

    <form action="highlights_handler.php" method="POST" style="display: flex; gap: 15px; align-items: flex-end; margin-bottom: 30px; border:none; padding:0; background:transparent;">
        <input type="hidden" name="action" value="add">
        <div class="premium-form-group" style="margin: 0; flex: 0 0 200px;">
            <label style="font-size: 10px;">SECTION TYPE</label>
            <select name="type" class="premium-input" style="height: 44px; padding: 0 15px;">
                <option value="currently_hot">Currently Hot</option>
                <option value="find_your_style">Find Your Style</option>
            </select>
        </div>
        <div class="premium-form-group" style="margin: 0; flex: 1;">
            <label style="font-size: 10px;">HIGHLIGHT TEXT</label>
            <input type="text" name="text" class="premium-input" placeholder="Enter bold statement..." required style="height: 44px;">
        </div>
        <button type="submit" class="btn btn-primary" style="height: 44px; padding: 0 30px;">ADD NEW</button>
    </form>

    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Display Text</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $highlights = $pdo->query("SELECT * FROM highlights ORDER BY type, id DESC")->fetchAll();
            foreach($highlights as $h): ?>
                <tr>
                    <td style="width: 200px;">
                        <span style="font-weight:700; color:var(--admin-accent); font-size: 11px; text-transform: uppercase;"><?= $h['type'] === 'currently_hot' ? 'Currently Hot' : 'Find Your Style'; ?></span>
                    </td>
                    <td>
                        <form action="highlights_handler.php" method="POST" style="display:flex; gap:10px; align-items:center; border:none; padding:0; background:transparent;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $h['id']; ?>">
                            <input type="text" name="text" class="premium-input" value="<?= htmlspecialchars($h['text']); ?>" required style="padding: 8px 15px; font-size: 13px;">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 15px; font-size: 10px; box-shadow: none;">Save</button>
                        </form>
                    </td>
                    <td style="text-align:right;">
                        <form action="highlights_handler.php" method="POST" onsubmit="return confirm('Expunge this highlight?');" style="display:inline; border:none; padding:0; background:transparent;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $h['id']; ?>">
                            <button type="submit" class="action-btn-sleek action-btn-delete" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: none; padding:0;"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
