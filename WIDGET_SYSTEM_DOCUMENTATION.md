# WIDGET SYSTEM - Kompletní Dokumentace

## 🎯 Přehled Systému

Widgetový systém umožňuje dynamickou tvorbu stránek pomocí přetahovatelných komponent. Každá stránka může mít neomezený počet widgetů, které se renderují podle pořadí.

## 📊 Databázová Struktura

### Tabulka: `widget_types`

Registr všech dostupných typů widgetů.

```sql
CREATE TABLE widget_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_key VARCHAR(50) UNIQUE NOT NULL,    -- např. 'hero', 'blog_posts_grid'
    label VARCHAR(100) NOT NULL,              -- Lidský název pro admin
    component_path VARCHAR(255) NOT NULL,     -- Cesta k view: 'widgets/hero'
    icon VARCHAR(50),                         -- Emoji nebo třída ikony
    category ENUM('layout','content','media','dynamic') DEFAULT 'content',
    schema JSON NOT NULL,                     -- JSON schema pro formulář
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**JSON Schema Format:**
```json
{
    "fields": [
        {
            "key": "title",
            "type": "text|textarea|select|checkbox|number|repeater|image_gallery",
            "label": "Nadpis",
            "required": true|false,
            "translatable": true|false,
            "default": "...",
            "options": [{"label": "...", "value": "..."}],  // Pro select
            "fields": [...],                                 // Pro repeater (sub-fields)
            "min": 0,                                        // Pro repeater/number
            "max": 10                                        // Pro repeater/gallery
        }
    ]
}
```

### Tabulka: `page_widgets`

Instance widgetů přiřazené ke stránkám.

```sql
CREATE TABLE page_widgets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    widget_type_key VARCHAR(50) NOT NULL,     -- Foreign key na widget_types.type_key
    display_order INT NOT NULL DEFAULT 0,     -- Pořadí zobrazení
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (widget_type_key) REFERENCES widget_types(type_key)
);
```

### Tabulka: `page_widget_translations`

Překlady obsahu widgetů (CS/EN).

```sql
CREATE TABLE page_widget_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_widget_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,             -- 'cs' nebo 'en'
    settings JSON NOT NULL,                   -- Data pro tento widget v tomto jazyce

    FOREIGN KEY (page_widget_id) REFERENCES page_widgets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_translation (page_widget_id, language)
);
```

**Settings JSON Format:**
```json
{
    "title": "Text hodnota",
    "content": "HTML obsah...",
    "rotatingText": [                         // Repeater pole
        {"text": "První položka"},
        {"text": "Druhá položka"}
    ],
    "backgroundImages": [                     // Image gallery
        "/uploads/media/2026/01/image1.jpg",
        "/uploads/media/2026/01/image2.jpg"
    ],
    "overlay": true,                          // Boolean
    "height": "full"                          // Select hodnota
}
```

## 🏗️ Architektura

### Backend (PHP)

**Modely:**
- `/app/Models/WidgetType.php` - Správa typů widgetů
- `/app/Models/PageWidget.php` - CRUD operace s widget instancemi

**Controllery:**
- `/app/Controllers/Admin/WidgetAdminController.php` - AJAX API pro admin
  - `getSchema($typeKey)` - Načte JSON schema
  - `getWidget($id)` - Načte widget s translations
  - `create($pageId)` - Vytvoří nový widget
  - `update($id)` - Updatuje widget
  - `delete($id)` - Smaže widget
  - `moveUp/moveDown($id)` - Přesuny v pořadí
  - `reorder($pageId, $widgetIds)` - Hromadné přeřazení

**Views:**
- `/app/Views/widgets/{type_key}.php` - Renderování jednotlivých widgetů
- `/app/Views/admin/pages/widgets-tab.php` - Admin UI (modal, formuláře)
- `/app/Views/pages/dynamic.php` - Dynamická stránka renderující widgety

### Frontend (JavaScript)

**Klíčové Funkce v `widgets-tab.php`:**

```javascript
// Modal management
window.openModal(modalId)
window.closeModal(modalId)

// Widget selection
window.selectWidgetType(typeKey)

// Widget editing
window.editWidget(widgetId)
    -> fetch widget data
    -> fetch schema
    -> buildWidgetForm(schema, data, language)
    -> openModal('edit-widget-modal')

