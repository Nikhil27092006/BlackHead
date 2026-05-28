<?php
$productId = $_GET['id'] ?? null;
if (!$productId) {
    echo "<script>window.location.href='index.php?page=products';</script>";
    exit;
}

// Fetch main product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo "<script>window.location.href='index.php?page=404';</script>";
    exit;
}

// Fetch all images for the gallery
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC");
$stmt->execute([$productId]);
$gallery = $stmt->fetchAll();
?>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .product-gallery { position: relative; width: 100%; aspect-ratio: 4/5; max-height: 700px; }
    .swiper { width: 100%; height: 100%; border-radius: 20px; overflow: hidden; background: #f8fafc; border: 1px solid #f1f5f9; }
    .swiper-slide { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
    .swiper-slide img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .swiper-pagination-bullet-active { background: #000 !important; }
    .swiper-button-next, .swiper-button-prev { color: #000 !important; transform: scale(0.7); opacity: 0; transition: 0.3s; }
    .product-gallery:hover .swiper-button-next, .product-gallery:hover .swiper-button-prev { opacity: 0.5; }
    .swiper-button-next:hover, .swiper-button-prev:hover { opacity: 1 !important; }
</style>

<div class="container">
    <div class="pdp-layout">
        <!-- Left: Image Gallery (Swiper Slider) -->
        <div class="product-gallery">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php if(!empty($gallery)): ?>
                        <?php foreach($gallery as $img): ?>
                            <div class="swiper-slide">
                                <img src="assets/images/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <img src="assets/images/placeholder.jpg" alt="No image">
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Pagination & Nav -->
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <!-- Right: Details & Purchase -->
        <div class="product-details">
            <div class="pdp-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
            <h1 class="pdp-name"><?php echo htmlspecialchars($product['name']); ?></h1>
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
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
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
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn btn-outline" style="padding: 18px 24px;">
                        <i class="<?php echo isLoggedIn() && isInWishlist($pdo, $_SESSION['user_id'], $product['id']) ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                    </button>
                </form>
            </div>

            <div class="pdp-section">
                <div class="pdp-section-title">Product Description</div>
                <p style="color: var(--light-text); font-size: 14px; line-height: 1.6;">
                    <?php echo htmlspecialchars($product['description'] ?: "No description provided."); ?>
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

    </section>

    <!-- Customer Reviews Section -->
    <?php
    // Fetch average rating and count
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM product_reviews WHERE product_id = ? AND status = 'active'");
    $stmt->execute([$product['id']]);
    $review_stats = $stmt->fetch();
    $avg_rating = round($review_stats['avg_rating'] ?? 0, 1);
    $review_count = $review_stats['review_count'] ?? 0;

    // Fetch active reviews
    $stmt = $pdo->prepare("SELECT * FROM product_reviews WHERE product_id = ? AND status = 'active' ORDER BY created_at DESC");
    $stmt->execute([$product['id']]);
    $reviews = $stmt->fetchAll();
    ?>
    
    <section id="reviews" style="margin-top: 80px; padding-top: 60px; border-top: 1px solid #eee;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px; flex-wrap: wrap; gap: 30px;">
            <div>
                <h2 style="font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; text-transform: uppercase; margin-bottom: 12px; letter-spacing: -0.5px;">Customer Voices</h2>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="display: flex; color: #ffab00; font-size: 18px;">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="fa-<?php echo ($i <= $avg_rating) ? 'solid' : (($i - 0.5 <= $avg_rating) ? 'solid fa-star-half-stroke' : 'regular'); ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <span style="font-weight: 800; font-size: 18px;"><?php echo $avg_rating; ?> / 5</span>
                    <span style="color: #64748b; font-size: 14px; font-weight: 500;">(Based on <?php echo $review_count; ?> reviews)</span>
                </div>
            </div>
            
            <?php if(isLoggedIn()): ?>
                <button onclick="document.getElementById('reviewForm').style.display = 'block'; this.style.display = 'none';" class="btn" style="background: #000; color: #fff; border-radius: 100px; padding: 14px 28px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Write a Review</button>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-outline" style="border-radius: 100px; padding: 14px 28px; font-weight: 700; font-size: 13px; text-transform: uppercase;">Login to Review</a>
            <?php endif; ?>
        </div>

        <!-- Hidden Review Form -->
        <div id="reviewForm" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; padding: 40px; border-radius: 24px; margin-bottom: 50px; animation: slideDown 0.4s ease;">
            <style>@keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }</style>
            <h3 style="font-weight: 800; margin-bottom: 24px; text-transform: uppercase; font-size: 18px;">Share Your Experience</h3>
            <form action="backend/handlers/review_handler.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 10px;">Select Rating</label>
                    <div style="display: flex; gap: 10px;" id="star-rating">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" style="display: none;" required <?php echo $i==5 ? 'checked' : ''; ?>>
                            <label for="star<?php echo $i; ?>" style="cursor: pointer; font-size: 24px; color: #cbd5e1; transition: 0.2s;" class="star-label"><i class="fa-solid fa-star"></i></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 10px;">Your Thoughts</label>
                    <textarea name="comment" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; font-family: inherit; font-size: 14px; min-height: 120px;" placeholder="Tell us about the quality, fit, and style..." required></textarea>
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary" style="padding: 14px 40px; border-radius: 100px;">Post Review</button>
                    <button type="button" onclick="document.getElementById('reviewForm').style.display='none'; document.querySelector('button[onclick*=\'reviewForm\']').style.display='block';" class="btn" style="background: none; border: 1px solid #cbd5e1; border-radius: 100px; padding: 14px 28px;">Cancel</button>
                </div>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 30px;">
            <?php if(count($reviews) > 0): ?>
                <?php foreach($reviews as $review): ?>
                    <div style="background: #fff; border: 1px solid #f1f5f9; padding: 30px; border-radius: 20px; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <div style="color: #ffab00; font-size: 13px;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="fa-<?php echo ($i <= $review['rating']) ? 'solid' : 'regular'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span style="font-size: 11px; color: #94a3b8; font-weight: 600;"><?php echo date('d M, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                        <p style="font-size: 14px; line-height: 1.6; color: #1e293b; font-weight: 500; margin-bottom: 20px;">"<?php echo nl2br(htmlspecialchars($review['comment'])); ?>"</p>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; color: #6366f1;">
                                <?php echo strtoupper(htmlspecialchars(substr($review['user_name'], 0, 1))); ?>
                            </div>
                            <span style="font-size: 13px; font-weight: 700; color: #475569;"><?php echo htmlspecialchars($review['user_name']); ?></span>
                            <span style="width: 4px; height: 4px; background: #cbd5e1; border-radius: 50%;"></span>
                            <span style="font-size: 11px; font-weight: 800; color: #22c55e; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa-solid fa-circle-check"></i> Verified Buyer</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: #f8fafc; border-radius: 24px; border: 1px dashed #e2e8f0;">
                    <i class="fa-regular fa-comment-dots" style="font-size: 40px; color: #cbd5e1; margin-bottom: 20px;"></i>
                    <p style="color: #64748b; font-weight: 600;">Be the first to share your thoughts on this piece.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Star Rating Logic
        document.querySelectorAll('.star-label').forEach(label => {
            label.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('for').replace('star', ''));
                document.querySelectorAll('.star-label').forEach((s, ix) => {
                    s.style.color = ix < rating ? '#ffab00' : '#cbd5e1';
                });
            });
            // Initial state set by HTML 'checked'
            if(document.getElementById(label.getAttribute('for')).checked) {
                const rating = parseInt(label.getAttribute('for').replace('star', ''));
                document.querySelectorAll('.star-label').forEach((s, ix) => {
                    if(ix < rating) s.style.color = '#ffab00';
                });
            }
        });
    </script>
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

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  var swiper = new Swiper(".mySwiper", {
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    autoplay: {
      delay: 3500,
      disableOnInteraction: false,
    },
    grabCursor: true,
  });
</script>
