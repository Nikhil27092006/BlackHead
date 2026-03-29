<div class="container section-padding">
    <h1 style="font-weight: 900; text-transform: uppercase; margin-bottom: var(--spacing-xxl);">Contact Us</h1>
    
    <div class="grid-2">
        <div>
            <h3 class="mb-3">Get in Touch</h3>
            <p class="mb-4" style="color: var(--light-text);">Have a question or feedback? We'd love to hear from you.</p>
            
            <form action="backend/handlers/contact_handler.php" method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        
        <div>
            <h3 class="mb-3">Contact Information</h3>
            <div class="mb-4">
                <h4 style="font-size: 14px; text-transform: uppercase;">Location</h4>
                <p style="color: var(--light-text);"><?php echo nl2br(htmlspecialchars(getSetting('store_address', 'Sector 14, Gurgaon, Haryana, India'))); ?></p>
            </div>
            <div class="mb-4">
                <h4 style="font-size: 14px; text-transform: uppercase;">Email</h4>
                <p style="color: var(--light-text);"><?php echo htmlspecialchars(getSetting('support_email', 'support@blackhead.com')); ?></p>
            </div>
            <div class="mb-4">
                <h4 style="font-size: 14px; text-transform: uppercase;">Phone</h4>
                <p style="color: var(--light-text);"><?php echo htmlspecialchars(getSetting('contact_phone', '+91 98765 43210')); ?></p>
            </div>
        </div>
    </div>
</div>
