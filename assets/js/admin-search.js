/**
 * Admin product management live search
 */
(function () {
    'use strict';

    function initAdminSearch() {
        const input = document.getElementById('admin-product-search');
        if (!input) return;

        const rows = document.querySelectorAll('[data-admin-product-row]');
        if (!rows.length) return;

        input.addEventListener('input', () => {
            const q = input.value.trim().toLowerCase();
            rows.forEach(row => {
                const name = (row.dataset.name || '').toLowerCase();
                const brand = (row.dataset.brand || '').toLowerCase();
                const category = (row.dataset.category || '').toLowerCase();
                const match = !q || name.includes(q) || brand.includes(q) || category.includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initAdminSearch);
})();
