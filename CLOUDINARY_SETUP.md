# 🖼️ Cloudinary Integration - Setup Complete!

**Status:** ✅ Plně funkční
**Datum:** 2026-01-25

## 📋 Co bylo nainstalováno

### 1. Cloudinary PHP SDK
```bash
composer require cloudinary/cloudinary_php
```

### 2. Nové soubory
- `config/cloudinary.php` - Konfigurace Cloudinary
- `app/Services/CloudinaryService.php` - Service třída pro práci s Cloudinary
- `database/migrations/002_add_cloudinary_support.sql` - DB migrace

### 3. Upravené soubory
- `app/Controllers/Admin/MediaAdminController.php` - Upload a delete používají Cloudinary
- `app/Helpers/functions.php` - Přidány helper funkce pro media URLs
- `.env.example` - Přidány Cloudinary environment variables

### 4. Databázové změny
- Nový sloupec `media.cloudinary_public_id` (VARCHAR 255)
- Index na `cloudinary_public_id`

---

## ⚙️ Konfigurace

### Lokální vývoj (.env)

```env
# Cloudinary credentials
CLOUDINARY_CLOUD_NAME=duu1utinb
CLOUDINARY_API_KEY=346893493165239
CLOUDINARY_API_SECRET=hlCY5wdext-7WYLh-ygowOKYFMg
CLOUDINARY_FOLDER=labyrint
```

### Production (Railway.app)

Přidejte tyto proměnné v Railway dashboard → Variables:

```
CLOUDINARY_CLOUD_NAME=duu1utinb
CLOUDINARY_API_KEY=346893493165239
CLOUDINARY_API_SECRET=hlCY5wdext-7WYLh-ygowOKYFMg
CLOUDINARY_FOLDER=labyrint
```

---

## 🚀 Jak to funguje

### Upload obrázků

1. **Admin nahraje obrázek** → `/admin/media/upload`
2. **Server uploaduje do Cloudinary** → `CloudinaryService::upload()`
3. **Cloudinary vrátí:**
   - `public_id` - Unikátní ID (např. `labyrint/media/image_1234567890`)
   - `url` - CDN URL (např. `https://res.cloudinary.com/duu1utinb/image/upload/v123/labyrint/media/image_1234567890.jpg`)
   - `width`, `height`, `bytes` - Metadata
4. **Uložení do DB:**
   ```sql
   filename = 'https://res.cloudinary.com/...'
   cloudinary_public_id = 'labyrint/media/image_1234567890'
   folder = 'cloudinary'
   ```

### Zobrazení obrázků

```php
// V šablonách použijte helper funkci
<img src="<?= e(mediaUrl($media->filename)) ?>" alt="...">

// S transformacemi (automatické resize)
<img src="<?= e(mediaUrl($media->filename, ['width' => 800, 'quality' => 'auto'])) ?>" alt="...">

// Responsive images
<img
    src="<?= e(mediaUrl($media->filename, ['width' => 1024])) ?>"
    srcset="<?= e(mediaResponsiveSrcset($media->filename)) ?>"
    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 1024px"
    alt="...">
```

### Mazání obrázků

1. **Admin smaže obrázek** → `/admin/media/{id}/delete`
2. **Server smaže z Cloudinary** → `CloudinaryService::delete($publicId)`
3. **Server smaže z DB** → `Media::delete($id)`

---

## 🎯 Výhody Cloudinary

### ✅ Persistentní storage
- Obrázky přežijí redeploy na Railway
- Žádné ztráty dat při restartu aplikace

### ✅ Automatická optimizace
- `quality: auto` - Cloudinary automaticky optimalizuje kvalitu
- `fetch_format: auto` - Automaticky převede na WebP pokud prohlížeč podporuje
- Rychlejší načítání stránek

### ✅ On-the-fly transformace
```php
// Resize na 400px šířku
mediaUrl($image, ['width' => 400])

// Thumbnail 200x200 (crop)
mediaUrl($image, ['width' => 200, 'height' => 200, 'crop' => 'fill'])

// Lepší kvalita
mediaUrl($image, ['quality' => 100])

// WebP formát
mediaUrl($image, ['fetch_format' => 'webp'])
```

### ✅ CDN delivery
- Globální CDN - rychlé načítání odkudkoliv
- HTTPS z boxu
- Automatické cache

### ✅ Free tier
- **25 GB** storage
- **25 GB** bandwidth měsíčně
- **Unlimited** transformations
- Dostatečné pro menší weby

---

## 📊 Použití v kódu

### CloudinaryService API

```php
use App\Services\CloudinaryService;

$cloudinary = new CloudinaryService();

// Upload
$result = $cloudinary->upload('/path/to/file.jpg', [
    'folder' => 'labyrint/gallery',
    'public_id' => 'my-image',
]);

// Delete
$success = $cloudinary->delete('labyrint/gallery/my-image');

// Get URL with transformations
$url = $cloudinary->getUrl('labyrint/gallery/my-image', [
    'width' => 800,
    'height' => 600,
    'crop' => 'fill',
    'quality' => 'auto',
]);

// Get responsive URLs
$urls = $cloudinary->getResponsiveUrls('labyrint/gallery/my-image');
// Returns: [320 => 'url320', 640 => 'url640', 1024 => 'url1024', 1920 => 'url1920']

// Check if configured
if ($cloudinary->isConfigured()) {
    // Cloudinary is ready
}
```

