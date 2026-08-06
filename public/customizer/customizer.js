let schema;
let page;
let sections = [];
let activeId = null;
let dirty = false;
let sortable = null;
let openRepeaterItems = new Set();

const sectionList = document.querySelector('#sectionList');
const fields = document.querySelector('#fields');
const activeName = document.querySelector('#activeName');
const saveState = document.querySelector('#saveState');
const saveButton = document.querySelector('#saveButton');
let preview = document.querySelector('#preview');
const editPanel = document.querySelector('#editPanel');
const closePanel = document.querySelector('#closePanel');
const addSectionButton = document.querySelector('#addSectionButton');
const addSectionMenu = document.querySelector('#addSectionMenu');
const shell = document.querySelector('.customizer-shell');
const endpoints = {
  schema: shell?.dataset.schemaUrl || 'section-schemas.php',
  template: shell?.dataset.templateUrl || 'page.json',
  save: shell?.dataset.saveUrl || 'save-page.php',
  upload: shell?.dataset.uploadUrl || 'upload-image.php',
  preview: shell?.dataset.previewUrl || 'index.php'
};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

const cloneDefault = (definition) => {
  if (definition.type === 'array') return [];
  if (definition.type === 'textarea' || definition.type === 'text' || definition.type === 'image') return '';
  return '';
};

const makeDefaultItem = (itemSchema) => {
  const item = {};
  Object.entries(itemSchema).forEach(([key, definition]) => {
    item[key] = cloneDefault(definition);
  });
  return item;
};

const cloneValue = (value) => JSON.parse(JSON.stringify(value));

function defaultDataForDefinition(definition) {
  const data = definition.presets ? cloneValue(definition.presets) : {};
  Object.entries(definition.schema).forEach(([key, field]) => {
    if (key in data) return;
    if (field.type === 'array') {
      data[key] = [makeDefaultItem(field.item)];
      return;
    }
    data[key] = cloneDefault(field);
  });
  return data;
}

const setDirty = () => {
  dirty = true;
  saveState.textContent = 'Unsaved changes';
};

async function refreshPreview() {
  try {
    const response = await fetch(`${endpoints.preview}?preview_ts=${Date.now()}`, {
      cache: 'no-store',
      credentials: 'same-origin',
      headers: { 'Accept': 'text/html' }
    });
    if (!response.ok) throw new Error(`Preview failed (${response.status})`);
    const html = await response.text();
    swapPreview(html);
  } catch (error) {
    console.error(error);
  }
}

function swapPreview(html) {
  const currentPreview = preview;
  const nextPreview = currentPreview.cloneNode(false);
  nextPreview.removeAttribute('src');
  nextPreview.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;visibility:hidden;';
  currentPreview.parentNode.appendChild(nextPreview);

  nextPreview.addEventListener('load', () => {
    nextPreview.removeAttribute('style');
    currentPreview.replaceWith(nextPreview);
    preview = nextPreview;
  }, { once: true });

  nextPreview.srcdoc = html;
}

const pathGet = (root, path) => path.reduce((value, key) => value?.[key], root);
const pathSet = (root, path, value) => {
  let pointer = root;
  path.slice(0, -1).forEach((key) => {
    pointer = pointer[key];
  });
  pointer[path[path.length - 1]] = value;
};

function definitionForType(type) {
  return schema.sections.find((section) => section.id === type);
}

function orderedSections() {
  return (page.order || [])
    .map((id) => {
      const instance = page.sections[id];
      if (!instance) return null;
      const definition = definitionForType(instance.type || id);
      if (!definition) return null;
      if (!instance.data) instance.data = {};
      return { id, instance, definition };
    })
    .filter(Boolean);
}

function activeSection() {
  return sections.find((section) => section.id === activeId) || null;
}

