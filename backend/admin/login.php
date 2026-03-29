<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | BLACKHEAD</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body style="background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="auth-container" style="background: white; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <div class="auth-header">
            <h2 style="font-size: 1.5rem;">Admin Access</h2>
            <p>Secure login for management</p>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="auth_handler.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="admin@blackhead.com">
            </div>
            <div class="form-group mb-4">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Login to Dashboard</button>
        </form>
    </div>
</body>
</html>
