# 🗄️ Railway Database Setup - Localhost připojení

**Datum:** 2026-01-28
**Stav:** ✅ Aktivní (localhost používá Railway produkční DB)

---

## 📊 Konfigurace

### Localhost připojení
Localhost je nyní **přímo připojen** na Railway produkční databázi:

```
Host: trolley.proxy.rlwy.net
Port: 55333
Database: railway
User: root
```

### Upravené soubory
- `.env` - Railway credentials
- `config/config.local.php` - přímé Railway připojení

---

## ✅ Provedené kroky

### 1. Export lokální databáze
```bash
mysqldump -u root -proot labyrint > database_backup_20260128_065005.sql
```
**Záloha:** `database_backup_20260128_065005.sql` (91KB)

### 2. Import do Railway
```bash
export PATH="/Applications/MAMP/Library/bin/mysql80/bin:$PATH"
railway connect mysql < database_backup_20260128_065005.sql
```

### 3. Vytvoření chybějící sessions tabulky
```bash
railway connect mysql < database/migrations/010_add_sessions_table.sql
```

### 4. Výsledek
- **23 tabulek** v Railway databázi
- Localhost i produkce sdílejí stejná data
- Sessions fungují (DB-backed)

---

## 🔧 Výhody tohoto nastavení

✅ **Jedna databáze pro vše**
- Žádná synchronizace
- Žádné duplikátní migrace
- Konzistentní data

✅ **Okamžitá viditelnost změn**
- Změny v localhostu → okamžitě na produkci
- Testování s reálnými daty

⚠️ **Varování**
- Pracujete s PRODUKČNÍMI daty!
- Všechny změny jsou okamžitě živé
- Buďte opatrní při testování

---

## 🔄 Spouštění migrací

### Lokálně (propaguje se na produkci)
```bash
php public/run_migration_XXX.php
```

### Přímo na Railway
```bash
export PATH="/Applications/MAMP/Library/bin/mysql80/bin:$PATH"
railway connect mysql < database/migrations/XXX_migration.sql
```

---

## 📦 Zálohy

### Lokální databáze (před přepnutím)
- `database_backup_20260128_065005.sql`
- `.env.local-backup` (původní konfigurace)

### Pro návrat na lokální MAMP DB
```php
// config/config.local.php
'DB_HOST' => '127.0.0.1',
'DB_PORT' => '8889',
'DB_NAME' => 'labyrint',
'DB_USER' => 'root',
'DB_PASS' => 'root',
```

### Obnovení lokální databáze
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot labyrint < database_backup_20260128_065005.sql
```

---

## 🗃️ Railway Database - Přehled tabulek

### Core
- `pages`, `page_translations`
- `page_widgets`, `page_widget_translations`
- `widget_types`
- `translations`
- `users`
- `cache`
- `sessions` (DB-backed PHP sessions)
- `seo_metadata`
- `theme_settings`

### Blog
- `blog_posts`, `blog_translations`
- `blog_categories`, `blog_category_translations`
- `blog_post_categories`

### Features
- `events`, `event_translations`
- `quotes`, `quote_translations`
- `media`, `media_translations`
- `contact_submissions`

**Celkem:** 23 tabulek

---

## 🔍 Užitečné příkazy

### Přímé připojení k Railway MySQL
```bash
export PATH="/Applications/MAMP/Library/bin/mysql80/bin:$PATH"
railway connect mysql
```

### Zobrazit tabulky
```sql
SHOW TABLES;
```

### Počet záznamů
```sql
SELECT
  'pages' as table_name, COUNT(*) as count FROM pages
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'media', COUNT(*) FROM media;
```

### Aktuální sessions
```sql
SELECT
  id,
  FROM_UNIXTIME(last_activity) as last_active,
  LENGTH(data) as data_size
FROM sessions
ORDER BY last_activity DESC
LIMIT 10;
```

---

## 📞 Railway Dashboard

**Project:** labyrint-lhota
**Environment:** production
**Services:**
- labyrint-lhota (Web App)
- MySQL (Database)

**URL:** https://railway.app/project/784031c7-5084-4b72-8234-b59b9a58f62c

---

## ✨ Workflow

### Vývoj
1. Pracujete lokálně na http://localhost:8888/labyrint
2. Data jsou okamžitě na produkci
3. Git commit & push → automatický deploy

### Migrace
1. Vytvořte SQL v `database/migrations/`
2. Spusťte lokálně nebo přes Railway CLI
3. Hotovo! (propagováno automaticky)

---

**Vytvořeno:** 2026-01-28
**Status:** ✅ Production Ready
