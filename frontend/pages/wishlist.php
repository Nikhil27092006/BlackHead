<style>
.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-xl);
    padding: var(--spacing-xl) 0;
}

.wishlist-item {
    position: relative;
    border: 1px solid var(--border-color);
    padding: var(--spacing-md);
}

.remove-wishlist {
    position: absolute;
    top: 10px;
    right: 10px;
    background: white;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: var(--error-color);
}
</style>

<?php
if (!isLoggedIn()) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT p.*, pi.image as product_image, w.id as wishlist_id 
                     FROM wishlist w 
                     JOIN products p ON w.product_id = p.id 
                     LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
                     WHERE w.user_id = ?");
$stmt->execute([$userId]);
$wishlistItems = $stmt->fetchAll();
?>

<div class="container">
    <h1 style="font-weight: 900; text-transform: uppercase;">My Wishlist (<?php echo count($wishlistItems); ?>)</h1>

    <?php if(count($wishlistItems) > 0): ?>
    <div class="wishlist-grid">
        <?php foreach($wishlistItems as $item): ?>
        <div class="wishlist-item">
            <form action="backend/handlers/wishlist_handler.php" method="POST">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                <button type="submit" class="remove-wishlist"><i class="fa-solid fa-xmark"></i></button>
            </form>
            
            <div style="aspect-ratio: 1/1; background: var(--light-bg); margin-bottom: var(--spacing-md); overflow: hidden;">
                <img src="assets/images/<?php echo $item['product_image'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $item['name']; ?>" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            
            <h3 style="font-weight: 700; text-transform: uppercase; margin-bottom: 5px; font-size: 16px;"><?php echo $item['name']; ?></h3>
            <p class="mb-3"><?php echo formatPrice($item['price']); ?></p>
            
            <form action="backend/handlers/cart_handler.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 12px; padding: 10px;">Move to Bag</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div style="text-align: center; padding: 100px 0;">
            <i class="fa-regular fa-heart" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p>Your wishlist is empty.</p>
            <a href="index.php?page=products" class="btn btn-primary mt-3">Explore Products</a>
        </div>
    <?php endif; ?>
</div>
