<?php
/**
 * Widgets Tab - Page Widget Manager
 * Manages widgets for a page
 */
?>

<div class="widget-manager">
    <div class="widget-manager-header">
        <h3>Správa widgetů</h3>
        <button type="button" class="btn btn-primary" id="add-widget-btn">+ Přidat widget</button>
    </div>

    <div class="widgets-list" id="widgets-list">
        <?php if (empty($widgets)): ?>
            <div class="empty-state" id="empty-state">
                <p>Zatím nemáte žádné widgety. Klikněte na "Přidat widget" pro vytvoření prvního widgetu.</p>
            </div>
        <?php else: ?>
            <?php foreach ($widgets as $widget): ?>
                <div class="widget-item" data-widget-id="<?= $widget->id ?>">
                    <div class="widget-item-header">
                        <span class="widget-icon"><?= e($widget->icon ?? '📦') ?></span>
                        <div class="widget-info">
                            <strong><?= e($widget->widget_label) ?></strong>
                            <small><?= e($widget->type_key) ?></small>
                        </div>
                        <div class="widget-actions">
                            <button type="button" class="btn-icon" onclick="moveWidgetUp(<?= $widget->id ?>)" title="Posunout nahoru">↑</button>
                            <button type="button" class="btn-icon" onclick="moveWidgetDown(<?= $widget->id ?>)" title="Posunout dolů">↓</button>
                            <button type="button" class="btn-icon btn-edit" onclick="editWidget(<?= $widget->id ?>)" title="Upravit">✏️</button>
                            <button type="button" class="btn-icon btn-delete" onclick="deleteWidget(<?= $widget->id ?>)" title="Smazat">🗑️</button>
                        </div>
                    </div>
                    <?php
                    // Show preview of settings
                    $settings = json_decode($widget->settings ?? '{}', true);
                    if (!empty($settings)):
                    ?>
                    <div class="widget-preview">
                        <?php if (isset($settings['title'])): ?>
                            <div><strong>Nadpis:</strong> <?= e(substr($settings['title'], 0, 100)) ?></div>
                        <?php endif; ?>
                        <?php if (isset($settings['sectionTitle'])): ?>
                            <div><strong>Sekce:</strong> <?= e(substr($settings['sectionTitle'], 0, 100)) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="margin-top: 20px;">
        <a href="<?= url($page->slug) ?>" target="_blank" class="btn btn-outline">👁️ Zobrazit stránku (frontend)</a>
    </div>
</div>

<!-- Modal: Add Widget -->
<div id="add-widget-modal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Přidat widget</h2>
            <button type="button" class="modal-close" onclick="closeModal('add-widget-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="widget-type-tabs">
                <?php
                $categories = ['layout' => 'Layout', 'content' => 'Obsah', 'media' => 'Média', 'dynamic' => 'Dynamické'];
                $first = true;
                foreach ($categories as $catKey => $catLabel):
                    if (isset($widgetTypes[$catKey])):
                ?>
                    <button type="button" class="widget-tab-btn <?= $first ? 'active' : '' ?>" data-category="<?= $catKey ?>">
                        <?= e($catLabel) ?>
                    </button>
                <?php
                    $first = false;
                    endif;
                endforeach;
                ?>
            </div>

            <?php foreach ($categories as $catKey => $catLabel): ?>
                <?php if (isset($widgetTypes[$catKey])): ?>
                <div class="widget-type-grid" data-category="<?= $catKey ?>" style="<?= $catKey === array_key_first($widgetTypes) ? '' : 'display:none;' ?>">
                    <?php foreach ($widgetTypes[$catKey] as $type): ?>
                        <div class="widget-type-card" onclick="selectWidgetType('<?= e($type->type_key) ?>')">
                            <div class="widget-type-icon"><?= e($type->icon ?? '📦') ?></div>
                            <div class="widget-type-label"><?= e($type->label) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal: Edit Widget -->
<div id="edit-widget-modal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2 id="edit-widget-title">Upravit widget</h2>
            <button type="button" class="modal-close" onclick="closeModal('edit-widget-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-tabs">
                <button type="button" class="modal-tab-btn active" data-modal-tab="widget-cs">Čeština (CS)</button>
                <button type="button" class="modal-tab-btn" data-modal-tab="widget-en">Angličtina (EN)</button>
            </div>

            <form id="widget-form">
                <input type="hidden" id="widget-id" name="widget_id">
                <input type="hidden" id="widget-type-key" name="widget_type_key">

                <div class="modal-tab-content active" id="tab-widget-cs">
                    <div id="widget-form-cs"></div>
                </div>

                <div class="modal-tab-content" id="tab-widget-en">
                    <div id="widget-form-en"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-widget-modal')">Zrušit</button>
                    <button type="button" class="btn btn-primary" onclick="saveWidget()">Uložit widget</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Media Library -->
