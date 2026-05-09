# AI Studio Mobile UX Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Improve the mobile AI-studio flow so users can generate an AI artwork, immediately see it on the shirt, and use it in the constructor without hunting for controls.

**Architecture:** Keep the existing `resources/views/constructor/ai-studio.blade.php` single-page implementation. Add mobile-specific CSS classes and JS behavior: after a successful generation we (a) scroll the preview into view and (b) reveal a sticky mobile CTA that reuses `openGeneratedPrintInConstructor()`. The sticky CTA mirrors the desktop button's `is-disabled` state so users never see a "live" red button without a generated print behind it.

**Tech Stack:** Laravel Blade, vanilla JS, CSS media queries, PHPUnit feature tests.

---

## Что улучшаем

Текущая AI-студия адаптируется в одну колонку на mobile, но этого мало. Нужно:

- sticky mobile CTA снизу — **скрыт до первой успешной генерации**, зеркалит `disabled` состояние десктопной кнопки;
- auto-scroll к preview после генерации, обёрнутый в `requestAnimationFrame` чтобы layout успел стабилизироваться после рендера картинки;
- компактный prompt на телефоне;
- стабильный preview без overflow на маленьких экранах (iPhone SE 375×667);
- явный текст про лимит 2 генерации рисунка в день рядом с кнопкой;
- `-webkit-backdrop-filter` fallback для старых iOS Safari.

---

### Task 1: Mobile UX Test Coverage

**Files:**

- Modify: `tests/Feature/AiPrintStudioPageTest.php`
- Test: `tests/Feature/AiPrintStudioPageTest.php`

- [x] **Step 1: Write the failing test**

Add a new test. Note: `ai_studio_daily_limit_hint` is already covered by `test_ai_print_studio_page_renders_chat_and_preview_ui`, so we don't duplicate it here — мы хотим, чтобы новый тест падал ТОЛЬКО на новых ассершенах.

```php
public function test_ai_print_studio_includes_mobile_ux_hooks(): void
{
    $product = $this->productWithVariant();

    $response = $this->get(route('ai-studio.show', $product));

    $response
        ->assertOk()
        ->assertSee('class="studio-mobile-cta', false)
        ->assertSee('id="aiStudioMobileCta"', false)
        ->assertSee('id="aiStudioMobileOpenConstructor"', false)
        ->assertSee('aria-hidden="true"', false)
        ->assertSee('function scrollAiStudioPreviewIntoView', false)
        ->assertSee('function syncAiStudioMobileCta', false)
        ->assertSee('@media (max-width: 640px)', false)
        ->assertSee('-webkit-backdrop-filter', false);
}
```

- [ ] **Step 2: Run the test and verify red**

```bash
rtk php artisan test --filter=test_ai_print_studio_includes_mobile_ux_hooks
```

Expected: FAIL on every new assertion (CTA markup, helper functions, mobile media query, Safari vendor prefix). Other tests in the file должны оставаться зелёными.

---

### Task 2: Sticky Mobile CTA (initially hidden, mirrors disabled)

**Files:**

- Modify: `resources/views/constructor/ai-studio.blade.php`
- Test: `tests/Feature/AiPrintStudioPageTest.php`

- [x] **Step 1: Add mobile CTA markup (hidden + disabled by default)**

Near the end of `.studio-shell`, before `</div>`, add:

```blade
<div class="studio-mobile-cta" id="aiStudioMobileCta" aria-hidden="true">
  <button
    class="studio-mobile-cta__button"
    id="aiStudioMobileOpenConstructor"
    type="button"
    disabled
    onclick="openGeneratedPrintInConstructor(event)"
  >
    {{ __('site.ai_studio_open_constructor') }}
  </button>
</div>
```

CTA стартует скрытым и `disabled` — открывается только после успешной генерации (Task 3). Это убирает always-visible красную полосу до того, как пользователю есть что открывать.

- [x] **Step 2: Add CSS with default-hidden + visible state + Safari prefix**

```css
.studio-mobile-cta {
  display: none;
}

.studio-mobile-cta__button {
  width: 100%;
  min-height: 52px;
  border: 0;
  border-radius: 8px;
  background: #e50914;
  color: #fff;
  font: inherit;
  font-weight: 900;
  cursor: pointer;
}

.studio-mobile-cta__button[disabled] {
  opacity: 0.55;
  cursor: not-allowed;
}

@media (max-width: 640px) {
  .studio-mobile-cta.is-visible {
    position: sticky;
    bottom: 0;
    z-index: 30;
    display: block;
    padding: 10px 14px calc(10px + env(safe-area-inset-bottom));
    border-top: 1px solid #e4e4e7;
    background: rgba(255, 255, 255, 0.94);
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
  }
}
```

- [ ] **Step 3: Run focused test**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Expected: CTA markup, vendor prefix, and media query assertions pass; helper-function assertions all still fail (это нормально — они закроются в Task 3).

