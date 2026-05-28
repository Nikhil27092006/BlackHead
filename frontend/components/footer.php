    </main>

    <footer style="background: #05050a; padding: 100px 0 0; color: white; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 0;">
        <div class="container">
            <!-- Footer Top Grid -->
            <div class="footer-grid footer-main-grid">
                <!-- Brand Column -->
                <div class="footer-col footer-brand-col">
                    <a href="index.php" class="logo footer-logo" style="margin-bottom: 24px; display: inline-block; font-size: 30px; color:#fff; -webkit-text-fill-color:#fff; background:none; letter-spacing:-1px;">BLACKHEAD</a>
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
                        <?php
                        $footerCats = $pdo->query("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY name ASC LIMIT 4")->fetchAll();
                        foreach($footerCats as $fc):
                        ?>
                        <li><a href="index.php?page=products&category=<?php echo $fc['slug']; ?>" style="color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; transition: color 0.3s; text-decoration:none;" onmouseover="this.style.color='#a78bfa'" onmouseout="this.style.color='rgba(255,255,255,0.6)'"><?php echo htmlspecialchars($fc['name']); ?></a></li>
                        <?php endforeach; ?>
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

                <!-- Newsletter -->
                <div class="footer-col" style="grid-column: span 2;">
                    <h4 style="font-size: 11px; letter-spacing: 3px; color: rgba(255,255,255,0.4); margin-bottom: 28px; font-weight: 700; text-transform: uppercase;">The Inner Circle</h4>
                    <p style="color: rgba(255,255,255,0.35); font-size: 13px; line-height: 1.6; margin-bottom: 24px;">Unlock priority access to limited experimental drops and private seasonal offers.</p>
                    <form id="newsletterForm" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <input type="email" id="subscriberEmail" placeholder="Enter your email" required 
                               style="flex: 1; min-width: 200px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 12px 20px; border-radius: 8px; color: #fff; font-size: 14px; outline: none; transition: all 0.3s;"
                               onfocus="this.style.borderColor='rgba(139,92,246,0.5)'; this.style.background='rgba(255,255,255,0.05)';">
                        <button type="submit" style="background: #8b5cf6; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.background='#a78bfa'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.background='#8b5cf6'; this.style.transform='translateY(0)';">
                            Join Now
                        </button>
                    </form>
                    <div id="newsletterMsg" style="margin-top: 15px; font-size: 12px; font-weight: 600; display: none; padding: 8px 12px; border-radius: 6px;"></div>
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
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <p>© <?php echo date('Y'); ?> BLACKHEAD INDIA. ALL RIGHTS RESERVED.</p>
                        <a href="backend/admin/index.php" class="btn" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6); padding: 8px 16px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);" 
                           onmouseover="this.style.background='rgba(139, 92, 246, 0.1)'; this.style.borderColor='rgba(139, 92, 246, 0.3)'; this.style.color='#fff'; this.style.transform='translateY(-2px)';" 
                           onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.6)'; this.style.transform='translateY(0)';">
                            <i class="fa-solid fa-user-shield" style="font-size: 12px; color: #8b5cf6;"></i>
                            Admin Portal
                        </a>
                    </div>
                    <div style="display: flex; align-items: center; gap: 24px;">
                        <span style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-shield-halved" style="color:#8b5cf6; font-size:11px;"></i> SECURE PAYMENTS</span>
                        <span style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-truck" style="color:#8b5cf6; font-size:11px;"></i> FREE SHIPPING ₹999+</span>
                        <span>INDIA / ENGLISH</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/918767487353" target="_blank" class="whatsapp-float" style="position: fixed; bottom: 100px; right: 30px; width: 60px; height: 60px; background: #25d366; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 10px 25px rgba(37,211,102,0.3); z-index: 9999; text-decoration: none; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <i class="fa-brands fa-whatsapp"></i>
        <style>
            .whatsapp-float:hover { transform: scale(1.1) translateY(-5px); box-shadow: 0 15px 35px rgba(37,211,102,0.4); }
            @keyframes whatsapp-pulse {
                0% { box-shadow: 0 0 0 0 rgba(37,211,102, 0.4); }
                70% { box-shadow: 0 0 0 20px rgba(37,211,102, 0); }
                100% { box-shadow: 0 0 0 0 rgba(37,211,102, 0); }
            }
            .whatsapp-float { animation: whatsapp-pulse 2s infinite; }
        </style>
    </a>

    <script>
    document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('subscriberEmail').value;
        const msgDiv = document.getElementById('newsletterMsg');
        const btn = this.querySelector('button');
        
        btn.disabled = true;
        btn.innerText = 'PROCESSING...';
        
        fetch('backend/handlers/subscribe_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}`
        })
        .then(r => r.json())
        .then(data => {
            msgDiv.style.display = 'block';
            msgDiv.innerText = data.message;
            msgDiv.style.color = data.success ? '#10b981' : '#ef4444';
            msgDiv.style.background = data.success ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)';
            if (data.success) {
                document.getElementById('subscriberEmail').value = '';
                btn.innerText = 'SUCCESS';
            } else {
                btn.innerText = 'JOIN NOW';
                btn.disabled = false;
            }
        });
    });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