<div id="media-library-modal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Media Library</h2>
            <button type="button" class="modal-close" onclick="closeModal('media-library-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Upload Section -->
            <div class="media-upload-section" style="margin-bottom: 20px; padding: 20px; border: 2px dashed #ddd; border-radius: 8px; text-align: center;">
                <input type="file" id="media-upload-input" accept="image/*" style="display: none;" />
                <button type="button" class="btn btn-primary" onclick="document.getElementById('media-upload-input').click()">
                    📤 Nahrát nový obrázek
                </button>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    Podporované formáty: JPEG, PNG, GIF, WebP (max 32MB)
                </p>
                <div id="upload-progress" style="display: none; margin-top: 10px;">
                    <div style="background: #f0f0f0; height: 20px; border-radius: 10px; overflow: hidden;">
                        <div id="upload-progress-bar" style="background: var(--admin-primary, #667eea); height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <p id="upload-status" style="margin-top: 5px; font-size: 13px;"></p>
                </div>
            </div>

            <!-- Media Grid -->
            <div id="media-library-grid" class="media-library-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; max-height: 400px; overflow-y: auto;">
                <!-- Media items will be loaded here -->
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">
                    <p>Načítání médií...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('media-library-modal')">Zavřít</button>
        </div>
    </div>
</div>

<script>
(function() {
'use strict';

const pageId = <?= $page->id ?>;
const csrfToken = '<?= \App\Core\Session::generateCSRFToken() ?>';

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return (meta && meta.content) ? meta.content : csrfToken;
}

function withCsrfHeaders(headers = {}) {
    return Object.assign({}, headers, {
        'X-CSRF-Token': getCsrfToken()
    });
}

function appendCsrf(formData) {
    if (!formData.has('csrf_token')) {
        formData.append('csrf_token', getCsrfToken());
    }
    return formData;
}

// Open/close modals
window.openModal = function(modalId) {
    console.log('openModal called with:', modalId);
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }
    console.log('Modal element found, adding active class');
    modal.classList.add('active');
    console.log('Modal classes:', modal.className);
}

window.closeModal = function(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {

// Add widget button
const addWidgetBtn = document.getElementById('add-widget-btn');
if (addWidgetBtn) {
    addWidgetBtn.addEventListener('click', function() {
        openModal('add-widget-modal');
    });
}

// Widget type category tabs
document.querySelectorAll('.widget-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;

        // Update active tab
        document.querySelectorAll('.widget-tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Show corresponding grid
        document.querySelectorAll('.widget-type-grid').forEach(grid => {
            grid.style.display = grid.dataset.category === category ? 'grid' : 'none';
        });
    });
});

// Modal tab switching (for edit widget modal)
document.querySelectorAll('.modal-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.modalTab;
        console.log('Modal tab clicked:', tabName);

        // Remove active from all modal tabs
        document.querySelectorAll('.modal-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.modal-tab-content').forEach(c => c.classList.remove('active'));

        // Add active to clicked tab
        this.classList.add('active');
        const targetContent = document.getElementById('tab-' + tabName);
        if (targetContent) {
            targetContent.classList.add('active');
            console.log('Activated tab:', tabName);
        } else {
            console.error('Tab content not found:', 'tab-' + tabName);
        }
    });
});

}); // End DOMContentLoaded

// Functions accessible from HTML onclick attributes

// Select widget type (open edit form for new widget)
window.selectWidgetType = function(typeKey) {
    closeModal('add-widget-modal');

    // Load schema and show edit form
    fetch(`<?= adminUrl('widgets/schema/') ?>${typeKey}`)
        .then(r => r.json())
        .then(schema => {
            document.getElementById('widget-id').value = '';
            document.getElementById('widget-type-key').value = typeKey;
            document.getElementById('edit-widget-title').textContent = 'Nový widget';

            // Build form
            buildWidgetForm(schema, {}, 'cs');
            buildWidgetForm(schema, {}, 'en');

            openModal('edit-widget-modal');
        })
        .catch(err => {
            alert('Chyba načítání widget schématu');
            console.error(err);
        });
}

