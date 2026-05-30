/**
 * ByteStore Rule-Based Chatbot
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'bytestore_chat_history';

    const QUICK_ACTIONS = [
        { label: 'Track Order', query: 'track my order' },
        { label: 'Return Policy', query: 'return policy' },
        { label: 'Shipping Info', query: 'shipping information' },
        { label: 'Payment Methods', query: 'payment methods' },
        { label: 'Contact Support', query: 'contact support' },
        { label: 'Browse Products', query: 'product categories' }
    ];

    const RESPONSES = [
        { keywords: ['track', 'order status', 'where is my order', 'order tracking'],
          answer: 'To track your order, log in and visit **Order History** in your dashboard. Status flow: Pending, Processing, Shipped, Delivered. Pending or Processing orders can be cancelled from your cart page.' },
        { keywords: ['shipping', 'delivery', 'how long', 'ship'],
          answer: '**Shipping:** Kathmandu Valley 1-2 days, major cities 2-4 days, remote areas 5-7 days. Free shipping above Rs. 50,000 in Kathmandu Valley.' },
        { keywords: ['return', 'refund', 'exchange'],
          answer: '**Returns:** 7-day window for unopened items, 14-day exchange for defective products. Refunds in 5-7 business days. See our Return Policy page.' },
        { keywords: ['warranty', 'guarantee'],
          answer: 'All products include official manufacturer warranty (typically 1-2 years). Keep your invoice for claims.' },
        { keywords: ['payment', 'pay', 'esewa', 'cod', 'cash'],
          answer: '**Payments:** Cash on Delivery nationwide, eSewa online payments, bank transfer for orders above Rs. 100,000.' },
        { keywords: ['contact', 'support', 'help', 'phone', 'email'],
          answer: '**Contact:** Times Square Mall, Durbar Marg, Kathmandu. Tel: +977-980-355-8024. Email: support@bytestore.com.np. Hours: Sun-Fri 10AM-7PM.' },
        { keywords: ['account', 'login', 'register', 'password', 'profile'],
          answer: 'Register on our site, manage profile and orders from **Account Dashboard**. Demo account password equals your email.' },
        { keywords: ['category', 'categories', 'browse', 'products', 'shop', 'laptop', 'phone', 'gaming'],
          answer: 'Browse Laptops, Phones, Gaming, Audio, Monitors, Storage, Processors, Networking, Cameras and more from the Shop page.' },
        { keywords: ['wishlist', 'save', 'favorite'],
          answer: 'Click the heart icon on product cards to save items. Access your Wishlist from the account menu.' },
        { keywords: ['compare', 'comparison'],
          answer: 'Use the compare icon to add up to 4 products for side-by-side comparison on the Compare page.' },
        { keywords: ['hello', 'hi', 'hey', 'good morning', 'good evening'],
          answer: 'Hello! Welcome to ByteStore. How can I help you today?' },
        { keywords: ['faq', 'frequently asked'],
          answer: 'Visit our FAQ page for common questions, or ask me here.' },
        { keywords: ['dark mode', 'theme', 'light mode'],
          answer: 'Toggle Light/Dark mode using the theme icon in the navigation bar. Your preference is saved automatically.' },
        { keywords: ['price', 'discount', 'offer', 'sale'],
          answer: 'Look for sale badges on product cards. Subscribe to our newsletter for exclusive deals.' }
    ];

    const DEFAULT_RESPONSE = 'I can help with order tracking, shipping, returns, payments, product categories, and account help. Try a quick action below.';

    function getHistory() {
        try { return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || []; }
        catch { return []; }
    }

    function saveHistory(history) {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(history.slice(-50)));
    }

    function findResponse(input) {
        const lower = input.toLowerCase();
        for (const item of RESPONSES) {
            if (item.keywords.some(kw => lower.includes(kw))) return item.answer;
        }
        return DEFAULT_RESPONSE;
    }

    function formatMarkdown(text) {
        return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    }

    function addMessage(container, text, type) {
        const msg = document.createElement('div');
        msg.className = `chat-msg chat-msg--${type}`;
        if (type === 'bot') msg.innerHTML = formatMarkdown(text);
        else msg.textContent = text;
        container.appendChild(msg);
        container.scrollTop = container.scrollHeight;
    }

    function showTyping(container) {
        const typing = document.createElement('div');
        typing.className = 'chat-msg chat-msg--bot chat-msg--typing';
        typing.innerHTML = '<span></span><span></span><span></span>';
        typing.id = 'chat-typing';
        container.appendChild(typing);
        container.scrollTop = container.scrollHeight;
        return typing;
    }

    function renderQuickActions(container) {
        container.innerHTML = '';
        QUICK_ACTIONS.forEach(action => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chatbot-quick-btn';
            btn.textContent = action.label;
            btn.addEventListener('click', () => handleUserMessage(action.query));
            container.appendChild(btn);
        });
    }

    function handleUserMessage(text) {
        const messages = document.getElementById('chatbot-messages');
        if (!messages || !text.trim()) return;

        addMessage(messages, text.trim(), 'user');
        const history = getHistory();
        history.push({ role: 'user', text: text.trim() });

        const typing = showTyping(messages);
        setTimeout(() => {
            typing.remove();
            const response = findResponse(text);
            addMessage(messages, response, 'bot');
            history.push({ role: 'bot', text: response });
            saveHistory(history);
        }, 600 + Math.random() * 400);
    }

    function initChatbot() {
        const widget = document.getElementById('chatbot-widget');
        const toggle = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close');
        const panel = document.getElementById('chatbot-panel');
        const form = document.getElementById('chatbot-form');
        const input = document.getElementById('chatbot-input');
        const messages = document.getElementById('chatbot-messages');
        const quickActions = document.getElementById('chatbot-quick-actions');

        if (!widget || !toggle || !panel) return;

        function openChat() {
            widget.classList.add('open');
            panel.removeAttribute('hidden');
            toggle.setAttribute('aria-expanded', 'true');
            if (input) setTimeout(() => input.focus(), 280);
        }

        function closeChat(e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }
            widget.classList.remove('open');
            panel.setAttribute('hidden', '');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            widget.classList.contains('open') ? closeChat(e) : openChat();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeChat);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && widget.classList.contains('open')) closeChat(e);
        });

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                handleUserMessage(input.value);
                input.value = '';
            });
        }

        if (quickActions) renderQuickActions(quickActions);

        const history = getHistory();
        if (history.length === 0) {
            setTimeout(() => {
                addMessage(messages, 'Welcome to ByteStore! I am your virtual assistant. Ask about orders, shipping, returns, or payments.', 'bot');
            }, 400);
        } else {
            history.forEach(msg => addMessage(messages, msg.text, msg.role === 'user' ? 'user' : 'bot'));
        }
    }

    document.addEventListener('DOMContentLoaded', initChatbot);
})();
