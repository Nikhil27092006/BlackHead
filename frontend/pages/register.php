<div class="container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Create Account</h2>
            <p style="text-align: center; color: var(--light-text); margin-bottom: var(--spacing-xl);">Join BLACKHEAD for a personalized experience.</p>
        </div>



        <form action="backend/handlers/auth_handler.php" method="POST">
            <input type="hidden" name="action" value="register">
            <?php if(isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="example@email.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Minimum 8 characters">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Re-enter password">
            </div>

            <div class="terms-text mb-4">
                By creating an account, you agree to our <a href="index.php?page=terms">Terms & Conditions</a> and <a href="index.php?page=privacy">Privacy Policy</a>.
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Register Now <i class="fa-solid fa-arrow-right" style="margin-left: 10px;"></i></button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="index.php?page=login<?php echo isset($_GET['redirect']) ? '&redirect=' . htmlspecialchars($_GET['redirect']) : ''; ?>">Login Here</a>
        </div>
    </div>
</div>