// Form building
buildWidgetForm(schema, data, language)
    -> loops schema.fields
    -> creates form inputs based on field.type
    -> supports: text, textarea, select, checkbox, number, repeater, image_gallery

buildRepeaterField(field, data, language)
    -> creates dynamic list of items
    -> add/remove buttons
    -> nested form fields

buildImageGalleryField(field, data)
    -> creates image grid with previews
    -> "Select/Upload" button opens Media Library

// Data collection
collectFormData(language)
    -> extracts values from form
    -> handles simple fields, repeaters, galleries
    -> returns JSON object

// Save
window.saveWidget()
    -> collects data for both CS and EN
    -> validates required fields
    -> POST to /admin/widgets/{id}/update
```

## 🎨 Field Types

### 1. `text`
Simple text input.
```json
{
    "key": "title",
    "type": "text",
    "label": "Nadpis",
    "required": true,
    "translatable": true
}
```

### 2. `textarea` / `wysiwyg`
Multi-line text. WYSIWYG adds rich text editor class.
```json
{
    "key": "content",
    "type": "wysiwyg",
    "label": "Obsah",
    "translatable": true
}
```

### 3. `select`
Dropdown selection.
```json
{
    "key": "height",
    "type": "select",
    "label": "Výška sekce",
    "options": [
        {"label": "Celá obrazovka", "value": "full"},
        {"label": "Střední", "value": "medium"}
    ],
    "default": "full"
}
```

### 4. `checkbox`
Boolean toggle.
```json
{
    "key": "overlay",
    "type": "checkbox",
    "label": "Tmavý overlay",
    "default": true
}
```

### 5. `repeater`
Array of objects with sub-fields. Supports add/remove items.
```json
{
    "key": "rotatingText",
    "type": "repeater",
    "label": "Rotující texty",
    "min": 0,
    "max": 10,
    "fields": [
        {
            "key": "text",
            "type": "text",
            "label": "Text položky",
            "required": true
        }
    ],
    "translatable": true
}
```

**Data Output:**
```json
{
    "rotatingText": [
        {"text": "První text"},
        {"text": "Druhý text"}
    ]
}
```

### 6. `image_gallery`
Array of image URLs. Opens Media Library modal for selection.
```json
{
    "key": "backgroundImages",
    "type": "image_gallery",
    "label": "Obrázky na pozadí",
    "max": 5,
    "translatable": false
}
```

**Data Output:**
```json
{
    "backgroundImages": [
        "/uploads/media/2026/01/image1.jpg",
        "/uploads/media/2026/01/image2.jpg"
    ]
}
```

## 📸 Media Library System

### Databáze

```sql
CREATE TABLE media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,           -- Cesta: /uploads/media/YYYY/MM/file.jpg
    original_name VARCHAR(255) NOT NULL,      -- Původní název souboru
    mime_type VARCHAR(100) NOT NULL,          -- image/jpeg
    file_size INT UNSIGNED NOT NULL,          -- Velikost v bytes
    width INT UNSIGNED,                       -- Šířka obrázku
    height INT UNSIGNED,                      -- Výška obrázku
    type ENUM('image','document','video') DEFAULT 'image',
    folder VARCHAR(100) DEFAULT 'general',
    uploaded_by INT UNSIGNED,                 -- User ID
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE media_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    alt_text VARCHAR(200),
    caption TEXT,

    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
);
```

### API Endpointy

**GET `/admin/media/api/all`**
Vrací všechna média pro modal.
```json
{
    "media": [
        {
            "id": 1,
            "filename": "/uploads/media/2026/01/abc123.jpg",
            "title_cs": "Název obrázku",
            "alt_cs": "Alt text",
            "width": 1920,
            "height": 1080
        }
    ]
}
```

**POST `/admin/media/upload`**
Nahraje nový soubor a **automaticky optimalizuje**.

Request: `multipart/form-data`
- `file`: File
- `csrf_token`: String

Response:
```json
{
    "success": true,
    "media_id": 123,
    "filename": "/uploads/media/2026/01/xyz789.jpg"
}
```

### 🖼️ Automatická Optimalizace

**Konzervativní přístup:**
- ✅ Max rozměr: **1920px** (Full HD)
- ✅ JPEG kvalita: **85%**
- ✅ PNG komprese: **Level 9** (max)
- ✅ WebP kvalita: **85%**
- ✅ GIF: beze změny (zachová animace)
- ✅ Zachování průhlednosti (PNG, GIF)
- ✅ Proporcionální resize (aspect ratio)

**Průměrná úspora:**
- JPEG: 30-50% velikosti
- PNG: 20-40% velikosti
- WebP: 30-50% velikosti

**Příklad:**
```
Původní: 8.99 MB (4000x3000px)
Optimalizovaný: 1.2 MB (1920x1440px)
Úspora: 86.7%
```

**Debug logy (development mode):**
```
Image optimized: photo.jpg
  Original: 8990.4 KB
  Optimized: 1228.7 KB
  Saved: 7761.7 KB (86.3%)
  Dimensions: 1920x1440
