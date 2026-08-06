document.querySelectorAll('[data-media-picker]').forEach((picker) => {
    const input = picker.querySelector('[data-media-input]');
    const text = picker.querySelector('[data-media-text]');
    const path = picker.querySelector('[data-media-path]');
    const preview = picker.querySelector('.media-preview');
    const assetUrl = (value) => `/${value.replace(/^\/+/, '')}`;
    const select = (value) => {
        input.value = value;
        text.value = value;
        path.textContent = value || 'Choose from the media library below';
        preview.innerHTML = value ? `<img src="${assetUrl(value)}" alt="">` : '<span>No image selected</span>';
        picker.querySelectorAll('[data-media-value]').forEach((choice) => choice.classList.toggle('selected', choice.dataset.mediaValue === value));
    };
    picker.querySelectorAll('[data-media-value]').forEach((choice) => choice.addEventListener('click', () => select(choice.dataset.mediaValue)));
    picker.querySelector('[data-media-clear]')?.addEventListener('click', () => select(''));
    text?.addEventListener('input', () => select(text.value));
});

document.querySelectorAll('[data-gallery-picker]').forEach((picker) => {
    const inputs = picker.querySelector('[data-gallery-inputs]');
    const preview = picker.querySelector('[data-gallery-preview]');
    const values = new Set(Array.from(inputs.querySelectorAll('input')).map((input) => input.value).filter(Boolean));

    const render = () => {
        inputs.innerHTML = '';
        preview.innerHTML = '';
        if (!values.size) {
            preview.innerHTML = '<p class="empty-state">No gallery images selected.</p>';
        }
        values.forEach((value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'gallery_images[]';
            input.value = value;
            inputs.appendChild(input);

            const item = document.createElement('div');
            item.className = 'gallery-preview-item';
            item.innerHTML = `<img src="/${value.replace(/^\/+/, '')}" alt=""><button type="button" data-gallery-remove="${value}">Remove</button>`;
            preview.appendChild(item);
        });
        picker.querySelectorAll('[data-gallery-value]').forEach((choice) => choice.classList.toggle('selected', values.has(choice.dataset.galleryValue)));
    };

    picker.querySelectorAll('[data-gallery-value]').forEach((choice) => {
        choice.addEventListener('click', () => {
            const value = choice.dataset.galleryValue;
            values.has(value) ? values.delete(value) : values.add(value);
            render();
        });
    });
    preview.addEventListener('click', (event) => {
        const button = event.target.closest('[data-gallery-remove]');
        if (!button) return;
        values.delete(button.dataset.galleryRemove);
        render();
    });
});

