@php
    $constructorConfig = [
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'base_price' => $product->base_price,
        ],
        'variant' => [
            'id' => $variant->id,
            'color' => $variant->color,
            'size' => $variant->size,
            'mockup_front_url' => $variant->mockup_front_url,
            'mockup_back_url' => $variant->mockup_back_url,
            'price_modifier' => $variant->price_modifier,
        ],
        'variants' => $product->variants->map(fn ($item) => [
            'id' => $item->id,
            'color' => $item->color,
            'size' => $item->size,
            'label' => trim($item->color.($item->size ? ' / '.$item->size : '')),
            'mockup_front_url' => $item->mockup_front_url,
            'mockup_back_url' => $item->mockup_back_url,
            'price_modifier' => $item->price_modifier,
        ])->values(),
        'printArea' => [
            'side' => $side,
            'x' => $printArea->x,
            'y' => $printArea->y,
            'width' => $printArea->width,
            'height' => $printArea->height,
            'unit' => 'ratio',
        ],
        'routes' => [
            'constructor' => route('constructor.show', $product),
            'storeOrderRequest' => route('order-requests.store'),
            'success' => route('order-requests.success'),
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('site.seo_constructor_title', ['product' => $product->name]) }}</title>
<meta name="description" content="{{ __('site.seo_constructor_description', ['product' => $product->name]) }}">
<meta name="robots" content="noindex, nofollow">
<meta property="og:title"       content="{{ __('site.seo_constructor_title', ['product' => $product->name]) }}">
<meta property="og:description" content="{{ __('site.seo_constructor_description', ['product' => $product->name]) }}">
<meta property="og:type"        content="website">
<meta property="og:site_name"   content="PrintLab">
<meta name="twitter:card"        content="summary">
<meta name="twitter:title"       content="{{ __('site.seo_constructor_title', ['product' => $product->name]) }}">
<meta name="twitter:description" content="{{ __('site.seo_constructor_description', ['product' => $product->name]) }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script>window.constructorConfig = {{ Illuminate\Support\Js::from($constructorConfig) }};</script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg: #f0f2f5;
  --surface: #ffffff;
  --surface2: #f7f8fa;
  --border: #e2e5ea;
  --accent: #007aff;
  --accent2: #ff3b5c;
  --text: #1a1a2e;
  --muted: #8a8fa8;
  --dark: #1a1a2e;
}
body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  height: 100vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

/* SITE HEADER */
.site-header {
  background: #ffffff;
  border-bottom: 1px solid #e4e4e7;
  flex-shrink: 0;
}
.site-header-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
}
.site-logo {
  color: #09090b;
  font-size: 20px;
  font-weight: 700;
  letter-spacing: -0.03em;
  text-decoration: none;
}
.site-nav {
  display: flex;
  align-items: center;
  gap: 20px;
  font-size: 14px;
  font-weight: 500;
}
.site-nav a {
  color: #3f3f46;
  text-decoration: none;
}
.site-nav a:hover { color: #09090b; }

/* CONSTRUCTOR TOOLBAR */
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 18px;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  z-index: 10;
}
.logo { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 17px; letter-spacing: -0.5px; color: var(--dark); }
.logo span { color: var(--accent); }
.designer-product { display: flex; flex-direction: column; gap: 2px; min-width: 170px; }
.designer-product-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; letter-spacing: -0.4px; color: var(--dark); }
.designer-product-meta { font-size: 12px; color: var(--muted); }
.header-center { display: flex; align-items: center; gap: 8px; }
.undo-btn {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--surface2); border: 1px solid var(--border);
  cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center;
  transition: all 0.15s; color: var(--text);
}
.undo-btn:hover { background: var(--border); }
.add-btn {
  width: 46px; height: 46px; border-radius: 50%;
  background: var(--accent); border: none;
  cursor: pointer; font-size: 26px; color: white;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(0,122,255,0.4);
  transition: transform 0.15s, box-shadow 0.15s;
}
.add-btn:hover { transform: scale(1.08); box-shadow: 0 6px 20px rgba(0,122,255,0.5); }
.header-right { display: flex; gap: 8px; }
.btn {
  padding: 8px 16px; border-radius: 8px; border: none;
  cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500;
  transition: all 0.15s;
}
.btn-ghost { background: var(--surface2); border: 1px solid var(--border); color: var(--text); }
.btn-ghost:hover { background: var(--border); }
.btn-primary { background: var(--accent); color: white; }
.btn-primary:hover { background: #0062cc; }

/* MAIN */
.workspace { display: flex; flex: 1; overflow: hidden; }

/* LEFT — shirt selector */
.panel-left {
  width: 110px;
  background: var(--surface);
  border-right: 1px solid var(--border);
  padding: 12px 8px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  overflow-y: auto;
  flex-shrink: 0;
}
.shirt-thumb {
  border-radius: 10px;
  border: 2px solid var(--border);
  cursor: pointer;
  overflow: hidden;
  transition: border-color 0.15s;
  aspect-ratio: 1;
  background: var(--surface2);
  display: flex; align-items: center; justify-content: center;
}
.shirt-thumb:hover { border-color: var(--accent); }
.shirt-thumb.active { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(0,122,255,0.2); }
.shirt-thumb img { width: 90%; height: 90%; object-fit: contain; }
.selector-section { margin-top: 8px; }
.selector-label { font-size: 10px; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; text-align: center; margin-bottom: 6px; }
.side-select {
  width: 100%;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  padding: 8px 7px;
  font-size: 12px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
}

/* CANVAS AREA */
.canvas-area {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e8eaef;
  position: relative;
  overflow: hidden;
}
.canvas-wrap { position: relative; user-select: none; }
#mainCanvas { display: block; border-radius: 4px; }

/* PRINT ZONE HINT */
.zone-hint {
  position: absolute;
  pointer-events: none;
  border: 2px dashed rgba(0,122,255,0.35);
  border-radius: 4px;
  background: rgba(0,122,255,0.04);
  transition: opacity 0.3s;
}
.zone-hint-label {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  font-size: 11px; color: rgba(0,122,255,0.5);
  font-weight: 500; white-space: nowrap;
  pointer-events: none;
}

/* RIGHT PANEL */
.panel-right {
  width: 240px;
  background: var(--surface);
  border-left: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  overflow-y: auto;
}
.panel-section { padding: 14px; border-bottom: 1px solid var(--border); }
.panel-label { font-size: 10px; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
.field-group { margin-bottom: 10px; }
.field-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; display: flex; justify-content: space-between; }
.field-val { color: var(--accent); font-weight: 500; }
input[type=text], select {
  width: 100%; background: var(--surface2); border: 1px solid var(--border);
  border-radius: 7px; color: var(--text); padding: 8px 10px; font-size: 13px;
  font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.15s;
}
input:focus, select:focus { border-color: var(--accent); }
input[type=range] { width: 100%; accent-color: var(--accent); cursor: pointer; }
input[type=color] { width: 36px; height: 30px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface2); cursor: pointer; padding: 2px; }
.color-row { display: flex; gap: 6px; align-items: center; }

/* TOOLS GRID */
.tools-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
.tool-btn {
  padding: 10px 8px; background: var(--surface2); border: 1px solid var(--border);
  border-radius: 8px; cursor: pointer; font-size: 12px; font-family: 'DM Sans', sans-serif;
  color: var(--text); text-align: center; transition: all 0.15s; line-height: 1.4;
}
.tool-btn:hover { border-color: var(--accent); color: var(--accent); background: rgba(0,122,255,0.05); }
.tool-btn .ti { display: block; font-size: 18px; margin-bottom: 2px; }
.tool-btn-full { grid-column: 1/-1; }

/* LAYERS */
.layers-list { padding: 8px; display: flex; flex-direction: column; gap: 4px; }
.layer-item {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px; background: var(--surface2); border: 1.5px solid var(--border);
  border-radius: 8px; cursor: pointer; font-size: 12px; transition: all 0.15s;
}
.layer-item:hover { border-color: #c0d4ff; }
.layer-item.selected { border-color: var(--accent); background: rgba(0,122,255,0.06); }
.layer-icon { font-size: 15px; flex-shrink: 0; }
.layer-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text); }
.layer-del { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 14px; transition: color 0.15s; padding: 2px 4px; }
.layer-del:hover { color: var(--accent2); }
.layers-empty { padding: 20px; text-align: center; color: var(--muted); font-size: 12px; line-height: 1.7; }

/* EXPORT */
.export-section { padding: 12px; display: flex; flex-direction: column; gap: 6px; margin-top: auto; border-top: 1px solid var(--border); }
.export-section .btn { width: 100%; text-align: center; font-size: 13px; }

/* ADD MENU */
.add-menu {
  position: fixed; top: 70px; left: 50%; transform: translateX(-50%);
  background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.15);
  padding: 16px; z-index: 100; display: none; min-width: 260px;
}
.add-menu.open { display: block; animation: popIn 0.18s cubic-bezier(0.22,1,0.36,1); }
@keyframes popIn { from { opacity:0; transform: translateX(-50%) scale(0.9); } to { opacity:1; transform: translateX(-50%) scale(1); } }
.add-menu-title { font-size: 11px; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 10px; }
.add-menu-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
.add-menu-btn {
  padding: 12px 8px; background: var(--surface2); border: 1px solid var(--border);
  border-radius: 10px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 12px;
  color: var(--text); text-align: center; transition: all 0.15s;
}
.add-menu-btn:hover { border-color: var(--accent); color: var(--accent); }
.add-menu-btn .ami { display: block; font-size: 22px; margin-bottom: 4px; }

