# HLUBOKÁ ANALÝZA - ROOT CAUSE A ŘEŠENÍ

## Problém: Proč Router nemůže zpracovat admin routes

### 1. Architektura Routeru
Router v `app/Core/Router.php` je navržen **výhradně pro routes s jazykovým prefixem**:

```php
// Router->dispatch() řádek 98-105
$this->currentLanguage = $this->detectLanguage($requestUri);
$cleanUri = $this->removeLanguageFromUri($requestUri);
```

**Router očekává URI formátu**: `/cs/blog`, `/en/contact`, `/cs/blog/slug`

**Router NEZVLÁDÁ**: `/admin`, `/admin/pages`, `/admin/widgets/2`

### 2. Proč původní kód byl správný

Původní `index.php` měl **manuální routing pro admin**:

```php
if (strpos($requestUri, '/admin') === 0) {
    // Manuálně zpracovat každou admin route
    if ($adminPath === '/admin') {
        $controller = new DashboardController();
        $controller->index();
        exit;
    }
    if ($adminPath === '/admin/login') { ... }
    // atd.
}
```

**Důvod**: Admin routes:
- NEMAJÍ jazykový prefix
- Mají jinou kontroler strukturu (`Admin\DashboardController` místo `DashboardController`)
- Někdy vracejí JSON místo HTML
- Mají jiné middleware (CSRF, autentizace)

### 3. Co se pokazilo když jsem změnil na Router

Když jsem změnil na:
```php
if (strpos($requestUri, '/admin') === 0) {
    $router->dispatch();
}
```

**Router se zhroutil protože**:
1. Očekával jazyk v URI (`/cs/admin`) - žádný není
2. `detectLanguage()` vrátil default 'cs'
3. `removeLanguageFromUri('/admin')` vrátil prázdný string
4. Route matching selhal
5. 404 error

### 4. Proč widget routes musí být manuální

Widget routes jsou:
- `GET /admin/widgets/schema/:typeKey`
- `GET /admin/widgets/:id`
- `POST /admin/pages/:pageId/widgets/create`
- `POST /admin/widgets/:id/update`
- `POST /admin/widgets/:id/delete`
- `POST /admin/widgets/:id/move-up`
- `POST /admin/widgets/:id/move-down`
- `POST /admin/pages/:pageId/widgets/reorder`

Tyto routes:
- Nemají jazyk
- Vracejí JSON
- Potřebují parametry z URL (`:typeKey`, `:id`, `:pageId`)
- Musí kontrolovat CSRF token
- Musí kontrolovat autentizaci

**Router JE schopen parsovat parametry** (má `convertPatternToRegex()`), ALE:
- Router je uvnitř if bloku pro admin, takže by se musel volat tam
- Ale Router pak matchuje proti routes definovaným v `config/routes.php`
- A ty routes mají Admin/ prefix, který Router musí správně zpracovat

## ŘEŠENÍ

### Varianta A: Rozšířit manuální routing (DOPORUČENO)
Přidat do manuálního admin bloku zpracování widget routes s parametry.

**Výhody**:
- Jednoduchý, explicitní kód
- Plná kontrola nad každou route
- Snadné debugování
- Žádné magické chování

**Nevýhody**:
- Více kódu
- Musíme přidat každou novou admin route ručně

### Varianta B: Upravit Router aby podporoval routes bez jazyka
Změnit Router aby detekoval admin routes a přeskočil jazykové zpracování.

**Výhody**:
- Všechny routes v jednom místě (routes.php)
- Automatické URL parsing

**Nevýhody**:
- Komplexnější Router
- Více míst kde se může něco pokazit
- Porušuje single responsibility principle

### Varianta C: Vytvořit separátní AdminRouter
Nový router jen pro admin routes.

**Výhody**:
- Čisté oddělení concerns
- Každý router dělá jednu věc dobře

**Nevýhody**:
- Duplikace kódu
- Více složitosti

## IMPLEMENTACE - Varianta A (Rozšířit manuální routing)

```php
// Handle admin routes manually
if (strpos($requestUri, '/admin') === 0) {
    $_SESSION['language'] = 'cs';

    $method = $_SERVER['REQUEST_METHOD'];

    // Dashboard
    if ($requestUri === '/admin' || $requestUri === '/admin/') {
        $controller = new \App\Controllers\Admin\DashboardController();
        $controller->index();
        exit;
    }

    // Auth
    if ($requestUri === '/admin/login') {
        $controller = new \App\Controllers\Admin\AuthController();
        if ($method === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        exit;
    }

    if ($requestUri === '/admin/logout') {
        $controller = new \App\Controllers\Admin\AuthController();
        $controller->logout();
        exit;
    }

    // Widget API - GET schema
    if (preg_match('#^/admin/widgets/schema/([a-z_]+)$#', $requestUri, $matches)) {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->getSchema($matches[1]);
        exit;
    }

    // Widget API - GET widget
    if (preg_match('#^/admin/widgets/(\d+)$#', $requestUri, $matches) && $method === 'GET') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->getWidget((int)$matches[1]);
        exit;
    }

    // Widget API - CREATE
    if (preg_match('#^/admin/pages/(\d+)/widgets/create$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->create((int)$matches[1]);
        exit;
    }

    // Widget API - UPDATE
    if (preg_match('#^/admin/widgets/(\d+)/update$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->update((int)$matches[1]);
        exit;
    }

    // Widget API - DELETE
    if (preg_match('#^/admin/widgets/(\d+)/delete$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->delete((int)$matches[1]);
        exit;
    }

    // Widget API - MOVE UP
    if (preg_match('#^/admin/widgets/(\d+)/move-up$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->moveUp((int)$matches[1]);
        exit;
    }

    // Widget API - MOVE DOWN
    if (preg_match('#^/admin/widgets/(\d+)/move-down$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->moveDown((int)$matches[1]);
        exit;
    }

    // Widget API - REORDER
    if (preg_match('#^/admin/pages/(\d+)/widgets/reorder$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->reorder((int)$matches[1]);
        exit;
    }

    // Pages CRUD
    if ($requestUri === '/admin/pages') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->index();
        exit;
    }

    if ($requestUri === '/admin/pages/create') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->create();
        exit;
    }

    if ($requestUri === '/admin/pages/store' && $method === 'POST') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->store();
        exit;
    }

    if (preg_match('#^/admin/pages/(\d+)/edit$#', $requestUri, $matches)) {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->edit((int)$matches[1]);
        exit;
    }

    if (preg_match('#^/admin/pages/(\d+)/update$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->update((int)$matches[1]);
        exit;
    }

    if (preg_match('#^/admin/pages/(\d+)/delete$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->delete((int)$matches[1]);
        exit;
    }

    // Blog, Events, Media, etc. - podobně...

    // If no route matched, 404
    http_response_code(404);
    if ($method === 'GET') {
        echo '<h1>404 - Admin Page Not Found</h1>';
    } else {
        echo json_encode(['error' => 'Route not found']);
    }
    exit;
}
```

## Session handling

Session FUNGUJE správně, problém byl že:
1. Uživatel se přihlásil
2. Session uložila `$_SESSION['user_id']`
3. AJAX request šel na `/admin/widgets/schema/hero`
4. Ale routing se pokazil (404), takže se ani nedostal do WidgetAdminController
5. Proto vrátil 401 - protože se request vůbec nespustil

Po opravě routingu bude session fungovat normálně.
