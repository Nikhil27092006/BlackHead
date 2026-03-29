<div class="container">
    <div class="auth-container">
        <div class="auth-header">
            <h2>Forgot Password</h2>
            <p>Enter your email to receive a password reset link.</p>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-msg">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="error-msg" style="background: #f6fff6; color: var(--success-color); border-color: var(--success-color);">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="backend/handlers/auth_handler.php" method="POST">
            <input type="hidden" name="action" value="forgot_password">
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="example@email.com">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Send Reset Link <i class="fa-solid fa-paper-plane" style="margin-left: 10px;"></i></button>
        </form>

        <div class="auth-footer">
            Remembered your password? <a href="index.php?page=login">Back to Login</a>
        </div>
    </div>
</div>
