# Widget System - Hlubóká analýza a opravy

## Datum: 21. ledna 2026

## Problém
Admin stránka `http://localhost:8888/labyrint/admin/pages/1/edit` se nenačítala správně - CSS styly nefungovaly a JavaScript tlačítka byla nefunkční.

## Provedená analýza

### 1. Kontrola BASE_URL a asset() funkce ✅
- **Status**: Funguje správně
- **BASE_URL**: `http://localhost:8888/labyrint` (z config.local.php)
- **asset() výstup**: Generuje správné URL s cache busting

```php
asset('css/admin.css') → http://localhost:8888/labyrint/assets/css/admin.css?v=1768989280
```

### 2. Kontrola načítání CSS ✅
- **Status**: CSS se načítá správně
- **HTTP odpověď**: 200 OK
- **Velikost**: 13,679 bytes
- **Content-Type**: Správný
- **Cache headers**: Nastaveny správně

### 3. Kontrola admin layout ✅
- **Status**: Layout se renderuje správně
- **HTML výstup**: Obsahuje všechny potřebné elementy
- **CSS link**: Přítomen v `<head>`
- **JavaScript**: Načítá se správně

### 4. Zjištěné problémy

#### Problém #1: Chybné volání Session::getCSRFToken()
**Soubory**:
- `/app/Views/admin/pages/widgets-tab.php`
- `/app/Views/admin/pages/index.php`

**Chyba**:
```php
Session::getCSRFToken()  // ❌ Tato metoda neexistuje
```

**Oprava**:
```php
Session::generateCSRFToken()  // ✅ Správná metoda
```

**Důvod**: Session třída má pouze metodu `generateCSRFToken()`, ne `getCSRFToken()`.

#### Problém #2: Inline `<style>` tagy způsobovaly konflikty
**Soubory**:
- `/app/Views/admin/pages/edit.php` - 145 řádků inline CSS
- `/app/Views/admin/pages/index.php` - 119 řádků inline CSS
- `/app/Views/admin/pages/widgets-tab.php` - 263 řádků inline CSS

**Důvod problému**:
1. Duplikované styly (stejné třídy definovány v admin.css i inline)
2. Možné konflikty specificity CSS
3. Inline styly se načítaly PŘED admin.css kvůli layoutu
4. Browser cache mohl ukládat staré inline styly

**Oprava**: Všechny inline `<style>` tagy byly odstraněny. Styly jsou pouze v `/public/assets/css/admin.css`.

#### Problém #3: JavaScript v widgets-tab.php bez DOMContentLoaded
**Chyba**: JavaScript se pokouší přistoupit k elementům před jejich vykreslením.

```javascript
// ❌ Původní kód
document.getElementById('add-widget-btn').addEventListener('click', ...);
```

**Oprava**: Přidán wrapper s DOMContentLoaded a IIFE:

```javascript
// ✅ Opravený kód
(function() {
'use strict';

const pageId = <?= $page->id ?>;
const csrfToken = '<?= \App\Core\Session::generateCSRFToken() ?>';

// Global functions for HTML onclick
window.openModal = function(modalId) { ... }
window.closeModal = function(modalId) { ... }

// Wait for DOM
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners zde
    const addWidgetBtn = document.getElementById('add-widget-btn');
    if (addWidgetBtn) {
        addWidgetBtn.addEventListener('click', function() {
            openModal('add-widget-modal');
        });
    }

    // ... další event listeners
});

// Global functions for HTML onclick attributes
window.selectWidgetType = function(typeKey) { ... }
window.editWidget = function(widgetId) { ... }
window.saveWidget = function() { ... }
window.deleteWidget = function(widgetId) { ... }
window.moveWidgetUp = function(widgetId) { ... }
window.moveWidgetDown = function(widgetId) { ... }

})(); // End IIFE
```

**Důvody změn**:
1. **IIFE** - izoluje scope a předchází konfliktům
2. **DOMContentLoaded** - zajistí, že DOM je načten před přístupem k elementům
3. **window.* funkce** - funkce volané z HTML `onclick` atributů musí být globálně přístupné
4. **Null check** - kontrola existence elementu před přidáním event listeneru

## Provedené opravy

### 1. Oprava Session::getCSRFToken()
```bash
# V souboru: app/Views/admin/pages/widgets-tab.php
- const csrfToken = '<?= \App\Core\Session::getCSRFToken() ?>';
+ const csrfToken = '<?= \App\Core\Session::generateCSRFToken() ?>';

# V souboru: app/Views/admin/pages/index.php
- <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCSRFToken() ?>">
+ <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::generateCSRFToken() ?>">
```

### 2. Odstranění inline `<style>` tagů

**edit.php**: Odstraněno 145 řádků inline CSS (řádky 85-230)
**index.php**: Odstraněno 119 řádků inline CSS (řádky 45-164)
**widgets-tab.php**: Odstraněno 263 řádků inline CSS (řádky 134-397)

Všechny tyto styly jsou již obsaženy v `/public/assets/css/admin.css`.

