<div class="container" style="text-align: center; padding: 100px 0;">
    <div style="font-size: 80px; color: var(--success-color); margin-bottom: var(--spacing-lg);">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h1 style="font-weight: 900; text-transform: uppercase; margin-bottom: 5px;">Order Placed!</h1>
    <p class="mb-5">Thank you for your purchase. Your order number is <strong>#<?php echo $_SESSION['last_order_number'] ?? 'BH-ERROR'; ?></strong>.</p>
    
    <div style="max-width: 500px; margin: 0 auto; background: var(--light-bg); padding: var(--spacing-xl); text-align: left;">
        <h4 class="mb-3">What's Next?</h4>
        <ul style="font-size: 14px; color: var(--light-text);">
            <li class="mb-2"><i class="fa-solid fa-envelope" style="margin-right: 10px;"></i> Confirmation email sent to your inbox.</li>
            <li class="mb-2"><i class="fa-solid fa-box-open" style="margin-right: 10px;"></i> We'll notify you once your order is shipped.</li>
            <li><i class="fa-solid fa-truck" style="margin-right: 10px;"></i> Estimated delivery: 3-5 business days.</li>
        </ul>
    </div>

    <div style="margin-top: 40px; display: flex; gap: 20px; justify-content: center;">
        <a href="index.php?page=products" class="btn btn-primary">Continue Shopping</a>
        <a href="index.php?page=order_history" class="btn btn-outline">View Order History</a>
    </div>
</div>