// Edit existing widget
window.editWidget = function(widgetId) {
    console.log('editWidget called with ID:', widgetId);

    fetch(`<?= adminUrl('widgets/') ?>${widgetId}`)
        .then(r => {
            console.log('Widget response status:', r.status);
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(widget => {
            console.log('Widget data:', widget);

            document.getElementById('widget-id').value = widgetId;
            document.getElementById('widget-type-key').value = widget.widget_type_key;
            document.getElementById('edit-widget-title').textContent = 'Upravit widget';

            // Load schema
            return fetch(`<?= adminUrl('widgets/schema/') ?>${widget.widget_type_key}`)
                .then(r => {
                    console.log('Schema response status:', r.status);
                    if (!r.ok) {
                        throw new Error(`HTTP error! status: ${r.status}`);
                    }
                    return r.json();
                })
                .then(schema => {
                    console.log('Schema data:', schema);
                    console.log('Widget translations:', widget.translations);

                    if (!schema || !schema.fields) {
                        console.error('Invalid schema - missing fields array:', schema);
                        alert('Chyba: Neplatné schéma widgetu');
                        return;
                    }

                    buildWidgetForm(schema, widget.translations.cs || {}, 'cs');
                    buildWidgetForm(schema, widget.translations.en || {}, 'en');

                    console.log('About to open modal: edit-widget-modal');
                    openModal('edit-widget-modal');
                    console.log('Modal opened');
                });
        })
        .catch(err => {
            alert('Chyba načítání widgetu: ' + err.message);
            console.error('editWidget error:', err);
        });
}

// Update visibility of conditional fields
function updateConditionalFields(container, controllingFieldKey, controllingValue, isCheckbox = false) {
    const conditionalGroups = container.querySelectorAll('[data-conditional-field="' + controllingFieldKey + '"]');

    conditionalGroups.forEach(group => {
        const requiredValue = group.dataset.conditionalValue;

        // Check if the controlling field's value matches the required value
        const shouldShow = (controllingValue == requiredValue);

        group.style.display = shouldShow ? 'block' : 'none';

        // If hiding, clear the input values to prevent saving invalid data
        if (!shouldShow) {
            const inputs = group.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });
        }
    });
}

// Build dynamic form from schema
function buildWidgetForm(schema, data, language) {
    console.log(`buildWidgetForm called for language: ${language}`);
    console.log('Schema:', schema);
    console.log('Data:', data);

    const container = document.getElementById(`widget-form-${language}`);
    if (!container) {
        console.error(`Container not found: widget-form-${language}`);
        return;
    }

    container.innerHTML = '';

    if (!schema || !schema.fields || !Array.isArray(schema.fields)) {
        console.error('Invalid schema - fields is not an array:', schema);
        container.innerHTML = '<p style="color: red;">Chyba: Neplatné schéma widgetu</p>';
        return;
    }

    console.log(`Rendering ${schema.fields.length} fields`);

    schema.fields.forEach(field => {
        const formGroup = document.createElement('div');
        formGroup.className = 'form-group';

        // Handle conditional fields
        if (field.conditional) {
            formGroup.dataset.conditionalField = field.conditional.field;
            formGroup.dataset.conditionalValue = field.conditional.value;
            formGroup.style.display = 'none'; // Initially hidden
        }

        const label = document.createElement('label');
        label.textContent = field.label + (field.required ? ' *' : '');
        formGroup.appendChild(label);

        let input;
        const value = data[field.key] || field.default || '';

        switch (field.type) {
            case 'text':
                input = document.createElement('input');
                input.type = 'text';
                input.value = value;
                break;

            case 'textarea':
            case 'wysiwyg':
                input = document.createElement('textarea');
                input.rows = 8;
                input.value = value;
                if (field.type === 'wysiwyg') {
                    input.classList.add('wysiwyg-editor');
                }
                break;

            case 'select':
                input = document.createElement('select');
                field.options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.label;
                    if (value === opt.value) option.selected = true;
                    input.appendChild(option);
                });
                break;

            case 'checkbox':
                input = document.createElement('input');
                input.type = 'checkbox';
                input.checked = !!value;
                break;

            case 'number':
                input = document.createElement('input');
                input.type = 'number';
                input.value = value;
                if (field.min !== undefined) input.min = field.min;
                if (field.max !== undefined) input.max = field.max;
                break;

            case 'repeater':
                // Repeater field - array of items with subfields
                input = buildRepeaterField(field, value, language);
                formGroup.appendChild(input);
                container.appendChild(formGroup);
                return; // Skip adding input separately

            case 'image':
                // Single image with media library picker
                input = buildImageField(field, value);
                formGroup.appendChild(input);
                container.appendChild(formGroup);
                return; // Skip adding input separately

            case 'image_gallery':
                // Image gallery - array of image URLs
                input = buildImageGalleryField(field, value);
                formGroup.appendChild(input);
                container.appendChild(formGroup);
                return; // Skip adding input separately

            default:
                input = document.createElement('input');
                input.type = 'text';
                input.value = value;
        }

        input.className = 'form-control';
        input.dataset.fieldKey = field.key;
        if (field.required) input.required = true;

        // Add change listener for fields that control conditional visibility
        input.addEventListener('change', function() {
            updateConditionalFields(container, field.key, this.value, this.type === 'checkbox' ? this.checked : null);
        });

        formGroup.appendChild(input);
        container.appendChild(formGroup);
    });

    console.log(`Form built for ${language}, container has ${container.children.length} elements`);
    console.log('Container HTML:', container.innerHTML.substring(0, 500));

    // Initialize conditional field visibility based on current values
    schema.fields.forEach(field => {
        const input = container.querySelector(`[data-field-key="${field.key}"]`);
        if (input) {
            const value = input.type === 'checkbox' ? input.checked : input.value;
            updateConditionalFields(container, field.key, value);
        }
    });
}

