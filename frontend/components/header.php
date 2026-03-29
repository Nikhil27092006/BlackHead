<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Official Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- GSAP + ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Custom Cursor -->
    <div id="cursor-dot"></div>
    <div id="cursor-ring"></div>
    <div class="top-bar">
        <i class="fa-solid fa-bolt" style="color: #a78bfa; margin-right: 6px;"></i>
        <?php echo getSetting('offer_bar_text', 'Join the Blackhead Club & get 20% off your first order | Free shipping over ₹999'); ?>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert-container alert-success">
            <div class="container">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert-container alert-error">
            <div class="container">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        </div>
    <?php endif; ?>

    <header id="site-header">
        <div class="container navbar">
            <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="index.php" class="logo">BLACKHEAD</a>

            <div class="mobile-overlay" id="mobileOverlay"></div>

            <nav class="nav-links" id="navLinks">
                <button class="menu-close" id="menuClose" style="display:none; position:absolute; top:20px; right:20px; background:none; border:none; font-size:22px; cursor:pointer; color:rgba(255,255,255,0.7);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <a href="index.php?page=products&category=men">MEN</a>
                <a href="index.php?page=products&category=women">WOMEN</a>
                <a href="index.php?page=products&category=kids">KIDS</a>
                <a href="index.php?page=products&collection=new" style="color: #a78bfa;">NEW DROPS</a>
                
                <!-- Mobile Menu Icons -->
                <div class="mobile-menu-icons">
                    <a href="index.php?page=wishlist" title="Wishlist">
                        <i class="fa-regular fa-heart"></i> Wishlist
                    </a>
                    <a href="index.php?page=cart" title="Cart">
                        <i class="fa-solid fa-cart-shopping"></i> Cart
                    </a>
                    <a href="index.php?page=<?php echo isLoggedIn() ? 'account' : 'login'; ?>" title="Account">
                        <i class="fa-regular fa-user"></i> <?php echo isLoggedIn() ? 'Account' : 'Login'; ?>
                    </a>
                </div>
            </nav>

            <div class="nav-icons">
                <!-- Live Search -->
                <div class="search-wrapper" style="position: relative; display: flex; align-items: center;">
                    <form action="index.php" method="GET" class="search-form" style="display:flex; align-items:center; background:rgba(255,255,255,0.07); padding:7px 16px; border-radius:100px; border:1px solid rgba(255,255,255,0.1); gap:8px;">
                        <input type="hidden" name="page" value="products">
                        <input type="text" name="q" id="liveSearchInput" placeholder="Search..." style="background:none; border:none; outline:none; font-size:12px; width:110px; color:#fff; font-weight:500;" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off">
                        <button type="submit" style="background:none; border:none; cursor:pointer; color:rgba(255,255,255,0.5); font-size:13px; display:flex; padding:0;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                    
                    <!-- Dropdown Results Container -->
                    <div id="searchResultsDropdown" style="position: absolute; top: calc(100% + 10px); right: 0; width: 320px; background: rgba(10,10,10,0.95); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(16px); border-radius: 16px; padding: 10px; display: none; flex-direction: column; gap: 8px; z-index: 9999; box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 0 1px rgba(139,92,246,0.15);">
                        <div id="searchResultsList" style="display:flex; flex-direction:column; gap:4px; max-height:350px; overflow-y:auto;">
                            <!-- JS injected results -->
                        </div>
                        <a href="#" id="searchViewAllBtn" style="display:none; text-align:center; padding:12px 10px; font-size:11px; font-weight:800; color:#c4b5fd; text-decoration:none; text-transform:uppercase; letter-spacing:1.5px; border-top:1px solid rgba(255,255,255,0.05); margin-top:6px; transition: color 0.3s; background: rgba(255,255,255,0.02); border-radius: 10px;" onmouseover="this.style.color='#fff'; this.style.background='rgba(139,92,246,0.15)';" onmouseout="this.style.color='#c4b5fd'; this.style.background='rgba(255,255,255,0.02)';">View All Results <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('liveSearchInput');
                    const searchDropdown = document.getElementById('searchResultsDropdown');
                    const resultsList = document.getElementById('searchResultsList');
                    const viewAllBtn = document.getElementById('searchViewAllBtn');
                    let debounceTimer;
                    let lastQuery = '';

                    searchInput.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        const q = this.value.trim();
                        
                        if (q.length < 2) {
                            searchDropdown.style.display = 'none';
                            lastQuery = q;
                            return;
                        }
                        
                        if (q === lastQuery) {
                            searchDropdown.style.display = 'flex';
                            return;
                        }
                        
                        debounceTimer = setTimeout(() => {
                            lastQuery = q;
                            fetch(`backend/handlers/ajax_search.php?q=${encodeURIComponent(q)}`)
                                .then(res => res.json())
                                .then(data => {
                                    resultsList.innerHTML = '';
                                    if (data.length > 0) {
                                        data.forEach(item => {
                                            const a = document.createElement('a');
                                            a.href = item.url;
                                            a.style.cssText = 'display:flex; align-items:center; gap:12px; padding:10px; border-radius:12px; transition:all 0.2s; text-decoration:none;';
                                            a.onmouseover = () => { a.style.background = 'rgba(255,255,255,0.08)'; a.style.transform = 'translateX(4px)'; };
                                            a.onmouseout = () => { a.style.background = 'transparent'; a.style.transform = 'translateX(0)'; };
                                            
                                            a.innerHTML = `
                                                <div style="width:44px; height:44px; border-radius:8px; overflow:hidden; background:#111; flex-shrink:0;">
                                                    <img src="${item.image}" style="width:100%; height:100%; object-fit:cover;">
                                                </div>
                                                <div style="flex:1; min-width:0; padding-right:8px;">
                                                    <div style="color:#fff; font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:2px;">${item.name}</div>
                                                    <div style="color:#8b5cf6; font-size:11px; font-weight:800;">${item.price}</div>
                                                </div>
                                            `;
                                            resultsList.appendChild(a);
                                        });
                                        viewAllBtn.style.display = 'block';
                                        viewAllBtn.href = `index.php?page=products&q=${encodeURIComponent(q)}`;
                                    } else {
                                        resultsList.innerHTML = `<div style="padding:24px 16px; text-align:center;">
                                            <i class="fa-solid fa-magnifying-glass" style="font-size:24px; color:rgba(255,255,255,0.1); margin-bottom:12px;"></i>
                                            <div style="color:rgba(255,255,255,0.4); font-size:12px; font-weight:600;">No products found for "${q}"</div>
                                        </div>`;
                                        viewAllBtn.style.display = 'none';
                                    }
                                    searchDropdown.style.display = 'flex';
                                })
                                .catch(err => {
                                    console.error('Search error:', err);
                                });
                        }, 300); // 300ms debounce
                    });
                    
                    // Close dropdown when interacting anywhere outside the search form
                    document.addEventListener('click', function(e) {
                        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                            searchDropdown.style.display = 'none';
                        }
                    });
                    
                    // Expansion effect
                    searchInput.addEventListener('focus', function() {
                        this.style.width = '180px';
                        this.style.background = 'rgba(255,255,255,0.12)';
                        if (this.value.trim().length >= 2 && resultsList.innerHTML !== '') {
                            searchDropdown.style.display = 'flex';
                        }
                    });
                    
                    searchInput.addEventListener('blur', function() {
                        if (this.value.trim() === '') {
                            this.style.width = '110px';
                            this.style.background = 'none';
                        }
                    });
                });
                </script>

                <a href="index.php?page=wishlist" title="Wishlist">
                    <i class="fa-regular fa-heart"></i>
                </a>

                <a href="index.php?page=cart" style="position:relative;" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php $count = getCartCount($pdo); if($count > 0): ?>
                        <span style="position:absolute; top:-9px; right:-9px; background:linear-gradient(135deg,#8b5cf6,#6366f1); color:#fff; border-radius:50%; width:18px; height:18px; font-size:10px; display:flex; align-items:center; justify-content:center; font-weight:800; box-shadow:0 2px 8px rgba(139,92,246,0.5);">
                            <?php echo $count; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <a href="index.php?page=<?php echo isLoggedIn() ? 'account' : 'login'; ?>" style="display:flex; align-items:center; gap:6px;" title="Account">
                    <i class="fa-regular fa-user"></i>
                    <?php if(isLoggedIn()): ?>
                        <span style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.7);"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <main>
