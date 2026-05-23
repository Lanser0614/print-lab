# PrintLab PWA App Shell Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build an installable PWA shell so PrintLab works like a mobile app on iOS and Android while preserving the existing Laravel site and constructor.

**Architecture:** Add static PWA assets and metadata at the public layout level, register a conservative service worker from the existing Vite JS entry, and add a shared Blade mobile shell for public commerce pages only. The constructor keeps its own specialized mobile controls with safe-area adjustments.

**Tech Stack:** Laravel Blade, Laravel feature tests, Vite, vanilla JavaScript, CSS, public static assets.

---

### Task 1: PWA Metadata and Static Assets

**Files:**
- Create: `public/manifest.webmanifest`
- Create: `public/sw.js`
- Create: `public/icons/icon.svg`
- Create: `public/icons/icon-192.png`
- Create: `public/icons/icon-512.png`
- Create: `public/icons/apple-touch-icon.png`
- Modify: `resources/views/components/layouts/public.blade.php`
- Test: `tests/Feature/PwaAppShellTest.php`

- [ ] **Step 1: Write failing tests for manifest, service worker, and layout metadata**

Create `tests/Feature/PwaAppShellTest.php` with assertions that `/manifest.webmanifest` and `/sw.js` are available, and that `/ru` includes manifest, theme color, Apple metadata, and service worker registration entrypoint.

- [ ] **Step 2: Run PWA tests and verify they fail**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php`
Expected: FAIL because the files and layout metadata do not exist yet.

- [ ] **Step 3: Add manifest, icons, service worker, and layout metadata**

Add the manifest and icons under `public/`, add conservative service worker fetch behavior, and link metadata in `resources/views/components/layouts/public.blade.php`.

- [ ] **Step 4: Run PWA tests and verify they pass**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php`
Expected: PASS.

### Task 2: Service Worker Registration and Install Prompt

**Files:**
- Modify: `resources/js/app.js`
- Modify: `resources/css/app.css`
- Create: `resources/views/partials/pwa-install-prompt.blade.php`
- Modify: `resources/views/components/layouts/public.blade.php`
- Test: `tests/Feature/PwaAppShellTest.php`

- [ ] **Step 1: Extend tests for install prompt markup**

Assert that public pages include a non-blocking install prompt shell and a script target for Android/iOS installation education.

- [ ] **Step 2: Run tests and verify failure**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php`
Expected: FAIL until the partial and JS are added.

- [ ] **Step 3: Implement registration and prompt behavior**

Register `sw.js` after window load when supported, capture `beforeinstallprompt`, show Android install action, show iOS education copy for Safari standalone candidates, and persist dismissal in local storage.

- [ ] **Step 4: Run tests and verify pass**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php`
Expected: PASS.

### Task 3: Public Mobile App Shell

**Files:**
- Create: `resources/views/partials/mobile-app-shell.blade.php`
- Modify: `resources/views/catalog/home-v2.blade.php`
- Modify: `resources/views/catalog/index.blade.php`
- Modify: `resources/views/products/show.blade.php`
- Modify: `resources/views/prints/show.blade.php`
- Modify: `resources/views/account/index.blade.php`
- Modify: `resources/css/app.css`
- Test: `tests/Feature/PwaAppShellTest.php`

- [ ] **Step 1: Add failing tests for public shell inclusion and constructor exclusion**

Assert that home/catalog/product/print/account pages include `pl-mobile-app-shell`, and constructor pages do not include it.

- [ ] **Step 2: Run tests and verify failure**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php`
Expected: FAIL until the shell partial is included.

- [ ] **Step 3: Implement mobile shell partial and include it only on public pages**

Use five tabs: Home, Catalog, Create, Prints, Account. Keep it hidden on desktop via CSS. Do not include it in constructor Blade files.

- [ ] **Step 4: Run tests and verify pass**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php`
Expected: PASS.

### Task 4: Constructor PWA Safe-Area Guardrails

**Files:**
- Modify: `resources/views/constructor/v2.blade.php`
- Test: `tests/Feature/PwaAppShellTest.php`

- [ ] **Step 1: Add tests for constructor standalone metadata and no public shell**

Assert constructor pages include PWA metadata through their own head where needed or at minimum retain service worker availability globally, and that they do not render public mobile nav.

- [ ] **Step 2: Add safe-area CSS guardrails**

Adjust constructor mobile bottom bar, add menu, drawer panels, and order dialog spacing so standalone iOS viewport does not hide controls.

- [ ] **Step 3: Run constructor-related tests**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php tests/Feature/ConstructorRedesignFlagTest.php tests/Feature/ConstructorAiPrintUiTest.php`
Expected: PASS.

### Task 5: Build and Regression Verification

**Files:**
- Modify only if verification exposes a root cause.

- [ ] **Step 1: Run frontend build**

Run: `rtk npm run build`
Expected: PASS.

- [ ] **Step 2: Run relevant PHP tests**

Run: `rtk php artisan test tests/Feature/PwaAppShellTest.php tests/Feature/PublicLayoutFoundationTest.php tests/Feature/PrintlabFeatureFlagsTest.php tests/Feature/ConstructorRedesignFlagTest.php tests/Feature/ConstructorAiPrintUiTest.php`
Expected: PASS.

- [ ] **Step 3: Inspect git diff**

Run: `rtk git diff --stat`
Expected: only PWA shell, metadata, assets, tests, and plan/spec files changed.
