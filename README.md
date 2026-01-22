# Škola Labyrint - Website

Moderní školní web s vlastním MVC frameworkem, multijazyčností (CS/EN) a admin panelem.

## 🎯 Projekt Info

- **Hosting**: Wedos (levný hosting pod 100 Kč/měsíc)
- **Tech Stack**: PHP 8+ (bez frameworků), MySQL, vanilla CSS/JS
- **Jazyk Admin**: Pouze čeština (bez jazykového routingu)
- **Jazyky Frontend**: CS + EN (automatická detekce z URL)

## 📊 Aktuální Stav (Leden 2026)

### ✅ Co Funguje

**Core Framework:**
- ✅ Custom MVC architektura (Router, Controller, Model, View)
- ✅ PSR-4 autoloading (fallback bez Composeru)
- ✅ Database layer (PDO, prepared statements)
- ✅ Session management (bezpečné, CSRF ochrana)
- ✅ I18n systém (překládání z databáze + cache)

**Frontend:**
- ✅ Homepage (multijazyčná) s novým Hygge designem
  - Hero sekce s rotujícím textem a fotografií na pozadí
  - Dvousloupcový layout (text + YouTube video)
  - Feature cards sekce (4 barevné karty)
- ✅ URL routing s jazykovým prefixem (/cs/, /en/)
- ✅ Automatické přesměrování root na jazyk
- ✅ Helper funkce (url(), asset(), adminUrl(), e(), __(), atd.)
- ✅ Widget systém (znovupoužitelné komponenty)

**Admin Panel:**
- ✅ Login/logout systém (bez jazykových parametrů)
- ✅ Dashboard se statistikami
- ✅ Přístup na `/admin` (BEZ /cs/ prefixu)
- ✅ Auto-redirect z `/cs/admin` na `/admin`
- ✅ **Theme Settings** (`/admin/theme`) - Centralizovaná správa designu
  - Color picker pro všechny barvy
  - Editace typografie (fonty, velikosti, váhy)
  - Spacing & layout (border-radius, padding, margins)
  - Efekty (shadows, transitions, gradienty)
  - Export/Import nastavení jako JSON
  - Auto-generování CSS souboru

**Database:**
- ✅ Kompletní schema (15 tabulí - včetně theme_settings)
- ✅ Seed data (admin user, kategorie, překlady, theme settings)
- ✅ Extended i18n translations (50+ klíčů)
- ✅ Theme settings (80+ design parametrů)

**Design System:**
- ✅ CSS Custom Properties (všechny barvy, fonty, spacing editovatelné z adminu)
- ✅ Auto-generovaný `theme.css` z databáze
- ✅ Hygge/Severský barevný styl (přírodní, pastelové tóny)
- ✅ Flat design (16px border-radius)
- ✅ Responsive (768px breakpoint)
- ✅ Widget komponenty (hero, text-block, video-section, cards)

### ⚠️ Co Ještě Není Hotové

**Controllers (chybí):**
- ❌ EventController
- ❌ ContactController
- ❌ GalleryController
- ❌ SitemapController
- ❌ Admin CRUD controllery (BlogAdmin, PageAdmin, EventAdmin, MediaAdmin, I18nAdmin, ContactAdmin)

**Views (chybí):**
- ❌ Blog templates (index, show, category)
- ❌ Event templates
- ❌ Contact form
- ❌ Gallery templates
- ❌ Static page template

**Services (chybí):**
- ❌ ImageProcessor
- ❌ FileUploader
- ❌ EmailService
- ❌ SEO service

**Ostatní:**
- ❌ CSS/JS assets nemusí být kompletní
- ❌ Real design (momentálně placeholder gradient theme)

## 🛠️ Local Development Setup (MAMP)

### Prerekvizity

- **MAMP** nainstalovaný a spuštěný
- **Apache port**: 8888
- **MySQL port**: 8889
- **PHP verze**: 8.x

### Database Setup

```bash
# MySQL přes MAMP (full path)
/Applications/MAMP/Library/bin/mysql80/bin/mysql \
  --socket=/Applications/MAMP/tmp/mysql/mysql.sock \
  -u root -proot

# Vytvoření databáze
CREATE DATABASE labyrint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import schema
/Applications/MAMP/Library/bin/mysql80/bin/mysql \
  --socket=/Applications/MAMP/tmp/mysql/mysql.sock \
  -u root -proot labyrint < database/schema.sql

# Import extended translations (DŮLEŽITÉ!)
/Applications/MAMP/Library/bin/mysql80/bin/mysql \
  --socket=/Applications/MAMP/tmp/mysql/mysql.sock \
  -u root -proot labyrint < database/i18n_extended.sql
```

