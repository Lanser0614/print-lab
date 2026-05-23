import './bootstrap';

const isStandalone = () => window.matchMedia('(display-mode: standalone)').matches
    || window.navigator.standalone === true;

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((error) => {
            if (import.meta.env.DEV) {
                console.warn('PrintLab service worker registration failed', error);
            }
        });
    });
}

window.addEventListener('DOMContentLoaded', () => {
    const prompt = document.querySelector('[data-pwa-install-prompt]');

    if (!prompt || isStandalone()) {
        return;
    }

    const dismissedAt = Number.parseInt(localStorage.getItem('printlab-pwa-install-dismissed-at') || '0', 10);
    const dismissedRecently = dismissedAt && Date.now() - dismissedAt < 1000 * 60 * 60 * 24 * 14;

    if (dismissedRecently) {
        return;
    }

    const action = prompt.querySelector('[data-pwa-install-action]');
    const dismiss = prompt.querySelector('[data-pwa-install-dismiss]');
    const text = prompt.querySelector('[data-pwa-install-text]');
    let deferredPrompt = null;

    const showPrompt = () => {
        prompt.hidden = false;
    };

    const hidePrompt = () => {
        prompt.hidden = true;
        localStorage.setItem('printlab-pwa-install-dismissed-at', Date.now().toString());
    };

    dismiss?.addEventListener('click', hidePrompt);

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;
        showPrompt();
    });

    action?.addEventListener('click', async () => {
        if (!deferredPrompt) {
            return;
        }

        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
        hidePrompt();
    });

    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    const isSafari = /^((?!chrome|android|crios|fxios).)*safari/i.test(window.navigator.userAgent);

    if (isIos && isSafari) {
        action?.setAttribute('hidden', 'hidden');
        if (text) {
            text.textContent = 'Safari: Share -> Add to Home Screen';
        }
        window.setTimeout(showPrompt, 1200);
    }
});