```

### JavaScript API

```javascript
// Open media library with callback
window.openMediaLibrary(function(filePath) {
    console.log('Selected image:', filePath);
    // Use the selected image path
});

// Load media from API
loadMediaLibrary();
    -> fetches /admin/media/api/all
    -> renders grid
    -> attaches click handlers

// Upload handling
document.getElementById('media-upload-input').addEventListener('change', ...)
    -> FormData with file + CSRF token
    -> POST to /admin/media/upload
    -> Shows progress bar
    -> Reloads library on success
```

## 🔄 Routing

### Admin Routes (Manual Routing)

Admin routes **NEMAJÍ** jazykový prefix a jsou zpracovány **manuálně** v `/public/index.php`:

```php
// Widget API
if (preg_match('#^/admin/widgets/schema/([a-z_]+)$#', $requestUri, $matches)) {
    $controller = new \App\Controllers\Admin\WidgetAdminController();
    $controller->getSchema($matches[1]);
    exit;
}

if (preg_match('#^/admin/widgets/(\d+)$#', $requestUri, $matches) && $method === 'GET') {
    $controller = new \App\Controllers\Admin\WidgetAdminController();
    $controller->getWidget((int)$matches[1]);
    exit;
}

// ... další widget routes

// Media API
if ($requestUri === '/admin/media/api/all' && $method === 'GET') {
    $controller = new \App\Controllers\Admin\MediaAdminController();
    $controller->getAll();
    exit;
}

if ($requestUri === '/admin/media/upload' && $method === 'POST') {
    $controller = new \App\Controllers\Admin\MediaAdminController();
    $controller->upload();
    exit;
}
```

**Proč manuální routing?**
- Router je navržen JEN pro routes s jazykem (`/cs/blog`, `/en/contact`)
- Admin routes nemají jazyk → Router by selhal
- Manuální routing = explicitní, debugovatelný, jednoduchý

### Frontend Routes (Router)

Frontend routes mají jazykový prefix a jsou zpracovány `Router::dispatch()`:

```php
// config/routes.php
$router->get('/', 'HomeController@index');
$router->get('/:slug', 'PageController@show');
```

## 🎬 Workflow: Přidání Nového Widgetu

### 1. Definice v databázi

```sql
INSERT INTO widget_types (type_key, label, component_path, icon, category, schema) VALUES (
    'testimonials',
    'Testimonials (hodnocení)',
    'widgets/testimonials',
    '💬',
    'content',
    '{
        "fields": [
            {
                "key": "title",
                "type": "text",
                "label": "Nadpis sekce",
                "required": true,
                "translatable": true
            },
            {
                "key": "items",
                "type": "repeater",
                "label": "Hodnocení",
                "min": 1,
                "max": 6,
                "fields": [
                    {"key": "name", "type": "text", "label": "Jméno", "required": true},
                    {"key": "rating", "type": "number", "label": "Hodnocení (1-5)", "min": 1, "max": 5},
                    {"key": "text", "type": "textarea", "label": "Text hodnocení", "required": true}
                ],
                "translatable": true
            }
        ]
    }'
);
```

### 2. Vytvoření View

`/app/Views/widgets/testimonials.php`:

```php
<?php
$settings = $widget['settings'] ?? [];
$title = $settings['title'] ?? '';
$items = $settings['items'] ?? [];
?>

<section class="testimonials">
    <div class="container">
        <h2><?= e($title) ?></h2>

        <div class="testimonials-grid">
            <?php foreach ($items as $item): ?>
                <div class="testimonial-card">
                    <div class="rating">
                        <?php for ($i = 0; $i < ($item['rating'] ?? 5); $i++): ?>⭐<?php endfor; ?>
                    </div>
                    <p><?= e($item['text'] ?? '') ?></p>
                    <strong><?= e($item['name'] ?? '') ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