### Apache Konfigurace

**Důležité změny v `/Applications/MAMP/conf/apache/httpd.conf`:**

```apache
# mod_rewrite MUSÍ BÝT povolený
LoadModule rewrite_module modules/mod_rewrite.so
```

Po změně: **Restart MAMP servers!**

### Local Config

Soubor: `/config/config.local.php`

```php
<?php
return [
    'ENVIRONMENT' => 'development',
    'BASE_URL' => 'http://localhost:8888/labyrint',
    'DB_HOST' => 'localhost:8889',  // MAMP MySQL port!
    'DB_NAME' => 'labyrint',
    'DB_USER' => 'root',
    'DB_PASS' => 'root',
    'CONTACT_EMAIL' => 'developer@localhost',
];
```

### URLs

- **Frontend (CS)**: http://localhost:8888/labyrint/cs/
- **Frontend (EN)**: http://localhost:8888/labyrint/en/
- **Admin Login**: http://localhost:8888/labyrint/admin/login
- **Admin Dashboard**: http://localhost:8888/labyrint/admin

⚠️ **POZOR**: Admin URL NESMÍ obsahovat `/cs/` nebo `/en/`!

### Přihlašovací údaje

**Admin Panel:**
- Username: `admin`
- Password: `admin123`

## 🏗️ Architektura

### Project Structure

```
/Users/karellycka/weby/labyrint/
├── app/
│   ├── Controllers/
│   │   ├── Admin/              # Admin controllers (BEZ language parametru!)
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ThemeController.php    # ⭐ Theme management
│   │   ├── BlogController.php
│   │   ├── HomeController.php
│   │   └── PageController.php
│   ├── Core/
│   │   ├── Controller.php      # Base controller (default lang='cs')
│   │   ├── Database.php        # PDO singleton
│   │   ├── Model.php           # Base model
│   │   ├── Router.php          # URL routing
│   │   └── Session.php         # Session + CSRF
│   ├── Helpers/
│   │   ├── functions.php       # url(), adminUrl(), e(), __(), atd.
│   │   └── widgets.php         # ⭐ Widget helpers (renderButton, renderCard, etc.)
│   ├── Models/
│   │   ├── Blog.php
│   │   ├── Event.php
│   │   ├── Media.php
│   │   ├── Page.php
│   │   ├── ThemeSettings.php   # ⭐ Theme model
│   │   └── User.php
│   ├── Services/
│   │   ├── I18n.php            # Překlady z DB + cache
│   │   └── ThemeService.php    # ⭐ CSS generování z DB
│   └── Views/
│       ├── components/         # ⭐ Znovupoužitelné widgety
│       │   ├── hero.php
│       │   ├── text-block.php
│       │   └── video-section.php
│       ├── layouts/
│       │   ├── main.php        # Frontend layout (používá adminUrl()!)
│       │   └── admin.php       # Admin layout
│       ├── pages/
│       │   └── home.php        # ⭐ Nový Hygge design
│       ├── admin/
│       │   ├── login.php
│       │   ├── dashboard.php
│       │   └── theme/
│       │       └── index.php   # ⭐ Theme settings admin
│       └── errors/
│           └── 404.php
├── config/
│   ├── config.php              # Main config (loaduje local jako první!)
│   ├── config.local.php        # Local overrides (gitignored)
│   ├── database.php
│   └── routes.php
├── database/
│   ├── schema.sql              # Kompletní schema + seed data
│   ├── i18n_extended.sql       # Extended translations
│   └── theme_settings.sql      # ⭐ Theme settings (barvy, fonty, spacing)
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── theme.css       # ⭐ AUTO-GENEROVANÝ z DB (NEKOPÍROVAT do gitu!)
│   │   │   ├── main.css        # ⭐ Používá CSS custom properties z theme.css
│   │   │   └── admin.css
│   │   ├── js/
│   │   │   ├── text-rotator.js # ⭐ Animace měnícího se textu v hero
│   │   │   ├── mobile-menu.js  # ⭐ Hamburger menu toggle
│   │   │   ├── main.js
│   │   │   └── admin.js
│   │   └── images/
│   │       ├── logo/
│   │       │   └── labyrint_rc_basic.svg  # ⭐ Logo školy
│   │       └── hero/
│   │           └── hero-bg.jpg # ⭐ Hero pozadí
│   ├── uploads/                # User uploads
│   ├── .htaccess               # RewriteBase /labyrint/
│   └── index.php               # Front controller (admin routing zde!)
└── storage/
    ├── cache/                  # I18n cache, page cache, theme cache
    └── logs/
```