document.querySelectorAll('[data-variant-builder]').forEach((builder) => {
    const type = builder.querySelector('[data-product-type]');
    const optionStore = builder.querySelector('[data-variant-options]');
    const attributeList = builder.querySelector('[data-attribute-list]');
    const attributeEntry = builder.querySelector('[data-attribute-name-entry]');
    const valueEntry = builder.querySelector('[data-option-value-entry]');
    const rows = builder.querySelector('[data-variant-rows]');
    const empty = builder.querySelector('[data-variant-empty]');
    const baseSku = document.querySelector('input[name="sku"]');
    const basePrice = document.querySelector('input[name="price"]');
    const baseDiscount = document.querySelector('input[name="discount_price"]');
    const baseStock = document.querySelector('input[name="stock"]');
    const samePricing = builder.querySelector('[data-same-pricing]');
    const variantTabs = builder.querySelectorAll('[data-product-tab="attributes"], [data-product-tab="variants"]');
    const attributes = new Map();

    const slug = (value) => value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').toUpperCase();
    const escapeAttr = (value) => String(value).replaceAll('&', '&amp;').replaceAll('"', '&quot;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
    const combinations = (groups) => groups.reduce((acc, group) => acc.flatMap((item) => group.values.map((value) => [...item, { name: group.name, value }])), [[]]);
    const groups = () => Array.from(attributes.entries()).map(([name, values]) => ({ name, values: Array.from(values) })).filter((group) => group.name && group.values.length);

    optionStore.querySelectorAll('[data-option-name-hidden]').forEach((nameInput, index) => {
        const name = nameInput.value.trim();
        const values = optionStore.querySelectorAll('[data-option-values-hidden]')[index]?.value.split(',').map((value) => value.trim()).filter(Boolean) || [];
        if (name && values.length) attributes.set(name, new Set(values));
    });

    const renderAttributes = () => {
        optionStore.innerHTML = '';
        attributeList.innerHTML = '';
        groups().forEach((group, index) => {
            optionStore.insertAdjacentHTML('beforeend', `<input type="hidden" name="variant_options[${index}][name]" value="${escapeAttr(group.name)}" data-option-name-hidden><input type="hidden" name="variant_options[${index}][values]" value="${escapeAttr(group.values.join(', '))}" data-option-values-hidden>`);
            const card = document.createElement('div');
            card.className = 'attribute-card';
            card.innerHTML = `<div><b>${escapeAttr(group.name)}</b><small>${group.values.length} value(s)</small></div><div class="variant-value-list">${group.values.map((value) => `<div class="variant-value-item" data-attribute="${escapeAttr(group.name)}"><input value="${escapeAttr(value)}" data-option-value readonly><button type="button" title="Edit value" data-edit-value>Edit</button><button type="button" title="Remove value" data-remove-value>Remove</button></div>`).join('')}</div>`;
            attributeList.appendChild(card);
        });
    };

    const splitValues = (value) => value.split(/[,|]/).map((item) => item.trim()).filter(Boolean);
    const addValue = () => {
        const name = attributeEntry.value.trim();
        const values = splitValues(valueEntry.value);
        if (!name || !values.length) return;
        if (!attributes.has(name)) attributes.set(name, new Set());
        values.forEach((value) => attributes.get(name).add(value));
        attributeEntry.value = '';
        valueEntry.value = '';
        renderAttributes();
        attributeEntry.focus();
    };

    const toggle = () => {
        const variable = type.value === 'variable';
        variantTabs.forEach((tab) => tab.hidden = !variable);
        if (!variable && builder.querySelector('[data-product-tab].active')?.hidden) {
            builder.querySelector('[data-product-tab="general"]')?.click();
        }
    };

    type.addEventListener('change', toggle);
    toggle();

    builder.querySelectorAll('[data-product-tab]').forEach((tab) => {
        tab.addEventListener('click', () => {
            if (tab.hidden) return;
            builder.querySelectorAll('[data-product-tab]').forEach((item) => item.classList.toggle('active', item === tab));
            builder.querySelectorAll('[data-product-panel]').forEach((panel) => panel.classList.toggle('active', panel.dataset.productPanel === tab.dataset.productTab));
        });
    });

    builder.querySelector('[data-add-value]')?.addEventListener('click', addValue);
    [attributeEntry, valueEntry].forEach((input) => input?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        addValue();
    }));

    attributeList.addEventListener('click', (event) => {
        const item = event.target.closest('.variant-value-item');
        if (!item) return;
        const attribute = item.dataset.attribute;
        const input = item.querySelector('[data-option-value]');
        const editButton = event.target.closest('[data-edit-value]');
        if (editButton) {
            if (!input.readOnly) {
                const values = attributes.get(attribute) || new Set();
                values.delete(item.dataset.originalValue || input.defaultValue);
                if (input.value.trim()) values.add(input.value.trim());
                attributes.set(attribute, values);
                renderAttributes();
                return;
            }
            item.dataset.originalValue = input.value;
            input.readOnly = false;
            editButton.textContent = 'Save';
            input.focus();
            return;
        }
        if (event.target.closest('[data-remove-value]')) {
            const values = attributes.get(attribute);
            values?.delete(input.value.trim());
            if (!values || !values.size) attributes.delete(attribute);
            renderAttributes();
        }
    });

    builder.querySelector('[data-generate-variants]')?.addEventListener('click', () => {
        const currentGroups = groups();
        if (!currentGroups.length) return;
        rows.innerHTML = '';
        combinations(currentGroups).forEach((combo, index) => {
            const optionsObject = Object.fromEntries(combo.map((item) => [item.name, item.value]));
            const label = combo.map((item) => item.value).join(' / ');
            const sku = [baseSku?.value || 'VAR', ...combo.map((item) => slug(item.value))].filter(Boolean).join('-');
            const row = document.createElement('tr');
            row.innerHTML = `<td><input name="variants[${index}][name]" value="${escapeAttr(label)}" required><input type="hidden" name="variants[${index}][options]" value="${escapeAttr(JSON.stringify(optionsObject))}"></td><td><input name="variants[${index}][sku]" value="${escapeAttr(sku)}" required></td><td><input type="number" min="0" name="variants[${index}][price]" value="${escapeAttr(basePrice?.value || 0)}" required data-variant-price></td><td><input type="number" min="0" name="variants[${index}][discount_price]" value="${escapeAttr(baseDiscount?.value || '')}" data-variant-discount></td><td><input type="number" min="0" name="variants[${index}][stock]" value="${escapeAttr(baseStock?.value || 0)}" required></td><td><button type="button" class="text-danger" data-remove-variant>Remove</button></td>`;
            rows.appendChild(row);
        });
        applySamePricing();
        renderEmpty();
    });

    const applySamePricing = () => {
        const locked = samePricing?.checked;
        rows.querySelectorAll('[data-variant-price]').forEach((input) => {
            if (locked) input.value = basePrice?.value || 0;
            input.readOnly = !!locked;
        });
        rows.querySelectorAll('[data-variant-discount]').forEach((input) => {
            if (locked) input.value = baseDiscount?.value || '';
            input.readOnly = !!locked;
        });
    };

    samePricing?.addEventListener('change', applySamePricing);
    basePrice?.addEventListener('input', applySamePricing);
    baseDiscount?.addEventListener('input', applySamePricing);

    rows.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove-variant]');
        if (!button) return;
        button.closest('tr')?.remove();
        renderEmpty();
    });
    renderAttributes();
    renderEmpty();
    applySamePricing();
});
