const renderFilePreviews = (input, preview) => {
    preview.innerHTML = '';
    const files = Array.from(input.files || []);
    preview.hidden = files.length === 0;
    files.forEach((file) => {
        const item = document.createElement('div');
        item.className = 'upload-preview-item';
        if (file.type.startsWith('image/')) {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });
            item.appendChild(image);
        } else {
            const tile = document.createElement('span');
            tile.className = 'file-tile';
            tile.textContent = file.name.split('.').pop()?.toUpperCase() || 'FILE';
            item.appendChild(tile);
        }
        const name = document.createElement('small');
        name.textContent = file.name;
        item.appendChild(name);
        preview.appendChild(item);
    });
};

document.querySelectorAll('[data-media-upload-input]').forEach((input) => {
    const preview = input.closest('form')?.querySelector('[data-media-upload-preview]');
    if (preview) input.addEventListener('change', () => renderFilePreviews(input, preview));
});

document.querySelectorAll('[data-featured-upload-input]').forEach((input) => {
    const preview = input.closest('form')?.querySelector('[data-featured-upload-preview]');
    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file || !preview) return;
        const url = URL.createObjectURL(file);
        preview.innerHTML = `<img src="${url}" alt="Selected featured image">`;
        preview.querySelector('img')?.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });
    });
});

document.querySelectorAll('[data-gallery-upload-input]').forEach((input) => {
    const preview = input.closest('form')?.querySelector('[data-gallery-upload-preview]');
    if (preview) input.addEventListener('change', () => renderFilePreviews(input, preview));
});