### Routing Flow

**Frontend routes (s jazykem):**
```
/cs/                    → HomeController@index
/cs/blog                → BlogController@index
/cs/blog/:slug          → BlogController@show
/en/                    → HomeController@index (EN)
```

**Admin routes (BEZ jazyka):**
```
/admin                  → DashboardController@index()
/admin/login            → AuthController@showLogin() / login()
/admin/logout           → AuthController@logout()
/cs/admin/*             → Auto-redirect na /admin/*
```

## 🔑 Důležitá Rozhodnutí

### 1. Admin Bez Jazykového Routingu

**Rozhodnutí**: Admin sekce je pouze v češtině, nemá jazykové verze.

**Implementace**:
- Admin controller metody **NEMAJÍ** parametr `string $language`
- Admin layout používá `adminUrl()` místo `url('admin/...', 'cs')`
- Routing v `index.php` je speciální - admin se zpracovává PŘED main routerem
- Auto-redirect: `/cs/admin` → `/admin`

**Proč**:
- Admin je jen pro české adminy
- Jednodušší kód (méně parametrů)
- Méně chyb
- Konzistentnější

### 2. Config Loading

**Problém**: PHP konstanty nelze předefinovat.

**Řešení**: `config.local.php` se načítá **PŘED** definováním konstant v `config.php`:

```php
// config.php
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

define('BASE_URL', $localConfig['BASE_URL'] ?? 'https://labyrint.cz');
```

### 3. Subdirectory Installation

Project běží v subdirectory `/labyrint/`:

- **BASE_URL**: `http://localhost:8888/labyrint`
- **RewriteBase** v `.htaccess`: `/labyrint/`
- **Router** odstraňuje `/labyrint` prefix před zpracováním
- **index.php** odstraňuje `/labyrint` prefix hned na začátku

### 4. Theme Management System (Centralizované Styly)

**⚠️ KRITICKY DŮLEŽITÉ PRO VŠECHNY CLAUDE INSTANCE ⚠️**

**Rozhodnutí**: Všechny design parametry (barvy, fonty, spacing, efekty) jsou v databázi a editovatelné z admin panelu.

**Implementace**:
- **Tabulka `theme_settings`** - 80+ parametrů v databázi
- **Auto-generovaný CSS** - `/public/assets/css/theme.css` (NEKOPÍROVAT do gitu)
- **CSS Custom Properties** - Vše používá `var(--color-primary)` místo hardcoded hodnot
- **Admin Panel** - `/admin/theme` pro editaci všech parametrů

**Barevná paleta (Hygge/Severský styl)**:
- Primární zelená: `var(--color-primary)` = `#00792E`
- Hnědá (písková): `var(--color-brown)` = `#9C8672`
- Žlutá (medová): `var(--color-yellow)` = `#D4A574`
- Modrá (fjordová): `var(--color-blue)` = `#8BA5B2`
- Červená (terakota): `var(--color-red)` = `#B8664D`
- Tmavě šedá: `var(--color-secondary)` = `#2C323A`

**‼️ PRAVIDLA PRO PSANÍ CSS ‼️**:

❌ **NIKDY NEPOUŽÍVAT HARDCODED HODNOTY**:
```css
/* ❌ ŠPATNĚ - hardcoded barva */
.button {
    background: #00792E;
    padding: 16px;
    border-radius: 16px;
}
```

✅ **VŽDY POUŽÍVAT CSS CUSTOM PROPERTIES**:
```css
/* ✅ SPRÁVNĚ - CSS proměnné z theme.css */
.button {
    background: var(--color-primary);
    padding: var(--spacing-md);
    border-radius: var(--border-radius);
}
```