// Build repeater field (array of items with subfields)
function buildRepeaterField(field, data, language) {
    const container = document.createElement('div');
    container.className = 'repeater-field';
    container.dataset.fieldKey = field.key;

    // Ensure data is array
    const items = Array.isArray(data) ? data : [];

    const itemsList = document.createElement('div');
    itemsList.className = 'repeater-items';

    // Render existing items
    items.forEach((item, index) => {
        const itemDiv = buildRepeaterItem(field, item, index);
        itemsList.appendChild(itemDiv);
    });

    container.appendChild(itemsList);

    // Add button
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'btn btn-sm btn-secondary';
    addButton.textContent = '+ Přidat položku';
    addButton.onclick = function() {
        const newIndex = itemsList.children.length;
        const newItem = buildRepeaterItem(field, {}, newIndex);
        itemsList.appendChild(newItem);
    };
    container.appendChild(addButton);

    return container;
}

// Build nested repeater (e.g. tags inside a card)
function buildNestedRepeaterField(subfield, value, parentPath) {
    const fullKey = parentPath + '.' + subfield.key;
    const container = document.createElement('div');
    container.className = 'repeater-field repeater-field-nested';
    container.dataset.fieldKey = fullKey;

    const items = Array.isArray(value) ? value : [];
    const itemsList = document.createElement('div');
    itemsList.className = 'repeater-items';

    items.forEach((item, j) => {
        const itemEl = buildNestedRepeaterItem(subfield, item, j, fullKey);
        itemsList.appendChild(itemEl);
    });
    container.appendChild(itemsList);

    const addBtn = document.createElement('button');
    addBtn.type = 'button';
    addBtn.className = 'btn btn-sm btn-secondary';
    addBtn.textContent = '+ Přidat štítek';
    addBtn.onclick = function() {
        const newIndex = itemsList.children.length;
        const newItem = buildNestedRepeaterItem(subfield, {}, newIndex, fullKey);
        itemsList.appendChild(newItem);
    };
    container.appendChild(addBtn);

    return container;
}

function buildNestedRepeaterItem(subfield, data, nestedIndex, parentPath) {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'repeater-item repeater-item-nested';
    itemDiv.dataset.index = nestedIndex;

    (subfield.fields || []).forEach(subsub => {
        const group = document.createElement('div');
        group.className = 'form-group-inline';
        const label = document.createElement('label');
        label.textContent = subsub.label + (subsub.required ? ' *' : '');
        group.appendChild(label);

        const val = data[subsub.key] || subsub.default || '';
        let input;
        const key = parentPath + '[' + nestedIndex + '].' + subsub.key;

        if (subsub.type === 'select') {
            input = document.createElement('select');
            (subsub.options || []).forEach(opt => {
                const o = document.createElement('option');
                o.value = opt.value;
                o.textContent = opt.label;
                if (val === opt.value) o.selected = true;
                input.appendChild(o);
            });
            input.className = 'form-control';
            input.dataset.fieldKey = key;
            if (subsub.required) input.required = true;
        } else {
            input = document.createElement('input');
            input.type = subsub.type === 'number' ? 'number' : 'text';
            input.value = val;
            input.className = 'form-control';
            input.dataset.fieldKey = key;
            if (subsub.required) input.required = true;
        }
        group.appendChild(input);
        itemDiv.appendChild(group);
    });

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'btn btn-sm btn-danger';
    delBtn.textContent = '×';
    delBtn.onclick = function() { itemDiv.remove(); };
    itemDiv.appendChild(delBtn);

    return itemDiv;
}

