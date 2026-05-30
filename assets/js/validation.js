/**
 * ByteStore Form Validation
 */
(function () {
    'use strict';

    const rules = {
        name: { test: v => v.trim().length >= 2 && /^[a-zA-Z\s.\'-]+$/.test(v.trim()), message: 'Enter a valid name (min 2 characters)' },
        email: { test: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()), message: 'Enter a valid email address' },
        phone: { test: v => /^[0-9+\-\s()]{7,20}$/.test(v.trim()), message: 'Enter a valid phone number' },
        password: { test: v => v.length >= 6, message: 'Password must be at least 6 characters' },
        confirm_password: { test: (v, form) => v === form.querySelector('[name="password"]')?.value, message: 'Passwords do not match' },
        address: { test: v => v.trim().length >= 5, message: 'Enter a complete address' },
        quantity: { test: v => parseInt(v) >= 1, message: 'Quantity must be at least 1' }
    };

    function validateField(input) {
        const ruleName = input.dataset.validate || input.name;
        const rule = rules[ruleName];
        if (!rule) return true;

        const form = input.closest('form');
        const valid = rule.test(input.value, form);
        input.classList.toggle('is-valid', valid && input.value.trim() !== '');
        input.classList.toggle('is-invalid', !valid && input.value.trim() !== '');

        let errorEl = input.parentElement.querySelector('.form-error');
        if (!valid && input.value.trim() !== '') {
            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.className = 'form-error';
                input.parentElement.appendChild(errorEl);
            }
            errorEl.textContent = rule.message;
        } else if (errorEl) {
            errorEl.remove();
        }
        return valid;
    }

    function initValidation() {
        document.querySelectorAll('[data-validate], .auth-container input, .checkout-form input, .profile-form input').forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) validateField(input);
            });
        });

        document.querySelectorAll('form[data-validate-form]').forEach(form => {
            form.addEventListener('submit', (e) => {
                let valid = true;
                form.querySelectorAll('[data-validate], [name="email"], [name="password"], [name="customer_name"], [name="customer_phone"], [name="customer_address"]').forEach(input => {
                    if (input.name && !validateField(input)) valid = false;
                });
                if (!valid) {
                    e.preventDefault();
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) firstInvalid.focus();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initValidation);
})();
