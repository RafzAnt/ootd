const container = document.getElementById('toasts');
const template = document.getElementById('toast-template');

const DURATION = 4500;

function dismiss(toast) {
    toast.style.transition = 'opacity 0.2s, transform 0.2s';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(0.25rem)';
    setTimeout(() => toast.remove(), 200);
}

function arm(toast) {
    let timer = setTimeout(() => dismiss(toast), DURATION);

    toast.addEventListener('mouseenter', () => clearTimeout(timer));
    toast.addEventListener('mouseleave', () => (timer = setTimeout(() => dismiss(toast), DURATION / 2)));
    toast.querySelector('[data-toast-close]')?.addEventListener('click', () => dismiss(toast));
}

// Toasts the server rendered (flash messages)
container?.querySelectorAll('[data-toast]').forEach(arm);

// For other scripts: window.toast('Saved')
window.toast = (message) => {
    if (!container || !template) return;

    const toast = template.content.firstElementChild.cloneNode(true);
    toast.querySelector('[data-toast-message]').textContent = message;
    container.append(toast);
    arm(toast);
};
