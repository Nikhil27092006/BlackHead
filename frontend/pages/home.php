<style>
/* ============================================
   HOME PAGE - PREMIUM OVERHAUL
============================================ */

/* -- Hero -- */
.hero-premium {
    min-height: 100vh;
    background: #000;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
}
.hero-orb-1, .hero-orb-2, .hero-orb-3 {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(80px);
    will-change: transform;
}
.hero-orb-1 {
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(139,92,246,0.45) 0%, transparent 70%);
    top: -200px; right: -150px;
}
.hero-orb-2 {
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(99,102,241,0.35) 0%, transparent 70%);
    bottom: -100px; left: -100px;
}
.hero-orb-3 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(236,72,153,0.2) 0%, transparent 70%);
    top: 40%; left: 40%;
}
.hero-noise {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
    opacity: 0.04;
    pointer-events: none;
}
.hero-grid-lines {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 80px 80px;
    pointer-events: none;
}
.hero-content-wrap {
    position: relative;
    z-index: 10;
    width: 100%;
}
.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(139,92,246,0.12);
    border: 1px solid rgba(139,92,246,0.35);
    color: #a78bfa;
    padding: 8px 20px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 32px;
}
.hero-eyebrow .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #8b5cf6;
    box-shadow: 0 0 8px #8b5cf6;
    animation: pulse-dot 2s ease infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
}
.hero-title {
    font-family: 'Syne', 'Outfit', sans-serif;
    font-size: clamp(3.5rem, 9vw, 7.5rem);
    font-weight: 800;
    line-height: 0.92;
    color: #fff;
    letter-spacing: -4px;
    margin-bottom: 32px;
    overflow: hidden;
}
.hero-line-1, .hero-line-2 {
    display: block;
}
.hero-line-2 {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 40%, #ec4899 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.hero-subtitle {
    font-size: 17px;
    color: rgba(255,255,255,0.5);
    max-width: 480px;
    line-height: 1.75;
    font-weight: 400;
    margin-bottom: 48px;
}
.hero-cta-group {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}
.hero-cta-group > * {  }
.btn-glow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 40px;
    border-radius: 100px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    cursor: pointer;
    border: none;
    transition: all 0.35s cubic-bezier(0.16,1,0.3,1);
    text-decoration: none;
}
.btn-glow-primary {
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    box-shadow: 0 8px 32px rgba(139,92,246,0.45);
}
.btn-glow-primary:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 16px 48px rgba(139,92,246,0.6);
    color: #fff;
}
.btn-glow-ghost {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.85);
    border: 1px solid rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
}
.btn-glow-ghost:hover {
    background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.3);
    transform: translateY(-3px);
    color: #fff;
}
.hero-scroll-hint {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,0.3);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    z-index: 10;
}
.scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, rgba(139,92,246,0.8), transparent);
}

/* -- Marquee Ticker -- */
.marquee-strip {
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    overflow: hidden;
    padding: 14px 0;
    position: relative;
    z-index: 5;
}
.marquee-track {
    display: flex;
    width: max-content;
    will-change: transform;
}
.marquee-item {
    display: inline-flex;
    align-items: center;
    gap: 18px;
    white-space: nowrap;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.9);
    padding: 0 32px;
}
.marquee-sep {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    flex-shrink: 0;
}

/* -- Stats Band -- */
.stats-band {
    background: #0a0a0a;
    padding: 72px 0;
    border-top: 1px solid rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
}
.stat-item {
    text-align: center;
    padding: 0 30px;
    position: relative;
    opacity: 0;
}
.stat-item + .stat-item::before {
    content: '';
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 1px;
    background: rgba(255,255,255,0.08);
}
.stat-number {
    font-family: 'Syne', 'Outfit', sans-serif;
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -2px;
    line-height: 1;
    margin-bottom: 8px;
}
.stat-number span { color: #8b5cf6; }
.stat-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
}

/* -- Section Header Shared -- */
.section-header {
    margin-bottom: 64px;
}
.section-eyebrow {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #8b5cf6;
    margin-bottom: 16px;
    display: block;
}
.section-title-xl {
    font-family: 'Syne', 'Outfit', sans-serif;
    font-size: clamp(2.2rem, 5vw, 4rem);
    font-weight: 800;
    letter-spacing: -2px;
    line-height: 1.05;
    color: inherit;
}

