<div class="container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Login</h2>
            <p style="text-align: center; color: var(--light-text); margin-bottom: var(--spacing-xl);">Welcome back. Enter your details to log in.</p>
        </div>



        <form action="<?php echo SITE_URL; ?>backend/handlers/auth_handler.php" method="POST">
            <input type="hidden" name="action" value="login">
            <?php if(isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="example@email.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                <div style="text-align: right; margin-top: 5px;">
                    <a href="index.php?page=forgot_password" style="font-size: 12px; color: var(--light-text); text-decoration: underline;">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Login <i class="fa-solid fa-arrow-right" style="margin-left: 10px;"></i></button>
        </form>

        <div style="margin: 25px 0; display: flex; align-items: center; color: #ccc;">
            <div style="flex-grow: 1; height: 1px; background: #eee;"></div>
            <span style="padding: 0 15px; font-size: 12px; font-weight: 700; color: var(--light-text);">OR</span>
            <div style="flex-grow: 1; height: 1px; background: #eee;"></div>
        </div>

        <?php 
            $redirect_query = isset($_GET['redirect']) ? '&redirect=' . htmlspecialchars($_GET['redirect']) : ''; 
        ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="index.php?page=social_auth&method=google<?php echo $redirect_query; ?>" class="btn" style="width: 100%; border: 1px solid #ddd; background: white; color: var(--text-color); display: flex; align-items: center; justify-content: center; gap: 10px;">
                <img src="https://www.google.com/favicon.ico" width="16" alt=""> Login with Google
            </a>
            <a href="index.php?page=phone_login<?php echo $redirect_query; ?>" class="btn" style="width: 100%; border: 1px solid #ddd; background: white; color: var(--text-color); display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fa-solid fa-phone" style="font-size: 14px;"></i> Login with Phone Number
            </a>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="index.php?page=register<?php echo $redirect_query; ?>">Register Now</a>
        </div>
    </div>
</div>
