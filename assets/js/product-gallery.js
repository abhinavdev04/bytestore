/**
 * ByteStore Product Gallery Zoom
 */
(function () {
    'use strict';

    function initGallery() {
        const main = document.getElementById('gallery-main');
        const img = document.getElementById('main-image');
        if (!main || !img) return;

        let scale = 1;
        let isDragging = false;
        let startX = 0, startY = 0, translateX = 0, translateY = 0;

        function applyTransform() {
            img.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
        }

        function resetZoom() {
            scale = 1;
            translateX = 0;
            translateY = 0;
            main.classList.remove('is-zoomed');
            img.style.cursor = 'zoom-in';
            applyTransform();
        }

        main.addEventListener('click', (e) => {
            if (e.target.closest('.product-gallery__thumb')) return;
            if (scale === 1) {
                scale = 2;
                main.classList.add('is-zoomed');
                img.style.cursor = 'zoom-out';
            } else {
                resetZoom();
            }
            applyTransform();
        });

        main.addEventListener('wheel', (e) => {
            if (!main.classList.contains('is-zoomed') && scale === 1) return;
            e.preventDefault();
            scale = clamp(scale + (e.deltaY < 0 ? 0.15 : -0.15), 1, 4);
            if (scale === 1) resetZoom();
            else applyTransform();
        }, { passive: false });

        main.addEventListener('mousedown', (e) => {
            if (scale <= 1) return;
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            img.style.cursor = 'grabbing';
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            applyTransform();
        });

        window.addEventListener('mouseup', () => {
            isDragging = false;
            if (scale > 1) img.style.cursor = 'grab';
        });

        let pinchStart = 0;
        main.addEventListener('touchstart', (e) => {
            if (e.touches.length === 2) {
                pinchStart = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
            }
        }, { passive: true });

        main.addEventListener('touchmove', (e) => {
            if (e.touches.length !== 2) return;
            e.preventDefault();
            const dist = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
            );
            scale = clamp(scale + (dist - pinchStart) * 0.01, 1, 4);
            pinchStart = dist;
            if (scale > 1) main.classList.add('is-zoomed');
            applyTransform();
        }, { passive: false });

        function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

        window.setMainImage = function (src, thumb) {
            resetZoom();
            img.src = src;
            document.querySelectorAll('.product-gallery__thumb').forEach(t => t.classList.remove('active'));
            if (thumb) thumb.classList.add('active');
        };
    }

    document.addEventListener('DOMContentLoaded', initGallery);
})();