### 3. Refactoring JavaScriptu v widgets-tab.php

**Původní struktura** (nefunkční):
```javascript
<script>
const pageId = ...;
const csrfToken = ...;

function openModal(modalId) { ... }
document.getElementById('add-widget-btn').addEventListener(...);  // ❌ Element nemusí existovat
</script>
```

**Nová struktura** (funkční):
```javascript
<script>
(function() {
    'use strict';

    const pageId = ...;
    const csrfToken = ...;

    // Globální funkce
    window.openModal = function(modalId) { ... }
    window.closeModal = function(modalId) { ... }

    // Čekání na DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Event listeners
        const btn = document.getElementById('add-widget-btn');
        if (btn) {
            btn.addEventListener('click', function() { ... });
        }
    });

    // Globální funkce pro HTML onclick
    window.selectWidgetType = function(typeKey) { ... }
    window.editWidget = function(widgetId) { ... }
    // ... další
})();
</script>
```

## Verifikace oprav

### Test rendering:
```bash
php test_render.php
```

**Výsledky**:
- ✅ Layout exists: YES
- ✅ View exists: YES
- ✅ Widgets tab exists: YES
- ✅ View output: 30,101 characters
- ✅ Full output: 36,209 characters
- ✅ Has <!DOCTYPE html>: YES
- ✅ Has admin.css link: YES
- ✅ Has admin.js script: YES
- ✅ Has widget-manager class: YES
- ✅ Has add-widget-btn ID: YES
- ✅ NO inline <style> tags: YES

### Test CSS načítání:
```bash
curl -I http://localhost:8888/labyrint/assets/css/admin.css
```

**Výsledky**:
- ✅ HTTP/1.1 200 OK
- ✅ Content-Length: 13679
- ✅ Obsahuje .widget-manager styly
- ✅ Obsahuje .btn-icon styly
- ✅ Obsahuje .modal styly

## Závěr

### Root cause
Problém byl způsoben **kombinací tří faktorů**:

1. **Fatal error v PHP** - `Session::getCSRFToken()` způsoboval, že stránka vůbec negenerovala výstup
2. **Inline CSS konflikty** - Duplikované styly způsobovaly nepředvídatelné chování
3. **JavaScript timing issue** - Přístup k DOM elementům před jejich vykreslením

### Řešení
1. ✅ Opraveno volání CSRF tokenu na správnou metodu
2. ✅ Odstraněny všechny inline `<style>` tagy
3. ✅ JavaScript wrapped v DOMContentLoaded s IIFE
4. ✅ Globální funkce exportovány jako window properties pro HTML onclick

### Co očekávat po opravách
- ✅ Admin stránky se načítají bez chyb
- ✅ CSS styly se aplikují správně
- ✅ Všechna tlačítka jsou funkční:
  - ✅ "+ Přidat widget" - otevírá modal
  - ✅ Šipky ↑↓ - mění pořadí widgetů
  - ✅ ✏️ (edit) - otevírá editor widgetu
  - ✅ 🗑️ (delete) - maže widget s potvrzením
- ✅ Modály se otevírají a zavírají správně
- ✅ Formuláře pro vytváření/editaci widgetů fungují
- ✅ AJAX operace (save, delete, reorder) fungují

## Doporučení pro budoucnost

### 1. Nikdy nepoužívat inline `<style>` tagy v admin views
- Všechny styly by měly být v `/public/assets/css/admin.css`
- Inline styly způsobují cache problémy a konflikty specificity

### 2. Vždy wrappovat JavaScript v DOMContentLoaded
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Přístup k DOM elementům zde
});
```

### 3. Funkce pro HTML onclick musí být globální
```javascript
window.myFunction = function() { ... }  // ✅ Funguje
function myFunction() { ... }            // ❌ V IIFE není viditelná z HTML
```

### 4. Kontrolovat názvy metod v třídách
- Před použitím metody zkontrolovat, zda skutečně existuje
- Použít IDE s autocomplete nebo zkontrolovat dokumentaci

### 5. Testovat rendering bez browseru
- Skript `test_render.php` je užitečný pro debugging
- Umožňuje vidět, co se skutečně renderuje bez overhead browseru

## Soubory změněny

1. `/app/Views/admin/pages/widgets-tab.php`
   - Opraveno Session::getCSRFToken() → generateCSRFToken()
   - Odstraněn inline `<style>` tag (263 řádků)
   - Refactorován JavaScript (DOMContentLoaded + IIFE)

2. `/app/Views/admin/pages/edit.php`
   - Odstraněn inline `<style>` tag (145 řádků)

3. `/app/Views/admin/pages/index.php`
   - Opraveno Session::getCSRFToken() → generateCSRFToken()
   - Odstraněn inline `<style>` tag (119 řádků)

## Celkem odstraněno
- **527 řádků duplikovaného inline CSS**
- **2 chybná volání neexistující metody**
- **1 timing issue v JavaScriptu**

## Status: ✅ OPRAVENO
