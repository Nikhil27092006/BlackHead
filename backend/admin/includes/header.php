<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | BLACKHEAD</title>
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
        .admin-user-name { font-weight: 700; font-size: 13px; color: #fff; line-height: 1.2; }
        .admin-user-role { font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 1px; }
        .admin-user-caret { font-size: 10px; color: rgba(255,255,255,0.25); margin-left: 4px; }

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

                <div class="sidebar-section-label">System</div>
                <li><a href="index.php?page=payment_settings" class="<?php echo $page == 'payment_settings' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-credit-card"></i> Payments
                </a></li>
                <li><a href="index.php?page=home_highlights" class="<?php echo $page == 'home_highlights' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-home"></i> Home Highlights
                </a></li>
                <li><a href="index.php?page=settings" class="<?php echo $page == 'settings' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-sliders"></i> Settings
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
            <div class="admin-user-bar">
                <div class="admin-user-avatar"><?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?></div>
                <div>
                    <div class="admin-user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                    <div class="admin-user-role">Administrator</div>
                </div>
                <i class="fa-solid fa-chevron-down admin-user-caret"></i>
            </div>
        </div>

        <!-- Session Alerts -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="admin-alert admin-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="admin-alert admin-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
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
    });
    </script>