**Dostupné CSS proměnné**:
- **Barvy**: `--color-primary`, `--color-brown`, `--color-yellow`, `--color-blue`, `--color-red`, `--color-text`, `--color-bg-light`, atd.
- **Typografie**: `--font-family-base`, `--font-size-base`, `--font-size-h1` až `--font-size-h6`, `--font-weight-bold`, atd.
- **Spacing**: `--spacing-xs`, `--spacing-sm`, `--spacing-md`, `--spacing-lg`, `--spacing-xl`, `--spacing-2xl`, `--spacing-3xl`, `--spacing-4xl`
- **Layout**: `--container-width`, `--border-radius`, `--border-radius-sm`, `--border-radius-lg`
- **Efekty**: `--shadow-sm`, `--shadow-md`, `--shadow-lg`, `--transition-speed-normal`, atd.

**Fallback hodnoty**:
Vždy použít fallback pro případ, že theme.css není načten:
```css
color: var(--color-primary, #00792E);
```

**Jak změnit barvy/styly**:
1. Jdi na `/admin/theme`
2. Uprav hodnoty ve formuláři
3. Klikni "Uložit změny a regenerovat CSS"
4. Automaticky se vygeneruje nový `theme.css`

**Widget System**:
- **Komponenty**: `/app/Views/components/` (hero.php, text-block.php, video-section.php)
- **Helper funkce**: `/app/Helpers/widgets.php` (renderButton(), renderCard(), renderTwoColumn(), atd.)
- Všechny widgety používají CSS proměnné

**Responsive Design**:
- **Breakpoint**: 768px (mobile < 768px, tablet/desktop >= 768px)
- Mobile-first přístup
- Používat `var(--section-padding-mobile)` a `var(--section-padding-desktop)`

## 🐛 Known Issues & Fixes

### Issue 1: mod_rewrite Not Enabled

**Error**: "Invalid command 'RewriteEngine'"

**Fix**:
```bash
# Edit httpd.conf
nano /Applications/MAMP/conf/apache/httpd.conf

# Uncomment this line:
LoadModule rewrite_module modules/mod_rewrite.so

# Restart MAMP
```

### Issue 2: .htaccess <Directory> Not Allowed

**Error**: "<Directory not allowed here"

**Fix**: Změnit na `<FilesMatch>`:
```apache
# ŠPATNĚ:
<Directory "uploads">
    <FilesMatch "\.php$">
        Deny from all
    </FilesMatch>
</Directory>

# SPRÁVNĚ:
<FilesMatch "^uploads/.*\.php$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

### Issue 3: Session se neukládá po loginu

**Problém**: Duplicitní `session_start()` v `index.php`.

**Fix**: Odstranit první `session_start()`, nechat jen `Session::start()`.

### Issue 4: BASE_URL se nepřepisovalo

**Problém**: `config.local.php` se načítal PO definování konstanty.

**Fix**: Načíst local config PŘED `define()`.

## 📝 Helper Functions

```php
// URL generation
url('blog')                    // http://localhost:8888/labyrint/cs/blog
url('blog', 'en')              // http://localhost:8888/labyrint/en/blog
adminUrl()                     // http://localhost:8888/labyrint/admin
adminUrl('blog')               // http://localhost:8888/labyrint/admin/blog

// Assets
asset('css/main.css')          // /assets/css/main.css?v=timestamp
upload('image.jpg')            // /uploads/image.jpg

// Translations
__('nav.home')                 // Překládá z DB podle aktuálního jazyka
__('welcome', ['name' => 'Jan'])  // S parametry: "Vítejte, Jan!"

// HTML
e($string)                     // htmlspecialchars()
csrfField()                    // <input type="hidden" name="csrf_token" ...>

// Auth
isLoggedIn()                   // bool
hasRole('admin')               // bool
userId()                       // ?int

// Utilities
generateSlug($text, 'cs')      // 'muj-clanek'
formatDate($date)              // '20.01.2026'
truncate($text, 100)           // 'Text...'

