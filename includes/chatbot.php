<!-- ByteStore Support Chatbot -->
<div id="chatbot-widget" class="chatbot-widget" aria-live="polite">
    <button id="chatbot-toggle" class="chatbot-toggle" aria-label="Open support chat" aria-expanded="false">
        <svg class="chatbot-toggle__icon chatbot-toggle__icon--open" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
        </svg>
        <svg class="chatbot-toggle__icon chatbot-toggle__icon--close" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
        </svg>
    </button>
    <div id="chatbot-panel" class="chatbot-panel" hidden>
        <div class="chatbot-header">
            <div class="chatbot-header__info">
                <div class="chatbot-header__avatar">BS</div>
                <div>
                    <strong>ByteStore Support</strong>
                    <span class="chatbot-header__status">Online</span>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-header__close" aria-label="Close chat" type="button"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="chatbot-messages" class="chatbot-messages"></div>
        <div id="chatbot-quick-actions" class="chatbot-quick-actions"></div>
        <form id="chatbot-form" class="chatbot-form">
            <input type="text" id="chatbot-input" placeholder="Type your question..." autocomplete="off" aria-label="Chat message">
            <button type="submit" aria-label="Send message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </form>
    </div>
</div>