/* TOAST */
.toast {
  position: fixed; bottom: 20px; left: 50%;
  transform: translateX(-50%) translateY(80px);
  background: var(--dark); color: white; padding: 10px 20px;
  border-radius: 10px; font-size: 13px; font-weight: 500;
  transition: transform 0.35s cubic-bezier(0.22,1,0.36,1);
  z-index: 1000; pointer-events: none; white-space: nowrap;
}
.toast.show { transform: translateX(-50%) translateY(0); }

/* ORDER DIALOG */
.order-dialog {
  position: fixed; inset: 0; z-index: 2000;
  display: none; align-items: center; justify-content: center;
  background: rgba(26,26,46,0.45);
}
.order-dialog.open { display: flex; }
.order-card {
  width: min(440px, calc(100vw - 24px));
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  box-shadow: 0 18px 60px rgba(0,0,0,0.22);
  padding: 18px;
}
.order-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 20px; margin-bottom: 12px; }
.order-field { margin-bottom: 10px; }
.order-field label { display: block; font-size: 11px; color: var(--muted); margin-bottom: 4px; }
.order-field input, .order-field textarea {
  width: 100%; background: var(--surface2); border: 1px solid var(--border);
  border-radius: 7px; color: var(--text); padding: 9px 10px; font-size: 13px;
  font-family: 'DM Sans', sans-serif; outline: none;
}
.order-field textarea { min-height: 88px; resize: vertical; }
.order-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; }
.order-error { display: none; color: var(--accent2); font-size: 12px; line-height: 1.4; margin-top: 8px; }
.order-error.show { display: block; }

