<?php
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit;
}

$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<div class="container" style="padding: 60px 0;">
    <div style="max-width: 800px; margin: 0 auto; border: 1px solid var(--border-color); padding: var(--spacing-xl);">
        <h2 style="font-weight: 900; text-transform: uppercase; margin-bottom: 30px;">Profile Settings</h2>
        
        <div style="margin-bottom: 50px;">
            <h3 class="mb-4">Personal Information</h3>
            <form action="backend/handlers/profile_handler.php" method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $userName; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $userEmail; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <hr style="margin: 40px 0; border: none; border-top: 1px solid var(--border-color);">

        <div>
            <h3 class="mb-4">Change Password</h3>
            <form action="backend/handlers/profile_handler.php" method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-outline">Change Password</button>
            </form>
        </div>
    </div>
</div>
