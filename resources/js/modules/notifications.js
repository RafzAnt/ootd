// Keeps the bell fresh: checks for new notifications every so often while the tab is visible.
const bell = document.querySelector('[data-bell]');
const INTERVAL = 45_000;

if (bell) {
    const badge = bell.querySelector('[data-bell-badge]');
    const list = bell.querySelector('[data-bell-list]');
    let unread = Number(bell.dataset.unread);
    let timer;

    const render = (count) => {
        badge.textContent = count > 9 ? '9+' : count;
        badge.classList.toggle('hidden', count === 0);
    };

    const refresh = async () => {
        try {
            const response = await fetch(bell.dataset.summaryUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            // Logged out or session expired: stop asking.
            if (!response.ok) return stop();

            const data = await response.json();

            if (data.unread > unread) window.toast?.('You have a new notification.');

            unread = data.unread;
            render(unread);
            list.innerHTML = data.html;
        } catch {
            // Offline or server hiccup; try again next time.
        }
    };

    const start = () => {
        stop();
        timer = setInterval(refresh, INTERVAL);
    };

    const stop = () => clearInterval(timer);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) return stop();
        refresh();
        start();
    });

    start();
}
