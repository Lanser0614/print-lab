# Ready Print From Ready Order Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Automatically create an active ready print when an order request changes to the `ready` status.

**Architecture:** Add nullable source links from `ready_prints` to `order_requests` and `designs`, then centralize creation in a use case so both Filament edit form saves and table status changes call the same idempotent logic. The use case uses the first order design with `preview_image_path`, creates a stable slug, and skips orders without previews.

**Tech Stack:** Laravel migrations, Eloquent models, Filament resource hooks, Laravel feature tests.

---

### Task 1: Database and Model Links

**Files:**
- Create: `database/migrations/2026_05_24_000000_add_order_source_to_ready_prints_table.php`
- Modify: `app/Models/ReadyPrint.php`
- Modify: `database/factories/ReadyPrintFactory.php`
- Test: `tests/Feature/ReadyPrintFromReadyOrderTest.php`

- [ ] **Step 1: Write failing tests for source fields and relations**

Create a feature test that expects a ready print to store `order_request_id` and `source_design_id`, and that manual ready prints may keep both fields null.

- [ ] **Step 2: Run the test and confirm failure**

Run: `rtk php artisan test tests/Feature/ReadyPrintFromReadyOrderTest.php`
Expected: FAIL because the columns and use case do not exist.

- [ ] **Step 3: Add nullable foreign keys**

Add `order_request_id` nullable with `nullOnDelete()`, and `source_design_id` nullable with `nullOnDelete()` and a unique index.

- [ ] **Step 4: Add model fillable fields and relations**

Add `orderRequest()` and `sourceDesign()` belongs-to relations on `ReadyPrint`.

### Task 2: Ready Print Creation Use Case

**Files:**
- Create: `app/UseCases/ReadyPrints/CreateReadyPrintFromOrderRequestUseCase.php`
- Test: `tests/Feature/ReadyPrintFromReadyOrderTest.php`

- [ ] **Step 1: Write tests for behavior**

Test these cases: creates a ready print for `ready` orders with preview designs, does not create duplicates, skips orders without previews, and does nothing for non-ready statuses.

- [ ] **Step 2: Implement use case**

Load order items and designs, pick the first design with `preview_image_path`, create an active `ReadyPrint` with stable slug `order-request-{id}-design-{id}`, title from product snapshot plus order number, and source links.

- [ ] **Step 3: Run use case tests**

Run: `rtk php artisan test tests/Feature/ReadyPrintFromReadyOrderTest.php`
Expected: PASS.

### Task 3: Filament Status Integration

**Files:**
- Modify: `app/Filament/Resources/OrderRequests/Pages/EditOrderRequest.php`
- Modify: `app/Filament/Resources/OrderRequests/Tables/OrderRequestsTable.php`
- Modify: `app/Filament/Resources/ReadyPrints/ReadyPrintResource.php`
- Test: `tests/Feature/ReadyPrintFromReadyOrderTest.php`

- [ ] **Step 1: Wire edit form status save**

After Filament saves an order request, call the use case when the saved status is `ready`.

- [ ] **Step 2: Wire table status column update**

After inline status update, call the same use case when the state is `ready`.

- [ ] **Step 3: Show source order link in ReadyPrint admin**

Add a table column for `orderRequest.id` and a form field or placeholder showing source order/design when present.

- [ ] **Step 4: Run focused tests**

Run: `rtk php artisan test tests/Feature/ReadyPrintFromReadyOrderTest.php tests/Feature/CatalogCategoriesTest.php`
Expected: PASS.

### Task 4: Verification

**Files:**
- Modify only if verification exposes a root cause.

- [ ] **Step 1: Run focused PHP tests**

Run: `rtk php artisan test tests/Feature/ReadyPrintFromReadyOrderTest.php tests/Feature/CatalogCategoriesTest.php tests/Feature/PrintlabFeatureFlagsTest.php`
Expected: PASS.

- [ ] **Step 2: Inspect diff**

Run: `rtk git diff --stat`
Expected: only ready-print automation files and previous pending UI icon/favicon files are changed.
