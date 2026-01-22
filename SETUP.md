# Setup Guide - Škola Labyrint

Quick start guide pro spuštění projektu na localhostu.

## Požadavky

- **PHP 8.1+** s rozšířeními: pdo, pdo_mysql, mbstring, gd, openssl, json, fileinfo
- **MySQL 5.7+** nebo **MariaDB 10.3+**
- **Apache** s mod_rewrite nebo PHP built-in server

## Instalace

### 1. Databáze

Vytvořte databázi a importujte schéma:

```bash
# V MySQL/MariaDB
mysql -u root -p

CREATE DATABASE labyrint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import schéma
mysql -u root -p labyrint < database/schema.sql

# Import rozšířené i18n klíče
mysql -u root -p labyrint < database/i18n_extended.sql
```

### 2. Konfigurace

Soubor `config/config.local.php` už existuje. Upravte pokud potřebujete:

```php
return [
    'ENVIRONMENT' => 'development',
    'BASE_URL' => 'http://localhost:8000',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'labyrint',
    'DB_USER' => 'root',
    'DB_PASS' => '',  // Vaše MySQL heslo
];
```

### 3. Oprávnění

```bash
chmod -R 755 storage/
chmod -R 755 public/uploads/
```

### 4. Spuštění

**Varianta A: PHP Built-in server** (doporučeno pro vývoj)

```bash
cd /Users/karellycka/weby/labyrint
php -S localhost:8000 -t public
```

**Varianta B: Apache**

Nastavte DocumentRoot na složku `/Users/karellycka/weby/labyrint/public`

## První přihlášení

1. Otevřete prohlížeč: `http://localhost:8000`
2. Přejděte na admin: `http://localhost:8000/cs/admin`
3. Přihlašovací údaje:
   - **Username:** `admin`
   - **Password:** `admin123`

⚠️ **DŮLEŽITÉ:** Změňte heslo po prvním přihlášení!

## Struktura URL

- Homepage: `http://localhost:8000/cs/` nebo `/en/`
- Blog: `/cs/blog`
- Events: `/cs/events`
- Contact: `/cs/contact`
- Admin: `/cs/admin`

## Co je hotové

✅ **Core Framework**
- MVC architektura
- Router s multijazyčností
- Database wrapper (PDO)
- Session management
- CSRF ochrana

✅ **Modely**
- User, Page, Blog, Event, Media
- Připravené metody pro CRUD operace

✅ **Admin Panel**
- Login systém
- Dashboard s statistikami
- Základní layout

✅ **Frontend**
- Homepage template
- Hlavní layout s navigací
- CSS a JS základy

✅ **Security**
- Prepared statements (SQL injection protection)
- CSRF tokens
- XSS protection (output escaping)
- Password hashing (Argon2)
- Rate limiting na login

✅ **i18n System**
- Translation service
- Database backed
- File caching
- Fallback mechanismus

## Co ještě chybí

❌ **Controllers pro frontend**
- PageController (statické stránky)
- BlogController (seznam a detail)
- EventController (kalendář)
- ContactController (formulář)
- GalleryController

❌ **Controllers pro admin**
- BlogAdminController
- PageAdminController
- EventAdminController
- MediaAdminController
- I18nAdminController

❌ **Views**
- Blog templates
- Event templates
- Contact form
- Gallery
- Error pages (404, 500)

❌ **Services**
- ImageProcessor (thumbnails, WebP)
- EmailService (contact form)
- FileUploader (media upload)
- SEO service

## Další kroky

1. **Vytvořte strukturu stránek** - řekněte mi jaké stránky budete potřebovat
2. **Implementuji controllery a views** pro každou sekci
3. **Nastavíme design** - můžete poskytnout mockupy nebo popsat vzhled
4. **Dotáhneme admin** - kompletní CRUD pro blog, events, pages
5. **Media manager** - upload a správa obrázků
6. **Testing** - otestujeme vše na localhostu
7. **Deployment** - nahrajeme na Wedos

## Troubleshooting

### Chyba připojení k databázi
- Zkontrolujte `config/config.local.php`
- Ověřte že MySQL běží: `mysql -u root -p`

### 404 na všech stránkách
- Ověřte že mod_rewrite je zapnutý (Apache)
- Zkontrolujte `.htaccess` v public/

### CSS/JS se nenačítá
- Zkontrolujte konzoli prohlížeče
- Ověřte cesty v asset() helper funkci

### "Class not found" errors
- Ujistěte se že namespace odpovídá složkové struktuře
- Zkontrolujte case-sensitivity názvů souborů

## Užitečné příkazy

```bash
# Smazat cache
rm -rf storage/cache/*

# Smazat logy
rm -rf storage/logs/*

# Resetovat databázi
mysql -u root -p labyrint < database/schema.sql

# Spustit server
php -S localhost:8000 -t public
```

## Další informace

- **ANALYSIS.md** - Kompletní technická analýza
- **README.md** - Dokumentace projektu
- **database/schema.sql** - Databázové schéma

---

**Ready to continue! 🚀**

Řekněte mi strukturu stránek a pokračujeme s implementací!
