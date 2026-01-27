# Návod: Jak přidat/opravit admin sekci

## 1. Kontrola databázového schématu
```bash
# Najdi tabulku v database/schema.sql
grep -A 30 "CREATE TABLE.*nazev_tabulky" database/schema.sql
```
**Důležité:** Zapamatuj si přesné názvy sloupců! (např. `start_date` ne `event_date`)

## 2. Controller (`app/Controllers/Admin/NazevAdminController.php`)
```php
namespace App\Controllers\Admin;

class NazevAdminController extends Controller
{
    private NazevModel $model;

    public function __construct()
    {
        parent::__construct();

        // Auth check
        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }

        $this->model = new NazevModel();
    }

    // CRUD metody: index(), create(), store(), edit($id), update($id), delete($id)
}
```

**Kontrola názvů sloupců v SQL dotazech!**

## 3. Views (`app/Views/admin/nazev/`)
Struktura:
- `index.php` - seznam
- `create.php` - formulář pro vytvoření
- `edit.php` - formulář pro úpravu

**DŮLEŽITÉ:**
- ❌ **ŽÁDNÉ inline styly!** Všechny třídy jsou v `public/assets/css/admin.css`
- ✅ Používej třídy: `.admin-header-bar`, `.form-container`, `.admin-table-container`, `.form-tabs`, `.tab-content`, `.form-group`, `.form-control`, `.btn`, atd.
- ✅ Pattern z `app/Views/admin/pages/` (fungující příklad)

## 4. Routing v `public/index.php`
**Admin routes NEJSOU v Routeru!** Jsou hardcoded v `public/index.php` okolo řádku 200+.

Přidej PŘED řádek `// For any other /admin/* routes`:
```php
// NazevSekce - Index
if ($requestUri === '/admin/nazev') {
    $controller = new \App\Controllers\Admin\NazevAdminController();
    $controller->index();
    exit;
}

// NazevSekce - Create (form)
if ($requestUri === '/admin/nazev/create') {
    $controller = new \App\Controllers\Admin\NazevAdminController();
    $controller->create();
    exit;
}

// NazevSekce - Store (submit)
if ($requestUri === '/admin/nazev/store' && $method === 'POST') {
    $controller = new \App\Controllers\Admin\NazevAdminController();
    $controller->store();
    exit;
}

// NazevSekce - Edit (form)
if (preg_match('#^/admin/nazev/(\d+)/edit$#', $requestUri, $matches)) {
    $controller = new \App\Controllers\Admin\NazevAdminController();
    $controller->edit((int)$matches[1]);
    exit;
}

// NazevSekce - Update (submit)
if (preg_match('#^/admin/nazev/(\d+)/update$#', $requestUri, $matches) && $method === 'POST') {
    $controller = new \App\Controllers\Admin\NazevAdminController();
    $controller->update((int)$matches[1]);
    exit;
}

// NazevSekce - Delete
if (preg_match('#^/admin/nazev/(\d+)/delete$#', $requestUri, $matches) && $method === 'POST') {
    $controller = new \App\Controllers\Admin\NazevAdminController();
    $controller->delete((int)$matches[1]);
    exit;
}
```

## 5. Checklist před testováním
- [ ] Kontroloval jsem přesné názvy sloupců v databázi
- [ ] Views NEMAJÍ inline CSS
- [ ] Routing je přidán v `public/index.php`
- [ ] Controller má auth check v `__construct()`
- [ ] Formuláře obsahují `<?= csrfField() ?>`
- [ ] Používám správné URL funkce: `adminUrl()`, `redirect()`

## 6. Common Issues
- **404:** Chybí routing v `public/index.php`
- **SQL error "unknown column":** Špatný název sloupce v controlleru
- **Views se nezobrazují:** Chybí adresář `app/Views/admin/nazev/`
- **Redirect na login:** Chybí auth check nebo session

## 7. Debug při problémech
```php
// V controlleru
error_log("=== DEBUG: method called ===");
error_log("POST: " . print_r($_POST, true));

// Pak odstraň!
```
