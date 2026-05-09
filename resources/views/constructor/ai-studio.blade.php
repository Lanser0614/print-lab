@php
    $productName = $product->localizedName();
    $constructorUrl = route('constructor.show', ['product' => $product, 'variant' => $variant->id, 'side' => $side]);
    $studioConfig = [
        'product' => [
            'id' => $product->id,
            'name' => $productName,
            'slug' => $product->slug,
        ],
        'variant' => [
            'id' => $variant->id,
            'color' => $variant->color,
            'size' => $variant->size,
            'mockup_front_url' => $variant->mockup_front_url,
            'mockup_back_url' => $variant->mockup_back_url,
        ],
        'printArea' => [
            'side' => $side,
            'x' => $printArea->x,
            'y' => $printArea->y,
            'width' => $printArea->width,
            'height' => $printArea->height,
            'unit' => 'ratio',
        ],
        'routes' => [
            'generatePrint' => url('/api/generated-prints'),
            'constructor' => $constructorUrl,
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
@include('partials.gtm-head')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('site.ai_studio_title') }} — {{ $productName }}</title>
<meta name="robots" content="noindex, nofollow">
<script>window.aiStudioConfig = {{ Illuminate\Support\Js::from($studioConfig) }};</script>
<style>
*, *::before, *::after { box-sizing: border-box; }
body {
  margin: 0;
  min-height: 100vh;
  background: #f4f5f7;
  color: #18181b;
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}
.studio-shell { min-height: 100vh; display: flex; flex-direction: column; }
.studio-header {
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 0 24px;
  background: #fff;
  border-bottom: 1px solid #e4e4e7;
}
.studio-brand {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 42px;
  padding: 0 18px;
  border-radius: 8px;
  background: #e50914;
  color: #fff;
  font-size: 22px;
  font-weight: 900;
  text-decoration: none;
}
.studio-product { min-width: 0; flex: 1; }
.studio-product-title { margin: 0; font-size: 18px; font-weight: 800; line-height: 1.2; }
.studio-product-meta { margin-top: 3px; color: #71717a; font-size: 13px; }
.studio-header-actions { display: flex; align-items: center; gap: 10px; }
.studio-link,
.studio-primary {
  display: inline-flex;
  min-height: 42px;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  padding: 0 16px;
  font-size: 14px;
  font-weight: 800;
  text-decoration: none;
}
.studio-link { border: 1px solid #d4d4d8; color: #27272a; background: #fff; }
.studio-primary { border: 1px solid #e50914; color: #fff; background: #e50914; }
.studio-primary.is-disabled {
  opacity: 0.55;
  cursor: not-allowed;
}
.studio-main {
  width: min(1440px, 100%);
  margin: 0 auto;
  padding: 24px;
  display: grid;
  grid-template-columns: minmax(320px, 430px) minmax(0, 1fr);
  gap: 24px;
  flex: 1;
}
.studio-panel {
  background: #fff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
}
.studio-chat {
  padding: 22px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.studio-eyebrow {
  margin: 0 0 8px;
  color: #e50914;
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}
.studio-title { margin: 0; font-size: 30px; line-height: 1.05; font-weight: 900; }
.studio-subtitle { margin: 10px 0 0; color: #52525b; font-size: 15px; line-height: 1.55; }
.studio-field { display: grid; gap: 8px; }
.studio-label { font-size: 13px; font-weight: 800; color: #27272a; }
.studio-textarea {
  width: 100%;
  min-height: 148px;
  resize: vertical;
  border: 1px solid #d4d4d8;
  border-radius: 8px;
  padding: 13px 14px;
  font: inherit;
  line-height: 1.45;
  outline: none;
}
.studio-textarea:focus { border-color: #e50914; box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.12); }
.studio-upload {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  min-height: 52px;
  border: 1px dashed #c4c4c8;
  border-radius: 8px;
  padding: 0 14px;
  color: #52525b;
  cursor: pointer;
}
.studio-upload strong { color: #27272a; font-size: 14px; }
.studio-upload span { color: #71717a; font-size: 12px; }
.studio-generate {
  min-height: 52px;
  border: 0;
  border-radius: 8px;
  background: #e50914;
  color: #fff;
  font: inherit;
  font-weight: 900;
  cursor: pointer;
}
.studio-hint { margin: 0; color: #71717a; font-size: 12px; line-height: 1.45; }
.studio-preview-panel {
  min-height: 620px;
  padding: 22px;
  display: grid;
  grid-template-rows: auto minmax(360px, 1fr) auto;
  gap: 18px;
}
.studio-preview-head { display: flex; justify-content: space-between; gap: 18px; align-items: start; }
.studio-preview-title { margin: 0; font-size: 18px; font-weight: 900; }
.studio-preview-meta { margin-top: 4px; color: #71717a; font-size: 13px; }
.studio-preview {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 420px;
  border-radius: 8px;
  background: #f8fafc;
  border: 1px solid #e4e4e7;
}
.studio-product-mockup { max-width: 82%; max-height: 82%; object-fit: contain; }
.studio-print-zone {
  position: absolute;
  border: 2px dashed rgba(229, 9, 20, 0.6);
  background: rgba(229, 9, 20, 0.04);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.studio-print-overlay {
  display: none;
  width: 70%;
  max-width: 70%;
  max-height: 70%;
  object-fit: contain;
  filter: drop-shadow(0 12px 18px rgba(24, 24, 27, 0.14));
}
.studio-print-overlay.visible { display: block; }
.studio-print-zone.has-result {
  border-color: rgba(22, 163, 74, 0.55);
  background: rgba(22, 163, 74, 0.04);
}
.studio-empty-print {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  width: min(220px, 42%);
  padding: 14px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid #e4e4e7;
  color: #52525b;
  text-align: center;
  font-size: 13px;
  font-weight: 700;
}
.studio-variants { display: grid; gap: 10px; }
.studio-variants-title { margin: 0; font-size: 13px; color: #71717a; font-weight: 900; letter-spacing: 0.12em; text-transform: uppercase; }
.studio-variant-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
.studio-variant-slot {
  aspect-ratio: 1.25;
  border-radius: 8px;
  border: 1px dashed #d4d4d8;
  background: #fafafa;
}
.studio-variant-slot.has-result {
  border-style: solid;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
}
.studio-variant-slot.has-result img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}
.studio-message {
  min-height: 18px;
  color: #71717a;
  font-size: 13px;
  line-height: 1.4;
}
.studio-message.error { color: #dc2626; }
.studio-message.success { color: #15803d; }
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
@media (max-width: 900px) {
  .studio-header { height: auto; padding: 14px; align-items: flex-start; flex-wrap: wrap; }
  .studio-brand { font-size: 18px; min-height: 38px; }
  .studio-header-actions { width: 100%; }
  .studio-header-actions a { flex: 1; }
  .studio-main { grid-template-columns: 1fr; padding: 14px; }
  .studio-title { font-size: 26px; }
  .studio-preview-panel { min-height: auto; }
  .studio-preview { min-height: 360px; }
}
@media (max-width: 640px) {
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
</style>
</head>
<body>
@include('partials.gtm-body')
<div class="studio-shell">
  <header class="studio-header">
    <a class="studio-brand" href="{{ route('home') }}">PrintLab</a>
    <div class="studio-product">
      <h1 class="studio-product-title">{{ $productName }}</h1>
      <div class="studio-product-meta">{{ $variant->color }}@if($variant->size) / {{ $variant->size }}@endif</div>
    </div>
    <div class="studio-header-actions">
      <a class="studio-link" href="{{ route('products.show', $product) }}">{{ __('site.nav_catalog') }}</a>
      <a class="studio-primary is-disabled" id="aiStudioOpenConstructor" href="{{ $constructorUrl }}" onclick="openGeneratedPrintInConstructor(event)">{{ __('site.ai_studio_open_constructor') }}</a>
    </div>
  </header>

  <main class="studio-main">
    <section class="studio-panel studio-chat" id="aiStudioChat" data-generate-route="/api/generated-prints" aria-label="{{ __('site.ai_studio_title') }}">
      <div>
        <p class="studio-eyebrow">{{ __('site.ai_studio_title') }}</p>
        <h2 class="studio-title">{{ __('site.ai_studio_title') }}</h2>
        <p class="studio-subtitle">{{ __('site.ai_studio_subtitle') }}</p>
      </div>

      <div class="studio-field">
        <label class="studio-label" for="aiStudioPrompt">{{ __('site.ai_studio_prompt_label') }}</label>
        <textarea
          class="studio-textarea"
          id="aiStudioPrompt"
          maxlength="250"
          placeholder="{{ __('site.ai_studio_prompt_placeholder') }}"
        ></textarea>
      </div>

      <label class="studio-upload" for="aiStudioReference">
        <strong>{{ __('site.ai_studio_reference') }}</strong>
        <span>PNG, JPG, WEBP</span>
        <input id="aiStudioReference" type="file" accept="image/png,image/jpeg,image/webp" hidden>
      </label>

      <button class="studio-generate" id="aiStudioGenerate" type="button" onclick="handleAiStudioGenerate()">{{ __('site.ai_studio_generate') }}</button>
      <div class="studio-message" id="aiStudioMessage">{{ __('site.ai_studio_daily_limit_hint') }}</div>
      <p class="studio-hint">{{ __('site.ai_studio_subtitle') }}</p>
    </section>

    <section class="studio-panel studio-preview-panel">
      <div class="studio-preview-head">
        <div>
          <h2 class="studio-preview-title">{{ __('site.ai_studio_preview') }}</h2>
          <div class="studio-preview-meta">{{ $variant->color }}@if($variant->size) / {{ $variant->size }}@endif</div>
        </div>
        <a class="studio-link" href="{{ $constructorUrl }}" onclick="openGeneratedPrintInConstructor(event)">{{ __('site.ai_studio_open_constructor') }}</a>
      </div>

      <div class="studio-preview" id="aiStudioPreview">
        <img class="studio-product-mockup" src="{{ $side === 'back' && $variant->mockup_back_url ? $variant->mockup_back_url : $variant->mockup_front_url }}" alt="{{ $productName }}">
        <div
          class="studio-print-zone"
          id="aiStudioPrintZone"
          style="left: {{ $printArea->x * 100 }}%; top: {{ $printArea->y * 100 }}%; width: {{ $printArea->width * 100 }}%; height: {{ $printArea->height * 100 }}%;"
          aria-hidden="true"
        >
          <img class="studio-print-overlay" id="aiStudioPrintOverlay" alt="{{ __('site.ai_studio_preview') }}">
        </div>
        <div class="studio-empty-print" id="aiStudioEmptyPrint">{{ __('site.ai_studio_preview_empty') }}</div>
      </div>

      <div class="studio-variants" id="aiStudioVariants">
        <p class="studio-variants-title">{{ __('site.ai_studio_variants') }}</p>
        <div class="studio-variant-row">
          <div class="studio-variant-slot"></div>
          <div class="studio-variant-slot"></div>
          <div class="studio-variant-slot"></div>
          <div class="studio-variant-slot"></div>
        </div>
      </div>
    </section>
  </main>

  <div class="studio-mobile-cta" id="aiStudioMobileCta" aria-hidden="true">
    <button
      class="studio-mobile-cta__button"
      id="aiStudioMobileOpenConstructor"
      type="button"
      disabled
      onclick="openGeneratedPrintInConstructor(event)"
    >{{ __('site.ai_studio_open_constructor') }}</button>
  </div>
</div>
<script>
const AI_STUDIO_STORAGE_KEY = 'printlab.pendingAiPrint';
let selectedAiStudioPrint = null;

function showAiStudioMessage(message, type = '') {
  const box = document.getElementById('aiStudioMessage');
  if (!box) return;
  box.textContent = message || '';
  box.classList.remove('error', 'success');
  if (type === 'error' || type === 'success') box.classList.add(type);
}

function readAiStudioReferenceImage() {
  const input = document.getElementById('aiStudioReference');
  const file = input?.files?.[0];
  if (!file) return Promise.resolve(null);

  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = event => resolve(event.target.result);
    reader.onerror = () => reject(new Error('reference_read_failed'));
    reader.readAsDataURL(file);
  });
}

function normalizeGeneratedImageUrl(imageUrl) {
  const url = new URL(imageUrl, window.location.href);
  return url.origin === window.location.origin ? url.href : `${url.pathname}${url.search}${url.hash}`;
}

async function imageUrlToDataUrl(imageUrl) {
  const response = await fetch(normalizeGeneratedImageUrl(imageUrl), { headers: { 'Accept': 'image/*' } });
  if (!response.ok) throw new Error('image_fetch_failed');
  const blob = await response.blob();

  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = event => resolve(event.target.result);
    reader.onerror = () => reject(new Error('image_read_failed'));
    reader.readAsDataURL(blob);
  });
}

function scrollAiStudioPreviewIntoView() {
  if (!window.matchMedia('(max-width: 640px)').matches) return;

  requestAnimationFrame(() => {
    document.getElementById('aiStudioPreview')?.scrollIntoView({
      behavior: 'smooth',
      block: 'center',
    });
  });
}

function syncAiStudioMobileCta() {
  const cta = document.getElementById('aiStudioMobileCta');
  const mobileBtn = document.getElementById('aiStudioMobileOpenConstructor');
  const desktopBtn = document.getElementById('aiStudioOpenConstructor');
  if (!cta || !mobileBtn || !desktopBtn) return;

  const ready = !desktopBtn.classList.contains('is-disabled');

  cta.classList.toggle('is-visible', ready);
  cta.setAttribute('aria-hidden', ready ? 'false' : 'true');
  mobileBtn.disabled = !ready;
}

function showAiStudioResult({ dataUrl, imageUrl, generatedPrintId, fileName }) {
  selectedAiStudioPrint = {
    dataUrl,
    imageUrl: imageUrl || null,
    generatedPrintId: generatedPrintId || null,
    fileName: fileName || 'ai-print.png',
  };

  const overlay = document.getElementById('aiStudioPrintOverlay');
  const zone = document.getElementById('aiStudioPrintZone');
  const empty = document.getElementById('aiStudioEmptyPrint');
  const firstSlot = document.querySelector('.studio-variant-slot');
  const openBtn = document.getElementById('aiStudioOpenConstructor');

  if (overlay) {
    overlay.src = dataUrl;
    overlay.classList.add('visible');
  }
  if (zone) zone.classList.add('has-result');
  if (empty) empty.style.display = 'none';
  if (openBtn) openBtn.classList.remove('is-disabled');
  if (firstSlot) {
    firstSlot.classList.add('has-result');
    firstSlot.innerHTML = `<img src="${dataUrl}" alt="{{ __('site.ai_studio_preview') }}">`;
  }

  showAiStudioMessage(@json(__('site.ai_studio_success')), 'success');
  syncAiStudioMobileCta();
  scrollAiStudioPreviewIntoView();
}

async function handleAiStudioGenerate() {
  const input = document.getElementById('aiStudioPrompt');
  const btn = document.getElementById('aiStudioGenerate');
  const prompt = input ? input.value.trim() : '';

  if (!prompt) {
    showAiStudioMessage(@json(__('site.ai_studio_prompt_required')), 'error');
    return;
  }

  btn.disabled = true;
  btn.textContent = @json(__('site.ai_studio_generating'));
  showAiStudioMessage(@json(__('site.ai_studio_generating')));

  try {
    const referenceImage = await readAiStudioReferenceImage();
    const response = await fetch(window.aiStudioConfig.routes.generatePrint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        prompt,
        reference_image: referenceImage,
      }),
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
      if (response.status === 429) {
        showAiStudioMessage(@json(__('site.ai_studio_daily_limit')), 'error');
      } else if (response.status === 422) {
        showAiStudioMessage(payload.message || @json(__('site.ai_studio_failed')), 'error');
      } else {
        showAiStudioMessage(@json(__('site.ai_studio_failed')), 'error');
      }
      return;
    }

    const data = payload.data || {};
    const dataUrl = await imageUrlToDataUrl(data.image_url);
    showAiStudioResult({
      dataUrl,
      imageUrl: data.image_url,
      generatedPrintId: data.id,
      fileName: data.asset?.file_name || 'ai-print.png',
    });
  } catch (error) {
    showAiStudioMessage(@json(__('site.ai_studio_failed')), 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = @json(__('site.ai_studio_generate'));
  }
}

function storeAiStudioSelection() {
  if (!selectedAiStudioPrint) return false;

  sessionStorage.setItem(AI_STUDIO_STORAGE_KEY, JSON.stringify({
    dataUrl: selectedAiStudioPrint.dataUrl,
    imageUrl: selectedAiStudioPrint.imageUrl,
    generatedPrintId: selectedAiStudioPrint.generatedPrintId,
    fileName: selectedAiStudioPrint.fileName || 'ai-print.png',
    productId: window.aiStudioConfig.product.id,
    variantId: window.aiStudioConfig.variant.id,
    side: window.aiStudioConfig.printArea.side || 'front',
  }));

  return true;
}

function openGeneratedPrintInConstructor(event) {
  if (event) event.preventDefault();
  if (!storeAiStudioSelection()) {
    showAiStudioMessage(@json(__('site.ai_studio_generate_first')), 'error');
    return;
  }

  window.location.href = window.aiStudioConfig.routes.constructor;
}
</script>
</body>
</html>
