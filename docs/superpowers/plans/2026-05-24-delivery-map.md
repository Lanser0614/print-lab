# Delivery Map Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add required Tashkent delivery address selection with GPS/map coordinates for order requests and show the delivery point in admin.

**Architecture:** Store delivery address and coordinates on `order_requests`, validate them server-side against a Tashkent service area, and extend constructor checkout payloads. The customer UI uses Yandex Maps with GPS fallback to manual draggable pin; Filament displays saved delivery details and a readonly map.

**Tech Stack:** Laravel FormRequest/DTO/use case, Blade, vanilla JS, Filament, PHPUnit, Yandex Maps JS API.

---

### Task 1: Backend delivery fields and validation

**Files:**
- Create: `database/migrations/2026_05_24_000100_add_delivery_coordinates_to_order_requests_table.php`
- Create: `app/Support/Geo/TashkentDeliveryArea.php`
- Modify: `app/Models/OrderRequest.php`
- Modify: `app/DTO/OrderRequests/CreateOrderRequestData.php`
- Modify: `app/UseCases/OrderRequests/CreateOrderRequestUseCase.php`
- Modify: `app/Http/Requests/StoreOrderRequestRequest.php`
- Test: `tests/Unit/Geo/TashkentDeliveryAreaTest.php`
- Test: `tests/Feature/OrderRequestStoreTest.php`

- [x] Add migration for `delivery_lat` and `delivery_lng` decimal nullable columns.
- [x] Add `TashkentDeliveryArea` with point-in-polygon validation and city normalization.
- [x] Require address, city, latitude and longitude in `StoreOrderRequestRequest`.
- [x] Save delivery fields through DTO and use case.
- [x] Add tests for inside/outside Tashkent and order request validation.

### Task 2: Constructor checkout map UI

**Files:**
- Modify: `config/services.php`
- Modify: `app/Http/Controllers/ConstructorController.php`
- Modify: `resources/views/constructor/v2.blade.php`
- Modify: `resources/views/constructor/show.blade.php`
- Modify: `resources/js/constructor/index.js`
- Modify: `lang/ru/site.php`
- Modify: `lang/uz/site.php`

- [x] Expose Yandex Maps key and Tashkent center to constructor views.
- [x] Add address textarea, hidden coordinates, GPS button and map container to checkout forms.
- [x] Load Yandex Maps JS only when a key exists.
- [x] Initialize a draggable marker, request GPS, and update hidden fields.
- [x] Extend order payloads with `customer_city`, `customer_address`, `delivery_lat`, `delivery_lng`.
- [x] Show a clear fallback if map key or geolocation is unavailable.

### Task 3: Filament admin delivery display

**Files:**
- Create: `resources/views/filament/order-request-delivery-map.blade.php`
- Modify: `app/Filament/Resources/OrderRequests/Schemas/OrderRequestForm.php`
- Modify: `app/Filament/Resources/OrderRequests/Tables/OrderRequestsTable.php`
- Modify: `app/Filament/Resources/OrderRequests/OrderRequestResource.php`

- [x] Add searchable address column to the order requests table.
- [x] Add "Доставка" section with city, address, coordinates and readonly map.
- [x] Eager-load nothing extra because delivery fields live on `order_requests`.

### Task 4: Verification

**Files:**
- Test commands only.

- [x] Run `php artisan test tests/Unit/Geo/TashkentDeliveryAreaTest.php`.
- [x] Run `php artisan test tests/Feature/OrderRequestStoreTest.php tests/Feature/Auth/AuthenticatedOrderRequestStoreTest.php tests/Feature/GoogleTagManagerOnConstructorTest.php`.
- [x] Run relevant formatter if needed.
