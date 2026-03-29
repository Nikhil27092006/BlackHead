<style>
.auth-container {
    max-width: 450px;
    margin: 80px auto;
    padding: var(--spacing-xl);
    border: 1px solid var(--border-color);
}
.otp-inputs {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: var(--spacing-xl) 0;
}
.otp-field {
    width: 45px;
    height: 55px;
    text-align: center;
    font-size: 24px;
    font-weight: 900;
    border: 1px solid var(--primary-color);
    outline: none;
}
</style>

<div class="container">
    <div class="auth-container">
        <div class="auth-header" style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-weight: 900; text-transform: uppercase;">Verify OTP</h2>
            <p style="font-size: 14px; color: var(--light-text);">A 6-digit code has been sent to your phone/email.</p>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div style="background: #e6ffed; color: #008033; padding: 10px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb; font-size: 14px;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div style="background: #fff5f5; color: #cc0000; padding: 10px; margin-bottom: 20px; text-align: center; border: 1px solid #f5c6cb; font-size: 14px;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo SITE_URL; ?>backend/handlers/auth_handler.php" method="POST">
            <input type="hidden" name="action" value="verify_otp">
            <?php if(isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
            <?php endif; ?>
            
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-field" required>
                <input type="text" maxlength="1" class="otp-field" required>
                <input type="text" maxlength="1" class="otp-field" required>
                <input type="text" maxlength="1" class="otp-field" required>
                <input type="text" maxlength="1" class="otp-field" required>
                <input type="text" maxlength="1" class="otp-field" required>
            </div>
            
            <input type="hidden" name="otp" id="full-otp">

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">VERIFY <i class="fa-solid fa-check" style="margin-left: 10px;"></i></button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <p style="font-size: 12px; color: var(--light-text);">Didn't receive the code?</p>
            <a href="#" style="font-size: 14px; color: var(--primary-color); font-weight: 700; text-decoration: underline;">Resend OTP</a>
        </div>
    </div>
</div>

<script>
const fields = document.querySelectorAll('.otp-field');
fields.forEach((f, i) => {
    f.addEventListener('input', () => {
        if(f.value && fields[i+1]) fields[i+1].focus();
        updateFullOtp();
    });
    f.addEventListener('keydown', (e) => {
        if(e.key === 'Backspace' && !f.value && fields[i-1]) fields[i-1].focus();
    });
});

function updateFullOtp() {
    let otp = "";
    fields.forEach(f => otp += f.value);
    document.getElementById('full-otp').value = otp;
}
</script>
