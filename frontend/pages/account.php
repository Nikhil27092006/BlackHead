<?php
// Protection: Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit;
}

$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<div class="container" style="padding: 60px 0;">
    <div style="max-width: 800px; margin: 0 auto; border: 1px solid var(--border-color); padding: var(--spacing-xl);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
            <h2 style="font-weight: 900; text-transform: uppercase;">My Account</h2>
            <a href="index.php?action=logout" class="btn btn-outline" style="font-size: 12px; padding: 8px 16px;">Logout</a>
        </div>

    <div class="grid-2-1">
            <aside>
                <ul style="border: 1px solid var(--border-color);">
                    <li style="padding: 15px; border-bottom: 1px solid var(--border-color); background: var(--primary-color); color: white; font-weight: 700;">Dashboard</li>
                    <li style="padding: 15px; border-bottom: 1px solid var(--border-color);"><a href="index.php?page=order_history">My Orders</a></li>
                    <li style="padding: 15px; border-bottom: 1px solid var(--border-color);"><a href="index.php?page=wishlist">Wishlist</a></li>
                    <li style="padding: 15px; border-bottom: 1px solid var(--border-color);"><a href="index.php?page=profile">Profile Settings</a></li>
                    <li style="padding: 15px;"><a href="index.php?page=address_book">Address Book</a></li>
                </ul>
            </aside>

            <section>
                <h3 class="mb-3">Hello, <?php echo $userName; ?>!</h3>
                <p class="mb-4" style="color: var(--light-text);">From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>

                <div class="grid-2">
                    <div style="padding: var(--spacing-md); background: var(--light-bg);">
                        <h4 class="mb-2">Account Details</h4>
                        <p style="font-size: 14px;"><?php echo $userName; ?></p>
                        <p style="font-size: 14px; color: var(--light-text);"><?php echo $userEmail; ?></p>
                        <a href="index.php?page=profile" style="font-size: 12px; font-weight: 700; text-decoration: underline; display: inline-block; margin-top: 10px;">Edit Profile</a>
                    </div>
                    <div style="padding: var(--spacing-md); background: var(--light-bg);">
                        <h4 class="mb-2">Default Address</h4>
                        <p style="font-size: 14px; color: var(--light-text);">No default address set yet.</p>
                        <a href="index.php?page=address_book" style="font-size: 12px; font-weight: 700; text-decoration: underline; display: inline-block; margin-top: 10px;">Manage Addresses</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
