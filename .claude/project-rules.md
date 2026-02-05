# Pravidla projektu Škola Labyrint

## CSS a Styling

### ❌ ZAKÁZÁNO: Inline styly v HTML

**Nepoužívat inline styly pro standardní HTML elementy**, zejména:
- `<h1>`, `<h2>`, `<h3>`, `<h4>`, `<h5>`, `<h6>` - nadpisy
- `<p>` - odstavce
- `<a>` - odkazy
- `<ul>`, `<ol>`, `<li>` - seznamy

**Proč:**
- Globální konzistence designu
- Snadná údržba a změny
- Lepší performance (cache)
- Přehlednost kódu

### ✅ POVOLENO: Inline styly pouze v těchto případech

Inline styly můžete použít **POUZE** po výslovném svolení pro:
1. **Individuální komponenty** specifické pro jednu stránku
2. **Dynamické hodnoty** z databáze (barvy, obrázky)
3. **Prototypování** nových komponent (dočasně)

### 📋 Správný postup

**Místo inline stylů:**
```html
<!-- ❌ ŠPATNĚ -->
<h2 style="text-align: center; color: #666;">Nadpis</h2>

<!-- ✅ SPRÁVNĚ -->
<h2 class="section-title">Nadpis</h2>
```

**Pokud potřebujete speciální styl:**
1. Vytvořte CSS třídu v `main.css`
2. Nebo vytvořte komponentu v `components/`
3. Nebo použijte existující utility třídy

### 🎨 Hierarchie stylů

1. **theme.css** - CSS Custom Properties (barvy, fonty, spacing)
2. **main.css** - Globální styly, komponenty, utility třídy
3. **komponenty** - Styly specifické pro komponentu (v `<style>` tagu komponenty)
4. **inline styly** - POUZE po svolení

### 📁 Struktura CSS

```
public/assets/css/
├── theme.css        # Auto-generované CSS variables z DB
└── main.css         # Hlavní styly webu

app/Views/components/
└── *.php           # Komponenty mohou mít vlastní <style> tag
```

## Typografie

### Nadpisy - Globální styly

Všechny nadpisy jsou definovány v `main.css`:

```css
h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-family-heading);
    line-height: var(--line-height-tight, 1.25);
    font-weight: var(--font-weight-bold, 700);
}

h1 { font-size: var(--font-size-h1, 48px); }
h2 { font-size: var(--font-size-h2, 36px); }
h3 { font-size: var(--font-size-h3, 28px); }
h4 { font-size: var(--font-size-h4, 24px); }
h5 { font-size: var(--font-size-h5, 20px); }
h6 { font-size: var(--font-size-h6, 16px); }
```

### Utility třídy pro nadpisy

```css
.section-title        # Nadpis sekce (centrovaný)
.text-center         # Text zarovnaný na střed
.text-light          # Světlejší barva textu
```

## Kontrola před commitem

Před commitem zkontrolujte:
- [ ] Žádné inline styly na H1-H6
- [ ] Žádné inline styly na standardních HTML elementech
- [ ] Používají se CSS Custom Properties z theme.css
- [ ] Nové komponenty mají styly v `<style>` tagu nebo main.css

## Media Management (Cloudinary)

### ✅ VŽDY používat Cloudinary pro media

**Proč:**
- Persistentní storage (přežije Railway redeploy)
- Automatická optimalizace (WebP, compression, quality auto)
- CDN delivery (rychlé načítání globálně)
- On-the-fly transformace (resize, crop bez generování souborů)

### 📋 Správný postup

**Pro upload obrázků:**
```php
// ✅ SPRÁVNĚ - použít CloudinaryService
$cloudinary = new \App\Services\CloudinaryService();
$result = $cloudinary->upload($tmpFile, [
    'folder' => 'labyrint/gallery',
]);

// Uložit do DB
$media->create([
    'filename' => $result['url'],  // Cloudinary URL
    'cloudinary_public_id' => $result['public_id'],
    'folder' => 'cloudinary',
]);
```

**Pro zobrazení obrázků:**
```php
// ✅ SPRÁVNĚ - použít helper funkce
<img src="<?= e(mediaUrl($media->filename)) ?>" alt="...">

// S transformacemi (resize, quality)
<img src="<?= e(mediaUrl($media->filename, ['width' => 800, 'quality' => 'auto'])) ?>" alt="...">

// Responsive images
<img
    src="<?= e(mediaUrl($media->filename, ['width' => 1024])) ?>"
    srcset="<?= e(mediaResponsiveSrcset($media->filename)) ?>"
    sizes="(max-width: 640px) 100vw, 1024px"
    alt="...">
```

**❌ NEPOUŽÍVAT:**
```php
// ❌ ŠPATNĚ - manuální upload do /public/uploads
move_uploaded_file($tmpFile, PUBLIC_PATH . '/uploads/image.jpg');

// ❌ ŠPATNĚ - hardcoded cesty k obrázkům
<img src="/uploads/image.jpg" alt="...">
```

### Dostupné helper funkce

```php
mediaUrl($filename, $transformations = [])
// Vrací URL obrázku (Cloudinary nebo lokální fallback)
// Transformace: width, height, crop, quality, fetch_format

mediaResponsiveSrcset($filename, $widths = [320, 640, 1024, 1920])
// Generuje srcset pro responsive images
```

**Dokumentace**: Viz `CLOUDINARY_SETUP.md`

## Deployment (Railway.app)

### 🚀 Automatický deployment

**Workflow:**
1. Lokální vývoj (MAMP + MySQL)
2. Commit změn: `git add . && git commit -m "..."`
3. Push na GitHub: `git push origin main`
4. Railway automaticky deployuje! ✅

**❌ NEPOUŽÍVAT:**
- Manuální FTP upload
- Přímé editace souborů na serveru
- Kopírování souborů mimo Git

### Environment Variables

**Lokálně**: `.env` soubor (gitignored)
**Production**: Railway dashboard → Variables

**Kritické proměnné:**
- `CLOUDINARY_*` - Cloudinary credentials
- `DB_*` - Database credentials (reference z MySQL service)
- `BASE_URL` - Production URL

**Dokumentace**: Viz `DEPLOYMENT_RAILWAY.md`

## Lokální vývojové prostředí

### URL formát
**Lokální server:** `http://localhost:8888/labyrint`

**Příklady URL:**
- Homepage: `http://localhost:8888/labyrint/`
- Admin: `http://localhost:8888/labyrint/admin`
- Migrace: `http://localhost:8888/labyrint/run_migration_XXX.php`

### MAMP nastavení
- PHP Server: localhost:8888
- MySQL: localhost:8889
- Document Root: obsahuje složku `labyrint`

## Datum vytvoření
2026-01-21

## Datum poslední aktualizace
2026-02-05