/* -- Categories -- */
.categories-section-wrap {
    padding: 100px 0;
    background: #fff;
}
.categories-section-wrap .section-title-xl { color: #0f172a; }
.cat-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr;
    gap: 20px;
    align-items: stretch;
}
.premium-cat-card {
    border-radius: 24px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    background: #111;
    aspect-ratio: auto;
    height: 520px;
    will-change: transform, opacity;
}
.premium-cat-card.cat-tall { height: 520px; }
.premium-cat-card img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.9s cubic-bezier(0.16,1,0.3,1);
}
.premium-cat-card:hover img { transform: scale(1.08); }
.premium-cat-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.1) 60%, transparent 100%);
}
.premium-cat-content {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 36px;
}
.cat-tag {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #a78bfa;
    margin-bottom: 10px;
    display: block;
}
.cat-name {
    font-family: 'Syne', 'Outfit', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -1.5px;
    line-height: 1;
    margin-bottom: 20px;
}
.cat-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #fff;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(8px);
    padding: 10px 22px;
    border-radius: 100px;
    transition: all 0.3s ease;
    text-decoration: none;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
}
.premium-cat-card:hover .cat-cta {
    opacity: 1;
    transform: translateY(0);
    background: rgba(139,92,246,0.3);
    border-color: rgba(139,92,246,0.5);
}

/* -- Products Section -- */
.products-section-wrap {
    padding: 100px 0;
    background: #0a0a0a;
}
.products-section-wrap .section-title-xl,
.products-section-wrap .section-eyebrow { /* keep eyebrow purple */ }
.products-section-wrap .section-title-xl { color: #fff; }
.products-section-wrap .reveal-heading { color: #fff; }
.product-grid-new {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}
.premium-product-card {
    background: #111;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.06);
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    transition: all 0.5s cubic-bezier(0.16,1,0.3,1);
    will-change: transform, opacity;
}
.premium-product-card:hover {
    transform: translateY(-8px);
    border-color: rgba(139,92,246,0.3);
    box-shadow: 0 24px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(139,92,246,0.15);
}
.product-image-container {
    aspect-ratio: 3/4;
    overflow: hidden;
    position: relative;
    background: #1a1a2e;
}
.product-image-container img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.7s cubic-bezier(0.16,1,0.3,1);
}
.premium-product-card:hover .product-image-container img { transform: scale(1.07); }
.product-badge-premium {
    position: absolute;
    top: 16px; left: 16px;
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 100px;
    z-index: 3;
}
.product-wishlist-btn {
    position: absolute;
    top: 16px; right: 16px;
    width: 40px; height: 40px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    z-index: 3;
    color: #fff;
    transition: all 0.3s ease;
    font-size: 14px;
}
.product-wishlist-btn:hover {
    background: rgba(236,72,153,0.3);
    border-color: rgba(236,72,153,0.5);
    color: #f472b6;
    transform: scale(1.1);
}
.premium-product-info {
    padding: 36px 32px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.premium-product-cat {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.25);
    margin-bottom: 12px;
}
.premium-product-name {
    font-family: 'Syne', 'Outfit', sans-serif;
    font-size: clamp(1.2rem, 2vw, 1.7rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.5px;
    line-height: 1.15;
    margin-bottom: 20px;
    text-decoration: none;
    display: block;
}
.premium-product-name:hover { color: #a78bfa; }
.product-dna {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 20px 0;
    border-top: 1px solid rgba(255,255,255,0.07);
    border-bottom: 1px solid rgba(255,255,255,0.07);
    margin-bottom: 24px;
}
.dna-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    font-weight: 500;
}
.dna-item i { color: #6366f1; font-size: 11px; width: 14px; }
.dna-badge {
    background: rgba(139,92,246,0.12);
    color: #a78bfa;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid rgba(139,92,246,0.2);
}
.premium-product-price {
    font-family: 'Syne', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -1px;
    margin-bottom: 24px;
}
.btn-product-shop {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.35s cubic-bezier(0.16,1,0.3,1);
    box-shadow: 0 8px 24px rgba(139,92,246,0.3);
    align-self: flex-start;
}
.btn-product-shop:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 36px rgba(139,92,246,0.5);
    color: #fff;
}
.products-all-btn-wrap { margin-top: 56px; text-align: center; }
.btn-outline-glow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 48px;
    border-radius: 100px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    background: transparent;
    border: 1px solid rgba(139,92,246,0.4);
    color: #a78bfa;
    text-decoration: none;
    transition: all 0.35s cubic-bezier(0.16,1,0.3,1);
}
.btn-outline-glow:hover {
    background: rgba(139,92,246,0.1);
    border-color: #8b5cf6;
    color: #c4b5fd;
    transform: translateY(-3px);
    box-shadow: 0 0 30px rgba(139,92,246,0.2);
}

/* -- Newsletter -- */
.newsletter-section {
    padding: 60px 0 100px;
    background: #0a0a0a;
}
.newsletter-inner {
    background: linear-gradient(135deg, rgba(139,92,246,0.15) 0%, rgba(99,102,241,0.08) 100%);
    border: 1px solid rgba(139,92,246,0.2);
    border-radius: 32px;
    padding: 80px 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
    opacity: 0;
}
.newsletter-inner::before {
    content: '';
    position: absolute;
    top: -100px; left: 50%;
    transform: translateX(-50%);
    width: 500px; height: 300px;
    background: radial-gradient(ellipse, rgba(139,92,246,0.2) 0%, transparent 70%);
    pointer-events: none;
}
.newsletter-eyebrow {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #8b5cf6;
    margin-bottom: 20px;
    display: block;
}
.newsletter-heading {
    font-family: 'Syne', 'Outfit', sans-serif;
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -2px;
    line-height: 1.05;
    margin-bottom: 20px;
}
.newsletter-sub {
    color: rgba(255,255,255,0.45);
    font-size: 16px;
    max-width: 520px;
    margin: 0 auto 48px;
    line-height: 1.75;
}
.newsletter-form {
    display: flex;
    max-width: 520px;
    margin: 0 auto;
    gap: 0;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 100px;
    padding: 6px 6px 6px 28px;
    backdrop-filter: blur(10px);
    transition: border-color 0.3s;
}
.newsletter-form:focus-within {
    border-color: rgba(139,92,246,0.5);
    box-shadow: 0 0 0 4px rgba(139,92,246,0.1);
}
.newsletter-input {
    flex: 1;
    background: none;
    border: none;
    outline: none;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
}
.newsletter-input::placeholder { color: rgba(255,255,255,0.3); }
.newsletter-submit {
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    border: none;
    padding: 14px 32px;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 8px 24px rgba(139,92,246,0.35);
}
.newsletter-submit:hover {
    transform: scale(1.04);
    box-shadow: 0 12px 32px rgba(139,92,246,0.5);
}

/* -- Custom Cursor -- */
#cursor-dot {
    width: 8px; height: 8px;
    background: #8b5cf6;
    border-radius: 50%;
    position: fixed;
    top: 0; left: 0;
    pointer-events: none;
    z-index: 99999;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 10px rgba(139,92,246,0.8);
}
#cursor-ring {
    width: 36px; height: 36px;
    border: 1.5px solid rgba(139,92,246,0.6);
    border-radius: 50%;
    position: fixed;
    top: 0; left: 0;
    pointer-events: none;
    z-index: 99998;
    transform: translate(-50%, -50%);
    transition: transform 0.1s;
}