### Helper funkce

```php
// Základní URL
mediaUrl($media->filename)
// → 'https://res.cloudinary.com/...' nebo 'http://localhost/uploads/...'

// S transformacemi
mediaUrl($media->filename, ['width' => 800, 'quality' => 'auto'])
// → 'https://res.cloudinary.com/.../w_800,q_auto/...'

// Responsive srcset
mediaResponsiveSrcset($media->filename, [320, 640, 1024, 1920])
// → 'https://...w_320/... 320w, https://...w_640/... 640w, ...'
```

---

## 🧪 Testování

### 1. Lokální test (MAMP)

Spusťte lokální server:
```bash
cd /Users/karellycka/weby/labyrint/public
/Applications/MAMP/bin/php/php8.3.28/bin/php -S localhost:8000
```

Přejděte na:
```
http://localhost:8000/admin/media
```

Nahrajte testovací obrázek:
1. Klikněte "Upload"
2. Vyberte obrázek (JPEG, PNG, GIF, WebP)
3. Zkontrolujte, že se objevil v seznamu
4. URL by měla být: `https://res.cloudinary.com/duu1utinb/...`

### 2. Production test (Railway)

Po deployu na Railway:
1. Přidejte Cloudinary environment variables
2. Push změny do Gitu
3. Railway automaticky deployuje
4. Otestujte upload obrázků

---

## 🔧 Troubleshooting

### Problém: "Cloudinary upload failed"

**Řešení:**
1. Zkontrolujte `.env` - jsou správné credentials?
2. Zkontrolujte internet připojení
3. Zkontrolujte Cloudinary dashboard - je účet aktivní?

```bash
# Test credentials
php -r "
require 'vendor/autoload.php';
\$c = new \Cloudinary\Cloudinary([
    'cloud' => [
        'cloud_name' => 'duu1utinb',
        'api_key' => '346893493165239',
        'api_secret' => 'hlCY5wdext-7WYLh-ygowOKYFMg',
    ]
]);
echo 'Cloudinary configured: ' . \$c->configuration->cloud->cloudName . PHP_EOL;
"
```

### Problém: "Column cloudinary_public_id doesn't exist"

**Řešení:**
Spusťte migraci:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot labyrint < database/migrations/002_add_cloudinary_support.sql
```

### Problém: Staré obrázky nefungují

**Vysvětlení:**
Staré obrázky jsou uložené lokálně v `/public/uploads/`. Nové obrázky jsou v Cloudinary.

**Řešení:**
Helper funkce `mediaUrl()` automaticky detekuje typ:
- Cloudinary URLs začínají `https://res.cloudinary.com/`
- Lokální cesty začínají `/uploads/`

Oboje bude fungovat!

### Problém: Image transformations nefungují

**Příčina:**
Transformace fungují jen pro Cloudinary URLs, ne pro lokální soubory.

**Řešení:**
```php
// ✅ Funguje (Cloudinary URL)
mediaUrl('https://res.cloudinary.com/...', ['width' => 800])

// ❌ Nefunguje (lokální cesta)
mediaUrl('/uploads/image.jpg', ['width' => 800])
```

---

## 📈 Migrace starých obrázků (volitelné)

Pokud chcete migrovat staré lokální obrázky do Cloudinary:

```php
// Script: migrate_to_cloudinary.php
require 'vendor/autoload.php';
require 'config/config.php';

$cloudinary = new \App\Services\CloudinaryService();
$media = new \App\Models\Media();

$localImages = $media->query("SELECT * FROM media WHERE folder != 'cloudinary'");

foreach ($localImages as $image) {
    $localPath = PUBLIC_PATH . $image->filename;

    if (!file_exists($localPath)) {
        echo "Skipping {$image->filename} - file not found\n";
        continue;
    }

    try {
        // Upload to Cloudinary
        $result = $cloudinary->upload($localPath);

        // Update database
        $media->execute("
            UPDATE media
            SET filename = ?, cloudinary_public_id = ?, folder = 'cloudinary'
            WHERE id = ?
        ", [$result['url'], $result['public_id'], $image->id]);

        echo "Migrated: {$image->filename} → {$result['url']}\n";

        // Optional: delete local file
        // unlink($localPath);

    } catch (\Exception $e) {
        echo "Error migrating {$image->filename}: {$e->getMessage()}\n";
    }
}

echo "Migration complete!\n";
```

---

## 🎉 Hotovo!

Cloudinary integrace je kompletní a připravená k použití:

- ✅ Obrázky se nahrávají do Cloudinary CDN
- ✅ Automatická optimizace (WebP, resize, compression)
- ✅ Persistentní storage (přežije Railway redeploy)
- ✅ Helper funkce pro snadné použití v šablonách
- ✅ Responsive images support
- ✅ On-the-fly transformace

**Next steps:**
1. Otestujte upload v adminu
2. Zkontrolujte obrázky na Cloudinary dashboardu
3. Nasaďte na Railway s Cloudinary credentials

---

**Cloudinary Dashboard:**
https://console.cloudinary.com/console/c-duu1utinb

**Cloud name:** duu1utinb
**API Key:** 346893493165239
**Folder:** labyrint/media/

---

Vytvořeno: 2026-01-25
Autor: Claude Code
Stack: PHP 8.3, Cloudinary PHP SDK 3.1
