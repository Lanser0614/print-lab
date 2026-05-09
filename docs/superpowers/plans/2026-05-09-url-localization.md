# URL Localization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add Russian and Uzbek URL-prefixed localization with cookie/session persistence.

**Architecture:** Public routes are grouped under `{locale}` with `ru` and `uz` constraints. Middleware reads the URL locale first, updates `app()->getLocale()`, session, and a long-lived cookie, while `/` redirects to the remembered locale or Russian by default. Layout helpers generate same-page language switch links and valid hreflang URLs.

**Tech Stack:** Laravel routes, middleware, Blade layouts, PHPUnit feature tests.

---

### Task 1: Tests

**Files:**
- Create: `tests/Feature/LocalizationRoutingTest.php`

- [ ] Add feature tests for `/` redirect, localized pages, cookie/session persistence, language switching, and invalid locale 404 behavior.
- [ ] Run `php artisan test tests/Feature/LocalizationRoutingTest.php` and confirm the tests fail before implementation.

### Task 2: Routing And Middleware

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Middleware/SetLocale.php`

- [ ] Group public GET/POST routes under `{locale}` with a `ru|uz` constraint.
- [ ] Keep `/` as a redirect to the saved cookie/session locale, falling back to `/ru`.
- [ ] Keep `/language/{locale}` as a compatibility redirect that saves the locale and redirects to `/{locale}`.
- [ ] Update middleware so URL locale takes priority and persists to session/cookie.

### Task 3: Layout Links

**Files:**
- Modify: `resources/views/components/layouts/public.blade.php`
- Modify: `resources/views/components/layouts/public.legacy.blade.php`

- [ ] Make canonical URLs include the localized current URL.
- [ ] Generate RU/UZ switch links by replacing the first URL segment.
- [ ] Generate hreflang links to the same current page in each language.

### Task 4: Verification

- [ ] Run `php artisan test tests/Feature/LocalizationRoutingTest.php`.
- [ ] Run the existing route-related tests if needed.
- [ ] Review `git diff` for unrelated changes.
