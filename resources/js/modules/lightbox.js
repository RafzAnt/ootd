// <a href="/photo.jpg" data-lightbox data-caption="Optional text">…</a> opens the photo full size.
const dialog = document.getElementById('lightbox');

if (dialog) {
    const image = dialog.querySelector('[data-lightbox-image]');
    const caption = dialog.querySelector('[data-lightbox-caption]');

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-lightbox]');
        if (!link) return;

        event.preventDefault();
        image.src = link.href;
        image.alt = link.dataset.caption || '';
        caption.textContent = link.dataset.caption || '';
        caption.hidden = !link.dataset.caption;
        dialog.showModal();
    });

    // Click outside the photo (or on the close button) to close
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog || event.target.closest('[data-lightbox-close]')) dialog.close();
    });
}
