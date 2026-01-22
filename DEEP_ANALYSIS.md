# HLUBOKÁ ANALÝZA ADMIN SYSTÉMU

## Co se pokazilo

1. `/admin` nefunguje
2. Session se neudržuje
3. AJAX requesty se vůbec nevolají (není v Network tabu)

## Co jsem změnil

V `public/index.php` jsem změnil admin routing:

### PŘED (funkční):
```php
if (strpos($requestUri, '/admin') === 0) {
    $_SESSION['language'] = 'cs';
    
    if ($adminPath === '/admin' || $adminPath === '/admin/') {
        $controller = new \App\Controllers\Admin\DashboardController();
        $controller->index();
        exit;
    }
    
    if ($adminPath === '/admin/login') {
        $controller = new \App\Controllers\Admin\AuthController();
        // ...
        exit;
    }
    // ... další
}
```

### PO (nefunkční):
```php
if (strpos($requestUri, '/admin') === 0) {
    $_SESSION['language'] = 'cs';
    
    try {
        $router->dispatch();
    } catch (Exception $e) {
        // ...
    }
    exit;
}
```

## Problém

Router->dispatch() očekává URI BEZ `/labyrint` prefixu, ale v bloku admin routes je URI už zpracované.

Také Router odstraňuje jazyk z URI, ale admin routes nemají jazyk!

## Analýza potřebná

1. Jak Router zpracovává URI?
2. Jak Router matchuje routes?
3. Proč admin routes byly separátní?
4. Jak session funguje across requests?
5. Proč se JavaScript nespouští?
