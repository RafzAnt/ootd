// <form data-favorite> toggles a favorite without reloading the page.
document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-favorite]');
    if (!form) return;

    event.preventDefault();

    const button = form.querySelector('button');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: 'same-origin',
        });

        if (!response.ok) throw new Error();

        const { favorite } = await response.json();

        button.setAttribute('aria-pressed', String(favorite));
        button.setAttribute('aria-label', favorite ? 'Remove from favorites' : 'Add to favorites');
        button.querySelector('svg').classList.toggle('fill-current', favorite);
        window.toast?.(favorite ? 'Added to favorites.' : 'Removed from favorites.');
    } catch {
        // Something went wrong (e.g. session expired): fall back to a normal page submit.
        form.submit();
    }
});