function icon(name) {
  const icons = {
    drag: '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M7 4h2v2H7V4Zm4 0h2v2h-2V4ZM7 9h2v2H7V9Zm4 0h2v2h-2V9Zm-4 5h2v2H7v-2Zm4 0h2v2h-2v-2Z"/></svg>',
    eye: '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 4c4.4 0 7.4 4.2 7.8 4.8l.2.3-.2.3c-.4.6-3.4 4.8-7.8 4.8S2.6 10 2.2 9.4L2 9.1l.2-.3C2.6 8.2 5.6 4 10 4Zm0 1.7c-2.7 0-5 2.1-6 3.4 1 1.3 3.3 3.4 6 3.4s5-2.1 6-3.4c-1-1.3-3.3-3.4-6-3.4Zm0 1.1a2.3 2.3 0 1 1 0 4.6 2.3 2.3 0 0 1 0-4.6Z"/></svg>',
    hidden: '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m3.3 2.4 14.3 14.3-1.1 1.1-2.4-2.4a8.3 8.3 0 0 1-4.1 1.1c-4.4 0-7.4-4.2-7.8-4.8L2 11.4l.2-.3a15 15 0 0 1 3-3.2l-3-3 1.1-1.1Zm3.1 6.7a12.8 12.8 0 0 0-2.4 2.3c1 1.3 3.3 3.4 6 3.4.9 0 1.8-.2 2.6-.6l-1.5-1.5a2.3 2.3 0 0 1-3-3L6.4 9.1ZM10 5.3c4.4 0 7.4 4.2 7.8 4.8l.2.3-.2.3c-.2.3-1 1.4-2.3 2.5l-1.2-1.2c.7-.6 1.3-1.2 1.7-1.7-1-1.3-3.3-3.4-6-3.4-.7 0-1.3.1-1.9.3L6.8 6c1-.5 2.1-.7 3.2-.7Z"/></svg>',
    cross: '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5.7 4.6 4.3 4.3 4.3-4.3 1.1 1.1-4.3 4.3 4.3 4.3-1.1 1.1-4.3-4.3-4.3 4.3-1.1-1.1 4.3-4.3-4.3-4.3 1.1-1.1Z"/></svg>',
    plus: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5.5v13M5.5 12h13" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/></svg>',
    chevron: '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m7.2 4.8 5.2 5.2-5.2 5.2 1.1 1.1 6.3-6.3-6.3-6.3-1.1 1.1Z"/></svg>'
  };
  return icons[name] || '';
}

function closeEditor() {
  activeId = null;
  render();
}

function openEditor(id) {
  activeId = activeId === id ? null : id;
  openRepeaterItems = new Set();
  render();
}

function moveSection(fromId, toId) {
  if (!fromId || fromId === toId) return;
  const nextOrder = [...page.order];
  const fromIndex = nextOrder.indexOf(fromId);
  const toIndex = nextOrder.indexOf(toId);
  if (fromIndex < 0 || toIndex < 0) return;
  nextOrder.splice(fromIndex, 1);
  nextOrder.splice(toIndex, 0, fromId);
  page.order = nextOrder;
  setDirty();
  render();
}

function uniqueSectionId(type) {
  if (!page.sections[type]) return type;
  let index = 2;
  let id = `${type}_${index}`;
  while (page.sections[id]) {
    index += 1;
    id = `${type}_${index}`;
  }
  return id;
}

function addSection(type) {
  const definition = definitionForType(type);
  if (!definition) return;
  const id = uniqueSectionId(type);
  page.sections[id] = {
    type,
    disabled: false,
    data: defaultDataForDefinition(definition)
  };
  page.order.push(id);
  activeId = id;
  addSectionMenu.hidden = true;
  setDirty();
  render();
}

function renderAddSectionMenu() {
  addSectionMenu.innerHTML = '';
  schema.sections
    .filter((definition) => !['header', 'footer'].includes(definition.id))
    .forEach((definition) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.textContent = definition.name;
      button.addEventListener('click', () => addSection(definition.id));
      addSectionMenu.appendChild(button);
    });
}

function initSortable() {
  if (sortable) sortable.destroy();
  if (!window.Sortable) return;

  sortable = Sortable.create(sectionList, {
    animation: 160,
    easing: 'cubic-bezier(.2, 0, .2, 1)',
    handle: '.drag-handle',
    draggable: '.section-item',
    ghostClass: 'section-sortable-ghost',
    chosenClass: 'section-sortable-chosen',
    dragClass: 'section-sortable-drag',
    forceFallback: true,
    fallbackOnBody: true,
    swapThreshold: 0.65,
    onEnd: () => {
      const nextOrder = Array.from(sectionList.querySelectorAll('.section-item'))
        .map((item) => item.dataset.id)
        .filter(Boolean);

      if (nextOrder.join('|') === page.order.join('|')) return;
      page.order = nextOrder;
      setDirty();
      render();
    }
  });
}