// Build single repeater item
function buildRepeaterItem(field, data, index) {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'repeater-item';
    itemDiv.dataset.index = index;

    // Build subfields
    field.fields.forEach(subfield => {
        const value = data[subfield.key] ?? subfield.default ?? '';

        if (subfield.type === 'repeater') {
            const wrapper = document.createElement('div');
            wrapper.className = 'form-group form-group-repeater-nested';
            const label = document.createElement('label');
            label.textContent = subfield.label + (subfield.required ? ' *' : '');
            wrapper.appendChild(label);
            const nested = buildNestedRepeaterField(subfield, value, field.key + '[' + index + ']');
            wrapper.appendChild(nested);
            itemDiv.appendChild(wrapper);
            return;
        }

        const subfieldGroup = document.createElement('div');
        subfieldGroup.className = 'form-group-inline';

        const label = document.createElement('label');
        label.textContent = subfield.label + (subfield.required ? ' *' : '');
        subfieldGroup.appendChild(label);

        let input;

        if (subfield.type === 'select') {
            input = document.createElement('select');
            subfield.options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                if (value === opt.value) option.selected = true;
                input.appendChild(option);
            });
            input.className = 'form-control';
            input.dataset.fieldKey = `${field.key}[${index}].${subfield.key}`;
            if (subfield.required) input.required = true;
            subfieldGroup.appendChild(input);
        } else if (subfield.type === 'image') {
            // Image field with preview and media library picker
            const imageContainer = document.createElement('div');
            imageContainer.style.marginTop = '10px';

            const img = document.createElement('img');
            img.src = value || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="100"%3E%3Crect fill="%23f0f0f0" width="150" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-size="12"%3ENení obrázek%3C/text%3E%3C/svg%3E';
            img.style.maxWidth = '200px';
            img.style.maxHeight = '150px';
            img.style.display = 'block';
            img.style.marginBottom = '8px';
            img.onerror = function() {
                this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="100"%3E%3Crect fill="%23ffebee" width="150" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23c62828" font-size="12"%3EChyba%3C/text%3E%3C/svg%3E';
            };
            imageContainer.appendChild(img);

            input = document.createElement('input');
            input.type = 'text';
            input.value = value;
            input.className = 'form-control';
            input.dataset.fieldKey = `${field.key}[${index}].${subfield.key}`;
            input.placeholder = 'Cloudinary URL';
            input.style.marginBottom = '8px';
            input.oninput = function() {
                img.src = this.value || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="100"%3E%3Crect fill="%23f0f0f0" width="150" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-size="12"%3ENení obrázek%3C/text%3E%3C/svg%3E';
            };
            if (subfield.required) input.required = true;
            imageContainer.appendChild(input);

            const selectBtn = document.createElement('button');
            selectBtn.type = 'button';
            selectBtn.className = 'btn btn-sm btn-primary';
            selectBtn.textContent = '📤 Vybrat obrázek';
            selectBtn.onclick = function() {
                window.openMediaLibrary(function(cloudinaryUrl) {
                    input.value = cloudinaryUrl;
                    img.src = cloudinaryUrl;
                });
            };
            imageContainer.appendChild(selectBtn);

            subfieldGroup.appendChild(imageContainer);
        } else if (subfield.type === 'wysiwyg') {
            input = document.createElement('textarea');
            input.rows = 8;
            input.value = value;
            input.className = 'form-control wysiwyg-editor';
            input.dataset.fieldKey = `${field.key}[${index}].${subfield.key}`;
            if (subfield.required) input.required = true;
            subfieldGroup.appendChild(input);
        } else {
            input = document.createElement('input');
            input.type = subfield.type === 'number' ? 'number' : 'text';
            input.value = value;
            input.className = 'form-control';
            input.dataset.fieldKey = `${field.key}[${index}].${subfield.key}`;
            if (subfield.required) input.required = true;
            subfieldGroup.appendChild(input);
        }

        itemDiv.appendChild(subfieldGroup);
    });

    // Delete button
    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.className = 'btn btn-sm btn-danger';
    deleteBtn.textContent = '×';
    deleteBtn.onclick = function() {
        itemDiv.remove();
    };
    itemDiv.appendChild(deleteBtn);

    return itemDiv;
}

