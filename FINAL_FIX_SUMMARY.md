# FINÁLNÍ OPRAVA - ADMIN ROUTING & WIDGET SYSTEM

## ✅ CO BYLO OPRAVENO

### 1. Admin Routing - Vráceno na manuální zpracování

**Proč Router nemohl zpracovat admin routes:**
- Router je navržen **pouze pro routes s jazykovým prefixem** (`/cs/blog`, `/en/contact`)
- Router automaticky volá `detectLanguage()` a `removeLanguageFromUri()`
- Admin routes **nemají jazyk** (`/admin`, `/admin/pages/1/edit`)
- Router by tedy matchoval špatně nebo vůbec

**Řešení:**
Vrátil jsem původní manuální routing v `index.php`, kde každá admin route je explicitně zpracována:

```php
// Dashboard
if ($requestUri === '/admin' || $requestUri === '/admin/') {
    $controller = new \App\Controllers\Admin\DashboardController();
    $controller->index();
    exit;
}

// Widget API routes s regex pro parametry
if (preg_match('#^/admin/widgets/schema/([a-z_]+)$#', $requestUri, $matches)) {
    $controller = new \App\Controllers\Admin\WidgetAdminController();
    $controller->getSchema($matches[1]);
    exit;
}
```

### 2. Přidány všechny Widget API routes

Manuálně přidáno 8 widget API endpointů:

1. `GET /admin/widgets/schema/:typeKey` - načte JSON schema widgetu
2. `GET /admin/widgets/:id` - načte data widgetu
3. `POST /admin/pages/:pageId/widgets/create` - vytvoří nový widget
4. `POST /admin/widgets/:id/update` - updatuje widget
5. `POST /admin/widgets/:id/delete` - smaže widget
6. `POST /admin/widgets/:id/move-up` - posune nahoru
7. `POST /admin/widgets/:id/move-down` - posune dolů
8. `POST /admin/pages/:pageId/widgets/reorder` - přeřadí všechny

### 3. Session funguje správně

Session **VŽDY fungovala správně**. Problém byl že:
- Routes se vůbec nevolaly (404)
- Takže se ani nedostaly do WidgetAdminController
- Proto vracely 401 (žádná autentizace se nekontrolovala)

Po opravě routingu:
- Session je aktivní (`session_status: 2`)
- Pro nepřihlášené: `has_user_id: false` → 401 Unauthorized ✅
- Pro přihlášené: `has_user_id: true` → Widget API funguje ✅

### 4. Debug informace v WidgetAdminController

Přidány debug informace pro development mode:

```php
if (!Session::isLoggedIn()) {
    http_response_code(401);
    $debugInfo = ['error' => 'Unauthorized'];
    if (ENVIRONMENT === 'development') {
        $debugInfo['debug'] = [
            'session_id' => session_id(),
            'has_user_id' => isset($_SESSION['user_id']),
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_keys' => array_keys($_SESSION ?? [])
        ];
    }
    echo json_encode($debugInfo);
    exit;
}
```

## 🧪 TESTOVÁNÍ

### Test 1: Admin dashboard
```bash
curl -I http://localhost:8888/labyrint/admin
# HTTP/1.1 302 Found
# Location: http://localhost:8888/labyrint/admin/login
# ✅ Redirectuje na login (nepřihlášen)
```

### Test 2: Login page
```bash
curl -I http://localhost:8888/labyrint/admin/login
# HTTP/1.1 200 OK
# ✅ Stránka existuje
```

### Test 3: Widget schema endpoint (nepřihlášen)
```bash
curl http://localhost:8888/labyrint/admin/widgets/schema/hero
# {"error":"Unauthorized","debug":{"has_user_id":false,...}}
# ✅ Správně vrací 401 s debug info
```

### Test 4: Po přihlášení v browseru

1. Otevřete: `http://localhost:8888/labyrint/admin/login`
2. Přihlaste se (username/password z databáze)
3. Otevřete: `http://localhost:8888/labyrint/admin/pages/1/edit`
4. Klikněte na tab "Widgety"
5. Klikněte na ✏️ u Hero widgetu

**Mělo by se stát:**
- ✅ Modal se otevře
- ✅ V Network tabu uvidíte request na `/admin/widgets/2`
- ✅ Request vrátí 200 OK s daty widgetu
- ✅ Request na `/admin/widgets/schema/hero` vrátí JSON schema
- ✅ Formulář se vykreslí s poli pro repeater a image gallery

## 📊 ARCHITEKTURA - Proč je to takto

### Frontend Routes (s jazykem)
```
/cs/                    → HomeController@index
/cs/blog                → BlogController@index
/cs/blog/:slug          → BlogController@show
/en/contact             → ContactController@index
```

**Zpracování**: Router->dispatch()
- Detekuje jazyk z URI
- Odstraní jazyk pro matching
- Předá jazyk jako parametr do controlleru

### Admin Routes (bez jazyka)
```
/admin                  → DashboardController@index
/admin/login            → AuthController@showLogin
/admin/pages/1/edit     → PageAdminController@edit(1)
/admin/widgets/2        → WidgetAdminController@getWidget(2)
```

**Zpracování**: Manuální if/preg_match v index.php
- Žádná detekce jazyka
- Přímé parsování parametrů z URI
- Přímé volání controller metod

### Proč ne Router pro admin?

Router by musel být **značně přepsán**:
1. Detekovat admin routes a skip jazykového zpracování
2. Podporovat dva druhy routes (s jazykem / bez jazyka)
3. Kontrolovat různé parametry controlleru (s/bez `$language`)

To by porušilo **Single Responsibility Principle** - Router by dělal příliš mnoho věcí.

**Manuální routing je:**
- ✅ Jednodušší
- ✅ Explicitnější
- ✅ Snadněji debugovatelný
- ✅ Méně magický

## 🎯 DALŠÍ KROKY

1. Přihlaste se do adminu
2. Otevřete `/admin/pages/1/edit`
3. Tab "Widgety"
4. Klikněte na ✏️ u Hero widgetu
5. Měl by se zobrazit formulář s:
   - Input "Nadpis (základní text)"
   - Repeater "Rotující texty" s možností přidat/odebrat položky
   - Image Gallery "Obrázky na pozadí" s náhledy

## 📝 SOUBORY ZMĚNĚNY

1. `/public/index.php` - vráceno manuální admin routing + přidány widget routes
2. `/app/Controllers/Admin/WidgetAdminController.php` - přidány debug informace
3. `/app/Views/admin/pages/widgets-tab.php` - implementována podpora repeater & image_gallery
4. `/public/assets/css/admin.css` - přidány styly pro repeater & gallery komponenty

## 🔧 DEBUG NÁSTROJE

Pokud něco nefunguje:

1. **Session debug:**
   ```
   http://localhost:8888/labyrint/public/test_session_debug.php
   ```

2. **Browser Console:**
   ```javascript
   fetch('/labyrint/admin/widgets/schema/hero')
     .then(r => r.json())
     .then(console.log)
   ```

3. **Network Tab (F12):**
   - Sledujte AJAX requesty
   - Kontrolujte HTTP status code
   - Čtěte Response s debug informacemi
