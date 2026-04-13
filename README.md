# TimeBooking Package

Laravel 11 időpontfoglaló csomag PostgreSQL 9.6 adatbázissal.

## Telepítés

### 1. Csomag telepítése

```bash
composer require weboldalnet/time-booking
```

### 2. Service Provider regisztrálása

A csomag automatikusan regisztrálja magát Laravel 11-ben az auto-discovery funkcióval.

### 3. Migrációk futtatása

```bash
php artisan migrate
```

### 4. Fájlok publikálása

#### Összes fájl publikálása egyszerre:
```bash
php artisan vendor:publish --tag=timebooking-all
```

#### Vagy külön-külön:

**Config fájl publikálása:**
```bash
php artisan vendor:publish --tag=timebooking-config
```

**View fájlok publikálása:**
```bash
php artisan vendor:publish --tag=timebooking-views
```

**Asset fájlok publikálása:**
```bash
php artisan vendor:publish --tag=timebooking-assets
```

## Konfiguráció

### Config fájl

A config fájl publikálása után a `config/timebooking.php` fájlban módosíthatja a beállításokat:

```php
return [
    // Adatbázis tábla nevek
    'database' => [
        'tables' => [
            'categories' => 'timebooking_categories',
            'appointments' => 'timebooking_appointments',
            'bookings' => 'timebooking_bookings',
        ],
    ],

    // Route beállítások
    'routes' => [
        'admin' => [
            'prefix' => 'timebooking',
            'middleware' => ['web', 'admin_share', 'auth:admin'],
        ],
        'site' => [
            'prefix' => 'idopontfoglalas',
            'middleware' => ['web', 'site_share'],
        ],
    ],

    // Foglalási beállítások
    'booking' => [
        'default_capacity' => 1,
        'max_capacity' => 100,
        'auto_confirm' => true,
        'allow_past_bookings' => false,
        'booking_deadline_hours' => 2,
    ],

    // Email értesítések
    'email' => [
        'enabled' => true,
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Example'),
        ],
    ],

    // Funkciók be/kikapcsolása
    'features' => [
        'bulk_appointment_creation' => true,
        'appointment_categories' => true,
        'booking_messages' => true,
        'email_notifications' => true,
        'admin_dashboard' => true,
        'export_functionality' => true,
        'real_time_updates' => false,
    ],
];
```

### Fájlok felülírása

A publikált fájlok **automatikusan felülírják** a csomagban lévő fájlokat. Ez azt jelenti, hogy:

1. **Config fájl**: A `config/timebooking.php` fájl értékei elsőbbséget élveznek a csomag alapértelmezett beállításaival szemben.

2. **View fájlok**: A publikált view fájlok (`resources/views/admin/` és `resources/views/site/`) felülírják a csomag view fájljait.

3. **Asset fájlok**: A publikált CSS és JS fájlok (`public/timebooking/`) felülírják a csomag asset fájljait.

### Konfiguráció használata a kódban

```php
// Config értékek elérése
$defaultCapacity = config('timebooking.booking.default_capacity');
$maxCapacity = config('timebooking.booking.max_capacity');
$emailEnabled = config('timebooking.email.enabled');

// Tábla nevek elérése
$categoriesTable = config('timebooking.database.tables.categories');
$appointmentsTable = config('timebooking.database.tables.appointments');
```

## Használat

### Admin felület

Az admin felület elérhető a következő URL-en:
```
https://admin.yourdomain.com/timebooking
```

**Funkciók:**
- Kategóriák kezelése
- Időpontok kezelése
- Tömeges időpont létrehozás
- Foglalások megtekintése és kezelése

### Nyilvános oldal

A foglalási felület elérhető a következő URL-en:
```
https://yourdomain.com/idopontfoglalas
```

**Funkciók:**
- Kategóriák böngészése
- Elérhető időpontok megtekintése
- Időpont foglalása
- Köszönő oldal

## Adatbázis struktúra

### Táblák

1. **timebooking_categories** - Kategóriák
2. **timebooking_appointments** - Időpontok
3. **timebooking_bookings** - Foglalások

### Kapcsolatok

- Egy kategóriához több időpont tartozhat
- Egy időponthoz több foglalás tartozhat

## Testreszabás

### View fájlok testreszabása

1. Publikálja a view fájlokat:
```bash
php artisan vendor:publish --tag=timebooking-views
```

2. Módosítsa a fájlokat a `resources/views/` könyvtárban.

### Asset fájlok testreszabása

1. Publikálja az asset fájlokat:
```bash
php artisan vendor:publish --tag=timebooking-assets
```

2. Módosítsa a CSS és JS fájlokat a `public/timebooking/` könyvtárban.

### Konfiguráció testreszabása

1. Publikálja a config fájlt:
```bash
php artisan vendor:publish --tag=timebooking-config
```

2. Módosítsa a beállításokat a `config/timebooking.php` fájlban.

## Frissítés

A csomag frissítésekor a publikált fájlok **nem** íródnak felül automatikusan. Ha új funkciókat szeretne használni, újra publikálnia kell a fájlokat:

```bash
php artisan vendor:publish --tag=timebooking-all --force
```

**Figyelem:** A `--force` kapcsoló felülírja a meglévő publikált fájlokat!

## Támogatás

Ha kérdése van vagy problémába ütközik, kérjük, vegye fel a kapcsolatot a fejlesztővel.

## Licenc

Ez a csomag a MIT licenc alatt áll.
