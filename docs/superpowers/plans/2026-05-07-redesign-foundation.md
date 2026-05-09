# Redesign Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the foundation for the marketplace redesign without changing public page behavior or touching the working constructor.

**Architecture:** Keep the current Blade pages active. Add feature flags, Tailwind design tokens, Manrope loading, and a legacy layout copy so later phases can switch views safely behind flags.

**Tech Stack:** Laravel 12, Blade components, Tailwind 4 `@theme`, Vite, PHPUnit.

---

### Task 1: Feature Flags

**Files:**
- Modify: `.env.example`
- Create: `config/printlab.php`
- Test: `tests/Feature/PrintlabFeatureFlagsTest.php`

- [ ] Add `REDESIGN_V2_ENABLED=false` and `CONSTRUCTOR_V2_ENABLED=false` to `.env.example`.
- [ ] Create `config/printlab.php` with boolean `redesign_v2_enabled` and `constructor_v2_enabled` values backed by `env()`.
- [ ] Add a feature test that verifies both config values default to `false`.
- [ ] Run `rtk php artisan test tests/Feature/PrintlabFeatureFlagsTest.php`.

### Task 2: Design Tokens

**Files:**
- Create: `resources/css/tokens.css`
- Modify: `resources/css/app.css`

- [ ] Create `resources/css/tokens.css` with the PrintLab red/yellow/gray tokens, Manrope `--font-sans`, radii, and shadows from `docs/redesign-tdd.md`.
- [ ] Import `tokens.css` from `resources/css/app.css` after `@import 'tailwindcss';`.
- [ ] Remove the old `--font-sans` theme block from `app.css` so the token file is the source of truth.
- [ ] Run `rtk npm run build`.

### Task 3: Public Layout Foundation

**Files:**
- Create: `resources/views/components/layouts/public.legacy.blade.php`
- Modify: `resources/views/components/layouts/public.blade.php`
- Test: `tests/Feature/PublicLayoutFoundationTest.php`

- [ ] Copy the current public layout into `public.legacy.blade.php`.
- [ ] Add Manrope preconnect and stylesheet links to the active public layout.
- [ ] Keep body/header/footer structure unchanged except for the font token taking effect.
- [ ] Add a feature test that renders `/` and verifies the Manrope stylesheet is present.
- [ ] Run `rtk php artisan test tests/Feature/PublicLayoutFoundationTest.php`.

### Task 4: Verification

**Files:**
- Verify all modified files.

- [ ] Run `rtk php artisan test`.
- [ ] Run `rtk npm run build`.
- [ ] Run `rtk git status --short`.
- [ ] Confirm `resources/views/constructor/show.blade.php` was not modified.
