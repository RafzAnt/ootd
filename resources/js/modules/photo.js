// Photo picker: shrink big phone photos in the browser, then show a preview.
// (PHP's default upload limit is small, and a 1600px photo is plenty for an outfit.)
const MAX_SIDE = 1600;

async function shrink(file) {
    if (!/^image\/(jpeg|png|webp)$/.test(file.type)) return file;

    try {
        const bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
        const scale = Math.min(1, MAX_SIDE / Math.max(bitmap.width, bitmap.height));

        if (scale === 1 && file.size < 1.5 * 1024 * 1024) return file;

        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.85));
        if (!blob || blob.size >= file.size) return file;

        return new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' });
    } catch {
        return file;
    }
}

document.querySelectorAll('[data-photo-input]').forEach((input) => {
    const preview = document.querySelector(input.dataset.photoInput);

    input.addEventListener('change', async () => {
        const original = input.files[0];
        if (!original) return;

        const file = await shrink(original);
        if (file !== original) {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
        }

        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            preview.nextElementSibling?.classList.add('hidden');
        }
    });
});
