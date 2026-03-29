<?php
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit;
}
$userId = $_SESSION['user_id'];
$addresses = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
$addresses->execute([$userId]);
$addresses = $addresses->fetchAll();
?>
<div class="container" style="padding: 60px 0;">
    <div style="max-width: 800px; margin: 0 auto; border: 1px solid var(--border-color); padding: var(--spacing-xl);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-weight: 900; text-transform: uppercase;">Address Book</h2>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 40px;">
            <?php foreach($addresses as $addr): ?>
                <div style="border: 1px solid var(--border-color); padding: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-weight: 700; margin-bottom: 5px;"><?php echo $addr['name']; ?> <?php if($addr['is_default']) echo '<span style="font-size: 10px; background: #eee; padding: 2px 6px; margin-left:10px;">DEFAULT</span>'; ?></div>
                        <p style="font-size: 14px; color: var(--light-text); line-height: 1.4;">
                            <?php echo $addr['address']; ?><br>
                            <?php echo $addr['city']; ?>, <?php echo $addr['state']; ?> - <?php echo $addr['pincode']; ?><br>
                            Phone: <?php echo $addr['phone']; ?>
                        </p>
                    </div>
                    <form action="backend/handlers/address_handler.php" method="POST">
                        <input type="hidden" name="action" value="delete_address">
                        <input type="hidden" name="id" value="<?php echo $addr['id']; ?>">
                        <button type="submit" style="background: none; border: none; color: var(--error-color); cursor: pointer;"><i class="fa-solid fa-trash-can"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
            
            <?php if(empty($addresses)): ?>
                <p style="text-align: center; color: var(--light-text); padding: 40px;">No addresses saved yet.</p>
            <?php endif; ?>
        </div>

        <div style="background: var(--light-bg); padding: 30px;">
            <h3 class="mb-4" style="text-transform: uppercase; font-size: 16px;">Add New Address</h3>
            <form action="backend/handlers/address_handler.php" method="POST">
                <input type="hidden" name="action" value="add_address">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Complete Address</label>
                    <textarea name="address" class="form-control" style="height: 100px;" required></textarea>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" class="form-control" required>
                    </div>
                </div>
                <div style="margin-top: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_default"> Set as default address
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Save Address</button>
            </form>
        </div>
    </div>
</div>