// Build single image field with media library picker
function buildImageField(field, data) {
    const container = document.createElement('div');
    container.className = 'image-field';
    container.dataset.fieldKey = field.key;

    const imageUrl = data || '';

    // Image preview and input container
    const imageContainer = document.createElement('div');
    imageContainer.className = 'image-field-container';

    // Thumbnail preview
    const preview = document.createElement('div');
    preview.className = 'image-preview';

    const img = document.createElement('img');
    img.src = imageUrl || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23f0f0f0" width="200" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999"%3ENení vybrán obrázek%3C/text%3E%3C/svg%3E';
    img.className = 'image-thumbnail';
    img.style.maxWidth = '300px';
    img.style.maxHeight = '200px';
    img.style.display = 'block';
    img.style.marginBottom = '10px';
    img.onerror = function() {
        console.error('Image load error:', this.src);
        this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23ffebee" width="200" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23c62828"%3EChyba načtení%3C/text%3E%3C/svg%3E';
    };
    preview.appendChild(img);
    imageContainer.appendChild(preview);

    // URL input (Cloudinary URL)
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.value = imageUrl;
    input.dataset.fieldKey = field.key;
    input.placeholder = 'Cloudinary URL';
    input.style.marginBottom = '10px';
    input.oninput = function() {
        img.src = this.value || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23f0f0f0" width="200" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999"%3ENení vybrán obrázek%3C/text%3E%3C/svg%3E';
    };
    imageContainer.appendChild(input);

    // Media library button
    const selectButton = document.createElement('button');
    selectButton.type = 'button';
    selectButton.className = 'btn btn-sm btn-primary';
    selectButton.textContent = '📤 Vybrat / Nahrát obrázek';
    selectButton.onclick = function() {
        window.openMediaLibrary(function(cloudinaryUrl) {
            input.value = cloudinaryUrl;
            img.src = cloudinaryUrl;
        });
    };
    imageContainer.appendChild(selectButton);

    // Clear button
    if (imageUrl) {
        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'btn btn-sm btn-secondary';
        clearButton.textContent = '× Vymazat';
        clearButton.style.marginLeft = '10px';
        clearButton.onclick = function() {
            input.value = '';
            img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23f0f0f0" width="200" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999"%3ENení vybrán obrázek%3C/text%3E%3C/svg%3E';
            this.remove();
        };
        imageContainer.appendChild(clearButton);
    }

    container.appendChild(imageContainer);
    return container;
}

// Build image gallery field
function buildImageGalleryField(field, data) {
    const container = document.createElement('div');
    container.className = 'image-gallery-field';
    container.dataset.fieldKey = field.key;

    // Ensure data is array
    const images = Array.isArray(data) ? data : [];

    const imagesList = document.createElement('div');
    imagesList.className = 'gallery-images';

    // Render existing images
    images.forEach((imageUrl, index) => {
        const imageItem = buildImageItem(imageUrl, index, field.key);
        imagesList.appendChild(imageItem);
    });

    container.appendChild(imagesList);

    // Add image button
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'btn btn-sm btn-primary';
    addButton.textContent = '📤 Vybrat / Nahrát obrázek';
    addButton.onclick = function() {
        const newIndex = imagesList.children.length;
        // Open media library with callback
        window.openMediaLibrary(function(filePath) {
            const imageItem = buildImageItem(filePath, newIndex, field.key);
            imagesList.appendChild(imageItem);
        });
    };
    container.appendChild(addButton);

    return container;
}

// Build single image item
function buildImageItem(imageUrl, index, fieldKey) {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'gallery-image-item';

    // Thumbnail preview (Cloudinary URL)
    const img = document.createElement('img');
    img.src = imageUrl || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect fill="%23ddd" width="100" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3E?%3C/text%3E%3C/svg%3E';
    img.className = 'gallery-thumbnail';
    img.onerror = function() {
        this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect fill="%23ddd" width="100" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3E?%3C/text%3E%3C/svg%3E';
    };
    itemDiv.appendChild(img);

    // URL input (Cloudinary URL)
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.value = imageUrl;
    input.dataset.fieldKey = `${fieldKey}[${index}]`;
    input.placeholder = 'Cloudinary URL';
    input.oninput = function() {
        img.src = this.value || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect fill="%23ddd" width="100" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3E?%3C/text%3E%3C/svg%3E';
    };
    itemDiv.appendChild(input);

    // Delete button
    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.className = 'btn btn-sm btn-danger';
    deleteBtn.textContent = '×';
    deleteBtn.onclick = function() {
        itemDiv.remove();
    };
    itemDiv.appendChild(deleteBtn);

    return itemDiv;
}

// Save widget
window.saveWidget = function() {
    const widgetId = document.getElementById('widget-id').value;
    const widgetTypeKey = document.getElementById('widget-type-key').value;

    // Collect form data for both languages
    const translations = {
        cs: collectFormData('cs'),
        en: collectFormData('en')
    };

    const url = widgetId
        ? `<?= adminUrl('widgets/') ?>${widgetId}/update`
        : `<?= adminUrl("pages/{$page->id}/widgets/create") ?>`;

    const formData = new FormData();
    appendCsrf(formData);
    formData.append('translations', JSON.stringify(translations));
    if (!widgetId) formData.append('widget_type_key', widgetTypeKey);

    fetch(url, {
        method: 'POST',
        headers: withCsrfHeaders(),
        body: formData,
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Widget uložen');
            location.reload();
        } else {
            alert('Chyba: ' + (result.error || 'Neznámá chyba'));
        }
    })
    .catch(err => {
        alert('Chyba ukládání widgetu');
        console.error(err);
    });
}