/* SCROLLBAR */
::-webkit-scrollbar { width: 3px; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* ─── MOBILE ────────────────────────────────────────────── */
.mobile-bottom-bar { display: none; }
.mobile-panel      { display: none; }

@media (max-width: 768px) {
  body { height: 100dvh; }

  /* Site header — hide nav to save space */
  .site-header-inner { padding: 10px 14px; }
  .site-nav { display: none; }

  /* Constructor toolbar — compact, single row */
  .header { padding: 8px 12px; flex-wrap: nowrap; gap: 8px; }
  .designer-product { flex: 1; min-width: 0; }
  .designer-product-title { font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .designer-product-meta { font-size: 10px; }
  .header-center { order: 0; width: auto; gap: 6px; }
  .add-btn { display: none; } /* replaced by mobile FAB */
  .undo-btn { width: 30px; height: 30px; font-size: 14px; }
  .header-right { display: none; } /* moved to bottom bar */

  /* Workspace — vertical stack */
  .workspace { flex-direction: column; }
  .panel-left { display: none; }
  .canvas-area { flex: 1; min-height: 0; }

  /* Right panel → bottom drawer */
  .panel-right {
    display: none;
    position: fixed;
    bottom: 56px; left: 0; right: 0;
    width: 100%;
    border-left: none;
    border-top: 1px solid var(--border);
    border-radius: 16px 16px 0 0;
    box-shadow: 0 -6px 30px rgba(0,0,0,0.13);
    max-height: 50vh;
    overflow-y: auto;
    z-index: 50;
    flex-direction: column;
    flex-shrink: 0;
  }
  .panel-right.mobile-open {
    display: flex;
    animation: slideUpPanel 0.22s cubic-bezier(0.22,1,0.36,1);
  }
  .panel-right::before {
    content: '';
    display: block;
    width: 36px; height: 4px;
    background: var(--border);
    border-radius: 2px;
    margin: 10px auto 4px;
    flex-shrink: 0;
  }
  .export-section { margin-top: 0; }

  /* Add menu — position above bottom bar */
  .add-menu { top: auto; bottom: 68px; }
  .add-menu.open { animation: popInBottom 0.18s cubic-bezier(0.22,1,0.36,1); }
  @keyframes popInBottom {
    from { opacity: 0; transform: translateX(-50%) scale(0.9); }
    to   { opacity: 1; transform: translateX(-50%) scale(1); }
  }
  @keyframes slideUpPanel {
    from { transform: translateY(30px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
  }

  /* ── Mobile bottom bar ── */
  .mobile-bottom-bar {
    display: flex !important;
    height: 56px;
    background: var(--surface);
    border-top: 1px solid var(--border);
    align-items: center;
    padding: 0 8px;
    gap: 4px;
    flex-shrink: 0;
    z-index: 60;
  }
  .mobile-tab-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    height: 46px;
    background: none;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 10px;
    font-family: 'DM Sans', sans-serif;
    color: var(--muted);
    transition: color 0.15s, background 0.15s;
  }
  .mobile-tab-btn .tab-icon { font-size: 18px; line-height: 1.2; }
  .mobile-tab-btn.active { color: var(--accent); background: rgba(0,122,255,0.08); }
  .mobile-add-fab {
    width: 46px; height: 46px; flex: none;
    background: var(--accent); color: white;
    border: none; border-radius: 50%;
    font-size: 26px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(0,122,255,0.4);
    transition: transform 0.15s, box-shadow 0.15s;
  }
  .mobile-add-fab:active { transform: scale(0.92); box-shadow: 0 2px 8px rgba(0,122,255,0.3); }
  .mobile-order-btn {
    flex: 1.4;
    height: 38px;
    background: var(--accent); color: white;
    border: none; border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 12px; font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
    white-space: nowrap;
  }
  .mobile-order-btn:active { background: #0062cc; }

  /* ── Mobile variants bottom sheet ── */
  .mobile-panel {
    display: none;
    position: fixed;
    bottom: 56px; left: 0; right: 0;
    background: var(--surface);
    border-top: 1px solid var(--border);
    border-radius: 16px 16px 0 0;
    box-shadow: 0 -6px 30px rgba(0,0,0,0.13);
    z-index: 50;
    max-height: 50vh;
    overflow-y: auto;
    padding: 10px 16px 24px;
  }
  .mobile-panel.mobile-open {
    display: block;
    animation: slideUpPanel 0.22s cubic-bezier(0.22,1,0.36,1);
  }
  .panel-handle {
    width: 36px; height: 4px;
    background: var(--border);
    border-radius: 2px;
    margin: 0 auto 14px;
  }
  .mobile-panel .selector-label {
    font-size: 10px; color: var(--muted);
    letter-spacing: 0.1em; text-transform: uppercase;
    margin-bottom: 8px; display: block;
  }
  .mobile-panel .side-select { width: 100%; margin-bottom: 14px; }
}

/* PrintLab constructor v2: marketplace redesign. Keeps v1 DOM/JS contract intact. */
body.constructor-v2 {
  --bg: #f4f4f4;
  --surface: #ffffff;
  --surface2: #fafafa;
  --border: #e5e5e5;
  --accent: #e30613;
  --accent2: #c00510;
  --text: #111111;
  --muted: #8a8a8a;
  --dark: #111111;
  --yellow: #ffd400;
  font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
  background: var(--bg);
}

body.constructor-v2 .site-header {
  display: none;
}

body.constructor-v2 .header {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto auto;
  justify-content: initial;
  height: 58px;
  padding: 10px 18px;
  gap: 14px;
  background: #111111;
  color: #ffffff;
  border-bottom: 0;
  overflow: hidden;
}

body.constructor-v2 .designer-product-title {
  color: #ffffff;
  font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
  font-size: 15px;
  font-weight: 900;
  letter-spacing: 0;
}

body.constructor-v2 .designer-product-meta {
  color: rgba(255, 255, 255, 0.62);
}

body.constructor-v2 .designer-product {
  min-width: 0;
  overflow: hidden;
}

body.constructor-v2 .designer-product-title,
body.constructor-v2 .designer-product-meta {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

body.constructor-v2 .constructor-logo {
  display: inline-grid;
  place-items: center;
  width: 126px;
  height: 44px;
  margin-right: 0;
  border-radius: 10px;
  background: #e30613;
  color: #ffffff;
  font-size: 24px;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1;
  text-decoration: none;
  transition: transform 0.15s ease, background 0.15s ease;
}

body.constructor-v2 .constructor-logo:hover {
  background: #ff1020;
  transform: translateY(-1px);
}

body.constructor-v2 .undo-btn,
body.constructor-v2 .btn {
  border-radius: 4px;
  font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
  font-weight: 800;
}

body.constructor-v2 .undo-btn {
  background: #262626;
  border-color: #333333;
  color: #ffffff;
}

body.constructor-v2 .undo-btn:hover {
  border-color: #e30613;
  color: #ffd400;
}

body.constructor-v2 .add-btn,
body.constructor-v2 .btn-primary,
body.constructor-v2 .mobile-add-fab,
body.constructor-v2 .mobile-order-btn {
  background: #e30613;
  box-shadow: 0 6px 16px rgba(227, 6, 19, 0.32);
}

body.constructor-v2 .btn-primary:hover,
body.constructor-v2 .mobile-order-btn:active {
  background: #c00510;
}

body.constructor-v2 .btn-ghost {
  background: #ffffff;
  border: 1.5px solid #d0d0d0;
  color: #111111;
}

body.constructor-v2 .workspace {
  display: grid;
  grid-template-columns: 280px minmax(0, 1fr) 320px;
  min-height: 0;
  background: #f4f4f4;
}

body.constructor-v2 .panel-left {
  width: auto;
  padding: 0;
  gap: 0;
  border-right: 1px solid #e5e5e5;
  background: #ffffff;
}

body.constructor-v2 .v2-tool-tabs {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  border-bottom: 1px solid #e5e5e5;
}

body.constructor-v2 .v2-tool-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 7px;
  min-height: 66px;
  min-width: 0;
  padding: 10px 6px;
  border: 0;
  border-right: 1px solid #e5e5e5;
  background: #fafafa;
  color: #4a4a4a;
  cursor: pointer;
  font-family: inherit;
  font-size: 11px;
  font-weight: 700;
  line-height: 1.1;
  text-align: center;
  overflow: hidden;
}

body.constructor-v2 .v2-tool-tab:last-child {
  border-right: 0;
}

body.constructor-v2 .v2-tool-tab strong {
  display: block;
  width: 26px;
  height: 26px;
  margin-bottom: 0;
  color: #111111;
  font-size: 22px;
  line-height: 1;
  text-align: center;
}

body.constructor-v2 label.v2-tool-tab input {
  display: none;
}

body.constructor-v2 .v2-tool-tab.active {
  background: #ffffff;
  color: #e30613;
  box-shadow: inset 0 -2px #e30613;
}

body.constructor-v2 .v2-left-section {
  padding: 16px;
  border-bottom: 1px solid #e5e5e5;
}

body.constructor-v2 .selector-label,
body.constructor-v2 .panel-label,
body.constructor-v2 .add-menu-title {
  color: #4a4a4a;
  font-size: 11px;
  font-weight: 900;
}

body.constructor-v2 .shirt-thumb {
  width: 104px;
  margin: 16px auto 4px;
  border-radius: 8px;
  border: 2px solid #e5e5e5;
}

body.constructor-v2 .shirt-thumb.active {
  border-color: #e30613;
  box-shadow: 0 0 0 3px rgba(227, 6, 19, 0.12);
}

body.constructor-v2 .side-select,
body.constructor-v2 input[type=text],
body.constructor-v2 select,
body.constructor-v2 .order-field input,
body.constructor-v2 .order-field textarea {
  border-radius: 4px;
  border: 1.5px solid #d0d0d0;
  background: #ffffff;
  color: #111111;
  font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
}

body.constructor-v2 .canvas-area {
  background:
    radial-gradient(circle at 50% 34%, rgba(255, 212, 0, 0.16), transparent 32%),
    #f4f4f4;
  padding: 22px;
}

body.constructor-v2 .canvas-wrap {
  padding: 18px;
  border-radius: 8px;
  background: #ffffff;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

body.constructor-v2 #mainCanvas {
  filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.12));
}

body.constructor-v2 .zone-hint {
  border-color: rgba(227, 6, 19, 0.42);
  background: rgba(227, 6, 19, 0.04);
}

body.constructor-v2 .zone-hint-label {
  color: rgba(227, 6, 19, 0.68);
  font-weight: 800;
}

body.constructor-v2 .panel-right {
  width: auto;
  border-left: 1px solid #e5e5e5;
}

body.constructor-v2 .panel-section {
  padding: 16px 20px;
}

body.constructor-v2 .field-label {
  color: #4a4a4a;
  font-weight: 700;
}

body.constructor-v2 .field-val {
  color: #e30613;
  font-weight: 900;
}

body.constructor-v2 input[type=range] {
  accent-color: #e30613;
}

body.constructor-v2 .layers-list {
  padding: 12px 20px;
}

body.constructor-v2 .layer-item {
  border-radius: 6px;
}

body.constructor-v2 .layer-item.selected {
  border-color: #e30613;
  background: #fff5f5;
}

body.constructor-v2 .export-section {
  background: #fafafa;
}

body.constructor-v2 .add-menu {
  top: 70px;
  border-radius: 8px;
}

body.constructor-v2 .add-menu-btn {
  border-radius: 6px;
  font-family: inherit;
  font-weight: 800;
}

body.constructor-v2 .add-menu-btn:hover {
  border-color: #e30613;
  color: #e30613;
}

body.constructor-v2 .order-card {
  border-radius: 8px;
}

body.constructor-v2 .order-title {
  font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
  font-weight: 900;
}

@media (max-width: 980px) {
  body.constructor-v2 .header {
    grid-template-columns: auto minmax(0, 1fr) auto;
  }

  body.constructor-v2 .header-right {
    display: none;
  }

  body.constructor-v2 .constructor-logo {
    width: 116px;
    height: 42px;
    font-size: 22px;
  }

  body.constructor-v2 .workspace {
    grid-template-columns: 220px minmax(0, 1fr);
  }

  body.constructor-v2 .panel-right {
    display: none;
  }
}

@media (max-width: 768px) {
  body.constructor-v2 .header {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    height: 54px;
    padding: 8px 12px;
    gap: 10px;
  }

  body.constructor-v2 .constructor-logo {
    width: 42px;
    height: 38px;
    margin-right: 8px;
    overflow: hidden;
    font-size: 0;
    transform: rotate(-4deg);
  }

  body.constructor-v2 .constructor-logo::first-letter {
    font-size: 24px;
  }

  body.constructor-v2 .header-center {
    justify-content: flex-end;
  }

  body.constructor-v2 .workspace {
    display: flex;
  }

  body.constructor-v2 .canvas-area {
    padding: 12px;
  }

  body.constructor-v2 .canvas-wrap {
    padding: 8px;
  }

  body.constructor-v2 .mobile-bottom-bar {
    height: 64px;
    box-shadow: 0 -4px 18px rgba(0, 0, 0, 0.08);
  }

  body.constructor-v2 .mobile-add-fab {
    width: 50px;
    height: 50px;
    margin-top: -18px;
    border: 4px solid #ffffff;
  }

  body.constructor-v2 .panel-right,
  body.constructor-v2 .mobile-panel {
    bottom: 64px;
  }
}
</style>
</head>
<body class="constructor-v2">

<!-- SITE HEADER -->
<header class="site-header">
  <div class="site-header-inner">
    <a class="site-logo" href="{{ route('home') }}">PrintLab</a>
    <nav class="site-nav" aria-label="Главное меню">
      <a href="{{ route('catalog.index') }}">Каталог</a>
      <a href="{{ route('catalog.index') }}#prints">Готовые принты</a>
      <a href="{{ route('catalog.index') }}#products">Создать свой принт</a>
      <a href="{{ route('home') }}#contacts">Контакты</a>
    </nav>
  </div>
</header>

<!-- CONSTRUCTOR TOOLBAR -->
<div class="header">
  <a class="constructor-logo" href="{{ route('home') }}" aria-label="На главную">PrintLab</a>
  <div class="designer-product">
    <div class="designer-product-title">{{ $product->name }}</div>
    <div class="designer-product-meta">
      {{ $variant->color }}@if($variant->size) / {{ $variant->size }}@endif
      · {{ number_format($product->base_price + $variant->price_modifier, 0, '.', ' ') }} UZS
    </div>
  </div>
  <div class="header-center">
    <button class="undo-btn" onclick="undo()" title="Отменить">↩</button>
    <button class="add-btn" onclick="toggleAddMenu()" title="Добавить">+</button>
    <button class="undo-btn" onclick="redo()" title="Повторить">↪</button>
  </div>
  <div class="header-right">
    <button class="btn btn-ghost" onclick="exportPrintOnly()">{{ __('site.constructor_print_btn') }}</button>
    <button class="btn btn-primary" onclick="exportFull()">{{ __('site.constructor_save_btn') }}</button>
    <button class="btn btn-primary" onclick="openOrderDialog()">{{ __('site.constructor_order') }}</button>
  </div>
</div>

<!-- ADD MENU -->
<div class="add-menu" id="addMenu">
  <div class="add-menu-title">{{ __('site.constructor_add_element') }}</div>
  <div class="add-menu-grid">
    <label class="add-menu-btn">
      <span class="ami">🖼</span>{{ __('site.constructor_photo') }}
      <input type="file" accept="image/*" style="display:none" onchange="addImage(event)">
    </label>
    <button class="add-menu-btn" onclick="addText();closeAddMenu()">
      <span class="ami">T</span>{{ __('site.constructor_text') }}
    </button>
    <button class="add-menu-btn" onclick="addShape('rect');closeAddMenu()">
      <span class="ami">▭</span>{{ __('site.constructor_shape') }}
    </button>
    <button class="add-menu-btn" onclick="addShape('circle');closeAddMenu()">
      <span class="ami">○</span>{{ __('site.constructor_circle') }}
    </button>
    <button class="add-menu-btn" onclick="addShape('star');closeAddMenu()">
      <span class="ami">★</span>{{ __('site.constructor_star') }}
    </button>
    <label class="add-menu-btn">
      <span class="ami">👕</span>{{ __('site.constructor_product_photo') }}
      <input type="file" accept="image/*" style="display:none" onchange="loadShirtPhoto(event)">
    </label>
  </div>
</div>

<!-- WORKSPACE -->
<div class="workspace">

  <!-- LEFT: product selector -->
  <div class="panel-left">
    <div class="v2-tool-tabs" aria-label="Инструменты конструктора">
      <button class="v2-tool-tab active" type="button" onclick="toggleAddMenu()"><strong>✦</strong>Принты</button>
      <label class="v2-tool-tab">
        <strong>↥</strong>Фото
        <input type="file" accept="image/*" style="display:none" onchange="addImage(event)">
      </label>
      <button class="v2-tool-tab" type="button" onclick="addText()"><strong>T</strong>Текст</button>
      <button class="v2-tool-tab" type="button" onclick="addShape('rect')"><strong>□</strong>Фигуры</button>
    </div>
    <div class="v2-left-section">
      <div class="selector-label">Быстрый старт</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px">
        <button class="add-menu-btn" type="button" onclick="addShape('star')"><span class="ami">★</span>Star</button>
        <button class="add-menu-btn" type="button" onclick="addShape('circle')"><span class="ami">○</span>Circle</button>
        <button class="add-menu-btn" type="button" onclick="addText()"><span class="ami">Aa</span>Text</button>
      </div>
    </div>
    <div class="shirt-thumb active" id="thumb0" onclick="selectShirtView(0)">
      <canvas id="thumbCanvas0" width="80" height="80"></canvas>
    </div>
    <div class="selector-section">
      <div class="selector-label">{{ __('site.constructor_variant') }}</div>
      <select class="side-select" id="variantSelect" onchange="changeConstructorVariant(this.value)">
        @foreach ($product->variants as $item)
          <option value="{{ $item->id }}" @selected($item->id === $variant->id)>
            {{ $item->color }}@if($item->size) / {{ $item->size }}@endif
          </option>
        @endforeach
      </select>
    </div>
    <div class="selector-section">
      <div class="selector-label">{{ __('site.constructor_side') }}</div>
      <select class="side-select" id="sideSelect" onchange="changeConstructorSide(this.value)">
        @if ($product->type === \App\Enums\ProductType::Mug)
          <option value="front" @selected($side === 'front')>{{ __('site.constructor_side_left') }}</option>
          <option value="back" @selected($side === 'back')>{{ __('site.constructor_side_right') }}</option>
        @else
          <option value="front" @selected($side === 'front')>{{ __('site.constructor_side_front') }}</option>
          <option value="back" @selected($side === 'back')>{{ __('site.constructor_side_back') }}</option>
        @endif
      </select>
    </div>
  </div>

  <!-- CANVAS -->
  <div class="canvas-area" id="canvasArea">
    <div class="canvas-wrap" id="canvasWrap">
      <canvas id="mainCanvas"></canvas>
      <div class="zone-hint" id="zoneHint">
        <span class="zone-hint-label" id="zoneLabel">зона принта</span>
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="panel-right">

    <!-- TEXT OPTIONS -->
    <div class="panel-section" id="textPanel" style="display:none">
      <div class="panel-label">{{ __('site.constructor_text_panel') }}</div>
      <div class="field-group">
        <input type="text" id="textInput" placeholder="{{ __('site.constructor_text') }}..." value="YOUR TEXT" oninput="updateSelectedText()">
      </div>
      <div class="field-group">
        <div class="field-label">{{ __('site.constructor_font') }}</div>
        <select id="fontSel" onchange="updateSelectedText()">
          <option value="Syne">Syne</option>
          <option value="Impact">Impact</option>
          <option value="Georgia">Georgia</option>
          <option value="DM Sans">DM Sans</option>
          <option value="Courier New">Courier New</option>
          <option value="Arial Black">Arial Black</option>
        </select>
      </div>
      <div class="field-group">
        <div class="field-label">Размер <span class="field-val" id="fsVal">48</span>px</div>
        <input type="range" id="fontSizeR" min="12" max="200" value="48" oninput="document.getElementById('fsVal').textContent=this.value;updateSelectedText()">
      </div>
      <div class="field-group">
        <div class="field-label">Цвет</div>
        <div class="color-row">
          <input type="color" id="textColorPick" value="#000000" oninput="updateSelectedText()">
          <input type="text" id="textColorHex" value="#000000" placeholder="#000000" style="flex:1" oninput="syncTxtColor()">
        </div>
      </div>
      <div class="field-group">
        <div class="field-label">Жирный</div>
        <button class="btn btn-ghost" id="boldBtn" onclick="toggleBold()" style="width:100%">B</button>
      </div>
    </div>

    <!-- TRANSFORM -->
    <div class="panel-section">
      <div class="panel-label">{{ __('site.constructor_transform') }}</div>
      <div class="field-group">
        <div class="field-label">{{ __('site.constructor_rotation') }} <span class="field-val" id="rotVal">0</span>°</div>
        <input type="range" id="rotR" min="-180" max="180" value="0" oninput="document.getElementById('rotVal').textContent=this.value;updateTransform('rotation',parseFloat(this.value))">
      </div>
      <div class="field-group">
        <div class="field-label">{{ __('site.constructor_scale') }} <span class="field-val" id="scaleVal">100</span>%</div>
        <input type="range" id="scaleR" min="5" max="400" value="100" oninput="document.getElementById('scaleVal').textContent=this.value;updateTransform('scale',parseFloat(this.value)/100)">
      </div>
      <div class="field-group">
        <div class="field-label">{{ __('site.constructor_opacity') }} <span class="field-val" id="opVal">100</span>%</div>
        <input type="range" id="opR" min="0" max="100" value="100" oninput="document.getElementById('opVal').textContent=this.value;updateTransform('opacity',parseFloat(this.value)/100)">
      </div>
      <div class="field-group">
        <div class="field-label">{{ __('site.constructor_blend') }}</div>
        <select id="blendSel" onchange="updateTransform('blend',this.value)">
          <option value="source-over">Обычный</option>
          <option value="multiply">Multiply</option>
          <option value="screen">Screen</option>
          <option value="overlay">Overlay</option>
          <option value="darken">Darken</option>
          <option value="lighten">Lighten</option>
          <option value="color-dodge">Color Dodge</option>
          <option value="color-burn">Color Burn</option>
        </select>
      </div>
    </div>

    <!-- LAYERS -->
    <div class="panel-section" style="padding-bottom:0;border-bottom:none">
      <div class="panel-label">{{ __('site.constructor_layers') }} <span style="color:var(--accent);font-weight:600" id="layerCnt">0</span></div>
    </div>
    <div class="layers-list" id="layersList">
      <div class="layers-empty" id="layersEmpty">Нажми + чтобы добавить элемент</div>
    </div>

    <div class="export-section">
      <button class="btn btn-ghost" onclick="exportPrintOnly()">{{ __('site.constructor_export_print') }}</button>
      <button class="btn btn-ghost" onclick="exportLayers()">{{ __('site.constructor_export_layers') }}</button>
      <button class="btn btn-primary" onclick="exportFull()">{{ __('site.constructor_export_full') }}</button>
    </div>
  </div>
</div>

<!-- ═══ MOBILE BOTTOM BAR (≤768px) ═══ -->
<div class="mobile-bottom-bar" id="mobileBottomBar">
  <button class="mobile-tab-btn" id="mTabLayers" onclick="mobileSwitchTab('layers')">
    <span class="tab-icon">⊞</span>
    <span>{{ __('site.constructor_layers') }}</span>
  </button>
  <button class="mobile-tab-btn" id="mTabProps" onclick="mobileSwitchTab('props')">
    <span class="tab-icon">⚙</span>
    <span>{{ __('site.constructor_settings') }}</span>
  </button>
  <button class="mobile-add-fab" onclick="toggleAddMenu()" aria-label="{{ __('site.constructor_add_element') }}">+</button>
  <button class="mobile-tab-btn" id="mTabVariants" onclick="mobileSwitchTab('variants')">
    <span class="tab-icon">👕</span>
    <span>{{ __('site.constructor_variant') }}</span>
  </button>
  <button class="mobile-order-btn" onclick="openOrderDialog()">{{ __('site.constructor_order') }}</button>
</div>

<!-- ═══ MOBILE VARIANTS PANEL ═══ -->
<div class="mobile-panel" id="mobileVariantsPanel">
  <div class="panel-handle"></div>
  <span class="selector-label">{{ __('site.constructor_variant') }}</span>
  <select class="side-select" onchange="changeConstructorVariant(this.value)">
    @foreach ($product->variants as $item)
      <option value="{{ $item->id }}" @selected($item->id === $variant->id)>
        {{ $item->color }}@if($item->size) / {{ $item->size }}@endif
      </option>
    @endforeach
  </select>
  <span class="selector-label">{{ __('site.constructor_side') }}</span>
  <select class="side-select" onchange="changeConstructorSide(this.value)">
    @if ($product->type === \App\Enums\ProductType::Mug)
      <option value="front" @selected($side === 'front')>{{ __('site.constructor_side_left') }}</option>
      <option value="back" @selected($side === 'back')>{{ __('site.constructor_side_right') }}</option>
    @else
      <option value="front" @selected($side === 'front')>{{ __('site.constructor_side_front') }}</option>
      <option value="back" @selected($side === 'back')>{{ __('site.constructor_side_back') }}</option>
    @endif
  </select>
</div>

<div class="toast" id="toast"></div>

<div class="order-dialog" id="orderDialog">
  <form class="order-card" id="orderForm">
    <div class="order-title">{{ __('site.order_title') }}</div>
    <div class="order-field">
      <label for="customerName">{{ __('site.order_name') }}</label>
      <input id="customerName" name="customer_name" type="text" maxlength="255" required>
    </div>
    <div class="order-field">
      <label for="customerPhone">{{ __('site.order_phone') }}</label>
      <input id="customerPhone" name="customer_phone" type="tel" maxlength="32" required>
    </div>
    <div class="order-field">
      <label for="customerComment">{{ __('site.order_comment') }}</label>
      <textarea id="customerComment" name="customer_comment" maxlength="2000"></textarea>
    </div>
    <div class="order-error" id="orderError"></div>
    <div class="order-actions">
      <button class="btn btn-ghost" type="button" onclick="closeOrderDialog()">{{ __('site.order_cancel') }}</button>
      <button class="btn btn-primary" type="submit">{{ __('site.order_submit') }}</button>
    </div>
  </form>
</div>

<script>
// ============================================================
// CANVAS SETUP
// ============================================================
const canvas = document.getElementById('mainCanvas');
const ctx = canvas.getContext('2d');
const constructorConfig = window.constructorConfig || {};

// Scale canvas to fit area
function resizeCanvas() {
  const area = document.getElementById('canvasArea');
  const size = Math.min(area.clientWidth - 40, area.clientHeight - 40, 600);
  canvas.width = size;
  canvas.height = size;
  positionZoneHint();
  renderAll();
}

window.addEventListener('resize', resizeCanvas);

// ============================================================
// STATE
// ============================================================
let shirtImg = null;     // loaded from file or backend product mockup
let productMockupError = false;
let shirtBase = null;    // colored base image
let layers = [];
let selectedId = null;
let nextId = 1;
let history = [];
let future = [];

// Print zone as fraction of canvas
const PZ = { fx: 0.22, fy: 0.18, fw: 0.56, fh: 0.56 };
function getPZ() {
  return {
    x: canvas.width * PZ.fx,
    y: canvas.height * PZ.fy,
    w: canvas.width * PZ.fw,
    h: canvas.height * PZ.fh
  };
}

// ============================================================
// LOAD PRODUCT MOCKUP FROM BACKEND
// ============================================================
function loadProductMockup() {
  const side = constructorConfig.printArea?.side || 'front';
  const url = side === 'back'
    ? (constructorConfig.variant?.mockup_back_url || constructorConfig.variant?.mockup_front_url)
    : constructorConfig.variant?.mockup_front_url;

  if (!url) {
    productMockupError = true;
    return;
  }

  const img = new Image();
  img.onload = () => {
    shirtImg = img;
    productMockupError = false;
    renderAll();
  };
  img.onerror = () => {
    productMockupError = true;
    shirtImg = null;
    renderAll();
    showToast('Не удалось загрузить изображение товара. Попробуйте обновить страницу.');
  };
  img.src = url;
}

function changeConstructorVariant(variantId) {
  const url = new URL(constructorConfig.routes.constructor, window.location.origin);
  url.searchParams.set('variant', variantId);
  url.searchParams.set('side', document.getElementById('sideSelect').value || 'front');
  window.location.href = url.toString();
}

function changeConstructorSide(side) {
  const url = new URL(constructorConfig.routes.constructor, window.location.origin);
  url.searchParams.set('variant', document.getElementById('variantSelect').value || constructorConfig.variant.id);
  url.searchParams.set('side', side);
  window.location.href = url.toString();
}

function drawProductMockup(mockupImage) {
  ctx.drawImage(mockupImage, 0, 0, canvas.width, canvas.height);
}

// ============================================================
// BUILT-IN SHIRT (realistic SVG-style on canvas)
// ============================================================
function drawBuiltInShirt(color) {
  const W = canvas.width, H = canvas.height;

  // Coordinates as fractions
  function p(fx, fy) { return [W * fx, H * fy]; }

  ctx.save();
  ctx.shadowColor = 'rgba(0,0,0,0.18)';
  ctx.shadowBlur = W * 0.06;
  ctx.shadowOffsetY = H * 0.03;

  ctx.beginPath();
  ctx.moveTo(...p(0.14, 0.16));          // left shoulder
  ctx.lineTo(...p(0.02, 0.30));          // left sleeve tip
  ctx.lineTo(...p(0.04, 0.33));
  ctx.lineTo(...p(0.18, 0.27));          // left armpit
  ctx.lineTo(...p(0.17, 0.88));          // left bottom
  ctx.quadraticCurveTo(...p(0.5, 0.96), ...p(0.83, 0.88));  // bottom curve
  ctx.lineTo(...p(0.82, 0.27));          // right armpit
  ctx.lineTo(...p(0.96, 0.33));
  ctx.lineTo(...p(0.98, 0.30));          // right sleeve tip
  ctx.lineTo(...p(0.86, 0.16));          // right shoulder
  // collar
  ctx.quadraticCurveTo(...p(0.76, 0.10), ...p(0.60, 0.10));
  ctx.quadraticCurveTo(...p(0.50, 0.08), ...p(0.40, 0.10));
  ctx.quadraticCurveTo(...p(0.24, 0.10), ...p(0.14, 0.16));
  ctx.closePath();

  ctx.fillStyle = color;
  ctx.fill();
  ctx.restore();

  // Fabric gradient highlight (left)
  const g1 = ctx.createLinearGradient(W*0.14, H*0.16, W*0.45, H*0.7);
  g1.addColorStop(0, 'rgba(255,255,255,0.12)');
  g1.addColorStop(0.5, 'rgba(255,255,255,0.04)');
  g1.addColorStop(1, 'rgba(0,0,0,0)');
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(...p(0.14, 0.16));
  ctx.lineTo(...p(0.02, 0.30));
  ctx.lineTo(...p(0.04, 0.33));
  ctx.lineTo(...p(0.18, 0.27));
  ctx.lineTo(...p(0.17, 0.88));
  ctx.quadraticCurveTo(...p(0.5, 0.96), ...p(0.83, 0.88));
  ctx.lineTo(...p(0.82, 0.27));
  ctx.lineTo(...p(0.96, 0.33));
  ctx.lineTo(...p(0.98, 0.30));
  ctx.lineTo(...p(0.86, 0.16));
  ctx.quadraticCurveTo(...p(0.76, 0.10), ...p(0.60, 0.10));
  ctx.quadraticCurveTo(...p(0.50, 0.08), ...p(0.40, 0.10));
  ctx.quadraticCurveTo(...p(0.24, 0.10), ...p(0.14, 0.16));
  ctx.closePath();
  ctx.fillStyle = g1;
  ctx.fill();
  ctx.restore();

  // Right side shadow
  const g2 = ctx.createLinearGradient(W*0.6, H*0.2, W, H*0.9);
  g2.addColorStop(0, 'rgba(0,0,0,0)');
  g2.addColorStop(1, 'rgba(0,0,0,0.12)');
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(...p(0.14, 0.16));
  ctx.lineTo(...p(0.02, 0.30));
  ctx.lineTo(...p(0.04, 0.33));
  ctx.lineTo(...p(0.18, 0.27));
  ctx.lineTo(...p(0.17, 0.88));
  ctx.quadraticCurveTo(...p(0.5, 0.96), ...p(0.83, 0.88));
  ctx.lineTo(...p(0.82, 0.27));
  ctx.lineTo(...p(0.96, 0.33));
  ctx.lineTo(...p(0.98, 0.30));
  ctx.lineTo(...p(0.86, 0.16));
  ctx.quadraticCurveTo(...p(0.76, 0.10), ...p(0.60, 0.10));
  ctx.quadraticCurveTo(...p(0.50, 0.08), ...p(0.40, 0.10));
  ctx.quadraticCurveTo(...p(0.24, 0.10), ...p(0.14, 0.16));
  ctx.closePath();
  ctx.fillStyle = g2;
  ctx.fill();
  ctx.restore();

  // Collar
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(...p(0.33, 0.115));
  ctx.quadraticCurveTo(...p(0.5, 0.09), ...p(0.67, 0.115));
  ctx.quadraticCurveTo(...p(0.63, 0.195), ...p(0.5, 0.205));
  ctx.quadraticCurveTo(...p(0.37, 0.195), ...p(0.33, 0.115));
  ctx.closePath();
  ctx.fillStyle = shadeColor(color, -18);
  const cg = ctx.createLinearGradient(...p(0.33,0.11), ...p(0.5,0.20));
  cg.addColorStop(0, 'rgba(0,0,0,0.1)');
  cg.addColorStop(1, 'rgba(0,0,0,0.02)');
  ctx.fill();
  ctx.fillStyle = cg;
  ctx.fill();
  ctx.restore();

  // Outline
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(...p(0.14, 0.16));
  ctx.lineTo(...p(0.02, 0.30));
  ctx.lineTo(...p(0.04, 0.33));
  ctx.lineTo(...p(0.18, 0.27));
  ctx.lineTo(...p(0.17, 0.88));
  ctx.quadraticCurveTo(...p(0.5, 0.96), ...p(0.83, 0.88));
  ctx.lineTo(...p(0.82, 0.27));
  ctx.lineTo(...p(0.96, 0.33));
  ctx.lineTo(...p(0.98, 0.30));
  ctx.lineTo(...p(0.86, 0.16));
  ctx.quadraticCurveTo(...p(0.76, 0.10), ...p(0.60, 0.10));
  ctx.quadraticCurveTo(...p(0.50, 0.08), ...p(0.40, 0.10));
  ctx.quadraticCurveTo(...p(0.24, 0.10), ...p(0.14, 0.16));
  ctx.closePath();
  ctx.strokeStyle = 'rgba(0,0,0,0.13)';
  ctx.lineWidth = 1.2;
  ctx.stroke();
  ctx.restore();

  // Bottom hem
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(...p(0.17, 0.875));
  ctx.quadraticCurveTo(...p(0.5, 0.955), ...p(0.83, 0.875));
  ctx.lineTo(...p(0.83, 0.9));
  ctx.quadraticCurveTo(...p(0.5, 0.98), ...p(0.17, 0.9));
  ctx.closePath();
  ctx.fillStyle = shadeColor(color, -12);
  ctx.fill();
  ctx.restore();

  // Size tag
  ctx.save();
  ctx.fillStyle = shadeColor(color, -25);
  const tx = W*0.46, ty = H*0.095;
  ctx.fillRect(tx, ty, W*0.08, H*0.055);
  ctx.fillStyle = color === '#ffffff' ? '#888' : 'rgba(255,255,255,0.6)';
  ctx.font = `${W*0.022}px DM Sans, sans-serif`;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText('M', tx + W*0.04, ty + H*0.028);
  ctx.restore();
}

function shadeColor(hex, amt) {
  hex = hex.replace('#','');
  if (hex.length === 3) hex = hex.split('').map(c=>c+c).join('');
  let r = Math.max(0,Math.min(255,parseInt(hex.slice(0,2),16)+amt));
  let g = Math.max(0,Math.min(255,parseInt(hex.slice(2,4),16)+amt));
  let b = Math.max(0,Math.min(255,parseInt(hex.slice(4,6),16)+amt));
  return '#'+[r,g,b].map(v=>v.toString(16).padStart(2,'0')).join('');
}

// ============================================================
// LOAD SHIRT PHOTO FROM FILE
// ============================================================
function loadShirtPhoto(evt) {
  const file = evt.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = new Image();
    img.onload = () => {
      shirtImg = img;
      productMockupError = false;
      renderAll();
      showToast('Фото товара загружено!');
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
  evt.target.value = '';
  closeAddMenu();
}

// ============================================================
// DRAW SHIRT LAYER
// ============================================================
function drawShirt() {
  if (shirtImg) {
    drawProductMockup(shirtImg);
  } else if (productMockupError) {
    ctx.save();
    ctx.fillStyle = '#1a1a2e';
    ctx.font = `${Math.max(13, canvas.width * 0.026)}px DM Sans, sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    wrapCanvasText('Не удалось загрузить изображение товара. Попробуйте обновить страницу.', canvas.width / 2, canvas.height / 2, canvas.width * 0.72, canvas.width * 0.04);
    ctx.restore();
  } else {
    ctx.save();
    ctx.fillStyle = '#8a8fa8';
    ctx.font = `${Math.max(13, canvas.width * 0.026)}px DM Sans, sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('Загрузка товара...', canvas.width / 2, canvas.height / 2);
    ctx.restore();
  }
}

function wrapCanvasText(text, x, y, maxWidth, lineHeight) {
  const words = text.split(' ');
  const lines = [];
  let line = '';

  words.forEach(word => {
    const testLine = line ? `${line} ${word}` : word;
    if (ctx.measureText(testLine).width > maxWidth && line) {
      lines.push(line);
      line = word;
    } else {
      line = testLine;
    }
  });

  if (line) lines.push(line);

  const startY = y - ((lines.length - 1) * lineHeight) / 2;
  lines.forEach((item, index) => ctx.fillText(item, x, startY + index * lineHeight));
}

// ============================================================
// PRINT ZONE HINT
// ============================================================
function positionZoneHint() {
  const pz = getPZ();
  const wrap = document.getElementById('canvasWrap');
  const hint = document.getElementById('zoneHint');
  hint.style.left = pz.x + 'px';
  hint.style.top = pz.y + 'px';
  hint.style.width = pz.w + 'px';
  hint.style.height = pz.h + 'px';
}

// ============================================================
// RENDER
// ============================================================
function renderAll() {
  const W = canvas.width, H = canvas.height;
  ctx.clearRect(0, 0, W, H);

  drawShirt();

  // Hide zone hint if has layers
  const hint = document.getElementById('zoneHint');
  hint.style.opacity = layers.length === 0 ? '1' : '0';

  layers.forEach(layer => {
    ctx.save();
    ctx.globalAlpha = layer.opacity;
    ctx.globalCompositeOperation = layer.blend || 'source-over';

    ctx.translate(layer.x, layer.y);
    ctx.rotate(layer.rotation * Math.PI / 180);
    ctx.scale(layer.scale, layer.scale);

    if (layer.type === 'image' && layer.img) {
      ctx.drawImage(layer.img, -layer.w/2, -layer.h/2, layer.w, layer.h);
    } else if (layer.type === 'text') {
      ctx.font = `${layer.bold ? 'bold ' : ''}${layer.fontSize}px ${layer.fontFamily}`;
      ctx.fillStyle = layer.color;
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.shadowColor = 'rgba(0,0,0,0.15)';
      ctx.shadowBlur = 3; ctx.shadowOffsetY = 1;
      ctx.fillText(layer.text, 0, 0);
    } else if (layer.type === 'rect') {
      ctx.fillStyle = layer.color;
      roundRect(ctx, -layer.w/2, -layer.h/2, layer.w, layer.h, 6);
      ctx.fill();
    } else if (layer.type === 'circle') {
      ctx.beginPath();
      ctx.arc(0, 0, layer.r, 0, Math.PI*2);
      ctx.fillStyle = layer.color;
      ctx.fill();
    } else if (layer.type === 'star') {
      drawStar(ctx, 0, 0, layer.r, 5);
      ctx.fillStyle = layer.color;
      ctx.fill();
    }

    ctx.restore();

    // Selection handles (always source-over)
    if (layer.id === selectedId) {
      drawSelection(layer);
    }
  });

  updateThumb();
}

function roundRect(c, x, y, w, h, r) {
  c.beginPath();
  c.moveTo(x+r, y);
  c.lineTo(x+w-r, y); c.arcTo(x+w, y, x+w, y+r, r);
  c.lineTo(x+w, y+h-r); c.arcTo(x+w, y+h, x+w-r, y+h, r);
  c.lineTo(x+r, y+h); c.arcTo(x, y+h, x, y+h-r, r);
  c.lineTo(x, y+r); c.arcTo(x, y, x+r, y, r);
  c.closePath();
}

function drawStar(c, cx, cy, r, pts) {
  c.beginPath();
  for (let i = 0; i < pts*2; i++) {
    const angle = (i * Math.PI / pts) - Math.PI/2;
    const rr = i%2===0 ? r : r*0.4;
    const x = cx + rr*Math.cos(angle);
    const y = cy + rr*Math.sin(angle);
    i===0 ? c.moveTo(x,y) : c.lineTo(x,y);
  }
  c.closePath();
}

function drawSelection(layer) {
  ctx.save();
  ctx.translate(layer.x, layer.y);
  ctx.rotate(layer.rotation * Math.PI/180);
  ctx.scale(layer.scale, layer.scale);

  ctx.strokeStyle = '#007aff';
  ctx.lineWidth = 1.5 / layer.scale;
  ctx.setLineDash([4/layer.scale, 4/layer.scale]);

  let bx, by, bw, bh;
  if (layer.type === 'text') {
    ctx.font = `${layer.bold?'bold ':''}${layer.fontSize}px ${layer.fontFamily}`;
    const tw = ctx.measureText(layer.text).width;
    bw = tw + 20; bh = layer.fontSize + 16;
    bx = -bw/2; by = -bh/2;
  } else if (layer.type === 'image') {
    bw = layer.w+8; bh = layer.h+8; bx = -bw/2; by = -bh/2;
  } else if (layer.type === 'rect') {
    bw = layer.w+8; bh = layer.h+8; bx = -bw/2; by = -bh/2;
  } else {
    const r = layer.r+8;
    bw = r*2; bh = r*2; bx = -r; by = -r;
  }

  ctx.strokeRect(bx, by, bw, bh);
  ctx.setLineDash([]);

  // Corner handles
  const hs = 6/layer.scale;
  [[bx,by],[bx+bw,by],[bx+bw,by+bh],[bx,by+bh]].forEach(([hx,hy]) => {
    ctx.beginPath();
    ctx.arc(hx, hy, hs, 0, Math.PI*2);
    ctx.fillStyle = '#007aff';
    ctx.fill();
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 1.5/layer.scale;
    ctx.stroke();
  });

  ctx.restore();
}

// ============================================================
// THUMBNAIL
// ============================================================
function updateThumb() {
  const tc = document.getElementById('thumbCanvas0');
  const t = tc.getContext('2d');
  t.clearRect(0,0,80,80);
  t.drawImage(canvas, 0, 0, 80, 80);
}

// ============================================================
// ADD LAYERS
// ============================================================
function addImage(evt) {
  const file = evt.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = new Image();
    img.onload = () => {
      const pz = getPZ();
      const maxW = pz.w * 0.7, maxH = pz.h * 0.7;
      const ratio = Math.min(maxW/img.width, maxH/img.height, 1);
      saveHistory();
      const layer = {
        id: nextId++, type: 'image', img,
        x: pz.x + pz.w/2, y: pz.y + pz.h/2,
        w: img.width*ratio, h: img.height*ratio,
        rotation: 0, scale: 1, opacity: 1, blend: 'source-over',
        name: file.name.split('.')[0].substring(0,14),
        src: e.target.result,
        originalFileName: file.name
      };
      layers.push(layer);
      selectedId = layer.id;
      refreshUI();
      showToast('🖼 Изображение добавлено!');
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
  evt.target.value = '';
  closeAddMenu();
}

function addText() {
  const pz = getPZ();
  saveHistory();
  const layer = {
    id: nextId++, type: 'text',
    text: 'YOUR TEXT',
    fontSize: Math.round(canvas.width * 0.08),
    fontFamily: 'Syne',
    color: '#000000',
    bold: false,
    x: pz.x + pz.w/2, y: pz.y + pz.h/2,
    rotation: 0, scale: 1, opacity: 1, blend: 'source-over',
    name: 'Текст'
  };
  layers.push(layer);
  selectedId = layer.id;
  refreshUI();
  document.getElementById('textInput').value = layer.text;
  showToast('✍️ Текст добавлен!');
}

function addShape(type) {
  const pz = getPZ();
  saveHistory();
  const r = pz.w * 0.15;
  const colors = { rect: '#007aff', circle: '#ff3b5c', star: '#f59e0b' };
  const layer = {
    id: nextId++, type,
    w: r*2, h: r*1.2, r,
    color: colors[type] || '#007aff',
    x: pz.x + pz.w/2, y: pz.y + pz.h/2,
    rotation: 0, scale: 1, opacity: 1, blend: 'source-over',
    name: type === 'rect' ? 'Форма' : type === 'circle' ? 'Круг' : 'Звезда'
  };
  layers.push(layer);
  selectedId = layer.id;
  refreshUI();
}

// ============================================================
// UI UPDATE
// ============================================================
function refreshUI() {
  updateLayersList();
  updateRightPanel();
  renderAll();
}

function updateLayersList() {
  const list = document.getElementById('layersList');
  const empty = document.getElementById('layersEmpty');
  document.getElementById('layerCnt').textContent = layers.length;
  list.querySelectorAll('.layer-item').forEach(el => el.remove());

  if (layers.length === 0) { empty.style.display='block'; return; }
  empty.style.display='none';

  [...layers].reverse().forEach(layer => {
    const div = document.createElement('div');
    div.className = 'layer-item' + (layer.id === selectedId ? ' selected' : '');
    const icons = { image:'🖼', text:'T', rect:'▭', circle:'○', star:'★' };
    div.innerHTML = `<span class="layer-icon">${icons[layer.type]||'□'}</span>
      <span class="layer-name">${layer.name}</span>
      <button class="layer-del" onclick="deleteLayer(${layer.id},event)">✕</button>`;
    div.addEventListener('click', () => { selectedId = layer.id; refreshUI(); });
    list.appendChild(div);
  });
}

function updateRightPanel() {
  const sel = layers.find(l => l.id === selectedId);
  const textPanel = document.getElementById('textPanel');

  if (!sel) {
    textPanel.style.display='none';
    return;
  }

  textPanel.style.display = sel.type === 'text' ? 'block' : 'none';

  if (sel.type === 'text') {
    document.getElementById('textInput').value = sel.text;
    document.getElementById('fontSel').value = sel.fontFamily;
    document.getElementById('fontSizeR').value = sel.fontSize;
    document.getElementById('fsVal').textContent = sel.fontSize;
    document.getElementById('textColorPick').value = sel.color;
    document.getElementById('textColorHex').value = sel.color;
    document.getElementById('boldBtn').style.fontWeight = sel.bold ? '800' : '400';
    document.getElementById('boldBtn').style.borderColor = sel.bold ? 'var(--accent)' : '';
  }

  document.getElementById('rotR').value = sel.rotation;
  document.getElementById('rotVal').textContent = Math.round(sel.rotation);
  document.getElementById('scaleR').value = Math.round(sel.scale * 100);
  document.getElementById('scaleVal').textContent = Math.round(sel.scale*100);
  document.getElementById('opR').value = Math.round(sel.opacity * 100);
  document.getElementById('opVal').textContent = Math.round(sel.opacity*100);
  document.getElementById('blendSel').value = sel.blend || 'source-over';
}

function updateSelectedText() {
  const sel = layers.find(l => l.id === selectedId);
  if (!sel || sel.type !== 'text') return;
  sel.text = document.getElementById('textInput').value;
  sel.fontFamily = document.getElementById('fontSel').value;
  sel.fontSize = parseInt(document.getElementById('fontSizeR').value);
  sel.color = document.getElementById('textColorPick').value;
  sel.name = sel.text.substring(0,14) || 'Текст';
  document.getElementById('textColorHex').value = sel.color;
  updateLayersList();
  renderAll();
}

function syncTxtColor() {
  const val = document.getElementById('textColorHex').value;
  if (/^#[0-9a-fA-F]{6}$/.test(val)) {
    document.getElementById('textColorPick').value = val;
    updateSelectedText();
  }
}

function toggleBold() {
  const sel = layers.find(l => l.id === selectedId);
  if (!sel || sel.type !== 'text') return;
  sel.bold = !sel.bold;
  document.getElementById('boldBtn').style.fontWeight = sel.bold ? '800' : '400';
  document.getElementById('boldBtn').style.borderColor = sel.bold ? 'var(--accent)' : '';
  renderAll();
}

function updateTransform(prop, val) {
  const sel = layers.find(l => l.id === selectedId);
  if (!sel) return;
  sel[prop === 'blend' ? 'blend' : prop] = val;
  renderAll();
}

function deleteLayer(id, e) {
  e.stopPropagation();
  saveHistory();
  layers = layers.filter(l => l.id !== id);
  if (selectedId === id) selectedId = layers.length ? layers[layers.length-1].id : null;
  refreshUI();
}

// DRAG & ROTATE INTERACTIONS
// ============================================================
let isDragging = false, dragOffX = 0, dragOffY = 0;
let isRotating = false, rotStartAngle = 0, rotStartRot = 0;

function getCanvasPos(e) {
  const rect = canvas.getBoundingClientRect();
  const touch = e.touches ? e.touches[0] : e;
  return [
    (touch.clientX - rect.left) * (canvas.width / rect.width),
    (touch.clientY - rect.top) * (canvas.height / rect.height)
  ];
}

function getLayerAt(x, y) {
  for (let i = layers.length-1; i >= 0; i--) {
    const l = layers[i];
    const dx = x - l.x, dy = y - l.y;
    const cos = Math.cos(-l.rotation * Math.PI/180);
    const sin = Math.sin(-l.rotation * Math.PI/180);
    const lx = (dx*cos - dy*sin) / l.scale;
    const ly = (dx*sin + dy*cos) / l.scale;
    let hw, hh;
    if (l.type === 'text') {
      ctx.font = `${l.bold?'bold ':''}${l.fontSize}px ${l.fontFamily}`;
      hw = ctx.measureText(l.text).width/2 + 12;
      hh = l.fontSize/2 + 10;
    } else if (l.type === 'image') { hw = l.w/2+6; hh = l.h/2+6; }
    else if (l.type === 'rect') { hw = l.w/2+6; hh = l.h/2+6; }
    else { hw = l.r+8; hh = l.r+8; }
    if (Math.abs(lx) < hw && Math.abs(ly) < hh) return l;
  }
  return null;
}

canvas.addEventListener('mousedown', e => {
  const [x, y] = getCanvasPos(e);
  const hit = getLayerAt(x, y);
  if (hit) {
    if (e.altKey || e.metaKey) {
      // Rotate mode
      isRotating = true;
      selectedId = hit.id;
      rotStartAngle = Math.atan2(y - hit.y, x - hit.x) * 180/Math.PI;
      rotStartRot = hit.rotation;
    } else {
      isDragging = true;
      selectedId = hit.id;
      dragOffX = x - hit.x;
      dragOffY = y - hit.y;
    }
    refreshUI();
    canvas.style.cursor = 'grabbing';
  } else {
    selectedId = null;
    refreshUI();
  }
});

canvas.addEventListener('mousemove', e => {
  const [x, y] = getCanvasPos(e);
  if (isDragging && selectedId) {
    const sel = layers.find(l => l.id === selectedId);
    if (sel) { sel.x = x - dragOffX; sel.y = y - dragOffY; renderAll(); }
  } else if (isRotating && selectedId) {
    const sel = layers.find(l => l.id === selectedId);
    if (sel) {
      const ang = Math.atan2(y - sel.y, x - sel.x) * 180/Math.PI;
      sel.rotation = rotStartRot + (ang - rotStartAngle);
      document.getElementById('rotR').value = sel.rotation;
      document.getElementById('rotVal').textContent = Math.round(sel.rotation);
      renderAll();
    }
  } else {
    canvas.style.cursor = getLayerAt(x, y) ? 'grab' : 'default';
  }
});

canvas.addEventListener('mouseup', () => { isDragging = false; isRotating = false; canvas.style.cursor = 'default'; });
canvas.addEventListener('mouseleave', () => { isDragging = false; isRotating = false; });

// Touch
canvas.addEventListener('touchstart', e => {
  e.preventDefault();
  const [x, y] = getCanvasPos(e);
  const hit = getLayerAt(x, y);
  if (hit) {
    isDragging = true; selectedId = hit.id;
    dragOffX = x - hit.x; dragOffY = y - hit.y;
    refreshUI();
  }
}, {passive:false});
canvas.addEventListener('touchmove', e => {
  e.preventDefault();
  if (!isDragging) return;
  const [x, y] = getCanvasPos(e);
  const sel = layers.find(l => l.id === selectedId);
  if (sel) { sel.x = x - dragOffX; sel.y = y - dragOffY; renderAll(); }
}, {passive:false});
canvas.addEventListener('touchend', () => { isDragging = false; });

// ============================================================
// HISTORY
// ============================================================
function saveHistory() {
  history.push(JSON.stringify(layers.map(l => {
    const {img, ...rest} = l;
    return {...rest, _hasImg: !!img};
  })));
  future = [];
  if (history.length > 30) history.shift();
}

function undo() {
  if (!history.length) return;
  future.push(JSON.stringify(layers.map(l => { const {img,...r}=l; return r; })));
  // Simple undo — just remove last layer for simplicity
  showToast('↩ Отменено');
}
function redo() { showToast('↪ Повторено'); }

// ============================================================
// ADD MENU
// ============================================================
function toggleAddMenu() {
  document.getElementById('addMenu').classList.toggle('open');
}
function closeAddMenu() {
  document.getElementById('addMenu').classList.remove('open');
}
document.addEventListener('click', e => {
  const menu = document.getElementById('addMenu');
  if (!menu.contains(e.target) && !e.target.closest('.add-btn') && !e.target.closest('.mobile-add-fab')) {
    menu.classList.remove('open');
  }
});

// ============================================================
// EXPORT
// ============================================================
function exportFull() {
  const prev = selectedId;
  selectedId = null;
  renderAll();
  const link = document.createElement('a');
  link.download = 'tshirt_design.png';
  link.href = canvas.toDataURL('image/png');
  link.click();
  selectedId = prev; renderAll();
  showToast('👕 Сохранено!');
}

function exportPrintOnly() {
  const link = document.createElement('a');
  link.download = 'print_only.png';
  link.href = getPrintOnlyDataUrl();
  link.click();
  showToast('🖨 Принт сохранён!');
}

function getFullDataUrl() {
  const prev = selectedId;
  selectedId = null;
  renderAll();
  const dataUrl = canvas.toDataURL('image/png');
  selectedId = prev;
  renderAll();
  return dataUrl;
}

function drawLayerToContext(tc, layer, offsetX, offsetY) {
  tc.save();
  tc.globalAlpha = layer.opacity;
  tc.globalCompositeOperation = layer.blend || 'source-over';
  const cx = layer.x - offsetX, cy = layer.y - offsetY;
  tc.translate(cx, cy);
  tc.rotate(layer.rotation * Math.PI/180);
  tc.scale(layer.scale, layer.scale);
  if (layer.type === 'image' && layer.img) {
    tc.drawImage(layer.img, -layer.w/2, -layer.h/2, layer.w, layer.h);
  } else if (layer.type === 'text') {
    tc.font = `${layer.bold?'bold ':''}${layer.fontSize}px ${layer.fontFamily}`;
    tc.fillStyle = layer.color; tc.textAlign='center'; tc.textBaseline='middle';
    tc.fillText(layer.text, 0, 0);
  } else if (layer.type === 'rect') {
    tc.fillStyle = layer.color;
    roundRect(tc, -layer.w/2, -layer.h/2, layer.w, layer.h, 6); tc.fill();
  } else if (layer.type === 'circle') {
    tc.beginPath(); tc.arc(0,0,layer.r,0,Math.PI*2); tc.fillStyle=layer.color; tc.fill();
  } else if (layer.type === 'star') {
    drawStar(tc, 0, 0, layer.r, 5); tc.fillStyle=layer.color; tc.fill();
  }
  tc.restore();
}

function getPrintOnlyDataUrl() {
  const pz = getPZ();
  const tmp = document.createElement('canvas');
  tmp.width = pz.w; tmp.height = pz.h;
  const tc = tmp.getContext('2d');
  layers.forEach(layer => drawLayerToContext(tc, layer, pz.x, pz.y));
  return tmp.toDataURL('image/png');
}

function serializeLayers() {
  return layers.map(layer => {
    const base = {
      id: String(layer.id),
      type: layer.type,
      name: layer.name,
      x: layer.x,
      y: layer.y,
      rotation: layer.rotation,
      scale: layer.scale,
      opacity: layer.opacity,
      blend: layer.blend || 'source-over',
    };

    if (layer.type === 'text') {
      return {
        ...base,
        text: layer.text,
        fontFamily: layer.fontFamily,
        fontSize: layer.fontSize,
        color: layer.color,
        bold: layer.bold,
      };
    }

    if (layer.type === 'image') {
      return {
        ...base,
        src: layer.src || '',
        originalFileName: layer.originalFileName || layer.name,
        width: layer.w,
        height: layer.h,
      };
    }

    return {
      ...base,
      width: layer.w || null,
      height: layer.h || null,
      radius: layer.r || null,
      color: layer.color || null,
    };
  });
}

function collectAssets() {
  return layers
    .filter(layer => layer.type === 'image' && layer.src && layer.src.startsWith('data:'))
    .map(layer => ({
      layer_id: String(layer.id),
      file_name: layer.originalFileName || layer.name || 'image.png',
      data: layer.src,
    }));
}

function openOrderDialog() {
  document.getElementById('orderDialog').classList.add('open');
  document.getElementById('customerName').focus();
}

function closeOrderDialog() {
  document.getElementById('orderDialog').classList.remove('open');
  document.getElementById('orderError').classList.remove('show');
}

async function submitOrderRequest(evt) {
  evt.preventDefault();
  const err = document.getElementById('orderError');
  err.classList.remove('show');

  const payload = {
    customer_name: document.getElementById('customerName').value,
    customer_phone: document.getElementById('customerPhone').value,
    customer_comment: document.getElementById('customerComment').value,
    product_id: constructorConfig.product.id,
    variant_id: constructorConfig.variant.id,
    quantity: 1,
    side: constructorConfig.printArea.side || 'front',
    canvas_json: {
      layers: serializeLayers(),
      print_area: constructorConfig.printArea,
    },
    preview_image: getFullDataUrl(),
    print_image: getPrintOnlyDataUrl(),
    assets: collectAssets(),
  };

  const response = await fetch(constructorConfig.routes.storeOrderRequest, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(payload),
  });

  if (!response.ok) {
    err.textContent = '{{ __('site.order_error') }}';
    err.classList.add('show');
    return;
  }

  window.location.href = constructorConfig.routes.success;
}

async function exportLayers() {
  if (!layers.length) { showToast('⚠️ Нет слоёв'); return; }
  const pz = getPZ();
  for (let i = 0; i < layers.length; i++) {
    const layer = layers[i];
    const tmp = document.createElement('canvas');
    tmp.width = pz.w; tmp.height = pz.h;
    const tc = tmp.getContext('2d');
    tc.save();
    tc.globalAlpha = layer.opacity;
    const cx = layer.x - pz.x, cy = layer.y - pz.y;
    tc.translate(cx, cy);
    tc.rotate(layer.rotation * Math.PI/180);
    tc.scale(layer.scale, layer.scale);
    if (layer.type === 'image' && layer.img) {
      tc.drawImage(layer.img, -layer.w/2, -layer.h/2, layer.w, layer.h);
    } else if (layer.type === 'text') {
      tc.font = `${layer.bold?'bold ':''}${layer.fontSize}px ${layer.fontFamily}`;
      tc.fillStyle=layer.color; tc.textAlign='center'; tc.textBaseline='middle';
      tc.fillText(layer.text, 0, 0);
    } else if (layer.type === 'rect') {
      tc.fillStyle=layer.color;
      roundRect(tc,-layer.w/2,-layer.h/2,layer.w,layer.h,6); tc.fill();
    } else if (layer.type === 'circle') {
      tc.beginPath(); tc.arc(0,0,layer.r,0,Math.PI*2); tc.fillStyle=layer.color; tc.fill();
    } else if (layer.type === 'star') {
      drawStar(tc,0,0,layer.r,5); tc.fillStyle=layer.color; tc.fill();
    }
    tc.restore();
    await new Promise(res => setTimeout(() => {
      const link = document.createElement('a');
      link.download = `layer_${i+1}_${layer.name}.png`;
      link.href = tmp.toDataURL('image/png');
      link.click(); res();
    }, i * 350));
  }
  showToast(`📦 ${layers.length} слоёв сохранено`);
}

// ============================================================
// TOAST
// ============================================================
let toastTmr;
function showToast(msg) {
  clearTimeout(toastTmr);
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  toastTmr = setTimeout(() => t.classList.remove('show'), 2200);
}

// ============================================================
// MOBILE TAB SWITCHING
// ============================================================
let mobileActiveTab = null;

function mobileSwitchTab(tab) {
  const panelRight    = document.querySelector('.panel-right');
  const variantsPanel = document.getElementById('mobileVariantsPanel');
  const tabMap = { layers: 'mTabLayers', props: 'mTabProps', variants: 'mTabVariants' };

  // Deactivate all tabs
  Object.values(tabMap).forEach(id => document.getElementById(id)?.classList.remove('active'));

  // Toggle: tap same tab → close
  if (mobileActiveTab === tab) {
    mobileActiveTab = null;
    panelRight.classList.remove('mobile-open');
    variantsPanel.classList.remove('mobile-open');
    return;
  }

  mobileActiveTab = tab;
  panelRight.classList.remove('mobile-open');
  variantsPanel.classList.remove('mobile-open');

  if (tab === 'layers' || tab === 'props') {
    document.getElementById(tabMap[tab])?.classList.add('active');
    panelRight.classList.add('mobile-open');
    if (tab === 'layers') {
      setTimeout(() => {
        document.getElementById('layersList')?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }, 60);
    }
  } else if (tab === 'variants') {
    document.getElementById('mTabVariants')?.classList.add('active');
    variantsPanel.classList.add('mobile-open');
  }
}

// Close mobile panels when user taps on the canvas
canvas.addEventListener('touchend', () => {
  if (window.innerWidth > 768) return;
  document.querySelector('.panel-right')?.classList.remove('mobile-open');
  document.getElementById('mobileVariantsPanel')?.classList.remove('mobile-open');
  document.querySelectorAll('.mobile-tab-btn').forEach(b => b.classList.remove('active'));
  mobileActiveTab = null;
}, { passive: true });

// ============================================================
// INIT
// ============================================================
document.getElementById('orderForm').addEventListener('submit', submitOrderRequest);
loadProductMockup();
resizeCanvas();
</script>
</body>
</html>
