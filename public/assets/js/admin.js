(() => {
    'use strict';
    let dirty = false, ready = false, removed = null;
    const form = document.querySelector('[data-editor-form]');
    function markDirty() {
        if (!form || !ready) return;
        dirty = true;
        const status = form.querySelector('[data-editor-status]');
        status.textContent = 'Nespremljene promjene'; status.classList.add('is-dirty');
        form.querySelector('[data-save-note]').textContent = 'Imate promjene koje još nisu spremljene.';
    }
    function syncSource(wrapper) {
        const editor = wrapper.querySelector('trix-editor');
        const input = wrapper.querySelector('[data-rich-input]');
        const source = wrapper.querySelector('[data-rich-source]');
        if (wrapper.dataset.mode === 'html') input.value = source.value;
        else source.value = input.value;
        return editor;
    }
    document.addEventListener('trix-before-initialize', () => {
        if (window.Trix) Trix.config.blockAttributes.heading1.tagName = 'h2';
    });
    document.addEventListener('trix-file-accept', event => event.preventDefault());
    document.addEventListener('trix-initialize', event => {
        event.target.dataset.initialized = 'true';
        const toolbar = event.target.toolbarElement;
        const labels = {bold:'Podebljano',italic:'Kurziv',strike:'Precrtano',href:'Poveznica',heading1:'Podnaslov',quote:'Citat',bullet:'Popis',number:'Numerirani popis',undo:'Poništi',redo:'Ponovi',increaseNestingLevel:'Uvuci',decreaseNestingLevel:'Smanji uvlaku'};
        toolbar.querySelectorAll('[data-trix-attribute], [data-trix-action]').forEach(button => {
            const key = button.dataset.trixAttribute || button.dataset.trixAction;
            if (labels[key]) { button.title = labels[key]; button.setAttribute('aria-label', labels[key]); }
        });
        toolbar.querySelectorAll('[data-trix-action="link"]').forEach(button => { button.value = 'Dodaj poveznicu'; });
        toolbar.querySelectorAll('[data-trix-method="setAttribute"]').forEach(button => { button.value = 'Dodaj poveznicu'; });
        toolbar.querySelectorAll('[data-trix-method="removeAttribute"]').forEach(button => { button.value = 'Ukloni poveznicu'; });
    });
    document.addEventListener('trix-change', event => {
        const wrapper = event.target.closest('[data-rich-editor]');
        if (wrapper && wrapper.dataset.mode !== 'html') syncSource(wrapper);
        if (event.target.dataset.initialized) markDirty();
    });
    function updateRepeater(repeater) {
        const rows = [...repeater.querySelector('[data-repeat-list]').children];
        repeater.querySelector('[data-item-count]').textContent = rows.length;
        repeater.querySelector('[data-empty]').hidden = rows.length > 0;
        repeater.querySelectorAll('[data-add-item]').forEach(button => button.disabled = rows.length >= Number(repeater.dataset.max));
        rows.forEach((row, index) => {
            row.querySelector('[data-row-up]').disabled = index === 0;
            row.querySelector('[data-row-down]').disabled = index === rows.length - 1;
        });
    }
    function resetImage(field) {
        const preview = field.querySelector('[data-image-preview]');
        if (field.dataset.objectUrl) URL.revokeObjectURL(field.dataset.objectUrl);
        field.querySelector('[data-image-input]').value = '';
        preview.hidden = !preview.dataset.originalSrc;
        if (preview.dataset.originalSrc) preview.src = preview.dataset.originalSrc;
        else preview.removeAttribute('src');
        field.querySelector('[data-image-empty]').hidden = !!preview.dataset.originalSrc;
        field.querySelector('[data-image-reset]').hidden = true;
        field.querySelector('[data-image-filename]').textContent = '';
    }
    document.addEventListener('click', event => {
        const passwordToggle = event.target.closest('[data-toggle-password]');
        if (passwordToggle) {
            const input = document.getElementById(passwordToggle.dataset.togglePassword);
            const show = input.type === 'password'; input.type = show ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', show);
            passwordToggle.setAttribute('aria-label', show ? 'Sakrij lozinku' : 'Prikaži lozinku');
        }
        const mode = event.target.closest('[data-rich-mode]');
        if (mode) {
            const wrapper = mode.closest('[data-rich-editor]');
            const editor = wrapper.querySelector('trix-editor');
            const source = wrapper.querySelector('[data-rich-source]');
            const input = wrapper.querySelector('[data-rich-input]');
            if (mode.dataset.richMode === 'html') { source.value = input.value; }
            else if (wrapper.dataset.mode === 'html') { input.value = source.value; editor.editor.loadHTML(source.value); }
            wrapper.dataset.mode = mode.dataset.richMode;
            source.hidden = mode.dataset.richMode !== 'html';
            editor.hidden = !source.hidden;
            editor.toolbarElement.hidden = !source.hidden;
            wrapper.querySelectorAll('[data-rich-mode]').forEach(button => { const active = button === mode; button.classList.toggle('is-active', active); button.setAttribute('aria-pressed', active); });
            if (!source.hidden) source.focus(); else editor.focus();
        }
        const add = event.target.closest('[data-add-item]');
        if (add) {
            const repeater = add.closest('[data-repeater]');
            const list = repeater.querySelector('[data-repeat-list]');
            if (list.children.length >= Number(repeater.dataset.max)) return;
            const index = repeater.dataset.nextIndex++;
            const template = document.createElement('template');
            template.innerHTML = repeater.querySelector('[data-repeat-template]').innerHTML.split('__INDEX__').join(String(index));
            const row = template.content.firstElementChild;
            list.appendChild(row); updateRepeater(repeater); markDirty();
            const focus = row.querySelector('input:not([type="hidden"]):not([type="file"]), select, [data-image-choose]');
            if (focus) focus.focus();
        }
        const action = event.target.closest('[data-row-up], [data-row-down], [data-row-remove]');
        if (action) {
            event.preventDefault(); event.stopPropagation();
            const row = action.closest('[data-repeat-item]'), list = row.parentElement, repeater = row.closest('[data-repeater]');
            if (action.hasAttribute('data-row-up') && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
            if (action.hasAttribute('data-row-down') && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
            if (action.hasAttribute('data-row-remove')) {
                removed = {row, list, next: row.nextElementSibling, repeater}; row.remove();
                document.querySelector('[data-undo-toast]').hidden = false;
            }
            updateRepeater(repeater); markDirty();
        }
        if (event.target.closest('[data-undo-remove]') && removed) {
            if (removed.list.children.length < Number(removed.repeater.dataset.max)) {
                removed.list.insertBefore(removed.row, removed.next?.parentNode === removed.list ? removed.next : null);
                updateRepeater(removed.repeater); removed.row.open = true; markDirty();
            }
            document.querySelector('[data-undo-toast]').hidden = true; removed = null;
        }
        const choose = event.target.closest('[data-image-choose]');
        if (choose) choose.closest('[data-image-field]').querySelector('[data-image-input]').click();
        const reset = event.target.closest('[data-image-reset]');
        if (reset) { resetImage(reset.closest('[data-image-field]')); markDirty(); }
        if (event.target.closest('[data-discard]')) dirty = false;
        const menu = event.target.closest('.admin-menu');
        if (menu) { const open = document.querySelector('.admin-sidebar').classList.toggle('is-open'); menu.setAttribute('aria-expanded', open); }
    });
    document.addEventListener('input', event => {
        if (event.target.matches('[data-icon-search]')) {
            const picker = event.target.closest('[data-icon-picker]');
            const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
            const query = normalize(event.target.value.trim()); let matches = 0;
            picker.querySelectorAll('[data-icon-option]').forEach(option => { option.hidden = !normalize(option.dataset.iconLabel).includes(query); if (!option.hidden) matches++; });
            picker.querySelector('[data-icon-empty]').hidden = matches > 0;
            return;
        }
        if (event.target.matches('[data-rich-source]')) syncSource(event.target.closest('[data-rich-editor]'));
        if (event.target.matches('[data-row-title-input]')) {
            const row = event.target.closest('[data-repeat-item]');
            if (row) row.querySelector('[data-row-title]').textContent = event.target.value.trim() || row.closest('[data-repeater]').dataset.itemName;
        }
        if (event.target.closest('[data-editor-form]')) markDirty();
    });
    document.addEventListener('change', event => {
        if (event.target.matches('[data-service-icon]')) {
            const icon = event.target.closest('[data-repeat-item]')?.querySelector('[data-row-icon] i');
            if (icon) icon.className = 'fa-light fa-' + event.target.value;
        }
        if (event.target.matches('[data-image-input]')) {
            const field = event.target.closest('[data-image-field]'), file = event.target.files[0];
            if (!file) return;
            const filename = field.querySelector('[data-image-filename]');
            if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 10 * 1024 * 1024) {
                resetImage(field); filename.textContent = 'Odaberite JPG, PNG ili WebP do 10 MB.'; filename.classList.add('field-error'); return;
            }
            filename.classList.remove('field-error');
            if (field.dataset.objectUrl) URL.revokeObjectURL(field.dataset.objectUrl);
            field.dataset.objectUrl = URL.createObjectURL(file);
            const preview = field.querySelector('[data-image-preview]'); preview.src = field.dataset.objectUrl; preview.hidden = false;
            field.querySelector('[data-image-empty]').hidden = true;
            field.querySelector('[data-image-reset]').hidden = false;
            filename.textContent = file.name + ' · sprema se s promjenama';
        }
        if (event.target.closest('[data-editor-form]')) markDirty();
    });
    if (form) form.addEventListener('submit', () => {
        form.querySelectorAll('[data-rich-editor]').forEach(syncSource);
        dirty = false;
        const button = form.querySelector('[data-save-button]'); button.disabled = true; button.textContent = 'Spremanje…';
    });
    window.addEventListener('beforeunload', event => { if (dirty) { event.preventDefault(); event.returnValue = ''; } });
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-repeater]').forEach(updateRepeater);
        ready = true;
        if (document.querySelector('.form-errors') && form) markDirty();
    });
})();
