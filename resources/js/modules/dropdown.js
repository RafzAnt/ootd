// <div data-dropdown>
//   <button data-dropdown-trigger>…</button>
//   <div data-dropdown-panel hidden>…</div>
// </div>
const dropdowns = document.querySelectorAll('[data-dropdown]');

function setOpen(dropdown, open) {
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-dropdown-panel]');

    panel.hidden = !open;
    trigger.setAttribute('aria-expanded', String(open));
}

dropdowns.forEach((dropdown) => {
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-dropdown-panel]');

    trigger.addEventListener('click', () => {
        const opening = panel.hidden;
        dropdowns.forEach((other) => setOpen(other, false));
        setOpen(dropdown, opening);
    });
});

document.addEventListener('click', (event) => {
    dropdowns.forEach((dropdown) => {
        if (!dropdown.contains(event.target)) setOpen(dropdown, false);
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;

    dropdowns.forEach((dropdown) => {
        if (!dropdown.querySelector('[data-dropdown-panel]').hidden) {
            setOpen(dropdown, false);
            dropdown.querySelector('[data-dropdown-trigger]').focus();
        }
    });
});