// Widget Helpers (z widgets.php)
renderButton($text, $url, 'primary')                    // Renderuje tlačítko
renderCard($data, ['shadow' => true])                   // Renderuje kartu
renderTwoColumn($left, $right, ['gap' => 'lg'])        // Dva sloupce
renderYouTubeVideo($videoId, ['aspectRatio' => '16:9']) // YouTube embed
renderSection($data, ['class' => 'bg-light'])          // Sekce s obsahem
```

## 🚀 Next Steps (TODO)

### High Priority
1. ✅ ~~Fix admin routing (BEZ jazyka)~~
2. ✅ ~~Fix URL generation helpers~~
3. ✅ ~~Fix BASE_URL config~~
4. ⏭️ Vytvořit BlogController views (index, show)
5. ⏭️ Vytvořit PageController logic (načítání ze DB)
6. ⏭️ Vytvořit Admin CRUD pro blog posty

### Medium Priority
7. ⏭️ EventController + views
8. ⏭️ ContactController + form
9. ⏭️ GalleryController + views
10. ⏭️ Admin CRUD pro pages, events, media

### Low Priority
11. ⏭️ ImageProcessor service (resize, WebP)
12. ⏭️ EmailService (contact form)
13. ⏭️ SitemapController (XML)
14. ⏭️ SEO metadata management
15. ⏭️ Real design (CSS)

## 📚 Další Dokumentace

- **ANALYSIS.md** - Kompletní technická analýza (60+ stránek)
- **SETUP.md** - Detailní setup guide
- **database/schema.sql** - Database structure + seed data

## 🔧 Pro Další Claude Instance

### ⚡ Quick Reference - Theme System

**PŘED psaním jakéhokoliv CSS si VŽDY přečti tuto sekci!**

```bash
# 1. Zkontroluj dostupné CSS proměnné
Otevři: http://localhost:8888/labyrint/admin/theme
Nebo přečti: /database/theme_settings.sql

# 2. Použij VŽDY CSS custom properties
✅ color: var(--color-primary);
❌ color: #00792E;

# 3. Vždy použij fallback
✅ color: var(--color-primary, #00792E);
❌ color: var(--color-primary);

# 4. Pro nové barvy/styly
Přidej do /database/theme_settings.sql, NE do CSS!
```

**Nejčastější proměnné**:
- Barvy: `--color-primary`, `--color-brown`, `--color-yellow`, `--color-blue`, `--color-red`
- Spacing: `--spacing-sm`, `--spacing-md`, `--spacing-lg`, `--spacing-xl`, `--spacing-2xl`
- Border: `--border-radius` (16px)
- Layout: `--container-width` (1200px)

### Quick Start Checklist

```bash
# 1. Check MAMP is running
# Ports: Apache 8888, MySQL 8889

# 2. Verify database exists
/Applications/MAMP/Library/bin/mysql80/bin/mysql \
  --socket=/Applications/MAMP/tmp/mysql/mysql.sock \
  -u root -proot -e "SHOW DATABASES LIKE 'labyrint';"

# 3. Test homepage
curl http://localhost:8888/labyrint/cs/

# 4. Test admin login page
curl http://localhost:8888/labyrint/admin/login

# 5. Check error logs
tail -f /Applications/MAMP/logs/php_error.log
```

### Important Files to Check First

1. `/config/config.local.php` - Local configuration
2. `/public/index.php` - Front controller + admin routing
3. `/app/Helpers/functions.php` - Helper functions
4. `/app/Views/layouts/admin.php` - Admin links (must use adminUrl()!)
5. `/.htaccess` - RewriteBase must be `/labyrint/`

### Common Pitfalls

⚠️ **DON'T**:
- Add language parameter to admin controllers
- Use `url('admin/...', 'cs')` in admin views
- Define constants before loading config.local.php
- Use `session_start()` twice
- Forget RewriteBase in .htaccess
- **❌ NIKDY hardcode barvy, fonty nebo spacing v CSS** (např. `color: #00792E;`)
- **❌ NIKDY psát inline styly s hardcoded hodnotami** v PHP/HTML
- **❌ NIKDY vytvářet nové CSS soubory bez použití proměnných**

✅ **DO**:
- Use `adminUrl()` for all admin links
- Load config.local.php before defining constants
- Use `Session::start()` only once
- Always use prepared statements
- Check MAMP ports (8888, 8889)
- **✅ VŽDY používat CSS custom properties** z `theme.css` (např. `var(--color-primary)`)
- **✅ VŽDY používat fallback hodnoty** (např. `var(--color-primary, #00792E)`)
- **✅ VŽDY kontrolovat existující proměnné** v `/admin/theme` před přidáním nových
- **✅ Pro nové barvy/styly** - přidat do databáze přes `theme_settings.sql`, ne do CSS

---

**Last Updated**: 20.01.2026
**MAMP Version**: Latest
**PHP Version**: 8.5.0/8.5.0RC5 (MAMP default)
**Status**: ✅ Core working, frontend routes working, admin working, **Theme Management System aktivní**
**Design**: ✅ Hygge/Severský styl, Flat design (16px border-radius), Responzivní (768px breakpoint)