function renderSectionList() {
  sectionList.innerHTML = '';
  sections.forEach((section) => {
    const item = document.createElement('div');
    item.className = `section-item${section.id === activeId ? ' active' : ''}${section.instance.disabled ? ' hidden-section' : ''}`;
    item.draggable = true;
    item.dataset.id = section.id;

    const drag = document.createElement('button');
    drag.type = 'button';
    drag.className = 'icon-button section-action drag-handle';
    drag.innerHTML = icon('drag');
    drag.setAttribute('aria-label', `Drag ${section.definition.name}`);

    const title = document.createElement('button');
    title.type = 'button';
    title.className = 'section-title';
    title.textContent = section.definition.name;
    title.addEventListener('click', () => openEditor(section.id));

    const visibility = document.createElement('button');
    visibility.type = 'button';
    visibility.className = `icon-button section-action visibility-toggle${section.instance.disabled ? ' is-hidden' : ''}`;
    visibility.innerHTML = icon(section.instance.disabled ? 'hidden' : 'eye');
    visibility.setAttribute('aria-label', section.instance.disabled ? 'Show section' : 'Hide section');
    visibility.addEventListener('click', () => {
      section.instance.disabled = !section.instance.disabled;
      setDirty();
      render();
    });

    item.append(drag, title, visibility);
    sectionList.appendChild(item);
  });
}

function renderField(definition, key, value, path) {
  if (definition.type === 'array') {
    const list = value || [];
    const wrapper = document.createElement('div');
    wrapper.className = 'field array-field';
    const title = document.createElement('div');
    title.className = 'array-title';
    title.textContent = definition.label || key;
    wrapper.appendChild(title);

    list.forEach((item, index) => {
      const itemKey = [activeId, ...path, index].join('.');
      const isOpen = openRepeaterItems.has(itemKey);
      const itemBox = document.createElement('div');
      itemBox.className = `array-item${isOpen ? ' open' : ''}`;

      const itemHeader = document.createElement('div');
      itemHeader.className = 'array-item-head';

      const toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = 'array-toggle';
      toggle.innerHTML = `${icon('chevron')}<span>${definition.label || key} ${index + 1}</span>`;
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      toggle.addEventListener('click', () => {
        if (isOpen) {
          openRepeaterItems.delete(itemKey);
        } else {
          openRepeaterItems.add(itemKey);
        }
        render();
      });

      const remove = document.createElement('button');
      remove.type = 'button';
      remove.className = 'icon-button remove';
      remove.innerHTML = icon('cross');
      remove.setAttribute('aria-label', 'Remove item');
      remove.disabled = list.length <= 1;
      remove.title = list.length <= 1 ? 'At least one item is required' : 'Remove item';
      remove.addEventListener('click', () => {
        if (list.length <= 1) return;
        const currentList = pathGet(activeSection().instance.data, path);
        currentList.splice(index, 1);
        setDirty();
        render();
      });

      itemHeader.appendChild(toggle);
      if (isOpen) itemHeader.appendChild(remove);
      itemBox.appendChild(itemHeader);

      if (isOpen) {
        Object.entries(definition.item).forEach(([childKey, childDefinition]) => {
          itemBox.appendChild(renderField(childDefinition, childKey, item[childKey], [...path, index, childKey]));
        });
      }

      wrapper.appendChild(itemBox);
    });

    const add = document.createElement('button');
    add.type = 'button';
    add.className = 'add-item';
    add.innerHTML = `${icon('plus')}<span>Add item</span>`;
    add.addEventListener('click', () => {
      const currentList = pathGet(activeSection().instance.data, path);
      currentList.push(makeDefaultItem(definition.item));
      setDirty();
      render();
    });
    wrapper.appendChild(add);
    return wrapper;
  }

  if (definition.type === 'image') {
    const wrapper = document.createElement('div');
    wrapper.className = 'field image-field';
    const label = document.createElement('label');
    label.textContent = definition.label || key;

    const input = document.createElement('input');
    input.type = 'url';
    input.placeholder = 'https://... or assets/uploads/image.jpg';
    input.value = value ?? '';
    input.addEventListener('input', (event) => {
      pathSet(activeSection().instance.data, path, event.target.value);
      setDirty();
    });

    const tools = document.createElement('div');
    tools.className = 'image-tools';
    const file = document.createElement('input');
    file.type = 'file';
    file.accept = 'image/*';
    file.addEventListener('change', async () => {
      if (!file.files || !file.files[0]) return;
      const formData = new FormData();
      formData.append('image', file.files[0]);
      const response = await fetch(endpoints.upload, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData });
      const result = await response.json();
      if (!response.ok || !result.ok) {
        saveState.textContent = result.message || 'Upload failed';
        return;
      }
      input.value = result.path;
      previewImage.style.backgroundImage = `url("${result.path}")`;
      pathSet(activeSection().instance.data, path, result.path);
      setDirty();
    });

    const previewImage = document.createElement('div');
    previewImage.className = 'image-preview';
    if (value) previewImage.style.backgroundImage = `url("${value}")`;

    input.addEventListener('input', () => {
      previewImage.style.backgroundImage = input.value ? `url("${input.value}")` : '';
    });

    tools.appendChild(file);
    wrapper.append(label, input, tools, previewImage);
    return wrapper;
  }

  const wrapper = document.createElement('div');
  wrapper.className = 'field';
  const label = document.createElement('label');
  label.textContent = definition.label || key;
  const input = definition.type === 'textarea' ? document.createElement('textarea') : document.createElement('input');
  input.value = value ?? '';
  input.addEventListener('input', (event) => {
    pathSet(activeSection().instance.data, path, event.target.value);
    setDirty();
  });
  wrapper.append(label, input);
  return wrapper;
}