function collectFormData(language) {
    const container = document.getElementById(`widget-form-${language}`);
    const data = {};

    // Collect simple fields
    const simpleInputs = container.querySelectorAll('[data-field-key]:not([data-field-key*="["])');
    simpleInputs.forEach(input => {
        const key = input.dataset.fieldKey;

        // Skip if this is a repeater/gallery container
        if (input.classList.contains('repeater-field') || input.classList.contains('image-gallery-field')) {
            return;
        }

        if (input.type === 'checkbox') {
            data[key] = input.checked;
        } else {
            data[key] = input.value;
        }
    });

    function escapeRegExp(s) {
        return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    function collectRepeaterItems(repeaterEl, fieldKey) {
        const out = [];
        const re = new RegExp('^' + escapeRegExp(fieldKey) + '\\[(\\d+)\\]\\.(.+)$');
        repeaterEl.querySelectorAll('.repeater-item').forEach(item => {
            const tagData = {};
            item.querySelectorAll('input[data-field-key], select[data-field-key], textarea[data-field-key]').forEach(inp => {
                const m = inp.dataset.fieldKey.match(re);
                if (!m) return;
                tagData[m[2]] = inp.type === 'checkbox' ? inp.checked : inp.value;
            });
            if (Object.keys(tagData).length > 0) out.push(tagData);
        });
        return out;
    }

    // Collect repeater fields (top-level only; nested handled per-item)
    const repeaters = container.querySelectorAll('.repeater-field');
    repeaters.forEach(repeater => {
        const fieldKey = repeater.dataset.fieldKey;
        if (fieldKey && fieldKey.includes('].')) {
            return;
        }
        const items = [];
        const matchIndex = new RegExp('^' + escapeRegExp(fieldKey) + '\\[(\\d+)\\]\\.(.+)$');

        repeater.querySelectorAll(':scope > .repeater-items > .repeater-item').forEach(item => {
            const itemData = {};
            const directInputs = item.querySelectorAll('input[data-field-key], select[data-field-key], textarea[data-field-key]');
            directInputs.forEach(input => {
                const m = input.dataset.fieldKey.match(matchIndex);
                if (!m) return;
                const subfieldKey = m[2];
                if (subfieldKey.includes('[')) return;
                const nest = item.querySelector('.repeater-field[data-field-key^="' + fieldKey + '[' + m[1] + ']."]');
                if (nest && input.closest('.repeater-field') === nest) return;
                itemData[subfieldKey] = input.type === 'checkbox' ? input.checked : input.value;
            });

            item.querySelectorAll(':scope > .form-group-repeater-nested .repeater-field').forEach(nestedRepeater => {
                const nk = nestedRepeater.dataset.fieldKey;
                if (!nk || !nk.startsWith(fieldKey + '[')) return;
                const prefix = nk.replace(/\.[^.]+$/, '');
                const nestedKey = nk.slice(prefix.length + 1);
                const nestedKeyBase = nestedKey.replace(/\[\d+\]$/, '');
                const collected = collectRepeaterItems(nestedRepeater, nk);
                itemData[nestedKeyBase] = collected;
            });

            if (Object.keys(itemData).length > 0) {
                items.push(itemData);
            }
        });

        data[fieldKey] = items;
    });

    // Collect image gallery fields
    const galleries = container.querySelectorAll('.image-gallery-field');
    galleries.forEach(gallery => {
        const fieldKey = gallery.dataset.fieldKey;
        const images = [];

        gallery.querySelectorAll('.gallery-image-item input').forEach(input => {
            if (input.value.trim()) {
                images.push(input.value.trim());
            }
        });

        data[fieldKey] = images;
    });

    return data;
}

// Delete widget
window.deleteWidget = function(widgetId) {
    if (!confirm('Opravdu chcete smazat tento widget?')) return;

    const formData = new FormData();
    appendCsrf(formData);

    fetch(`<?= adminUrl('widgets/') ?>${widgetId}/delete`, {
        method: 'POST',
        headers: withCsrfHeaders(),
        body: formData,
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Widget smazán');
            location.reload();
        } else {
            alert('Chyba: ' + (result.error || 'Neznámá chyba'));
        }
    })
    .catch(err => {
        alert('Chyba mazání widgetu');
        console.error(err);
    });
}

