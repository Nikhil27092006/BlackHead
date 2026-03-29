

<?php
$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();

// Fetch items (Simplified for now, should ideally use getCartItems function)
$sql = "SELECT c.id as cart_id, c.quantity, c.size, p.id as product_id, p.name, p.price, p.slug, pi.image as product_image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
        WHERE (c.user_id = ? OR (c.user_id IS NULL AND c.session_id = ?))";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $sessionId]);
$cartItems = $stmt->fetchAll();

$subtotal = 0;
$totalQuantity = 0;
foreach($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalQuantity += $item['quantity'];
}
$shipping = (count($cartItems) > 0 && $subtotal < 1000) ? 99 : 0;
$total = $subtotal + $shipping;
?>

<div class="container">
    <h1 style="font-weight: 900; text-transform: uppercase;">Your Shopping Bag (<?php echo $totalQuantity; ?>)</h1>
    
    <div class="cart-layout grid-2-1" style="padding: var(--spacing-xxl) 0;">
        <!-- Cart Items -->
        <div class="cart-items-section">
            <?php if(count($cartItems) > 0): ?>
            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="width: 150px;">Quantity</th>
                            <th style="width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cartItems as $item): ?>
                        <tr class="cart-item">
                            <td>
                                <div style="display: flex; gap: var(--spacing-md);">
                                    <div style="width: 100px; height: 100px; background: var(--light-bg); overflow: hidden;">
                                        <img src="assets/images/<?php echo $item['product_image'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $item['name']; ?>" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="display: flex; flex-direction: column; justify-content: center;">
                                        <a href="index.php?page=product&id=<?php echo $item['product_id']; ?>" style="font-weight: 700; text-transform: uppercase;"><?php echo $item['name']; ?></a>
                                        <div style="font-size: 13px; margin: 5px 0;">
                                            <?php if($item['size']): ?>
                                                <span style="background: #eee; padding: 2px 8px; font-weight: 700;">SIZE: <?php echo $item['size']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p style="font-size: 14px; color: var(--light-text);"><?php echo formatPrice($item['price']); ?></p>
                                        <form action="backend/handlers/cart_handler.php" method="POST" style="margin-top: 10px;">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <button type="submit" class="remove-btn">Remove Item</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form action="backend/handlers/cart_handler.php" method="POST" id="update-form-<?php echo $item['cart_id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-btn" onclick="this.nextElementSibling.stepDown(); this.closest('form').submit();">-</button>
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" readonly>
                                        <button type="button" class="quantity-btn" onclick="this.previousElementSibling.stepUp(); this.closest('form').submit();">+</button>
                                    </div>
                                </form>
                            </td>
                            <td style="font-weight: 700;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div style="text-align: center; padding: 100px 0;">
                    <i class="fa-solid fa-cart-arrow-down" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                    <p>Your cart is empty.</p>
                    <a href="index.php?page=products" class="btn btn-primary mt-3">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Summary -->
        <?php if(count($cartItems) > 0): ?>
        <div class="cart-summary">
            <h3 style="font-weight: 900; text-transform: uppercase; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; margin-bottom: var(--spacing-md);">Order Summary</h3>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?php echo formatPrice($subtotal); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span><?php echo ($shipping == 0) ? 'FREE' : formatPrice($shipping); ?></span>
            </div>
            <div class="summary-row">
                <span>Estimates Taxes</span>
                <span>Calculated at checkout</span>
            </div>

            <div class="summary-row summary-total">
                <span>Total</span>
                <span><?php echo formatPrice($total); ?></span>
            </div>

            <a href="index.php?page=checkout" class="btn btn-primary" style="width: 100%; padding: 18px; margin-top: var(--spacing-xl); display: flex; justify-content: space-between; align-items: center;">
                <span>CHECKOUT</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>

            <div style="margin-top: var(--spacing-lg); font-size: 12px; color: var(--light-text);">
                <p class="mb-2">WE ACCEPT:</p>
                <div style="display: flex; gap: 10px; font-size: 20px;">
                    <i class="fa-brands fa-cc-visa"></i>
                    <i class="fa-brands fa-cc-mastercard"></i>
                    <i class="fa-brands fa-cc-apple-pay"></i>
                    <i class="fa-brands fa-cc-paypal"></i>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
