<style>
.auth-container {
    max-width: 450px;
    margin: 80px auto;
    padding: var(--spacing-xl);
    border: 1px solid var(--border-color);
}
</style>

<div class="container">
    <div class="auth-container">
        <div class="auth-header" style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-weight: 900; text-transform: uppercase;">Phone Login</h2>
            <p style="font-size: 14px; color: var(--light-text);">We'll send a 6-digit OTP to your phone.</p>
        </div>

        <form action="<?php echo SITE_URL; ?>backend/handlers/social_auth_handler.php" method="POST">
            <input type="hidden" name="action" value="phone_login">
            
            <div class="form-group">
                <label>Mobile Number</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" value="+91" readonly style="width: 60px; text-align: center; border: 1px solid var(--primary-color);">
                    <input type="tel" name="phone" class="form-control" placeholder="9876543210" required pattern="[0-9]{10}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Get OTP</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php?page=login<?php echo isset($_GET['redirect']) ? '&redirect=' . htmlspecialchars($_GET['redirect']) : ''; ?>" style="font-size: 14px; color: var(--light-text); text-decoration: underline;">Back to standard login</a>
        </div>
    </div>
</div>
