<?php
include '../../backend/core/config.php';
include '../../backend/core/functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offerBarText = clean($_POST['offer_bar_text']);
    if (updateSetting('offer_bar_text', $offerBarText)) {
        $successMessage = "Offer bar text updated successfully!";
    } else {
        $errorMessage = "Failed to update offer bar text. Please try again.";
    }
}

// Fetch current offer bar text
$currentOfferBarText = getSetting('offer_bar_text', '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Bar Settings</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Offer Bar Settings</h1>

        <?php if (!empty($successMessage)) : ?>
            <div class="success-message"> <?= $successMessage; ?> </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)) : ?>
            <div class="error-message"> <?= $errorMessage; ?> </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="offer_bar_text">Offer Bar Text:</label>
                <textarea name="offer_bar_text" id="offer_bar_text" rows="3" class="form-control" required><?= htmlspecialchars($currentOfferBarText); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
