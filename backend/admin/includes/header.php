<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | BLACKHEAD</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../../assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon-16x16.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <style>
        /* ===== SIDEBAR ===== */
        body { overflow-x: hidden; }

        .admin-sidebar {
            width: 270px;
            background: #05050a;
            border-right: 1px solid rgba(255,255,255,0.05);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            padding: 0;
            z-index: 1001;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 36px 30px 28px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            flex-shrink: 0;
        }
        .sidebar-brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            text-decoration: none;
            display: block;
        }
        .sidebar-brand-sub {
            font-size: 10px;
            font-weight: 700;
            color: #8b5cf6;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .sidebar-nav { flex: 1; padding: 20px 0; }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 800;
            color: rgba(255,255,255,0.18);
            text-transform: uppercase;
            letter-spacing: 2.5px;
            padding: 16px 30px 8px;
        }

        .sidebar-links { list-style: none; padding: 0 14px; margin: 0; }
        .sidebar-links li { margin-bottom: 3px; }
        .sidebar-links li a {
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 13px;
            color: rgba(255,255,255,0.4);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            font-weight: 600;
            font-size: 12.5px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
        }
        .sidebar-links li a i {
            font-size: 15px;
            width: 18px;
            text-align: center;
            opacity: 0.7;
        }
        .sidebar-links li a:hover {
            color: #fff;
            background: rgba(255,255,255,0.06);
            transform: translateX(4px);
        }
        .sidebar-links li a:hover i { opacity: 1; }
        .sidebar-links li a.active {
            background: linear-gradient(135deg, rgba(139,92,246,0.25), rgba(99,102,241,0.15));
            color: #c4b5fd;
            border: 1px solid rgba(139,92,246,0.2);
        }
        .sidebar-links li a.active i { color: #a78bfa; opacity: 1; }

        .sidebar-logout {
            padding: 20px 14px 28px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-logout a {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 18px;
            color: rgba(239,68,68,0.7);
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar-logout a:hover {
            background: rgba(239,68,68,0.1);
            color: #f87171;
        }

        /* ===== MAIN ===== */
        .admin-main {
            margin-left: 270px;
            padding: 44px 48px;
            background: #0a0a0f;
            min-height: 100vh;
        }

        /* ===== TOP HEADER ===== */
        .admin-top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 28px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            position: relative;
            z-index: 10000;
        }
        .admin-page-title {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            line-height: 1;
            text-transform: uppercase;
        }
        .admin-page-subtitle {
            color: rgba(255,255,255,0.3);
            font-size: 13px;
            font-weight: 500;
            margin-top: 8px;
        }

        .admin-user-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 10px 18px 10px 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .admin-user-bar:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(139,92,246,0.35);
        }
        .admin-user-avatar {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 16px; color: #fff;
            box-shadow: 0 4px 12px rgba(139,92,246,0.4);
            flex-shrink: 0;
        }
        .admin-user-bar { font-family: 'Inter', sans-serif; }
        
        /* ===== NOTIFICATIONS ===== */
        .admin-notifications-wrapper {
            position: relative;
            margin-right: 15px;
            z-index: 10001;
        }

        .notification-bell {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .notification-bell:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-color: rgba(139,92,246,0.5);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff5630;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 10px;
            border: 2px solid #0a0a0f;
            min-width: 18px;
            text-align: center;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            width: 350px;
            background: #0f0f18;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            z-index: 10002;
            display: none;
            flex-direction: column;
            overflow: hidden;
            backdrop-filter: blur(20px);
        }

        .notification-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h4 { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            display: flex;
            gap: 15px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .notification-item:hover { background: rgba(255,255,255,0.03); }
        .notification-item.unread { background: rgba(139, 92, 246, 0.05); }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-cancel { background: rgba(255, 86, 48, 0.1); color: #ff5630; }
        .icon-return { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .icon-exchange { background: rgba(0, 184, 217, 0.1); color: #00b8d9; }
        .icon-info { background: rgba(255,255,255,0.05); color: #fff; }
        .icon-success { background: rgba(16, 185, 129, 0.1); color: #34d399; }

        .notification-content { flex: 1; }
        .notification-title { color: #fff; font-size: 13px; font-weight: 700; margin-bottom: 3px; }
        .notification-msg { color: rgba(255,255,255,0.4); font-size: 12px; line-height: 1.4; }
        .notification-time { font-size: 10px; color: rgba(255,255,255,0.2); margin-top: 6px; font-weight: 600; text-transform: uppercase; }

        /* ===== ALERTS ===== */
        .admin-alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 28px;
        }
        .admin-alert-success {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.2);
            color: #34d399;
        }
        .admin-alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 36px;
        }

        /* ===== MOBILE ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 1000;
        }
        .sidebar-overlay.active { display: block; }

        /* ===== TOAST NOTIFICATIONS ===== */
        .toast-container {
            position: fixed;
            bottom: 40px;
            right: 40px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .admin-toast {
            background: rgba(15, 15, 24, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-left: 4px solid #8b5cf6;
            padding: 20px;
            border-radius: 16px;
            width: 380px;
            color: #fff;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex;
            gap: 18px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            animation: slideInToast 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideInToast {
            from { transform: translateX(100%) scale(0.9); opacity: 0; }
            to { transform: translateX(0) scale(1); opacity: 1; }
        }

        .admin-toast:hover {
            transform: translateX(-10px);
            border-color: rgba(139,92,246,0.3);
            background: rgba(20, 20, 30, 0.95);
        }

        .admin-toast-icon {
            width: 44px;
            height: 44px;
            background: rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .toast-cancel .admin-toast-icon { background: rgba(255, 86, 48, 0.15); color: #ff5630; }
        .toast-cancel { border-left-color: #ff5630; }

        .toast-return .admin-toast-icon { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
        .toast-return { border-left-color: #8b5cf6; }

        .toast-exchange .admin-toast-icon { background: rgba(0, 184, 217, 0.15); color: #00b8d9; }
        .toast-exchange { border-left-color: #00b8d9; }

        .admin-toast-body { flex: 1; }
        .admin-toast-title { font-size: 14px; font-weight: 800; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .admin-toast-msg { font-size: 11px; color: rgba(255,255,255,0.4); line-height: 1.5; font-weight: 500; }
        .admin-toast-close { position: absolute; top: 10px; right: 10px; color: rgba(255,255,255,0.2); font-size: 12px; cursor: pointer; transition: 0.3s; }
        .admin-toast-close:hover { color: #fff; }

        .admin-mobile-header {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: #05050a;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 999;
        }
        .admin-menu-toggle {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            border-radius: 10px;
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 16px;
        }

        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
            }
            .admin-sidebar.active { transform: translateX(0); }
            .admin-mobile-header { display: flex; }
            .admin-main { margin-left: 0; padding: 80px 20px 40px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .admin-main { padding: 75px 16px 40px; }
        }
    </style>
</head>
<body>
    <!-- Custom Cursor -->
    <div id="cursor-dot"></div>
    <div id="cursor-ring"></div>

    <!-- Notifications Toast -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="adminSidebarOverlay"></div>

    <!-- Mobile Top Bar -->
    <div class="admin-mobile-header">
        <span style="font-family:'Syne',sans-serif; font-size:16px; font-weight:800; color:#fff; letter-spacing:-0.5px;">BLACKHEAD</span>
        <button class="admin-menu-toggle" id="adminSidebarToggle"><i class="fa-solid fa-bars"></i></button>
    </div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="index.php" class="sidebar-brand-name">BLACKHEAD</a>
            <div class="sidebar-brand-sub">Control Panel</div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Main</div>
            <ul class="sidebar-links" id="sidebarLinks">
                <li><a href="index.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </a></li>
                <li><a href="index.php?page=orders" class="<?php echo $page == 'orders' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-bag-shopping"></i> Orders
                </a></li>
                <li><a href="index.php?page=products" class="<?php echo $page == 'products' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box-open"></i> Products
                </a></li>
                <li><a href="index.php?page=categories" class="<?php echo $page == 'categories' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group"></i> Categories
                </a></li>
                <li><a href="index.php?page=users" class="<?php echo $page == 'users' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-group"></i> Users
                </a></li>
                <li><a href="index.php?page=messages" class="<?php echo $page == 'messages' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-envelope-open-text"></i> Messages
                </a></li>
                <li><a href="index.php?page=coupons" class="<?php echo $page == 'coupons' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tag"></i> Coupons
                </a></li>

                <div class="sidebar-section-label">System</div>
                <li><a href="index.php?page=payment_settings" class="<?php echo $page == 'payment_settings' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-credit-card"></i> Payments
                </a></li>
                <li><a href="index.php?page=home_highlights" class="<?php echo $page == 'home_highlights' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-home"></i> Home Highlights
                </a></li>
                <li><a href="index.php?page=newsletter" class="<?php echo $page == 'newsletter' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-paper-plane"></i> Newsletter
                </a></li>
                <li><a href="index.php?page=settings" class="<?php echo $page == 'settings' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-sliders"></i> Settings
                </a></li>
                <li><a href="index.php?page=security_settings" class="<?php echo $page == 'security_settings' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shield-halved"></i> Security
                </a></li>
            </ul>
        </nav>

        <div class="sidebar-logout">
            <a href="../../index.php?action=logout">
                <i class="fa-solid fa-power-off"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Page Header -->
        <div class="admin-top-header">
            <div>
                <h1 class="admin-page-title"><?php echo ucfirst(str_replace('_', ' ', $page)); ?></h1>
                <p class="admin-page-subtitle">Manage your BLACKHEAD e-commerce ecosystem</p>
            </div>
            <div style="display: flex; align-items: center;">
                <!-- Notifications -->
                <div class="admin-notifications-wrapper">
                    <div class="notification-bell" id="notifBell">
                        <i class="fa-solid fa-bell"></i>
                        <span class="notification-badge" id="notifCount" style="display:none;">0</span>
                    </div>
                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <span style="font-size: 10px; color: #8b5cf6; cursor: pointer; font-weight: 700;" onclick="markAllRead()">Mark all as read</span>
                        </div>
                        <div class="notification-list" id="notifList">
                            <!-- Injected by JS -->
                            <div style="padding: 40px 20px; text-align: center; color: rgba(255,255,255,0.2);">
                                <i class="fa-solid fa-bell-slash" style="font-size: 24px; margin-bottom: 15px;"></i>
                                <div style="font-size: 13px; font-weight: 600;">No new notifications</div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="index.php?page=security_settings" class="admin-user-bar">
                    <div class="admin-user-avatar"><?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?></div>
                    <div>
                        <div class="admin-user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                        <div class="admin-user-role">Administrator</div>
                    </div>
                    <i class="fa-solid fa-chevron-down admin-user-caret"></i>
                </a>
            </div>
        </div>

        <!-- Session Alerts -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="admin-alert admin-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="admin-alert admin-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

    <script>
    // Mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        // --- Custom Cursor Logic ---
        const cursor = document.getElementById('cursor-dot');
        const cursorRing = document.getElementById('cursor-ring');
        if (cursor && cursorRing && typeof gsap !== 'undefined') {
            let mouseX = 0, mouseY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                gsap.to(cursor, { x: mouseX, y: mouseY, duration: 0.1, ease: 'none' });
                gsap.to(cursorRing, { x: mouseX, y: mouseY, duration: 0.35, ease: 'power2.out' });
            });
            document.querySelectorAll('a, button, .admin-card-premium, .action-btn-sleek').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    gsap.to(cursorRing, { scale: 1.8, opacity: 0.6, background: 'rgba(139,92,246,0.15)', duration: 0.3 });
                    gsap.to(cursor, { scale: 0.5, duration: 0.3 });
                });
                el.addEventListener('mouseleave', () => {
                    gsap.to(cursorRing, { scale: 1, opacity: 1, background: 'rgba(139,92,246,0.05)', duration: 0.3 });
                    gsap.to(cursor, { scale: 1, duration: 0.3 });
                });
            });
        }

        const toggle  = document.getElementById('adminSidebarToggle');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminSidebarOverlay');

        const openSidebar  = () => { sidebar.classList.add('active'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; };
        const closeSidebar = () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); document.body.style.overflow = ''; };

        if (toggle)  toggle.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        // GSAP Admin Animations
        if (typeof gsap !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);

            // Sidebar links stagger in
            gsap.fromTo('#sidebarLinks li',
                { x: -20, opacity: 0 },
                { x: 0, opacity: 1, stagger: 0.06, duration: 0.5, ease: 'power3.out', delay: 0.2 }
            );

            // Page header
            gsap.fromTo('.admin-top-header',
                { y: -20, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.6, ease: 'power3.out', delay: 0.1 }
            );

            // Stat cards stagger
            gsap.fromTo('.stats-grid .admin-card-premium',
                { y: 30, opacity: 0 },
                { y: 0, opacity: 1, stagger: 0.1, duration: 0.6, ease: 'power3.out', delay: 0.3 }
            );

            // Table card
            gsap.fromTo('.dashboard-table-card, .admin-card-premium:not(.stat-card-modern)',
                { y: 30, opacity: 0 },
                { y: 0, opacity: 1, stagger: 0.1, duration: 0.7, ease: 'power3.out', delay: 0.6,
                  scrollTrigger: { trigger: '.dashboard-table-card', start: 'top 90%', once: true } }
            );

            // Alerts pop in
            gsap.fromTo('.admin-alert',
                { y: -15, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.5, ease: 'back.out(1.2)' }
            );
        }

        // --- Notifications Logic ---
        const notifBell = document.getElementById('notifBell');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifList = document.getElementById('notifList');
        const notifCount = document.getElementById('notifCount');

        if (notifBell) {
            notifBell.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.style.display = notifDropdown.style.display === 'flex' ? 'none' : 'flex';
                if (notifDropdown.style.display === 'flex') {
                    fetchNotifications();
                }
            });

            document.addEventListener('click', () => {
                if (notifDropdown) notifDropdown.style.display = 'none';
            });

            notifDropdown.addEventListener('click', (e) => e.stopPropagation());

            // Initial fetch
            var lastNotifId = localStorage.getItem('last_admin_notif_id') || 0;
            
            fetchNotifications(true); // Initial load (silent)

            // Polling every 10 seconds for faster updates
            setInterval(() => fetchNotifications(false), 10000);
        }

        function fetchNotifications(silent = false) {
            console.log('Fetching notifications...');
            fetch('../handlers/ajax_admin_notifications.php')
                .then(res => res.json())
                .then(data => {
                    console.log('Notifications data:', data);
                    if (data.error) {
                        console.warn('Notification fetch error:', data.error);
                        return;
                    }

                    const unreadCount = data.filter(n => !parseInt(n.is_read)).length;
                    
                    if (!silent && data.length > 0) {
                        // Check for new notifications
                        const newest = data[0];
                        const newestId = parseInt(newest.id);
                        
                        if (newestId > lastNotifId && !parseInt(newest.is_read)) {
                            showToastNotification(newest);
                            lastNotifId = newestId;
                            localStorage.setItem('last_admin_notif_id', lastNotifId);
                        }
                    } else if (silent && data.length > 0) {
                        // On first load, just synchronize the ID without popping up
                        lastNotifId = parseInt(data[0].id);
                        localStorage.setItem('last_admin_notif_id', lastNotifId);
                    }

                    if (unreadCount > 0) {
                        notifCount.textContent = unreadCount;
                        notifCount.style.display = 'block';
                    } else {
                        notifCount.style.display = 'none';
                    }

                    if (data.length > 0) {
                        notifList.innerHTML = data.map(n => `
                            <a href="${n.link || '#'}" class="notification-item ${!parseInt(n.is_read) ? 'unread' : ''}" onclick="markRead(${n.id})">
                                <div class="notification-icon icon-${n.type}">
                                    <i class="fa-solid ${getIcon(n.type)}"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">${n.title}</div>
                                    <div class="notification-msg">${n.message}</div>
                                    <div class="notification-time">${getTimeAgo(n.created_at)}</div>
                                </div>
                            </a>
                        `).join('');
                    } else {
                        notifList.innerHTML = `
                            <div style="padding: 40px 20px; text-align: center; color: rgba(255,255,255,0.2);">
                                <i class="fa-solid fa-bell-slash" style="font-size: 24px; margin-bottom: 15px;"></i>
                                <div style="font-size: 13px; font-weight: 600;">No new notifications</div>
                            </div>
                        `;
                    }
                })
                .catch(err => console.warn('Notification poll failed:', err));
        }

        function getIcon(type) {
            switch(type) {
                case 'cancel':   return 'fa-xmark';
                case 'return':   return 'fa-rotate-left';
                case 'exchange': return 'fa-right-left';
                case 'success':  return 'fa-check';
                default:         return 'fa-info';
            }
        }

        window.markAllRead = function() {
            fetch('../handlers/ajax_admin_notifications.php?action=mark_all_read')
                .then(() => fetchNotifications());
        };

        window.markRead = function(id) {
            fetch('../handlers/ajax_admin_notifications.php?action=mark_read&id=' + id)
                .then(() => fetchNotifications(true));
        };

        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
            return Math.floor(diffInSeconds / 86400) + 'd ago';
        }

        function showToastNotification(notif) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `admin-toast toast-${notif.type}`;
            toast.onclick = () => { window.location.href = notif.link || 'index.php?page=orders'; };
            
            toast.innerHTML = `
                <div class="admin-toast-icon">
                    <i class="fa-solid ${getIcon(notif.type)}"></i>
                </div>
                <div class="admin-toast-body">
                    <div class="admin-toast-title">${notif.title}</div>
                    <div class="admin-toast-msg">${notif.message}</div>
                </div>
                <div class="admin-toast-close" onclick="event.stopPropagation(); this.parentElement.remove();">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            `;

            container.appendChild(toast);

            // Play notification sound if possible
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.volume = 0.3;
            audio.play().catch(() => {});

            // Auto-remove after 8 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    gsap.to(toast, { 
                        x: 100, 
                        opacity: 0, 
                        duration: 0.5, 
                        onComplete: () => toast.remove() 
                    });
                }
            }, 8000);
        }
    });
    </script>