---

### Task 3: Auto-Scroll + Reveal Mobile CTA After Generation

**Files:**

- Modify: `resources/views/constructor/ai-studio.blade.php`
- Test: `tests/Feature/AiPrintStudioPageTest.php`

- [x] **Step 1: Add scroll helper (rAF-wrapped)**

```js
function scrollAiStudioPreviewIntoView() {
  if (!window.matchMedia('(max-width: 640px)').matches) return;

  requestAnimationFrame(() => {
    document.getElementById('aiStudioPreview')?.scrollIntoView({
      behavior: 'smooth',
      block: 'center',
    });
  });
}
```

`requestAnimationFrame` нужен на случай, если в `showAiStudioResult` ещё догружается `<img>` — без него позиция уплывёт после layout shift.

- [x] **Step 2: Add mobile-CTA sync helper**

Этот хелпер зеркалит `disabled` состояние десктопной кнопки в мобильную и показывает/прячет контейнер.

```js
function syncAiStudioMobileCta() {
  const cta = document.getElementById('aiStudioMobileCta');
  const mobileBtn = document.getElementById('aiStudioMobileOpenConstructor');
  const desktopBtn = document.getElementById('aiStudioOpenConstructor');
  if (!cta || !mobileBtn || !desktopBtn) return;

  const ready =
    !desktopBtn.classList.contains('is-disabled') && !desktopBtn.disabled;

  cta.classList.toggle('is-visible', ready);
  cta.setAttribute('aria-hidden', ready ? 'false' : 'true');
  mobileBtn.disabled = !ready;
}
```

- [x] **Step 3: Call helpers after successful result**

Inside `showAiStudioResult(...)`, после success-сообщения:

```js
syncAiStudioMobileCta();
scrollAiStudioPreviewIntoView();
```

Дополнительно вызывать `syncAiStudioMobileCta()` в каждой точке, где меняется состояние десктопной кнопки (start of fetch → disabled, error path → disabled, retry success → enabled), чтобы мобильная CTA не "залипала" в активном состоянии.

- [ ] **Step 4: Run focused test**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Expected: все helper-function ассершены теперь зелёные.

---

### Task 4: Mobile Layout Polish

**Files:**

- Modify: `resources/views/constructor/ai-studio.blade.php`
- Test: `tests/Feature/AiPrintStudioPageTest.php`

- [x] **Step 1: Add compact mobile CSS (safe sizes for iPhone SE)**

Inside `@media (max-width: 640px)` add:

```css
.studio-main {
  padding: 10px;
  gap: 12px;
}

.studio-chat {
  padding: 16px;
  gap: 14px;
}

.studio-textarea {
  min-height: 116px;
}

.studio-preview-panel {
  padding: 14px;
  gap: 12px;
}

.studio-preview {
  min-height: min(60vh, 420px);
  aspect-ratio: 4 / 5;
}

.studio-preview-head .studio-link {
  display: none;
}

.studio-variant-row {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
```

`min(60vh, 420px)` сохраняет читаемый preview на нормальных телефонах, но не выталкивает sticky CTA за экран на iPhone SE (568-667px высота).

- [x] **Step 2: Extend test (required, not optional)**

Расширить новый тест:

```php
->assertSee('min-height: 116px', false)
->assertSee('aspect-ratio: 4 / 5', false)
->assertSee('grid-template-columns: repeat(2, minmax(0, 1fr))', false)
->assertSee('min-height: min(60vh, 420px)', false)
```

- [ ] **Step 3: Run focused test**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Expected: все AI-studio page тесты проходят.

---

### Task 5: Verification

**Files:**

- Test only.

- [ ] **Step 1: Run AI-studio tests**

```bash
rtk php artisan test --filter=AiPrintStudio
```

- [ ] **Step 2: Run constructor regression**

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

- [ ] **Step 3: Manual mobile browser check на ДВУХ viewport**

Проверить и iPhone SE (`375×667`), и iPhone 14 Pro (`390×844`):

```text
http://localhost:8000/ru/ai-studio/classic-t-shirt
```

Чек-лист:

- prompt area не слишком высокий, помещается без CTA;
- preview имеет достаточную высоту, не выпадает за экран на 667px-высоких устройствах;
- **sticky CTA скрыт до первой генерации** — внизу экрана нет красной полосы пока пользователь ничего не получил;
- во время fetch десктопная и мобильная кнопки одновременно в `disabled`;
- после успешной генерации страница плавно скроллится к preview, sticky CTA появляется снизу;
- sticky CTA открывает конструктор и сгенерированный артворк появляется слоем;
- **после 2-й генерации за сутки** виден текст про лимит, и обе кнопки (desktop + mobile) уходят в `disabled`;
- z-index 30 не конфликтует с другими sticky/fixed элементами на странице.
