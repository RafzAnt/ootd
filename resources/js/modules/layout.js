// Mobile sidebar
const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');

function setSidebar(open) {
    if (!sidebar || !backdrop) return;
    sidebar.classList.toggle('-translate-x-full', !open);
    backdrop.classList.toggle('hidden', !open);
}

document.querySelectorAll('[data-sidebar-toggle]').forEach((el) => {
    el.addEventListener('click', () => setSidebar(sidebar.classList.contains('-translate-x-full')));
});

// Theme (light / dark / system), remembered in this browser
const applyTheme = (theme) => {
    const dark = theme === 'dark' || (theme !== 'light' && matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
};

document.querySelectorAll('input[name="theme"]').forEach((input) => {
    input.checked = input.value === (localStorage.getItem('theme') || 'system');
    input.addEventListener('change', () => {
        localStorage.setItem('theme', input.value);
        applyTheme(input.value);
    });
});

matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    applyTheme(localStorage.getItem('theme') || 'system');
});

// Confirm before destructive actions: <form data-confirm="Are you sure?">
document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;
    if (message && !confirm(message)) event.preventDefault();
});

// Filter forms that apply as soon as a select / checkbox changes: <form data-autosubmit>
document.querySelectorAll('form[data-autosubmit]').forEach((form) => {
    form.addEventListener('change', (event) => {
        if (event.target.matches('select, input[type="checkbox"]')) form.requestSubmit();
    });
});
