/**
 * ByteStore Custom Modal System
 */
(function () {
    'use strict';

    let resolvePromise = null;

    function getEls() {
        return {
            overlay: document.getElementById('bs-modal-overlay'),
            title: document.getElementById('bs-modal-title'),
            body: document.getElementById('bs-modal-body'),
            footer: document.getElementById('bs-modal-footer'),
            cancel: document.getElementById('bs-modal-cancel'),
            confirm: document.getElementById('bs-modal-confirm'),
            close: document.getElementById('bs-modal-close'),
        };
    }

    function closeModal(result) {
        const { overlay } = getEls();
        if (!overlay) return;
        overlay.classList.remove('bs-modal-overlay--visible');
        overlay.setAttribute('hidden', '');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        if (resolvePromise) {
            resolvePromise(result);
            resolvePromise = null;
        }
    }

    function openModal(options) {
        const els = getEls();
        if (!els.overlay) return Promise.resolve(false);

        els.title.textContent = options.title || 'Confirm';
        els.body.innerHTML = options.message || '';
        els.confirm.textContent = options.confirmText || 'Confirm';
        els.cancel.textContent = options.cancelText || 'Cancel';
        els.confirm.className = 'btn ' + (options.confirmClass || 'btn--primary');
        els.cancel.style.display = options.hideCancel ? 'none' : '';

        els.overlay.removeAttribute('hidden');
        els.overlay.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => els.overlay.classList.add('bs-modal-overlay--visible'));
        document.body.classList.add('modal-open');

        return new Promise((resolve) => {
            resolvePromise = resolve;

            const onConfirm = () => { cleanup(); closeModal(true); };
            const onCancel = () => { cleanup(); closeModal(false); };

            function cleanup() {
                els.confirm.removeEventListener('click', onConfirm);
                els.cancel.removeEventListener('click', onCancel);
                els.close.removeEventListener('click', onCancel);
                els.overlay.removeEventListener('click', onOverlay);
            }

            function onOverlay(e) {
                if (e.target === els.overlay) onCancel();
            }

            els.confirm.addEventListener('click', onConfirm);
            els.cancel.addEventListener('click', onCancel);
            els.close.addEventListener('click', onCancel);
            els.overlay.addEventListener('click', onOverlay);
        });
    }

    window.bsConfirm = function (message, options = {}) {
        return openModal({ ...options, message, title: options.title || 'Please Confirm' });
    };

    window.bsAlert = function (message, options = {}) {
        return openModal({
            ...options,
            message,
            title: options.title || 'Notice',
            hideCancel: true,
            confirmText: options.confirmText || 'OK',
        });
    };

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-confirm]');
        if (!trigger) return;
        e.preventDefault();
        const msg = trigger.getAttribute('data-confirm') || 'Are you sure?';
        const title = trigger.getAttribute('data-confirm-title') || 'Confirm Action';
        bsConfirm(msg, { title }).then((ok) => {
            if (ok) {
                if (trigger.tagName === 'A' && trigger.href) {
                    window.location.href = trigger.href;
                } else if (trigger.form) {
                    trigger.form.submit();
                } else if (trigger.dataset.href) {
                    window.location.href = trigger.dataset.href;
                }
            }
        });
    });

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form.hasAttribute('data-confirm-submit')) return;
        e.preventDefault();
        const msg = form.getAttribute('data-confirm-submit') || 'Are you sure?';
        bsConfirm(msg, { title: form.getAttribute('data-confirm-title') || 'Confirm' }).then((ok) => {
            if (ok) {
                form.removeAttribute('data-confirm-submit');
                form.submit();
            }
        });
    });
})();
