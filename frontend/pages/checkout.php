<style>
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: var(--spacing-xxl);
    padding: var(--spacing-xxl) 0;
}

.checkout-section {
    margin-bottom: var(--spacing-xxl);
}

.checkout-section h3 {
    font-weight: 900;
    text-transform: uppercase;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
    margin-bottom: var(--spacing-xl);
}

.address-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-md);
}

.address-card {
    border: 1px solid var(--border-color);
    padding: var(--spacing-md);
    cursor: pointer;
    position: relative;
    transition: var(--transition-fast);
}

.address-card.active {
    border-color: var(--primary-color);
    background: var(--light-bg);
}

.address-card input {
    position: absolute;
    top: 15px;
    right: 15px;
}

.payment-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid var(--border-color);
    cursor: pointer;
}

.payment-method.active {
    border-color: var(--primary-color);
}

@media (max-width: 768px) {
    .checkout-layout {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }
    
    .address-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
if (!isLoggedIn()) {
    $_SESSION['error'] = "Please login to checkout.";
    echo "<script>window.location.href='index.php?page=login&redirect=checkout';</script>";
    exit;
}

$userId = $_SESSION['user_id'];

// Get Cart Items and Total
$stmt = $pdo->prepare("SELECT c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

if (count($cartItems) == 0) {
    header("Location: index.php?page=cart");
    exit;
}

$subtotal = 0;
foreach($cartItems as $item) $subtotal += $item['price'] * $item['quantity'];
$shipping = (count($cartItems) > 0 && $subtotal < 1000) ? 99 : 0;
$total = $subtotal + $shipping;

// Get addresses
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
$stmt->execute([$userId]);
$addresses = $stmt->fetchAll();
?>

<div class="container">
    <h1 style="font-weight: 900; text-transform: uppercase;">Checkout</h1>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div style="background: #e6ffed; color: #008033; padding: 15px; margin-top: 20px; border: 1px solid #c3e6cb; font-size: 14px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #fff5f5; color: #cc0000; padding: 15px; margin-top: 20px; border: 1px solid #f5c6cb; font-size: 14px;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form id="checkout-form" action="backend/handlers/checkout_handler.php" method="POST">
        <div class="checkout-layout">
            <!-- Left Side: Address and Payment -->
            <div class="checkout-main">
                <!-- Shipping Address -->
                <div class="checkout-section">
                    <h3>Shipping Address</h3>
                    <?php if(count($addresses) > 0): ?>
                        <div class="address-grid">
                            <?php 
                            $first = true;
                            foreach($addresses as $addr): 
                                $checked = ($addr['is_default'] || (count($addresses) == 1 && $first)) ? 'checked' : '';
                                $first = false;
                            ?>
                                <label class="address-card <?php echo $checked ? 'active' : ''; ?>">
                                    <input type="radio" name="address_id" value="<?php echo $addr['id']; ?>" <?php echo $checked; ?>>
                                    <div style="font-weight: 700;"><?php echo $addr['name']; ?></div>
                                    <div style="font-size: 14px; color: var(--light-text); margin-top: 5px;">
                                        <?php echo $addr['address']; ?><br>
                                        <?php echo $addr['city']; ?>, <?php echo $addr['state']; ?> - <?php echo $addr['pincode']; ?><br>
                                        Phone: <?php echo $addr['phone']; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="padding: 20px; border: 1px dashed var(--border-color); text-align: center;">
                            <p style="margin-bottom: 20px; color: var(--light-text);">No shipping addresses found.</p>
                        </div>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-outline" id="show-address-form" style="margin-top: 20px; font-size: 12px; padding: 10px 20px;">+ Add New Address</button>
                </div>

                <!-- Payment Method -->
                <div class="checkout-section">
                    <h3>Payment Method</h3>
                    <div class="payment-options">
                        <?php 
                        // Fetch active payment settings
                        $p_settings = $pdo->query("SELECT * FROM payment_settings LIMIT 1")->fetch(); 
                        ?>
                        
                        <!-- Always show COD -->
                        <label class="payment-method active">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            <div>
                                <div style="font-weight: 700;">Cash on Delivery</div>
                                <div style="font-size: 12px; color: var(--light-text);">Pay when you receive the package.</div>
                            </div>
                        </label>
                        
                        <!-- Universal UPI -->
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="upi">
                            <i class="fa-solid fa-qrcode"></i>
                            <div>
                                <div style="font-weight: 800; text-transform: uppercase; font-size: 14px;">Generic UPI App (QR Scanner)</div>
                                <div style="font-size: 12px; color: var(--light-text);">Scan QR via any UPI App.</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="checkout-sidebar">
                <div class="cart-summary" style="position: sticky; top: 100px; background: var(--light-bg); padding: var(--spacing-xl);">
                    <h3 style="font-weight: 900; text-transform: uppercase; margin-bottom: var(--spacing-lg);">Order Review</h3>
                    <div class="summary-row"><span>Subtotal</span><span><?php echo formatPrice($subtotal); ?></span></div>
                    <div class="summary-row"><span>Shipping</span><span><?php echo ($shipping == 0) ? 'FREE' : formatPrice($shipping); ?></span></div>
                    <div class="summary-row summary-total"><span>Total</span><span><?php echo formatPrice($total); ?></span></div>
                    
                    <p style="font-size: 12px; color: var(--light-text); margin: 20px 0;">By placing an order, you agree to our terms of service.</p>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 18px; display: flex; justify-content: space-between; align-items: center;">
                        <span>PLACE ORDER</span>
                        <i class="fa-solid fa-lock"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- NEW ADDRESS FORM OVERLAY (OUTSIDE main form) -->
    <div id="address-modal-container" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; visibility: hidden; opacity: 0; transition: 0.3s;">
        <div id="new-address-form" style="background: white; padding: 40px; max-width: 600px; width: 90%; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <button type="button" id="close-modal" style="position: absolute; top: 20px; right: 20px; border: none; background: none; font-size: 24px; cursor: pointer;">&times;</button>
            <h4 class="mb-4" style="text-transform: uppercase; font-weight: 900; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Add Shipping Address</h4>
            <form action="backend/handlers/address_handler.php" method="POST">
                <input type="hidden" name="action" value="add_address">
                <input type="hidden" name="redirect" value="checkout">
                <div class="grid-2">
                    <div class="form-group">
                        <label style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="10-digit number" required>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Complete Address</label>
                    <textarea name="address" class="form-control" style="height: 100px;" placeholder="Street name, building, apartment number" required></textarea>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label style="font-size: 12px; font-weight: 700; text-transform: uppercase;">City</label>
                        <input type="text" name="city" class="form-control" placeholder="City" required>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 12px; font-weight: 700; text-transform: uppercase;">State</label>
                        <input type="text" name="state" class="form-control" placeholder="State" required>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Pincode</label>
                        <input type="text" name="pincode" class="form-control" placeholder="6-digit" required>
                    </div>
                </div>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 15px;">SAVE & USE ADDRESS</button>
                    <button type="button" id="cancel-address" class="btn btn-outline" style="padding: 15px 30px;">CANCEL</button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<script>
// Modal Logic
const modal = document.getElementById('address-modal-container');
const showBtn = document.getElementById('show-address-form');
const cancelBtn = document.getElementById('cancel-address');
const closeBtn = document.getElementById('close-modal');

showBtn.addEventListener('click', () => {
    modal.style.visibility = 'visible';
    modal.style.opacity = '1';
});

const closeModal = () => {
    modal.style.visibility = 'hidden';
    modal.style.opacity = '0';
};

cancelBtn.addEventListener('click', closeModal);
closeBtn.addEventListener('click', closeModal);

// Selection Highlight Logic
document.querySelectorAll('.address-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.address-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        this.querySelector('input').checked = true;
    });
});

document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        this.classList.add('active');
        this.querySelector('input').checked = true;
    });
});
</script>
