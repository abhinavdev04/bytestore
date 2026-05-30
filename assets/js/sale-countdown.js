/**
 * Sale countdown timers
 */
(function () {
    'use strict';

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const now = Math.floor(Date.now() / 1000);
        document.querySelectorAll('.sale-countdown[data-sale-end]').forEach((el) => {
            const end = parseInt(el.dataset.saleEnd, 10);
            const timer = el.querySelector('.sale-countdown__timer');
            if (!timer || !end) return;
            const diff = end - now;
            if (diff <= 0) {
                timer.textContent = 'Sale ended';
                el.style.display = 'none';
                return;
            }
            el.style.display = '';
            const d = Math.floor(diff / 86400);
            const h = Math.floor((diff % 86400) / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            timer.textContent = d > 0
                ? d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s)
                : pad(h) + ':' + pad(m) + ':' + pad(s);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        tick();
        setInterval(tick, 1000);
    });
})();
