# KOMPLEXNÍ ANALÝZA PRO TVORBU ŠKOLNÍHO WEBU LABYRINT

**Datum:** 2026-01-20
**Technologie:** PHP 8.1-8.3, MySQL/MariaDB, HTML, CSS (custom bez frameworků)
**Hosting:** Vedos.cz Nolimit (100 Kč/měsíc)
**Jazyky:** Čeština + Angličtina
**Cíle:** SEO, rychlost, multijazyčnost, redakční systém

---

## Obsah

1. [Architektura a struktura projektu](#1-architektura-a-struktura-projektu)
2. [Databázový design](#2-databázový-design)
3. [SEO optimalizace](#3-seo-optimalizace)
4. [Výkon a rychlost](#4-výkon-a-rychlost)
5. [Multijazyčnost](#5-multijazyčnost)
6. [Bezpečnost](#6-bezpečnost)
7. [Redakční systém](#7-redakční-systém)
8. [Blog systém](#8-blog-systém)
9. [Galerie a media](#9-galerie-a-media)
10. [Formuláře a události](#10-formuláře-a-události)
11. [Hosting kompatibilita](#11-hosting-kompatibilita)
12. [Development workflow](#12-development-workflow)

---

## 1. ARCHITEKTURA A STRUKTURA PROJEKTU

### Doporučená složková struktura

```
/labyrint/
├── public/                      # Document root (nastavit v hostingu)
│   ├── index.php               # Front controller
│   ├── .htaccess               # URL rewriting
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css
│   │   │   └── admin.css
│   │   ├── js/
│   │   │   ├── main.js
│   │   │   └── admin.js
│   │   └── images/
│   └── uploads/                # User uploads
│       ├── blog/
│       ├── gallery/
│       └── documents/
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── BlogController.php
│   │   ├── PageController.php
│   │   ├── EventController.php
│   │   ├── ContactController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── BlogAdminController.php
│   │       ├── PageAdminController.php
│   │       ├── I18nAdminController.php
│   │       ├── MediaAdminController.php
│   │       └── EventAdminController.php
│   ├── Models/
│   │   ├── Blog.php
│   │   ├── Page.php
│   │   ├── Event.php
│   │   ├── Media.php
│   │   ├── User.php
│   │   └── Translation.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── main.php
│   │   │   └── admin.php
│   │   ├── pages/
│   │   ├── blog/
│   │   ├── events/
│   │   ├── contact/
│   │   └── admin/
│   ├── Core/
│   │   ├── Router.php
│   │   ├── Database.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── View.php
│   │   ├── Session.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Validator.php
│   │   └── Cache.php
│   ├── Services/
│   │   ├── I18n.php
│   │   ├── SEO.php
│   │   ├── ImageProcessor.php
│   │   ├── EmailService.php
│   │   └── FileUploader.php
│   └── Helpers/
│       ├── functions.php
│       └── constants.php
├── config/
│   ├── config.php              # Main config
│   ├── config.local.php        # Local overrides (gitignored)
│   ├── database.php
│   └── routes.php
├── storage/
│   ├── cache/                  # File cache
│   ├── logs/                   # Error logs
│   └── sessions/               # Session files
├── database/
│   ├── schema.sql
│   └── migrations/
├── vendor/                      # Composer dependencies
├── .gitignore
├── composer.json
└── README.md
```

### Routing systém

**Strategie:** Front Controller Pattern s .htaccess

**public/.htaccess:**
```apache
RewriteEngine On
RewriteBase /

# Přesměrování na HTTPS (produkce)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Statické soubory - přímý přístup
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ - [L]

# Vše ostatní na index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Router.php koncept:**
- Zachytí URL: `/cs/blog/nazev-clanku`
- Parse jazykový prefix: `cs`
- Routing pattern matching: `blog/:slug`
- Dispatch na controller: `BlogController@show`

### MVC Pattern

**Důvody pro MVC:**
- Oddělení logiky od prezentace
- Snadná údržba a testování
- Strukturovaný kód

**Implementace:**
- **Model**: Database abstraction layer, business logika
- **View**: PHP templates s minimal logikou
- **Controller**: Request handling, model orchestration, response

### Autoloading

**Composer autoloader (doporučeno):**
```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "App\\Core\\": "app/Core/",
      "App\\Controllers\\": "app/Controllers/",
      "App\\Models\\": "app/Models/"
    },
    "files": ["app/Helpers/functions.php"]
  }
}
```

**Potenciální úskalí:**
- Case-sensitivity na Linux serverech (Wedos používá Linux)
- Namespace vs složková struktura musí přesně odpovídat
- Composer vyžaduje CLI přístup

---

## 2. DATABÁZOVÝ DESIGN

### Kompletní schéma

```sql
-- Users (administrátoři)
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pages (statické stránky)
CREATE TABLE pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL,
    template VARCHAR(50) DEFAULT 'default',
    published BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slug (slug),
    INDEX idx_published (published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Page translations
CREATE TABLE page_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    title VARCHAR(200) NOT NULL,
    meta_description VARCHAR(320),
    meta_keywords VARCHAR(255),
    content TEXT,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_lang (page_id, language),
    INDEX idx_language (language),
    FULLTEXT idx_content (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog posts
CREATE TABLE blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(150) NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    featured_image VARCHAR(255),
    published BOOLEAN DEFAULT 0,
    published_at TIMESTAMP NULL,
    views INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slug (slug),
    FOREIGN KEY (author_id) REFERENCES users(id),
    INDEX idx_published (published, published_at),
    INDEX idx_author (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog translations
CREATE TABLE blog_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    title VARCHAR(200) NOT NULL,
    excerpt TEXT,
    content MEDIUMTEXT NOT NULL,
    meta_description VARCHAR(320),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_post_lang (post_id, language),
    INDEX idx_language (language),
    FULLTEXT idx_search (title, excerpt, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog categories
CREATE TABLE blog_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL,
    UNIQUE KEY unique_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Category translations
CREATE TABLE blog_category_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cat_lang (category_id, language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Post-Category pivot
CREATE TABLE blog_post_categories (
    post_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events
CREATE TABLE events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(150) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    start_time TIME,
    end_time TIME,
    location VARCHAR(200),
    featured_image VARCHAR(255),
    published BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slug (slug),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_published (published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event translations
CREATE TABLE event_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_lang (event_id, language),
    INDEX idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media/Gallery
CREATE TABLE media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    width INT UNSIGNED,
    height INT UNSIGNED,
    type ENUM('image', 'document', 'video') DEFAULT 'image',
    folder VARCHAR(100) DEFAULT 'general',
    uploaded_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_folder (folder),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media translations (alt text, captions)
CREATE TABLE media_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    alt_text VARCHAR(200),
    caption TEXT,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
    UNIQUE KEY unique_media_lang (media_id, language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- i18n translations (static strings)
CREATE TABLE translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL,
    language VARCHAR(5) NOT NULL,
    value TEXT NOT NULL,
    context VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_key_lang (key_name, language),
    INDEX idx_language (language),
    INDEX idx_context (context)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact form submissions
CREATE TABLE contact_submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('new', 'read', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO metadata (pro přepsání defaults)
CREATE TABLE seo_metadata (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT UNSIGNED NOT NULL,
    language VARCHAR(5) NOT NULL,
    meta_title VARCHAR(70),
    meta_description VARCHAR(320),
    og_title VARCHAR(100),
    og_description VARCHAR(200),
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    robots VARCHAR(50) DEFAULT 'index,follow',
    UNIQUE KEY unique_entity (entity_type, entity_id, language),
    INDEX idx_entity (entity_type, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache table
CREATE TABLE cache (
    cache_key VARCHAR(255) PRIMARY KEY,
    cache_value MEDIUMTEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Best Practices

1. **utf8mb4_unicode_ci** - podpora emojis a správné řazení
2. **InnoDB engine** - ACID compliance, foreign keys
3. **TIMESTAMP** - automatické timezone handling
4. **UNSIGNED** pro ID - double kapacita
5. **ENUM** pro omezené hodnoty - paměťově efektivní
6. **ON DELETE CASCADE** - automatické čištění
7. **Normalizace** - translations v separátních tabulkách

---

## 3. SEO OPTIMALIZACE

### Meta tagy - klíčové prvky

```html
<!-- Title: max 60 znaků -->
<title><?= htmlspecialchars($seo['title']) ?></title>

<!-- Meta description: 150-160 znaků -->
<meta name="description" content="<?= htmlspecialchars($seo['description']) ?>">

<!-- Canonical URL -->
<link rel="canonical" href="<?= $seo['canonical'] ?>">

<!-- Robots -->
<meta name="robots" content="<?= $seo['robots'] ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= htmlspecialchars($seo['og']['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($seo['og']['description']) ?>">
<meta property="og:image" content="<?= $seo['og']['image'] ?>">
<meta property="og:url" content="<?= $seo['canonical'] ?>">
<meta property="og:type" content="<?= $seo['og']['type'] ?>">
```

### URL struktura

**Doporučená strategie:** `/jazyk/sekce/slug`

**Příklady:**
```
Homepage:       /cs/                    /en/
O nás:          /cs/o-nas               /en/about-us
Blog listing:   /cs/blog                /en/blog
Blog detail:    /cs/blog/nazev-clanku   /en/blog/article-title
Události:       /cs/udalosti            /en/events
```

**Best practices:**
- Lowercase pouze
- Pomlčky místo mezer
- Žádné diakritika
- Krátké a popisné (3-5 slov max)

### Hreflang tagy pro multijazyčnost

```php
<link rel="alternate" hreflang="cs" href="https://skola.cz/cs/o-nas">
<link rel="alternate" hreflang="en" href="https://skola.cz/en/about-us">
<link rel="alternate" hreflang="x-default" href="https://skola.cz/cs/o-nas">
```

---

## 4. VÝKON A RYCHLOST

### Caching strategie

1. **OPcache** (PHP) - automatická kompilace
2. **Page Cache** - celé HTML stránky (1800s TTL)
3. **Database Query Cache** - výsledky dotazů (3600s TTL)
4. **Asset versioning** - `main.css?v=timestamp`

### Optimalizace obrázků

**Automatické generování:**
- Thumbnail: 150px
- Small: 300px
- Medium: 600px
- Large: 1200px
- WebP konverze pro všechny velikosti

**Lazy loading:**
```html
<img src="placeholder.jpg"
     data-src="real-image.jpg"
     loading="lazy"
     class="lazyload">
```

### GZIP komprese

V .htaccess:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript
</IfModule>
```

---

## 5. MULTIJAZYČNOST

### URL struktura

**Strategie:** Subdirectory `/cs/`, `/en/`

**Výhody:**
- SEO friendly (Google doporučuje)
- Jednoduchá implementace
- Sdílené resources

### i18n systém

```php
// Použití v šablonách
<h1><?= __('homepage.welcome') ?></h1>
<p><?= __('homepage.intro') ?></p>
<button><?= __('common.submit') ?></button>
```

**Fallback mechanismus:**
1. Preferovaný jazyk (EN)
2. Výchozí jazyk (CS)
3. Klíč samotný

---

## 6. BEZPEČNOST

### Kritické prvky

1. **SQL Injection** - VŽDY prepared statements
2. **XSS** - VŽDY htmlspecialchars na output
3. **CSRF** - tokeny pro všechny formuláře
4. **Password hashing** - Argon2 / Bcrypt
5. **Session security** - HttpOnly, Secure, SameSite flags
6. **File uploads** - MIME validace, bezpečné názvy, zakázat PHP

### Příklad bezpečného formuláře

```php
// CSRF token
<input type="hidden" name="csrf_token" value="<?= Session::generateCSRFToken() ?>">

// Validace
if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('Invalid CSRF token');
}

// SQL
$stmt = $db->prepare("INSERT INTO ... VALUES (?, ?)");
$stmt->execute([$_POST['name'], $_POST['email']]);

// Output
echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');
```

---

## 7. REDAKČNÍ SYSTÉM

### Admin autentizace

- Login s rate limiting (max 5 pokusů za 15 minut)
- Session timeout (30 minut)
- Session validation (user agent, IP)
- Regenerate session ID po loginu

### Editace textů

Admin rozhraní pro editaci:
- i18n klíče (statické texty)
- Blog obsah (WYSIWYG - TinyMCE)
- Stránkový obsah (definované bloky)
- Alt texty pro obrázky

### Role systém

- **admin** - vše
- **editor** - může editovat obsah, nemůže spravovat uživatele

---

## 8. BLOG SYSTÉM

### Funkce

- ✅ Kategorie s multijazyčností
- ✅ Featured image
- ✅ Excerpt a full content
- ✅ Autor a datum
- ✅ Počet zobrazení
- ✅ SEO meta tagy
- ✅ RSS feed
- ✅ Pagination
- ✅ Related posts

### URL příklady

```
Listing:   /cs/blog
Detail:    /cs/blog/prvni-den-skoly-2026
Kategorie: /cs/blog/category/aktuality
RSS:       /cs/blog/feed
```

---

## 9. GALERIE A MEDIA

### Media Manager

**Funkce:**
- Upload obrázků a dokumentů
- Automatické thumbnail generování
- WebP konverze
- Alt texty a captions (multijazyčně)
- Organizace do složek
- Vyhledávání a filtrování

**Složková struktura:**
```
/uploads/
  /blog/2026/01/
  /gallery/2026/skolni-ples/
  /documents/2026/
```

---

## 10. FORMULÁŘE A UDÁLOSTI

### Kontaktní formulář

**Ochrana:**
- CSRF token
- Honeypot (spam bot detection)
- Rate limiting (3 odeslání za hodinu)
- Optional: reCAPTCHA v3

**Admin zobrazení:**
- Seznam všech zpráv
- Status: new/read/archived
- Email notifikace

### Kalendář událostí

**Funkce:**
- Listing nadcházejících událostí
- Měsíční zobrazení
- iCalendar export (.ics)
- Featured image
- Lokace a čas

---

## 11. HOSTING KOMPATIBILITA (Wedos.cz)

### .htaccess klíčové prvky

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header set X-XSS-Protection "1; mode=block"
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"

# GZIP compression
AddOutputFilterByType DEFLATE text/html text/css application/javascript

# Browser caching
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType text/css "access plus 1 month"

# Protect sensitive files
<FilesMatch "^(\.env|config\.local\.php)$">
    Deny from all
</FilesMatch>

# Front controller
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [QSA,L]
```

### PHP požadavky

**Minimální verze:** PHP 8.1

**Potřebné extensions:**
- pdo, pdo_mysql
- mbstring
- gd (pro zpracování obrázků)
- openssl
- json
- fileinfo

---

## 12. DEVELOPMENT WORKFLOW

### Git strategie

**Branches:**
- `main` - produkce
- `develop` - development
- `feature/*` - nové funkce

**Gitignore:**
```
/vendor/
/storage/cache/*
/storage/logs/*
/storage/sessions/*
/config/config.local.php
/public/uploads/*
.DS_Store
```

### Environment konfigurace

**config.php:**
```php
define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'production');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'https://vase-skola.cz');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
```

**config.local.php (gitignored):**
```php
// Lokální přepsání pro development
return [
    'environment' => 'development',
    'base_url' => 'http://localhost:8000',
    'debug' => true
];
```

### Deployment proces

1. **Lokální vývoj** → Git commit
2. **Push na GitHub/GitLab**
3. **Pull na Wedos** nebo automatický deploy
4. **Run migrations** pokud jsou změny v DB
5. **Clear cache**

### Backup strategie

**Co zálohovat:**
- Databáze (denně)
- Uploads složka (týdně)
- Config soubory

**Nástroje:**
- mysqldump pro databázi
- rsync pro soubory
- Cron job pro automatizaci

---

## CHECKLIST PRO START PROJEKTU

### Před začátkem kódování

- [ ] Dohodnout se na structure stránek s klientem
- [ ] Připravit wireframes/mockupy designu
- [ ] Vytvořit logo a brand assets
- [ ] Nastavit lokální development environment
- [ ] Vytvořit Git repository

### První fáze (Core systém)

- [ ] Vytvořit složkovou strukturu
- [ ] Nastavit Composer autoloading
- [ ] Implementovat Router
- [ ] Vytvořit Database wrapper
- [ ] Nastavit základní MVC struktur
- [ ] Implementovat Session management

### Druhá fáze (Admin & Autentizace)

- [ ] Vytvořit databázové schéma
- [ ] Implementovat User model
- [ ] Admin login systém
- [ ] Auth middleware
- [ ] Admin dashboard základy

### Třetí fáze (i18n & Content)

- [ ] i18n systém
- [ ] Page model a controller
- [ ] Blog systém
- [ ] Media manager
- [ ] WYSIWYG editor integrace

### Čtvrtá fáze (Funkce)

- [ ] Kontaktní formulář
- [ ] Kalendář událostí
- [ ] Galerie
- [ ] RSS feed
- [ ] Sitemap XML

### Pátá fáze (Optimalizace)

- [ ] SEO meta tagy
- [ ] Caching implementace
- [ ] Optimalizace obrázků
- [ ] Performance testing
- [ ] Security audit

### Šestá fáze (Launch)

- [ ] Testování na production-like prostředí
- [ ] Migrace na Wedos
- [ ] SSL certifikát setup
- [ ] Google Search Console setup
- [ ] Analytics setup
- [ ] Backup setup

---

## UŽITEČNÉ ZDROJE

### Dokumentace
- PHP: https://www.php.net/manual/en/
- MySQL: https://dev.mysql.com/doc/
- Schema.org: https://schema.org/

### Nástroje
- TinyMCE: https://www.tiny.cloud/
- Google PageSpeed Insights: https://pagespeed.web.dev/
- Google Search Console: https://search.google.com/search-console

### Testing
- Manual testing checklist
- Browser compatibility (Chrome, Firefox, Safari, Edge)
- Mobile responsiveness
- Lighthouse audit

---

**Dokument připraven:** 2026-01-20
**Pro projekt:** Škola Labyrint
**Developer:** Claude Code
