/**
 * ByteStore Main JavaScript
 */
(function () {
    'use strict';

    const BASE = document.documentElement.dataset.base || '';

    // Theme
    function initTheme() {
        const saved = localStorage.getItem('bytestore-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = saved || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
        updateThemeIcon(theme);
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('bytestore-theme', next);
        updateThemeIcon(next);
    }

    function updateThemeIcon(theme) {
        document.querySelectorAll('.theme-toggle-icon').forEach(el => {
            el.innerHTML = theme === 'dark'
                ? '<i class="fa-solid fa-sun"></i>'
                : '<i class="fa-solid fa-moon"></i>';
        });
    }

    // Page loader
    function initLoader() {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.remove('loading');
                document.body.classList.add('loaded');
            }, 300);
        });
    }

    // Sticky header
    function initStickyHeader() {
        const header = document.querySelector('.site-header');
        if (!header) return;
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });
    }

    // Mobile menu
    function initMobileMenu() {
        const toggle = document.getElementById('mobile-menu-toggle');
        const nav = document.getElementById('navbar-nav');
        if (!toggle || !nav) return;
        toggle.addEventListener('click', () => {
            nav.classList.toggle('mobile-open');
            toggle.setAttribute('aria-expanded', nav.classList.contains('mobile-open'));
        });
    }

    // Dropdowns
    function initDropdowns() {
        document.querySelectorAll('.navbar__dropdown').forEach(dropdown => {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            if (!trigger) return;
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const wasOpen = dropdown.classList.contains('open');
                document.querySelectorAll('.navbar__dropdown.open').forEach(d => {
                    d.classList.remove('open');
                    d.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
                });
                if (!wasOpen) {
                    dropdown.classList.add('open');
                    trigger.setAttribute('aria-expanded', 'true');
                } else {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
        document.addEventListener('click', () => {
            document.querySelectorAll('.navbar__dropdown.open').forEach(d => {
                d.classList.remove('open');
                d.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // Back to top
    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;
        window.addEventListener('scroll', () => {
            btn.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    // Toast notifications
    window.showToast = function (message, type = 'info', duration = 4000) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };

    // Wishlist toggle
    function initWishlist() {
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.wishlist-btn');
            if (!btn) return;
            e.preventDefault();
            const productId = btn.dataset.productId;
            try {
                const res = await fetch(BASE + 'api/wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId, action: 'toggle' })
                });
                const data = await res.json();
                if (data.success) {
                    btn.classList.toggle('active', data.in_wishlist);
                    const badge = document.getElementById('wishlist-count');
                    if (badge) badge.textContent = data.count;
                    showToast(data.message, 'success');
                } else {
                    if (data.redirect) window.location.href = data.redirect;
                    else showToast(data.message || 'Error', 'error');
                }
            } catch (err) {
                showToast('Could not update wishlist', 'error');
            }
        });
    }

    // Compare toggle
    function initCompare() {
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.compare-btn');
            if (!btn) return;
            e.preventDefault();
            const productId = btn.dataset.productId;
            try {
                const res = await fetch(BASE + 'api/compare.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId, action: 'add' })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const badge = document.getElementById('compare-count');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'flex' : 'none';
                    }
                } else {
                    showToast(data.message || 'Error', 'error');
                }
            } catch (err) {
                showToast('Could not add to compare', 'error');
            }
        });
    }

    // Newsletter
    function initNewsletter() {
        const form = document.getElementById('newsletter-form');
        if (!form) return;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = form.querySelector('input[name="email"]').value;
            try {
                const res = await fetch(BASE + 'api/newsletter.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) form.reset();
            } catch (err) {
                showToast('Subscription failed', 'error');
            }
        });
    }

    // Hero slider
    function initHeroSlider() {
        const slides = document.querySelectorAll('.hero__slide');
        const dots = document.querySelectorAll('.hero__dot');
        if (slides.length <= 1) return;
        let current = 0;
        let interval;

        function goTo(i) {
            slides[current].classList.remove('active');
            if (dots[current]) dots[current].classList.remove('active');
            current = i;
            slides[current].classList.add('active');
            if (dots[current]) dots[current].classList.add('active');
        }

        function next() { goTo((current + 1) % slides.length); }

        dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); resetInterval(); }));

        function resetInterval() {
            clearInterval(interval);
            interval = setInterval(next, 5000);
        }
        resetInterval();

        const hero = document.querySelector('.hero');
        if (hero) {
            hero.addEventListener('mouseenter', () => clearInterval(interval));
            hero.addEventListener('mouseleave', resetInterval);
        }
    }

    // Horizontal scroll rows
    window.scrollRow = function (id, direction) {
        const el = document.getElementById(id);
        if (!el) return;
        el.scrollBy({ left: direction === 'left' ? -300 : 300, behavior: 'smooth' });
    };

    // Theme toggle button
    document.addEventListener('click', (e) => {
        if (e.target.closest('#theme-toggle')) toggleTheme();
    });

    initTheme();
    initLoader();
    initStickyHeader();
    initMobileMenu();
    initDropdowns();
    initBackToTop();
    initWishlist();
    initCompare();
    initNewsletter();
    initHeroSlider();
})();