// Move widget up/down
window.moveWidgetUp = function(widgetId) {
    moveWidget(widgetId, 'move-up');
}

window.moveWidgetDown = function(widgetId) {
    moveWidget(widgetId, 'move-down');
}

function moveWidget(widgetId, direction) {
    const formData = new FormData();
    appendCsrf(formData);

    fetch(`<?= adminUrl('widgets/') ?>${widgetId}/${direction}`, {
        method: 'POST',
        headers: withCsrfHeaders(),
        body: formData,
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Nelze přesunout widget');
        }
    })
    .catch(err => {
        alert('Chyba přesunu widgetu');
        console.error(err);
    });
}

// ============================================
// MEDIA LIBRARY
// ============================================

let mediaLibraryCallback = null; // Callback when image is selected

// Open media library with callback
window.openMediaLibrary = function(callback) {
    mediaLibraryCallback = callback;
    loadMediaLibrary();
    openModal('media-library-modal');
}

// Load media from API
function loadMediaLibrary() {
    const grid = document.getElementById('media-library-grid');
    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;"><p>Načítání médií...</p></div>';

    fetch('<?= adminUrl('media/api/all') ?>')
        .then(r => r.json())
        .then(data => {
            if (!data.media || data.media.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;"><p>Žádná média nebyla nalezena. Nahrajte první obrázek!</p></div>';
                return;
            }

            grid.innerHTML = '';
            data.media.forEach(media => {
                const item = document.createElement('div');
                item.className = 'media-library-item';
                item.style.cssText = 'cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden; transition: all 0.2s;';
                const imageUrl = media.filename || media.file_path; // Cloudinary URL
                item.innerHTML = `
                    <img src="${imageUrl}"
                         alt="${media.title_cs || ''}"
                         style="width: 100%; height: 150px; object-fit: cover; display: block;"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22150%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22150%22 height=%22150%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3EChyba%3C/text%3E%3C/svg%3E'">
                    <div style="padding: 8px; background: #f8f9fa; font-size: 12px; text-align: center;">
                        ${media.title_cs || 'Bez názvu'}
                    </div>
                `;

                item.addEventListener('click', function() {
                    if (mediaLibraryCallback) {
                        mediaLibraryCallback(imageUrl);
                        closeModal('media-library-modal');
                    }
                });

                item.addEventListener('mouseenter', function() {
                    this.style.borderColor = 'var(--admin-primary, #667eea)';
                    this.style.transform = 'scale(1.05)';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.borderColor = 'transparent';
                    this.style.transform = 'scale(1)';
                });

                grid.appendChild(item);
            });
        })
        .catch(err => {
            console.error('Error loading media:', err);
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #dc3545;"><p>Chyba načítání médií. Zkuste to znovu.</p></div>';
        });
}

// Handle file upload
document.getElementById('media-upload-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    console.log('Upload started, file:', file.name, 'CSRF token length:', csrfToken.length);

    const formData = new FormData();
    formData.append('file', file);
    appendCsrf(formData);

    const progressDiv = document.getElementById('upload-progress');
    const progressBar = document.getElementById('upload-progress-bar');
    const statusText = document.getElementById('upload-status');

    progressDiv.style.display = 'block';
    progressBar.style.width = '0%';
    statusText.textContent = 'Nahrávání...';

    fetch('<?= adminUrl('media/upload') ?>', {
        method: 'POST',
        headers: withCsrfHeaders(),
        body: formData,
        credentials: 'same-origin'
    })
    .then(r => {
        console.log('Upload response status:', r.status);
        if (!r.ok) {
            // Try to get text first in case it's not JSON
            return r.text().then(text => {
                console.log('Error response text:', text);
                try {
                    const json = JSON.parse(text);
                    throw new Error(json.error || 'Upload failed');
                } catch (parseErr) {
                    throw new Error('Server error: ' + text.substring(0, 100));
                }
            });
        }
        return r.json();
    })
    .then(result => {
        progressBar.style.width = '100%';
        progressBar.style.background = '#28a745';
        statusText.textContent = '✓ Nahrání dokončeno!';
        statusText.style.color = '#28a745';

        // Reload media library
        setTimeout(() => {
            loadMediaLibrary();
            progressDiv.style.display = 'none';
            progressBar.style.width = '0%';
            progressBar.style.background = 'var(--admin-primary, #667eea)';
            e.target.value = ''; // Reset input
        }, 1000);
    })
    .catch(err => {
        progressBar.style.background = '#dc3545';
        statusText.textContent = '✗ Chyba: ' + err.message;
        statusText.style.color = '#dc3545';
        console.error('Upload error:', err);
    });
});

})(); // End IIFE
</script>
