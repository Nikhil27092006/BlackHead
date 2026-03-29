    </main>

    <footer style="background: #05050a; padding: 100px 0 0; color: white; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 0;">
        <div class="container">
            <!-- Footer Top Grid -->
            <div class="footer-grid" style="grid-template-columns: 2fr 1fr 1fr 1fr; gap: 60px; margin-bottom: 80px;">
                <!-- Brand Column -->
                <div class="footer-col">
                    <a href="index.php" class="logo" style="margin-bottom: 24px; display: inline-block; font-size: 30px; color:#fff; -webkit-text-fill-color:#fff; background:none; letter-spacing:-1px;">BLACKHEAD</a>
                    <p style="color: rgba(255,255,255,0.35); font-size: 14px; line-height: 1.85; max-width: 280px; font-weight: 400; margin-bottom: 32px;">Defining the future of premium apparel. Engineered for excellence, designed for the streets.</p>
                    <div style="display: flex; gap: 12px;">
                        <?php if(getSetting('instagram')): ?>
                        <a href="<?php echo htmlspecialchars(getSetting('instagram')); ?>" style="width: 42px; height: 42px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5); font-size: 15px; transition: all 0.3s ease; text-decoration:none;"
                           onmouseover="this.style.background='rgba(139,92,246,0.2)'; this.style.borderColor='rgba(139,92,246,0.4)'; this.style.color='#a78bfa';"
                           onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.08)'; this.style.color='rgba(255,255,255,0.5)';">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(getSetting('facebook')): ?>
                        <a href="<?php echo htmlspecialchars(getSetting('facebook')); ?>" style="width: 42px; height: 42px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5); font-size: 15px; transition: all 0.3s ease; text-decoration:none;"
                           onmouseover="this.style.background='rgba(139,92,246,0.2)'; this.style.borderColor='rgba(139,92,246,0.4)'; this.style.color='#a78bfa';"
                           onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.08)'; this.style.color='rgba(255,255,255,0.5)';">
                            <i class="fa-brands fa-facebook"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(getSetting('twitter')): ?>
                        <a href="<?php echo htmlspecialchars(getSetting('twitter')); ?>" style="width: 42px; height: 42px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5); font-size: 15px; transition: all 0.3s ease; text-decoration:none;"
                           onmouseover="this.style.background='rgba(139,92,246,0.2)'; this.style.borderColor='rgba(139,92,246,0.4)'; this.style.color='#a78bfa';"
                           onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.08)'; this.style.color='rgba(255,255,255,0.5)';">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Shop -->
                <div class="footer-col">
                    <h4 style="font-size: 11px; letter-spacing: 3px; color: rgba(255,255,255,0.4); margin-bottom: 28px; font-weight: 700; text-transform: uppercase;">Shop</h4>
                    <ul style="display: flex; flex-direction: column; gap: 16px;">
                        <li><a href="index.php?page=products&category=men" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Men's Elite</a></li>
                        <li><a href="index.php?page=products&category=women" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Women's Collection</a></li>
                        <li><a href="index.php?page=products&category=kids" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Youth Culture</a></li>
                        <li><a href="index.php?page=products" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">New Arrivals</a></li>
                    </ul>
                </div>

                <!-- Assistance -->
                <div class="footer-col">
                    <h4 style="font-size: 11px; letter-spacing: 3px; color: rgba(255,255,255,0.4); margin-bottom: 28px; font-weight: 700; text-transform: uppercase;">Assistance</h4>
                    <ul style="display: flex; flex-direction: column; gap: 16px;">
                        <li><a href="index.php?page=contact" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Contact Us</a></li>
                        <li><a href="index.php?page=shipping" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Shipping Policy</a></li>
                        <li><a href="index.php?page=size-guide" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Size Guide</a></li>
                        <li><a href="index.php?page=faqs" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Support Centre</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="footer-col">
                    <h4 style="font-size: 11px; letter-spacing: 3px; color: rgba(255,255,255,0.4); margin-bottom: 28px; font-weight: 700; text-transform: uppercase;">Legal</h4>
                    <ul style="display: flex; flex-direction: column; gap: 16px;">
                        <li><a href="index.php?page=privacy" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Privacy Policy</a></li>
                        <li><a href="index.php?page=terms" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Terms of Use</a></li>
                        <li><a href="index.php?page=about" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">About The Brand</a></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom Bar -->
            <div style="padding: 28px 0; border-top: 1px solid rgba(255,255,255,0.06);">
                <!-- Social Icons in Bottom Left -->
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <?php if(getSetting('instagram')): ?>
                    <a href="<?php echo htmlspecialchars(getSetting('instagram')); ?>" target="_blank" style="color: rgba(255,255,255,0.4); font-size: 18px; transition: all 0.3s ease; text-decoration:none;" onmouseover="this.style.color='#a78bfa'; this.style.transform='translateY(-2px)';" onmouseout="this.style.color='rgba(255,255,255,0.4)'; this.style.transform='translateY(0)';">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    <?php if(getSetting('facebook')): ?>
                    <a href="<?php echo htmlspecialchars(getSetting('facebook')); ?>" target="_blank" style="color: rgba(255,255,255,0.4); font-size: 18px; transition: all 0.3s ease; text-decoration:none;" onmouseover="this.style.color='#a78bfa'; this.style.transform='translateY(-2px)';" onmouseout="this.style.color='rgba(255,255,255,0.4)'; this.style.transform='translateY(0)';">
                        <i class="fa-brands fa-facebook"></i>
                    </a>
                    <?php endif; ?>
                    <?php if(getSetting('twitter')): ?>
                    <a href="<?php echo htmlspecialchars(getSetting('twitter')); ?>" target="_blank" style="color: rgba(255,255,255,0.4); font-size: 17px; transition: all 0.3s ease; text-decoration:none;" onmouseover="this.style.color='#a78bfa'; this.style.transform='translateY(-2px)';" onmouseout="this.style.color='rgba(255,255,255,0.4)'; this.style.transform='translateY(0)';">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- HQ & Contact info -->
                <div style="margin-bottom: 24px; font-size: 11px; line-height: 1.6; color: rgba(255,255,255,0.4);">
                    <div style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;">
                        <i class="fa-solid fa-location-dot" style="margin-top: 3px; font-size: 10px; color: #8b5cf6;"></i>
                        <span><?php echo nl2br(htmlspecialchars(getSetting('store_address', 'Sector 14, Gurgaon, Haryana, India'))); ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-phone" style="font-size: 10px; color: #8b5cf6;"></i>
                        <a href="tel:<?php echo htmlspecialchars(getSetting('contact_phone', '+91 98765 43210')); ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='#fff';" onmouseout="this.style.color='rgba(255,255,255,0.4)';"><?php echo htmlspecialchars(getSetting('contact_phone', '+91 98765 43210')); ?></a>
                        <span style="margin: 0 4px; opacity: 0.3;">|</span>
                        <i class="fa-solid fa-envelope" style="font-size: 10px; color: #8b5cf6;"></i>
                        <a href="mailto:<?php echo htmlspecialchars(getSetting('support_email', 'support@blackhead.in')); ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='#fff';" onmouseout="this.style.color='rgba(255,255,255,0.4)';"><?php echo htmlspecialchars(getSetting('support_email', 'support@blackhead.in')); ?></a>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: rgba(255,255,255,0.25); font-weight: 600; flex-wrap: wrap; gap: 16px;">
                    <p>© <?php echo date('Y'); ?> BLACKHEAD INDIA. ALL RIGHTS RESERVED.</p>
                    <div style="display: flex; align-items: center; gap: 24px;">
                        <span style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-shield-halved" style="color:#8b5cf6; font-size:11px;"></i> SECURE PAYMENTS</span>
                        <span style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-truck" style="color:#8b5cf6; font-size:11px;"></i> FREE SHIPPING ₹999+</span>
                        <span>INDIA / ENGLISH</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
