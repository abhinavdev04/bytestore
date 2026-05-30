/**
 * ByteStore Live Search — navbar + shop page
 */
(function () {
    'use strict';

    const BASE = document.documentElement.dataset.base || '';

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderSuggestions(data, container) {
        let html = '';
        if (data.brands?.length) {
            html += '<div class="search-suggestion-group">Brands</div>';
            data.brands.forEach(b => {
                html += `<a href="${BASE}customer/shop.php?brand=${encodeURIComponent(b)}" class="search-suggestion search-suggestion--brand"><i class="fa-solid fa-tag"></i> ${escapeHtml(b)}</a>`;
            });
        }
        if (data.categories?.length) {
            html += '<div class="search-suggestion-group">Categories</div>';
            data.categories.forEach(c => {
                html += `<a href="${BASE}customer/shop.php?category=${c.category_id}" class="search-suggestion search-suggestion--category"><i class="fa-solid fa-folder"></i> ${escapeHtml(c.category_name)}</a>`;
            });
        }
        if (data.products?.length) {
            html += '<div class="search-suggestion-group">Products</div>';
            data.products.forEach(p => {
                html += `<a href="${BASE}customer/product.php?id=${p.product_id}" class="search-suggestion">
                    <img src="${BASE}${p.product_image_path.replace(/^\//,'')}" alt="" onerror="this.src='${BASE}assets/images/placeholder.svg'">
                    <div><strong>${escapeHtml(p.product_name)}</strong>${p.brand ? '<br><small>' + escapeHtml(p.brand) + '</small>' : ''}</div>
                    <span class="search-suggestion__price">Rs. ${Number(p.product_price).toLocaleString()}</span>
                </a>`;
            });
        }
        if (!html) {
            html = '<div class="search-suggestion" style="cursor:default;color:var(--color-text-muted)">No results found</div>';
        }
        container.innerHTML = html;
        container.classList.add('active');
    }

    async function fetchSearch(query) {
        const res = await fetch(BASE + 'api/search.php?q=' + encodeURIComponent(query));
        return res.json();
    }

    function initNavbarSearch() {
        const input = document.getElementById('navbar-search');
        const suggestions = document.getElementById('search-suggestions');
        if (!input || !suggestions) return;

        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            const q = input.value.trim();
            if (q.length < 2) {
                suggestions.classList.remove('active');
                suggestions.innerHTML = '';
                return;
            }
            timer = setTimeout(async () => {
                try {
                    const data = await fetchSearch(q);
                    renderSuggestions(data, suggestions);
                } catch { suggestions.classList.remove('active'); }
            }, 200);
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const q = input.value.trim();
                if (q) window.location.href = BASE + 'customer/shop.php?search=' + encodeURIComponent(q);
            }
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.navbar__search')) suggestions.classList.remove('active');
        });
    }

    function initShopLiveSearch() {
        const input = document.getElementById('shop-live-search');
        const grid = document.getElementById('shop-product-grid');
        if (!input || !grid) return;

        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            const q = input.value.trim();
            timer = setTimeout(async () => {
                if (q.length < 2) {
                    document.querySelectorAll('.shop-product-item').forEach(el => el.style.display = '');
                    return;
                }
                grid.classList.add('shop-live-loading');
                try {
                    const data = await fetchSearch(q);
                    const ids = new Set(data.products.map(p => String(p.product_id)));
                    document.querySelectorAll('.shop-product-item').forEach(el => {
                        el.style.display = ids.has(el.dataset.productId) ? '' : 'none';
                    });
                } finally {
                    grid.classList.remove('shop-live-loading');
                }
            }, 250);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initNavbarSearch();
        initShopLiveSearch();
    });
})();
