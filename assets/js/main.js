document.addEventListener('DOMContentLoaded', function() {

    /* ============================================
       MOBILE MENU - Preserved from original
    ============================================ */
    const menuToggle = document.getElementById('menuToggle');
    const menuClose  = document.getElementById('menuClose');
    const navLinks   = document.getElementById('navLinks');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.add('active');
            if (menuClose) menuClose.style.display = 'block';
            if (mobileOverlay) mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    if (menuClose || mobileOverlay) {
        const closeMenu = function() {
            navLinks.classList.remove('active');
            if (menuClose) menuClose.style.display = 'none';
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        };
        if (menuClose) menuClose.addEventListener('click', closeMenu);
        if (mobileOverlay) mobileOverlay.addEventListener('click', closeMenu);
    }

    /* ============================================
       HEADER SCROLL EFFECT
    ============================================ */
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (!header) return;
        if (window.scrollY > 50) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    });

    /* ============================================
       GSAP ANIMATIONS (only if GSAP is loaded)
    ============================================ */
    if (typeof gsap === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    /* ---- 1. Custom Cursor & Interactions ---- */
    const cursor = document.getElementById('cursor-dot');
    const cursorRing = document.getElementById('cursor-ring');
    
    if (cursor && cursorRing) {
        let mouseX = 0, mouseY = 0;
        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            gsap.to(cursor, { x: mouseX, y: mouseY, duration: 0.1, ease: 'none' });
            gsap.to(cursorRing, { x: mouseX, y: mouseY, duration: 0.35, ease: 'power2.out' });
        });

        // Magnetic Buttons & Cards
        document.querySelectorAll('.btn-glow, .premium-cat-card, .btn-product-shop, .back-to-top').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                gsap.to(btn, { x: x * 0.3, y: y * 0.3, duration: 0.4, ease: 'power2.out' });
                gsap.to(cursorRing, { scale: 1.5, opacity: 0.5, backgroundColor: 'rgba(139,92,246,0.1)', duration: 0.3 });
            });
            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, { x: 0, y: 0, duration: 0.6, ease: 'elastic.out(1, 0.3)' });
                gsap.to(cursorRing, { scale: 1, opacity: 1, backgroundColor: 'transparent', duration: 0.3 });
            });
        });

        // Click Ripple Effect
        window.addEventListener('mousedown', () => {
            gsap.to(cursorRing, { scale: 0.7, opacity: 0.8, duration: 0.2 });
        });
        window.addEventListener('mouseup', () => {
            gsap.to(cursorRing, { scale: 1, opacity: 1, duration: 0.2 });
        });
    }

    /* ---- 2. SVG Path Drawing on Scroll ---- */
    const scrollPath = document.getElementById('scrollPath');
    if (scrollPath) {
        try {
            const pathLength = scrollPath.getTotalLength();
            gsap.set(scrollPath, { strokeDasharray: pathLength, strokeDashoffset: pathLength });
            gsap.to(scrollPath, {
                strokeDashoffset: 0,
                ease: 'none',
                scrollTrigger: {
                    trigger: 'body',
                    start: 'top top',
                    end: 'bottom bottom',
                    scrub: 1
                }
            });
        } catch(e) { console.warn("SVG path length calculation failed."); }
    }

    /* ---- 3. Split Text Reveal Animation ---- */
    document.querySelectorAll('.reveal-heading h2, .hero-subtitle').forEach(el => {
        const text = el.innerText;
        el.innerHTML = text.split(' ').map(word => `<span class="word">${word}</span>`).join(' ');
        
        gsap.fromTo(el.querySelectorAll('.word'), 
            { y: 40, opacity: 0, rotationX: -10 },
            {
                scrollTrigger: { trigger: el, start: 'top 85%', once: true },
                y: 0, opacity: 1, rotationX: 0,
                duration: 1, stagger: 0.05, ease: 'power4.out'
            }
        );
    });

    /* ---- 4. Hero Advanced Timeline ---- */
    const heroSection = document.querySelector('.hero-premium');
    if (heroSection) {
        const tl = gsap.timeline({ delay: 0.2 });
        tl.fromTo('.hero-orb-1', { scale: 0, opacity: 0 }, { scale: 1, opacity: 0.6, duration: 2, ease: 'power3.out' }, 0)
          .fromTo('.hero-orb-2', { scale: 0, opacity: 0 }, { scale: 1, opacity: 0.4, duration: 2.2, ease: 'power3.out' }, 0.2)
          .fromTo('.hero-orb-3', { scale: 0, opacity: 0 }, { scale: 1, opacity: 0.3, duration: 2, ease: 'power3.out' }, 0.4)
          .fromTo('.hero-eyebrow', { x: -30, opacity: 0 }, { x: 0, opacity: 1, duration: 0.8 }, 0.5)
          .fromTo('.hero-title span', { y: 100, opacity: 0, skewY: 7 }, { y: 0, opacity: 1, skewY: 0, duration: 1.2, stagger: 0.15, ease: 'power4.out' }, 0.6)
          .fromTo('.hero-cta-group > *', { y: 30, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: 'power3.out' }, 1.4)
          .fromTo('.hero-scroll-hint', { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 1, ease: 'power2.inOut' }, 2);

        // Advanced Mouse Parallax for Orbs
        window.addEventListener('mousemove', (e) => {
            const { clientX, clientY } = e;
            const xPos = (clientX / window.innerWidth - 0.5) * 60;
            const yPos = (clientY / window.innerHeight - 0.5) * 60;
            gsap.to('.hero-orb-1', { x: xPos, y: yPos, duration: 2, ease: 'power2.out' });
            gsap.to('.hero-orb-2', { x: -xPos * 0.8, y: -yPos * 0.8, duration: 2, ease: 'power2.out' });
        });
    }

    /* ---- 5. Marquee Ticker (Physics-like momentum) ---- */
    const marqueeTrack = document.getElementById('marqueeTrack');
    if (marqueeTrack) {
        gsap.to(marqueeTrack, {
            xPercent: -50,
            repeat: -1,
            duration: 30,
            ease: 'none'
        });
    }

    /* ---- 6. Professional Cards Entrance ---- */
    if (document.querySelector('.premium-cat-card')) {
        gsap.fromTo('.premium-cat-card', 
            { y: 100, opacity: 0, scale: 0.9, rotationY: 15 },
            {
                scrollTrigger: { trigger: '.cat-grid', start: 'top 80%', once: true },
                y: 0, opacity: 1, scale: 1, rotationY: 0,
                duration: 1.2, stagger: 0.2, ease: 'expo.out'
            }
        );
    }

    if (document.querySelector('.premium-product-card')) {
        gsap.fromTo('.premium-product-card', 
            { y: 60, opacity: 0 },
            {
                scrollTrigger: { trigger: '.product-grid-new', start: 'top 85%', once: true },
                y: 0, opacity: 1,
                duration: 1, stagger: 0.15, ease: 'power3.out'
            }
        );
    }

    /* ---- 7. Stats Counter (Physics-like finish) ---- */
    document.querySelectorAll('.stat-number').forEach(stat => {
        const target = parseInt(stat.getAttribute('data-count'));
        const suffix = stat.getAttribute('data-suffix') || '';
        ScrollTrigger.create({
            trigger: stat,
            start: 'top 90%',
            onEnter: () => {
                gsap.to({ val: 0 }, {
                    val: target,
                    duration: 2.5,
                    ease: 'elastic.out(1, 0.3)',
                    onUpdate: function() {
                        stat.innerText = Math.floor(this.targets()[0].val) + suffix;
                    }
                });
            }
        });
    });

    /* ---- 8. Back to Top & End of Page Animations ---- */
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        ScrollTrigger.create({
            trigger: 'body',
            start: '30% top',
            onToggle: self => {
                gsap.to(backToTop, {
                    scale: self.isActive ? 1 : 0,
                    opacity: self.isActive ? 1 : 0,
                    duration: 0.5,
                    ease: 'back.out(1.7)'
                });
            }
        });
        backToTop.addEventListener('click', () => {
            gsap.to(window, { scrollTo: 0, duration: 1.5, ease: 'power4.inOut' });
        });
    }

    // Newsletter section "reach end" reveal
    if (document.querySelector('.newsletter-section')) {
        gsap.fromTo('.newsletter-inner', 
            { scale: 0.8, opacity: 0, y: 100 },
            {
                scrollTrigger: { trigger: '.newsletter-section', start: 'top 90%', once: true },
                scale: 1, opacity: 1, y: 0,
                duration: 1.2, ease: 'elastic.out(1, 0.5)'
            }
        );
    }
});
