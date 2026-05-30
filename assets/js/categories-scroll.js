/**
 * Horizontal category scroller — wheel + fade indicators
 */
(function () {
    'use strict';

    function initScroller(wrap) {
        const el = wrap.querySelector('.categories-scroll');
        if (!el || el.dataset.scrollReady === '1') return;
        el.dataset.scrollReady = '1';

        const fadeL = wrap.querySelector('.categories-scroll-fade--left');
        const fadeR = wrap.querySelector('.categories-scroll-fade--right');

        function updateFades() {
            const max = el.scrollWidth - el.clientWidth;
            if (max <= 2) {
                fadeL?.classList.remove('visible');
                fadeR?.classList.remove('visible');
                return;
            }
            fadeL?.classList.toggle('visible', el.scrollLeft > 8);
            fadeR?.classList.toggle('visible', el.scrollLeft < max - 8);
        }

        el.addEventListener('scroll', updateFades, { passive: true });
        el.addEventListener('wheel', (e) => {
            if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;
            e.preventDefault();
            el.scrollLeft += e.deltaY;
            updateFades();
        }, { passive: false });

        updateFades();
        window.addEventListener('resize', updateFades);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.categories-scroll-wrap').forEach(initScroller);
    });
})();