```

### 3. Použití

1. Admin otevře `/admin/pages/1/edit`
2. Klikne na tab "Widgety"
3. Klikne "+ Přidat widget"
4. Vybere "Testimonials"
5. Vyplní formulář (automaticky generovaný ze schema)
6. Uloží
7. Widget se zobrazí na homepage

## 🔧 Helper Funkce

`/app/Helpers/widgets.php`:

```php
// Renderuje všechny widgety pro stránku
function renderWidgets(array $widgets): void

// Renderuje jeden widget
function renderWidget(object $widget): void

// Načte settings z JSON pro daný jazyk
function getWidgetSettings(object $widget, string $language): array
```

## 🎯 Klíčové Soubory

### Backend
```
/app/Models/WidgetType.php           - Model pro widget types
/app/Models/PageWidget.php           - Model pro widget instances
/app/Controllers/Admin/WidgetAdminController.php
/app/Controllers/Admin/MediaAdminController.php
/app/Helpers/widgets.php
/app/Views/widgets/*.php             - Widget view templates
/app/Views/admin/pages/widgets-tab.php - Admin UI
/app/Views/pages/dynamic.php        - Dynamická stránka
```

### Frontend
```
/public/assets/css/admin.css         - Admin styly (včetně modal, repeater, gallery)
/public/assets/js/admin.js           - Základní admin JS
```

### Config
```
/public/index.php                    - Manual routing pro admin
/config/routes.php                   - Router routes pro frontend
/config/config.local.php             - Local overrides (BASE_URL, DB credentials)
```

## 🐛 Debugging

### Browser Console Logs
```javascript
console.log('editWidget called with ID:', widgetId);
console.log('Widget data:', widget);
console.log('Schema data:', schema);
console.log('Form built for', language, 'container has', container.children.length, 'elements');
```

### PHP Error Log
```bash
tail -f /Applications/MAMP/logs/php_error.log
```

### Test Endpoints
```bash
# Get widget schema
curl http://localhost:8888/labyrint/admin/widgets/schema/hero

# Get widget data
curl http://localhost:8888/labyrint/admin/widgets/2

# Get all media
curl http://localhost:8888/labyrint/admin/media/api/all
```

## ⚠️ Důležité Poznámky

1. **Modal Taby**: Používají `.modal-tab-btn` a `.modal-tab-content` (NE `.tab-btn`!)
   - Důvod: Hlavní stránka má své taby, modal musí mít jiné třídy

2. **BASE_URL**: Musí být nastaveno v `/config/config.local.php`
   - Pro localhost: `http://localhost:8888/labyrint`
   - AJAX requesty by jinak šly na produkční URL!

3. **CSRF Token**: Generuje se pro celou session, ne per-request
   - `Session::generateCSRFToken()` (NE `getCSRFToken()`!)

4. **Image Paths**: Mohou být relativní (`/uploads/...`) nebo absolutní (`http://...`)
   - JavaScript automaticky přidá BASE_URL pokud je path relativní

5. **Database Columns**:
   - `media.filename` (NE file_path)
   - `media_translations.caption` (NE title)
   - `page_widgets.display_order` (NE sort_order)

## 🚀 Performance Tips

- Widgety se cachují pomocí `renderWidgets()` helper funkce
- JSON schema se načítá jen při editaci (ne při každém page load)
- Media library se načítá on-demand (ne při načtení stránky)
- Obrázky se ukládají do `/uploads/media/YYYY/MM/` pro lepší organizaci
- **Automatická optimalizace při uploadu** (resize + komprese)

## 📋 PHP Requirements

**GD Library** (pro optimalizaci obrázků):
```bash
php -m | grep -i gd
# Mělo by ukázat: gd
```

**Podporované formáty:**
- `imagecreatefromjpeg()` - JPEG
- `imagecreatefrompng()` - PNG (s alpha kanálem)
- `imagecreatefromgif()` - GIF (včetně animací)
- `imagecreatefromwebp()` - WebP

**PHP Limity:**
```ini
upload_max_filesize = 32M
post_max_size = 50M
memory_limit = 256M  # Pro zpracování velkých obrázků
```

---

**Naposledy aktualizováno:** 21. ledna 2026
**Verze:** 2.1 (s Media Library + Image Optimization)