/* -- Mobile Responsive -- */
@media (max-width: 1024px) {
    .cat-grid { grid-template-columns: 1fr 1fr; }
    .premium-cat-card.cat-tall { grid-row: span 1; height: 420px; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 40px; }
    .stat-item + .stat-item::before { display: none; }
}
@media (max-width: 768px) {
    .hero-title { letter-spacing: -2px; }
    .cat-grid { grid-template-columns: 1fr; }
    .premium-cat-card { height: 340px !important; }
    .product-grid-new { grid-template-columns: 1fr; }
    .premium-product-card { grid-template-columns: 1fr; }
    .product-image-container { aspect-ratio: 16/9; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .newsletter-inner { padding: 48px 28px; }
    #cursor-dot, #cursor-ring { display: none; }
}
@media (max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .hero-cta-group { flex-direction: column; }
    .btn-glow { justify-content: center; }
}

/* -- Advanced GSAP Utility -- */
.splitting .word {
    display: inline-block;
    will-change: transform, opacity;
}

/* -- Floating Deco Line -- */
.deco-line-container {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none;
    z-index: 1;
    opacity: 0.15;
}

/* -- Back to Top -- */
.back-to-top {
    position: fixed;
    bottom: 40px;
    right: 40px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 999;
    scale: 0;
    opacity: 0;
    box-shadow: 0 10px 30px rgba(139,92,246,0.4);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.back-to-top:hover {
    scale: 1.1 !important;
    box-shadow: 0 15px 40px rgba(139,92,246,0.6);
}
</style>

<!-- Floating Deco Line SVG -->
<div class="deco-line-container">
    <svg width="100%" height="100%" viewBox="0 0 1000 1000" preserveAspectRatio="none" fill="none">
        <path id="scrollPath" d="M -100 200 Q 250 100 500 500 T 1100 800" stroke="#8b5cf6" stroke-width="2" />
    </svg>
</div>

<!-- Back to Top Button -->
<div class="back-to-top" id="backToTop">
    <i class="fa-solid fa-arrow-up"></i>
</div>

<!-- ============ HERO SECTION ============ -->
<section class="hero-premium">
    <!-- Background elements -->
    <div class="hero-orb-1"></div>
    <div class="hero-orb-2"></div>
    <div class="hero-orb-3"></div>
    <div class="hero-noise"></div>
    <div class="hero-grid-lines"></div>

    <div class="hero-content-wrap">
        <div class="container">
            <div class="hero-eyebrow">
                <span class="dot"></span>
                SS 2025 — New Collection Dropped
            </div>
            <h1 class="hero-title">
                <span class="hero-line-1">ENGINEERED</span>
                <span class="hero-line-2">EXCELLENCE</span>
            </h1>
            <p class="hero-subtitle">Experience the intersection of luxury and street culture. Our new collection defines the future of premium apparel.</p>
            <div class="hero-cta-group">
                <a href="index.php?page=products" class="btn-glow btn-glow-primary">
                    Explore Collection <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="index.php?page=about" class="btn-glow btn-glow-ghost">
                    Our Story
                </a>
            </div>
        </div>
    </div>

    <div class="hero-scroll-hint">
        <div class="scroll-line"></div>
        SCROLL
    </div>
</section>

<!-- ============ MARQUEE TICKER ============ -->
<div class="marquee-strip">
    <div class="marquee-track" id="marqueeTrack">
        <?php $items = ['Free Shipping Over ₹999','New Drop: SS 2025','Premium Streetwear','Engineered Excellence','Limited Editions','Youth Culture','Elite Athleisure','100% Authentic']; for($i=0;$i<4;$i++) foreach($items as $item): ?>
        <span class="marquee-item">
            <?php echo $item; ?>
            <span class="marquee-sep"></span>
        </span>
        <?php endforeach; ?>
    </div>
</div>

<!-- ============ STATS BAND ============ -->
<section class="stats-band">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-count="12000" data-suffix="+">0+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="500" data-suffix="+">0+</div>
                <div class="stat-label">Products Available</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="98" data-suffix="%">0%</div>
                <div class="stat-label">Satisfaction Rate</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="4" data-suffix="+">0+</div>
                <div class="stat-label">Years Of Excellence</div>
            </div>
        </div>
    </div>
</section>

<!-- ============ CATEGORIES ============ -->
<section class="categories-section-wrap">
    <div class="container">
        <div class="section-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div class="reveal-heading">
                <span class="section-eyebrow"><?php echo htmlspecialchars(getSetting('categories_subtitle', 'Shop By Category')); ?></span>
                <h2 class="section-title-xl"><?php echo htmlspecialchars(getSetting('categories_title_1', 'FIND YOUR')); ?><br><em style="font-style:italic; font-weight:400;"><?php echo htmlspecialchars(getSetting('categories_title_2', 'STYLE')); ?></em></h2>
            </div>
            <a href="index.php?page=products" class="btn-outline-glow" style="border-color:rgba(0,0,0,0.15); color:#0f172a; margin-bottom:8px;" onmouseover="this.style.color='#6366f1'; this.style.borderColor='#6366f1';" onmouseout="this.style.color='#0f172a'; this.style.borderColor='rgba(0,0,0,0.15)';">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="cat-grid categories-section-wrap-grid">
            <!-- MEN - tall -->
            <div class="premium-cat-card cat-tall" onclick="location.href='index.php?page=products&category=men'" style="grid-row: span 1;">
                <img src="assets/images/cat-men.jpg" alt="Men's Collection" loading="lazy">
                <div class="premium-cat-overlay"></div>
                <div class="premium-cat-content">
                    <span class="cat-tag">Premium Essentials</span>
                    <h2 class="cat-name">MEN</h2>
                    <a href="index.php?page=products&category=men" class="cat-cta">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <!-- WOMEN -->
            <div class="premium-cat-card" onclick="location.href='index.php?page=products&category=women'">
                <img src="assets/images/cat-women.jpg" alt="Women's Collection" loading="lazy">
                <div class="premium-cat-overlay"></div>
                <div class="premium-cat-content">
                    <span class="cat-tag">Elite Athleisure</span>
                    <h2 class="cat-name">WOMEN</h2>
                    <a href="index.php?page=products&category=women" class="cat-cta">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <!-- KIDS -->
            <div class="premium-cat-card" onclick="location.href='index.php?page=products&category=kids'">
                <img src="assets/images/cat-kids.jpg" alt="Kids Collection" loading="lazy">
                <div class="premium-cat-overlay"></div>
                <div class="premium-cat-content">
                    <span class="cat-tag">Youth Culture</span>
                    <h2 class="cat-name">KIDS</h2>
                    <a href="index.php?page=products&category=kids" class="cat-cta">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ TRENDING PRODUCTS ============ -->
<section class="products-section-wrap">
    <div class="container">
        <div class="section-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div class="reveal-heading">
                <span class="section-eyebrow"><?php echo htmlspecialchars(getSetting('trending_subtitle', 'Top Trends')); ?></span>
                <h2 class="section-title-xl"><?php echo htmlspecialchars(getSetting('trending_title_1', 'CURRENTLY')); ?> <em style="font-style:italic; font-weight:400;"><?php echo htmlspecialchars(getSetting('trending_title_2', 'HOT')); ?></em></h2>
            </div>
            <a href="index.php?page=products" class="btn-outline-glow" style="margin-bottom:8px;">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="product-grid-new">
            <?php
            $products = getFeaturedProducts($pdo, 4);
            foreach($products as $p):
                $image = $p['product_image'] ?: 'placeholder.jpg';
            ?>
            <div class="premium-product-card">
                <div class="product-image-container">
                    <?php if($p['is_new']): ?>
                        <div class="product-badge-premium">New Drop</div>
                    <?php endif; ?>
                    <?php if(isset($p['total_stock']) && $p['total_stock'] > 0 && $p['total_stock'] <= 3): ?>
                        <div class="product-badge-premium" style="top:<?php echo $p['is_new'] ? '54px' : '16px'; ?>; background: linear-gradient(135deg,#ef4444,#dc2626);">Low Stock</div>
                    <?php endif; ?>
                    <a href="index.php?page=product&id=<?php echo $p['id']; ?>">
                        <img src="assets/images/<?php echo $image; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                    </a>
                    <form action="backend/handlers/wishlist_handler.php" method="POST">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="product-wishlist-btn" title="Add to wishlist">
                            <i class="<?php echo isLoggedIn() && isInWishlist($pdo, $_SESSION['user_id'], $p['id']) ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                        </button>
                    </form>
                </div>
                <div class="premium-product-info">
                    <div>
                        <div class="premium-product-cat">Clothing / Essentials</div>
                        <a href="index.php?page=product&id=<?php echo $p['id']; ?>" class="premium-product-name"><?php echo htmlspecialchars($p['name']); ?></a>
                        
                        <div class="premium-product-desc" style="font-size: 13px; color: rgba(255,255,255,0.45); line-height: 1.6; margin-bottom: 24px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3.2em;">
                            <?php echo htmlspecialchars($p['description']); ?>
                        </div>

                        <div class="product-dna">
                            <div class="dna-item"><i class="fa-solid fa-microchip"></i><span><span class="dna-badge">Tech</span> High-Density Breathable Luxury Fabric</span></div>
                            <div class="dna-item"><i class="fa-solid fa-ruler-combined"></i><span><span class="dna-badge">Fit</span> Precision Engineered Modern Silhouette</span></div>
                        </div>
                    </div>
                    <div style="margin-top: auto;">
                        <div class="premium-product-price" style="margin-bottom: 16px;"><?php echo formatPrice($p['price']); ?></div>
                        <a href="index.php?page=product&id=<?php echo $p['id']; ?>" class="btn-product-shop">
                            Shop Now <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="products-all-btn-wrap">
            <a href="index.php?page=products" class="btn-outline-glow">Browse Entire Collection <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- ============ NEWSLETTER ============ -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-inner">
            <span class="newsletter-eyebrow">Stay Elite</span>
            <h2 class="newsletter-heading">JOIN THE<br>BLACKHEAD CLUB</h2>
            <p class="newsletter-sub">Be the first to know about secret drops, exclusive events, and the latest trends from the BLACKHEAD laboratory.</p>
            <form class="newsletter-form" action="backend/handlers/contact_handler.php" method="POST">
                <input type="hidden" name="action" value="newsletter">
                <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address..." required>
                <button type="submit" class="newsletter-submit">Subscribe</button>
            </form>
        </div>
    </div>
</section>
