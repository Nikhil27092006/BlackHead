<style>
.pdp-layout {
    display: grid;
    grid-template-columns: 1fr 450px;
    gap: var(--spacing-xxl);
    padding: var(--spacing-xxl) 0;
}

.product-gallery {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.main-image {
    background: var(--light-bg);
    aspect-ratio: 1/1;
    overflow: hidden;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.product-details {
    position: sticky;
    top: 100px;
}

.pdp-brand {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--light-text);
    margin-bottom: 5px;
}

.pdp-name {
    font-size: 2.5rem;
    font-weight: 900;
    text-transform: uppercase;
    line-height: 1.1;
    margin-bottom: var(--spacing-sm);
}

.pdp-price {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: var(--spacing-xl);
}

.pdp-section {
    margin-bottom: var(--spacing-xl);
}

.pdp-section-title {
    font-size: 14px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: var(--spacing-md);
}

.size-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.size-box {
    padding: 12px 20px;
    border: 1px solid var(--border-color);
    background: white;
    cursor: pointer;
    font-weight: 700;
    transition: var(--transition-fast);
}

.size-box:hover, .size-box.active, .color-box:hover, .color-box.active {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
}

@media (max-width: 992px) {
    .pdp-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$productId = $_GET['id'] ?? null;
if (!$productId) {
    echo "<script>window.location.href='index.php?page=products';</script>";
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, pi.image as product_image FROM products p 
                     LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
                     WHERE p.id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo "<script>window.location.href='index.php?page=404';</script>";
    exit;
}
?>

<div class="container">
    <div class="pdp-layout">
        <!-- Left: Image Gallery -->
        <div class="product-gallery">
            <div class="main-image">
                <img src="assets/images/<?php echo $product['product_image'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            <!-- More images could go here -->
        </div>

        <!-- Right: Details & Purchase -->
        <div class="product-details">
            <div class="pdp-brand"><?php echo $product['brand']; ?></div>
            <h1 class="pdp-name"><?php echo $product['name']; ?></h1>
            <div class="pdp-price"><?php echo formatPrice($product['price']); ?></div>

            <?php
            // Fetch variants for this product
            $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ? AND status = 'active' ORDER BY id ASC");
            $stmt->execute([$productId]);
            $variants = $stmt->fetchAll();
            
            // Group variants by color
            $colors = [];
            foreach($variants as $v) {
                $c = !empty($v['color']) ? $v['color'] : 'Default';
                $colors[$c][] = $v;
            }
            $first_color = array_key_first($colors);
            $variants_json = json_encode($variants);
            ?>

            <?php if(count($colors) > 0 && !(count($colors) == 1 && $first_color == 'Default')): ?>
            <div class="pdp-section">
                <div class="pdp-section-title">Select Color</div>
                <div class="color-options" id="color-selector" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php foreach($colors as $colorName => $colorVariants): ?>
                        <div class="color-box <?php echo $colorName === $first_color ? 'active' : ''; ?>" 
                             data-color="<?php echo htmlspecialchars($colorName); ?>"
                             style="padding: 10px 15px; border: 1px solid var(--border-color); cursor: pointer; font-weight: 700; transition: all 0.2s;">
                            <?php echo htmlspecialchars($colorName); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="pdp-section">
                <div class="pdp-section-title">Select Size</div>
                <div class="size-options" id="size-selector">
                    <!-- populated by JS -->
                </div>
                <!-- Stock Alert Placeholder -->
                <div id="stock-alert" style="margin-top: 10px; font-weight: 700; font-size: 13px; color: #ff4757; min-height: 20px;"></div>
                <a href="index.php?page=size-guide" style="font-size: 12px; text-decoration: underline; margin-top: 10px; display: inline-block;">Size Guide</a>
            </div>

            <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-xl);">
                <form action="backend/handlers/cart_handler.php" method="POST" style="flex-grow: 1;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="variant_id" id="selected-variant-id" value="<?php echo !empty($variants) ? $variants[0]['id'] : ''; ?>">
                    <input type="hidden" name="size" id="selected-size" value="<?php echo !empty($variants) ? $variants[0]['size'] : ''; ?>">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 18px; display: flex; justify-content: space-between; align-items: center;">
                        <span>ADD TO CART</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
                <form action="backend/handlers/wishlist_handler.php" method="POST">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn btn-outline" style="padding: 18px 24px;">
                        <i class="<?php echo isLoggedIn() && isInWishlist($pdo, $_SESSION['user_id'], $product['id']) ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                    </button>
                </form>
            </div>

            <div class="pdp-section">
                <div class="pdp-section-title">Product Description</div>
                <p style="color: var(--light-text); font-size: 14px; line-height: 1.6;">
                    <?php echo $product['description'] ?: "No description provided."; ?>
                </p>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: var(--spacing-md);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 14px;">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Free delivery on orders over ₹999</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>30-day free return policy</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products or Recommendations can go here -->
</div>

<script>
const variantsData = <?php echo json_encode($colors); ?>;
let selectedColor = '<?php echo addslashes($first_color ?? 'Default'); ?>';
const sizeSelector = document.getElementById('size-selector');
const stockAlert = document.getElementById('stock-alert');
const hiddenVariantId = document.getElementById('selected-variant-id');
const hiddenSize = document.getElementById('selected-size');

function renderSizes(color) {
    sizeSelector.innerHTML = '';
    const variants = variantsData[color] || [];
    if(variants.length === 0) {
        sizeSelector.innerHTML = '<p style="color: var(--light-text); font-size: 12px;">No sizes available</p>';
        return;
    }
    
    let firstAvailableSet = false;
    
    variants.forEach((v, index) => {
        const box = document.createElement('div');
        const stockQuant = parseInt(v.stock_quantity || v.stock || 0);
        
        box.className = 'size-box';
        box.innerHTML = v.size || 'One Size';
        
        if (stockQuant <= 0) {
            box.style.opacity = '0.5';
            box.style.cursor = 'not-allowed';
            box.onclick = () => false;
        } else {
            box.onclick = () => {
                document.querySelectorAll('.size-box').forEach(b => b.classList.remove('active'));
                box.classList.add('active');
                if(hiddenSize) hiddenSize.value = v.size;
                if(hiddenVariantId) hiddenVariantId.value = v.id;
                updateStockAlert(stockQuant);
            };
            
            if (!firstAvailableSet) {
                box.classList.add('active');
                if(hiddenSize) hiddenSize.value = v.size;
                if(hiddenVariantId) hiddenVariantId.value = v.id;
                updateStockAlert(stockQuant);
                firstAvailableSet = true;
            }
        }
        sizeSelector.appendChild(box);
    });
    
    if (!firstAvailableSet) {
        if(hiddenSize) hiddenSize.value = '';
        if(hiddenVariantId) hiddenVariantId.value = '';
        updateStockAlert(0);
    }
}

function updateStockAlert(stock) {
    if (stock > 0 && stock <= 3) {
        stockAlert.innerHTML = `<i class="fa-solid fa-fire"></i> HURRY! ONLY ${stock} ITEMS LEFT IN THIS COLOR/SIZE`;
    } else if (stock === 0) {
        stockAlert.innerHTML = `OUT OF STOCK`;
    } else {
        stockAlert.innerHTML = '';
    }
}

document.querySelectorAll('.color-box').forEach(box => {
    box.addEventListener('click', function() {
        document.querySelectorAll('.color-box').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedColor = this.getAttribute('data-color');
        renderSizes(selectedColor);
    });
});

// Init
renderSizes(selectedColor);
</script>
