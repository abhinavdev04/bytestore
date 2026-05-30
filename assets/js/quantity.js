/**
 * ByteStore Quantity Stepper Controls
 */
(function () {
    'use strict';

    function initSteppers() {
        document.querySelectorAll('.qty-stepper').forEach((wrap) => {
            if (wrap.dataset.initialized) return;
            wrap.dataset.initialized = '1';

            const input = wrap.querySelector('.qty-stepper__input');
            const btnMinus = wrap.querySelector('.qty-stepper__minus');
            const btnPlus = wrap.querySelector('.qty-stepper__plus');
            if (!input) return;

            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || 9999;

            function clamp(val) {
                return Math.max(min, Math.min(max, val));
            }

            function update(val) {
                input.value = clamp(val);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }

            btnMinus?.addEventListener('click', () => update(parseInt(input.value) - 1));
            btnPlus?.addEventListener('click', () => update(parseInt(input.value) + 1));
            input.addEventListener('blur', () => update(parseInt(input.value) || min));
        });
    }

    document.addEventListener('DOMContentLoaded', initSteppers);
    window.initQtySteppers = initSteppers;
})();