function renderFields() {
  const section = activeSection();
  fields.innerHTML = '';

  if (!section) {
    activeName.textContent = 'Select a section';
    editPanel.classList.remove('open');
    editPanel.setAttribute('aria-hidden', 'true');
    return;
  }

  activeName.textContent = section.definition.name;
  editPanel.classList.add('open');
  editPanel.setAttribute('aria-hidden', 'false');

  Object.entries(section.definition.schema).forEach(([key, definition]) => {
    if (!(key in section.instance.data)) section.instance.data[key] = cloneDefault(definition);
    if (definition.type === 'array' && section.instance.data[key].length === 0) {
      section.instance.data[key].push(makeDefaultItem(definition.item));
    }
    fields.appendChild(renderField(definition, key, section.instance.data[key], [key]));
  });
}

function render() {
  sections = orderedSections();
  renderSectionList();
  initSortable();
  renderAddSectionMenu();
  renderFields();
}

async function loadTemplate() {
  const [schemaResponse, pageResponse] = await Promise.all([
    fetch(endpoints.schema + '?ts=' + Date.now(), { cache: 'no-store', credentials: 'same-origin' }),
    fetch(endpoints.template + '?ts=' + Date.now(), { cache: 'no-store', credentials: 'same-origin' })
  ]);
  schema = await schemaResponse.json();
  page = await pageResponse.json();
  render();
}

async function savePage() {
  saveState.textContent = 'Saving...';
  const response = await fetch(endpoints.save, {
    method: 'POST',
    cache: 'no-store',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify(page)
  });
  const result = await response.json().catch(() => ({ ok: false, message: 'Save failed: server did not return JSON' }));
  if (!response.ok || !result.ok) {
    saveState.textContent = result.message || 'Save failed';
    return;
  }
  dirty = false;
  saveState.textContent = result.saved_at ? `Saved ${result.saved_at}` : 'All changes saved';
  await loadTemplate();
  await refreshPreview();
}

saveButton.addEventListener('click', savePage);
closePanel.addEventListener('click', closeEditor);
addSectionButton.addEventListener('click', () => {
  addSectionMenu.hidden = !addSectionMenu.hidden;
});
document.addEventListener('click', (event) => {
  if (addSectionMenu.hidden) return;
  if (addSectionMenu.contains(event.target) || addSectionButton.contains(event.target)) return;
  addSectionMenu.hidden = true;
});
window.addEventListener('beforeunload', (event) => {
  if (!dirty) return;
  event.preventDefault();
  event.returnValue = '';
});

loadTemplate();
