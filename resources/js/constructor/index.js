const config = window.constructorConfig;

// ---- DOM REFS ----
const canvas = document.getElementById('mainCanvas');
const ctx = canvas.getContext('2d');
const imageInput = document.getElementById('imageInput');
const checkoutDialog = document.getElementById('checkoutDialog');
const checkoutForm = document.getElementById('checkoutForm');
const canvasError = document.getElementById('canvasError');

// ---- STATE ----
const state = {
    productId: config.product.id,
    variantId: config.variant.id,
    side: config.printArea.side,
    selectedLayerId: null,
    layers: [],
    mockups: {
        front: config.variant.mockup_front_url,
        back: config.variant.mockup_back_url,
    },
    printArea: config.printArea,
};

const imageCache = new Map();
const resolvedImages = new Map();
let mockupImage = null;
let mockupLoaded = false;
let dragging = null;
let history = [];
let future = [];
let layerCounter = 0;
let toastTimer = null;

const colorNameToHex = {
    white: '#ffffff', black: '#111827', navy: '#1e3a5f',
    red: '#dc2626', blue: '#2563eb', green: '#16a34a',
    gray: '#6b7280', grey: '#6b7280', yellow: '#f59e0b',
    purple: '#7c3aed', orange: '#ea580c', teal: '#0d9488',
    pink: '#ec4899', brown: '#92400e', beige: '#d4b896',
};

// ---- HISTORY ----
function snapshot(source = state) {
    return JSON.parse(JSON.stringify({
        productId: source.productId,
        variantId: source.variantId,
        side: source.side,
        selectedLayerId: source.selectedLayerId,
        layers: source.layers,
        mockups: source.mockups,
        printArea: source.printArea,
    }));
}

function pushHistory() {
    history.push(snapshot());
    if (history.length > 80) history.shift();
    future = [];
}

function restore(next) {
    Object.assign(state, snapshot(next));
    hydrateImages();
    renderAll();
    updatePanels();
}

function undo() {
    if (!history.length) return;
    future.push(snapshot());
    restore(history.pop());
    showToast('↩ Отменено');
}

function redo() {
    if (!future.length) return;
    history.push(snapshot());
    restore(future.pop());
    showToast('↪ Повторено');
}

// ---- CANVAS SIZE ----
function resizeCanvas() {
    canvas.width = 720;
    canvas.height = 720;
    renderAll();
    positionZoneHint();
}

// ---- IMAGE LOADING ----
function loadImage(src) {
    if (!src) return Promise.reject(new Error('Missing image src'));
    if (imageCache.has(src)) return imageCache.get(src);

    const promise = new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => { resolvedImages.set(src, img); resolve(img); };
        img.onerror = reject;
        img.src = src;
    });

    imageCache.set(src, promise);
    return promise;
}

function hydrateImages() {
    state.layers
        .filter(l => l.type === 'image')
        .forEach(l => loadImage(l.src).then(renderAll).catch(() => {}));
}

async function loadProductMockup() {
    try {
        mockupImage = await loadImage(state.mockups[state.side] || state.mockups.front);
        mockupLoaded = true;
        canvasError.hidden = true;
        renderAll();
    } catch {
        mockupLoaded = false;
        canvasError.hidden = false;
        renderAll();
    }
}

// ---- BUILT-IN SHIRT DRAWING ----
function shadeColor(hex, amt) {
    hex = hex.replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    const r = Math.max(0, Math.min(255, parseInt(hex.slice(0, 2), 16) + amt));
    const g = Math.max(0, Math.min(255, parseInt(hex.slice(2, 4), 16) + amt));
    const b = Math.max(0, Math.min(255, parseInt(hex.slice(4, 6), 16) + amt));
    return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
}

function drawShirtPath(context, W, H) {
    const p = (fx, fy) => [W * fx, H * fy];
    context.beginPath();
    context.moveTo(...p(0.14, 0.16));
    context.lineTo(...p(0.02, 0.30));
    context.lineTo(...p(0.04, 0.33));
    context.lineTo(...p(0.18, 0.27));
    context.lineTo(...p(0.17, 0.88));
    context.quadraticCurveTo(...p(0.5, 0.96), ...p(0.83, 0.88));
    context.lineTo(...p(0.82, 0.27));
    context.lineTo(...p(0.96, 0.33));
    context.lineTo(...p(0.98, 0.30));
    context.lineTo(...p(0.86, 0.16));
    context.quadraticCurveTo(...p(0.76, 0.10), ...p(0.60, 0.10));
    context.quadraticCurveTo(...p(0.50, 0.08), ...p(0.40, 0.10));
    context.quadraticCurveTo(...p(0.24, 0.10), ...p(0.14, 0.16));
    context.closePath();
}

function drawBuiltInShirt(color = '#ffffff') {
    const W = canvas.width, H = canvas.height;
    const p = (fx, fy) => [W * fx, H * fy];

    // Drop shadow
    ctx.save();
    ctx.shadowColor = 'rgba(0,0,0,0.18)';
    ctx.shadowBlur = W * 0.06;
    ctx.shadowOffsetY = H * 0.03;
    drawShirtPath(ctx, W, H);
    ctx.fillStyle = color;
    ctx.fill();
    ctx.restore();

    // Highlight gradient (left side)
    ctx.save();
    drawShirtPath(ctx, W, H);
    const g1 = ctx.createLinearGradient(W * 0.14, H * 0.16, W * 0.45, H * 0.7);
    g1.addColorStop(0, 'rgba(255,255,255,0.13)');
    g1.addColorStop(0.5, 'rgba(255,255,255,0.04)');
    g1.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = g1;
    ctx.fill();
    ctx.restore();

    // Shadow gradient (right side)
    ctx.save();
    drawShirtPath(ctx, W, H);
    const g2 = ctx.createLinearGradient(W * 0.6, H * 0.2, W, H * 0.9);
    g2.addColorStop(0, 'rgba(0,0,0,0)');
    g2.addColorStop(1, 'rgba(0,0,0,0.12)');
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
    ctx.fill();
    const cg = ctx.createLinearGradient(...p(0.33, 0.11), ...p(0.5, 0.20));
    cg.addColorStop(0, 'rgba(0,0,0,0.1)');
    cg.addColorStop(1, 'rgba(0,0,0,0.02)');
    ctx.fillStyle = cg;
    ctx.fill();
    ctx.restore();

    // Outline
    ctx.save();
    drawShirtPath(ctx, W, H);
    ctx.strokeStyle = 'rgba(0,0,0,0.13)';
    ctx.lineWidth = 1.5;
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
}

function getShirtColor() {
    const variant = config.variants.find(v => v.id === state.variantId) || config.variant;
    const name = (variant.color || 'white').toLowerCase();
    return colorNameToHex[name] || '#ffffff';
}

function drawShirt() {
    if (mockupLoaded && mockupImage) {
        ctx.drawImage(mockupImage, 0, 0, canvas.width, canvas.height);
    } else {
        drawBuiltInShirt(getShirtColor());
    }
}

// ---- PRINT AREA ----
function getPrintArea(targetCanvas = canvas, printArea = state.printArea) {
    return {
        x: targetCanvas.width * Number(printArea.x),
        y: targetCanvas.height * Number(printArea.y),
        width: targetCanvas.width * Number(printArea.width),
        height: targetCanvas.height * Number(printArea.height),
    };
}

function centerInPrintArea(width = 160, height = 80) {
    const area = getPrintArea();
    return {
        x: area.x + area.width / 2 - width / 2,
        y: area.y + area.height / 2 - height / 2,
    };
}

function positionZoneHint() {
    const pa = state.printArea;
    const hint = document.getElementById('zoneHint');
    if (!hint) return;
    hint.style.left   = (Number(pa.x) * 100) + '%';
    hint.style.top    = (Number(pa.y) * 100) + '%';
    hint.style.width  = (Number(pa.width) * 100) + '%';
    hint.style.height = (Number(pa.height) * 100) + '%';
}

// ---- LAYER DRAWING ----
function drawLayer(layer, targetContext = ctx, offsetX = 0, offsetY = 0) {
    targetContext.save();
    targetContext.globalAlpha = layer.opacity ?? 1;
    targetContext.globalCompositeOperation = layer.blend || 'source-over';

    const x = layer.x - offsetX;
    const y = layer.y - offsetY;

    if (layer.rotation) {
        const bounds = getLayerBounds(layer);
        targetContext.translate(x + bounds.width / 2, y + bounds.height / 2);
        targetContext.rotate((layer.rotation * Math.PI) / 180);
        targetContext.translate(-(x + bounds.width / 2), -(y + bounds.height / 2));
    }

    if (layer.type === 'text') {
        targetContext.font = `${layer.bold ? '700 ' : ''}${layer.fontSize * layer.scale}px ${layer.fontFamily}`;
        targetContext.fillStyle = layer.color;
        targetContext.textBaseline = 'top';
        targetContext.shadowColor = 'rgba(0,0,0,0.12)';
        targetContext.shadowBlur = 2;
        targetContext.shadowOffsetY = 1;
        targetContext.fillText(layer.text, x, y);
    }

    if (layer.type === 'image') {
        loadImage(layer.src).then(renderAll).catch(() => {});
        const img = resolvedImages.get(layer.src);
        if (img) targetContext.drawImage(img, x, y, layer.width * layer.scale, layer.height * layer.scale);
    }

    if (layer.type === 'rect') {
        targetContext.fillStyle = layer.color || '#111111';
        targetContext.beginPath();
        const rw = layer.width * layer.scale, rh = layer.height * layer.scale, r = 6;
        targetContext.moveTo(x + r, y);
        targetContext.lineTo(x + rw - r, y); targetContext.arcTo(x + rw, y, x + rw, y + r, r);
        targetContext.lineTo(x + rw, y + rh - r); targetContext.arcTo(x + rw, y + rh, x + rw - r, y + rh, r);
        targetContext.lineTo(x + r, y + rh); targetContext.arcTo(x, y + rh, x, y + rh - r, r);
        targetContext.lineTo(x, y + r); targetContext.arcTo(x, y, x + r, y, r);
        targetContext.closePath();
        targetContext.fill();
    }

    if (layer.type === 'circle') {
        targetContext.fillStyle = layer.color || '#111111';
        targetContext.beginPath();
        targetContext.ellipse(x + layer.width / 2, y + layer.height / 2, layer.width / 2, layer.height / 2, 0, 0, Math.PI * 2);
        targetContext.fill();
    }

    targetContext.restore();
}

function getLayerBounds(layer) {
    if (layer.type === 'text') {
        ctx.save();
        ctx.font = `${layer.bold ? '700 ' : ''}${layer.fontSize * layer.scale}px ${layer.fontFamily}`;
        const metrics = ctx.measureText(layer.text || '');
        ctx.restore();
        return { x: layer.x, y: layer.y, width: Math.max(metrics.width, 20), height: layer.fontSize * layer.scale * 1.2 };
    }
    return {
        x: layer.x, y: layer.y,
        width: (layer.width || 120) * (layer.scale || 1),
        height: (layer.height || 80) * (layer.scale || 1),
    };
}

function drawSelection() {
    const layer = selectedLayer();
    if (!layer) return;
    const bounds = getLayerBounds(layer);
    ctx.save();
    ctx.strokeStyle = '#007aff';
    ctx.lineWidth = 1.5;
    ctx.setLineDash([5, 5]);
    ctx.strokeRect(bounds.x - 6, bounds.y - 6, bounds.width + 12, bounds.height + 12);
    ctx.setLineDash([]);
    ctx.fillStyle = '#007aff';
    [
        [bounds.x - 8, bounds.y - 8],
        [bounds.x + bounds.width + 4, bounds.y - 8],
        [bounds.x - 8, bounds.y + bounds.height + 4],
        [bounds.x + bounds.width + 4, bounds.y + bounds.height + 4],
    ].forEach(([hx, hy]) => {
        ctx.beginPath();
        ctx.arc(hx + 4, hy + 4, 5, 0, Math.PI * 2);
        ctx.fillStyle = '#007aff';
        ctx.fill();
        ctx.strokeStyle = 'white';
        ctx.lineWidth = 1.5;
        ctx.stroke();
    });
    ctx.restore();
}

function drawPrintArea() {
    const area = getPrintArea();
    ctx.save();
    ctx.strokeStyle = 'rgba(0,122,255,0.18)';
    ctx.setLineDash([7, 7]);
    ctx.lineWidth = 1.5;
    ctx.strokeRect(area.x, area.y, area.width, area.height);
    ctx.restore();
}

function renderAll() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawShirt();
    drawPrintArea();
    state.layers.forEach(layer => drawLayer(layer));
    drawSelection();

    const hint = document.getElementById('zoneHint');
    if (hint) hint.style.opacity = state.layers.length === 0 ? '1' : '0';

    updateThumb();
}

// ---- LAYER OPERATIONS ----
function makeBaseLayer(type, name) {
    const position = centerInPrintArea();
    return {
        id: `${type}-${Date.now()}-${++layerCounter}`,
        type, name,
        x: position.x, y: position.y,
        rotation: 0, scale: 1, opacity: 1, blend: 'source-over',
    };
}

function addText() {
    pushHistory();
    const layer = {
        ...makeBaseLayer('text', 'Текст'),
        text: 'Ваш текст',
        fontSize: 52,
        fontFamily: 'Arial',
        color: '#111111',
        bold: false,
    };
    state.layers.push(layer);
    selectLayer(layer.id);
}

function addShape(type = 'rect') {
    pushHistory();
    const colors = { rect: '#007aff', circle: '#ff3b5c' };
    const layer = {
        ...makeBaseLayer(type, type === 'circle' ? 'Круг' : 'Форма'),
        width: 150, height: 100,
        color: colors[type] || '#007aff',
    };
    state.layers.push(layer);
    selectLayer(layer.id);
}

function addImage(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = () => {
        const src = String(reader.result);
        loadImage(src).then((img) => {
            pushHistory();
            const max = Math.min(getPrintArea().width, getPrintArea().height) * 0.8;
            const ratio = Math.min(max / img.width, max / img.height, 1);
            const width = img.width * ratio;
            const height = img.height * ratio;
            const position = centerInPrintArea(width, height);
            const layer = {
                ...makeBaseLayer('image', file.name),
                src,
                originalFileName: file.name,
                width, height,
                x: position.x, y: position.y,
            };
            state.layers.push(layer);
            selectLayer(layer.id);
            showToast('🖼 Изображение добавлено!');
        });
    };
    reader.readAsDataURL(file);
}

function duplicateLayer(id) {
    const layer = state.layers.find(l => l.id === id);
    if (!layer) return;
    pushHistory();
    const copy = { ...snapshot(layer), id: `${layer.type}-${Date.now()}-${++layerCounter}`, name: `${layer.name} (копия)`, x: layer.x + 16, y: layer.y + 16 };
    state.layers.push(copy);
    selectLayer(copy.id);
}

function deleteLayer(id) {
    pushHistory();
    state.layers = state.layers.filter(l => l.id !== id);
    if (state.selectedLayerId === id) state.selectedLayerId = null;
    renderAll();
    updatePanels();
}

function selectedLayer() {
    return state.layers.find(l => l.id === state.selectedLayerId) || null;
}

function selectLayer(id) {
    state.selectedLayerId = id;
    renderAll();
    updatePanels();
}

function getLayerAt(x, y) {
    for (let i = state.layers.length - 1; i >= 0; i--) {
        const layer = state.layers[i];
        const bounds = getLayerBounds(layer);
        if (x >= bounds.x && x <= bounds.x + bounds.width && y >= bounds.y && y <= bounds.y + bounds.height) {
            return layer;
        }
    }
    return null;
}

// ---- DRAG ----
function canvasPoint(event) {
    const rect = canvas.getBoundingClientRect();
    const clientX = event.touches ? event.touches[0].clientX : event.clientX;
    const clientY = event.touches ? event.touches[0].clientY : event.clientY;
    return {
        x: ((clientX - rect.left) / rect.width) * canvas.width,
        y: ((clientY - rect.top) / rect.height) * canvas.height,
    };
}

function startDrag(event) {
    const point = canvasPoint(event);
    const layer = getLayerAt(point.x, point.y);
    selectLayer(layer ? layer.id : null);
    if (layer) {
        pushHistory();
        dragging = { id: layer.id, dx: point.x - layer.x, dy: point.y - layer.y };
    }
}

function moveDrag(event) {
    if (!dragging) return;
    event.preventDefault();
    const point = canvasPoint(event);
    const layer = selectedLayer();
    if (!layer) return;
    layer.x = point.x - dragging.dx;
    layer.y = point.y - dragging.dy;
    renderAll();
}

function endDrag() {
    if (dragging) updatePanels();
    dragging = null;
}

// ---- UI: THUMBNAIL ----
function updateThumb() {
    const tc = document.getElementById('thumbCanvas');
    if (!tc) return;
    const t = tc.getContext('2d');
    t.clearRect(0, 0, 80, 80);
    t.drawImage(canvas, 0, 0, 80, 80);
}

// ---- UI: PRICE ----
function updatePrice() {
    const variant = config.variants.find(v => v.id === state.variantId) || config.variant;
    const price = Number(config.product.base_price) + Number(variant.price_modifier || 0);
    const el = document.getElementById('priceLabel');
    if (el) el.textContent = price.toLocaleString('ru-RU') + ' UZS';
}

// ---- UI: LAYER LIST ----
function renderLayerList() {
    const list = document.getElementById('layerList');
    const empty = document.getElementById('layersEmpty');
    const cnt = document.getElementById('layerCnt');

    if (!list) return;
    if (cnt) cnt.textContent = state.layers.length;

    list.querySelectorAll('.ctr-layer-item').forEach(el => el.remove());

    if (!state.layers.length) {
        if (empty) empty.style.display = 'block';
        return;
    }
    if (empty) empty.style.display = 'none';

    const icons = { image: '🖼', text: 'T', rect: '▭', circle: '○' };
    [...state.layers].reverse().forEach(layer => {
        const div = document.createElement('div');
        div.className = 'ctr-layer-item' + (layer.id === state.selectedLayerId ? ' selected' : '');
        div.innerHTML = `
            <span class="ctr-layer-icon">${icons[layer.type] || '□'}</span>
            <span class="ctr-layer-name">${escapeHtml(layer.name)}</span>
            <button class="ctr-layer-del" title="Удалить">✕</button>
        `;
        div.querySelector('.ctr-layer-del').addEventListener('click', e => {
            e.stopPropagation();
            deleteLayer(layer.id);
        });
        div.addEventListener('click', () => selectLayer(layer.id));
        list.appendChild(div);
    });
}

// ---- UI: RIGHT PANEL ----
function updateRightPanel() {
    const sel = selectedLayer();
    const textPanel = document.getElementById('textPanel');
    if (!textPanel) return;

    textPanel.style.display = sel?.type === 'text' ? 'block' : 'none';

    if (sel?.type === 'text') {
        setValue('textInput', sel.text);
        setValue('fontSel', sel.fontFamily);
        setSlider('fontSizeR', 'fsVal', sel.fontSize);
        const pick = document.getElementById('textColorPick');
        const hex = document.getElementById('textColorHex');
        if (pick) pick.value = sel.color;
        if (hex) hex.value = sel.color;
        const boldBtn = document.getElementById('boldBtn');
        if (boldBtn) {
            boldBtn.style.fontWeight = sel.bold ? '800' : '400';
            boldBtn.style.borderColor = sel.bold ? 'var(--ctr-accent)' : '';
        }
    }

    if (sel) {
        setSlider('rotR', 'rotVal', Math.round(sel.rotation));
        setSlider('scaleR', 'scaleVal', Math.round(sel.scale * 100));
        setSlider('opR', 'opVal', Math.round(sel.opacity * 100));
        setValue('blendSel', sel.blend || 'source-over');
    }
}

function setValue(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

function setSlider(sliderId, labelId, val) {
    const slider = document.getElementById(sliderId);
    const label = document.getElementById(labelId);
    if (slider) slider.value = val;
    if (label) label.textContent = val;
}

function updatePanels() {
    renderLayerList();
    updateRightPanel();
    updatePrice();
}

// ---- UI: ADD MENU ----
function toggleAddMenu() {
    const menu = document.getElementById('addMenu');
    const btn = document.getElementById('addMenuToggle');
    if (!menu.classList.contains('open')) {
        const rect = btn.getBoundingClientRect();
        menu.style.top = (rect.bottom + 8) + 'px';
    }
    menu.classList.toggle('open');
}

function closeAddMenu() {
    document.getElementById('addMenu')?.classList.remove('open');
}

// ---- UI: TOAST ----
function showToast(msg) {
    clearTimeout(toastTimer);
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

// ---- VARIANT / COLOR DOTS ----
function initColorDots() {
    const dotsEl = document.getElementById('colorDots');
    const sideBtnsEl = document.getElementById('sideBtns');
    const sideSectionEl = document.getElementById('sideSection');

    // Unique colors → variant dots
    if (dotsEl) {
        const seen = new Set();
        config.variants.forEach(v => {
            const colorKey = (v.color || 'white').toLowerCase();
            if (seen.has(colorKey)) return;
            seen.add(colorKey);

            const hex = colorNameToHex[colorKey] || '#888';
            const dot = document.createElement('div');
            dot.className = 'ctr-cdot' + (v.id === state.variantId ? ' active' : '');
            dot.style.background = hex;
            if (hex === '#ffffff') dot.style.border = '1.5px solid #ccc';
            dot.title = v.color;

            dot.addEventListener('click', () => {
                const next = config.variants.find(vv => vv.color.toLowerCase() === colorKey);
                if (!next || next.id === state.variantId) return;
                pushHistory();
                state.variantId = next.id;
                state.mockups.front = next.mockup_front_url;
                state.mockups.back = next.mockup_back_url;
                dotsEl.querySelectorAll('.ctr-cdot').forEach(d => d.classList.remove('active'));
                dot.classList.add('active');
                loadProductMockup();
                updatePanels();
                showToast('Цвет изменён');
            });

            dotsEl.appendChild(dot);
        });
    }

    // Side buttons (front / back)
    if (sideBtnsEl && sideSectionEl) {
        const hasBoth = config.variant.mockup_front_url && config.variant.mockup_back_url;
        if (hasBoth) {
            sideSectionEl.style.display = 'block';
            ['front', 'back'].forEach(side => {
                const btn = document.createElement('button');
                btn.className = 'ctr-side-btn' + (state.side === side ? ' active' : '');
                btn.textContent = side === 'front' ? 'Перед' : 'Зад';
                btn.addEventListener('click', () => {
                    if (state.side === side) return;
                    pushHistory();
                    state.side = side;
                    sideBtnsEl.querySelectorAll('.ctr-side-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    loadProductMockup();
                    updatePanels();
                });
                sideBtnsEl.appendChild(btn);
            });
        }
    }
}

// ---- EXPORT ----
function exportFull() {
    renderAll();
    return canvas.toDataURL('image/png');
}

function exportPrintOnly() {
    const area = getPrintArea();
    const printCanvas = document.createElement('canvas');
    printCanvas.width = Math.round(area.width);
    printCanvas.height = Math.round(area.height);
    const pc = printCanvas.getContext('2d');
    state.layers.forEach(layer => drawLayer(layer, pc, area.x, area.y));
    return printCanvas.toDataURL('image/png');
}

function exportLayers() {
    return snapshot({ ...state, layers: state.layers.map(l => ({ ...l })) });
}

function downloadAs(dataUrl, filename) {
    const a = document.createElement('a');
    a.download = filename;
    a.href = dataUrl;
    a.click();
}

function downloadFull() {
    const prev = state.selectedLayerId;
    state.selectedLayerId = null;
    renderAll();
    downloadAs(canvas.toDataURL('image/png'), 'tshirt_design.png');
    state.selectedLayerId = prev;
    renderAll();
    showToast('👕 Сохранено!');
}

function downloadPrint() {
    downloadAs(exportPrintOnly(), 'print_only.png');
    showToast('🖨 Принт сохранён!');
}

// ---- ORDER SUBMISSION ----
function collectAssets() {
    return state.layers
        .filter(l => l.type === 'image' && l.src.startsWith('data:'))
        .map(l => ({ layer_id: l.id, file_name: l.originalFileName || l.name, data: l.src }));
}

async function submitOrder(event) {
    event.preventDefault();
    const form = new FormData(checkoutForm);
    const payload = {
        customer_name: form.get('customer_name'),
        customer_phone: form.get('customer_phone'),
        customer_comment: form.get('customer_comment'),
        customer_city: form.get('customer_city') || 'Tashkent',
        customer_address: form.get('customer_address'),
        delivery_lat: form.get('delivery_lat'),
        delivery_lng: form.get('delivery_lng'),
        product_id: state.productId,
        variant_id: state.variantId,
        quantity: 1,
        side: state.side,
        canvas_json: { layers: exportLayers().layers, print_area: state.printArea },
        preview_image: exportFull(),
        print_image: exportPrintOnly(),
        assets: collectAssets(),
    };

    const response = await fetch(config.routes.storeOrderRequest, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    });

    if (!response.ok) {
        alert('Не удалось создать заявку. Проверьте данные и попробуйте снова.');
        return;
    }

    window.location.href = config.routes.success;
}

// ---- HELPERS ----
function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[c]);
}

// ---- EVENT LISTENERS ----

// Header controls
document.getElementById('undoBtn')?.addEventListener('click', undo);
document.getElementById('redoBtn')?.addEventListener('click', redo);
document.getElementById('addMenuToggle')?.addEventListener('click', toggleAddMenu);
document.getElementById('orderBtn')?.addEventListener('click', () => checkoutDialog.showModal());
document.getElementById('exportPrintBtn')?.addEventListener('click', downloadPrint);
document.getElementById('exportPrintBtnRight')?.addEventListener('click', downloadPrint);
document.getElementById('exportFullBtn')?.addEventListener('click', downloadFull);

// Add menu items
document.getElementById('addTextMenuBtn')?.addEventListener('click', () => {
    addText();
    closeAddMenu();
    showToast('✍️ Текст добавлен!');
});
document.getElementById('addRectMenuBtn')?.addEventListener('click', () => {
    addShape('rect');
    closeAddMenu();
    showToast('▭ Форма добавлена!');
});
document.getElementById('addCircleMenuBtn')?.addEventListener('click', () => {
    addShape('circle');
    closeAddMenu();
    showToast('○ Круг добавлен!');
});

imageInput?.addEventListener('change', e => {
    addImage(e.target.files[0]);
    e.target.value = '';
    closeAddMenu();
});

// Close add menu when clicking outside
document.addEventListener('click', e => {
    const menu = document.getElementById('addMenu');
    if (menu && !menu.contains(e.target) && !e.target.closest('#addMenuToggle')) {
        menu.classList.remove('open');
    }
});

// Text controls
document.getElementById('textInput')?.addEventListener('input', () => {
    const sel = selectedLayer();
    if (sel?.type !== 'text') return;
    sel.text = document.getElementById('textInput').value;
    sel.name = sel.text.substring(0, 14) || 'Текст';
    renderAll();
    renderLayerList();
});

document.getElementById('fontSel')?.addEventListener('change', () => {
    const sel = selectedLayer();
    if (sel?.type !== 'text') return;
    sel.fontFamily = document.getElementById('fontSel').value;
    renderAll();
});

document.getElementById('fontSizeR')?.addEventListener('input', function () {
    const sel = selectedLayer();
    if (sel?.type !== 'text') return;
    sel.fontSize = parseInt(this.value);
    document.getElementById('fsVal').textContent = this.value;
    renderAll();
});

document.getElementById('textColorPick')?.addEventListener('input', function () {
    const sel = selectedLayer();
    if (sel?.type !== 'text') return;
    sel.color = this.value;
    const hex = document.getElementById('textColorHex');
    if (hex) hex.value = this.value;
    renderAll();
});

document.getElementById('textColorHex')?.addEventListener('input', function () {
    if (!/^#[0-9a-fA-F]{6}$/.test(this.value)) return;
    const sel = selectedLayer();
    if (sel?.type !== 'text') return;
    sel.color = this.value;
    const pick = document.getElementById('textColorPick');
    if (pick) pick.value = this.value;
    renderAll();
});

document.getElementById('boldBtn')?.addEventListener('click', () => {
    const sel = selectedLayer();
    if (sel?.type !== 'text') return;
    sel.bold = !sel.bold;
    const btn = document.getElementById('boldBtn');
    btn.style.fontWeight = sel.bold ? '800' : '400';
    btn.style.borderColor = sel.bold ? 'var(--ctr-accent)' : '';
    renderAll();
});

// Transform controls
document.getElementById('rotR')?.addEventListener('input', function () {
    const sel = selectedLayer();
    if (!sel) return;
    sel.rotation = parseFloat(this.value);
    document.getElementById('rotVal').textContent = Math.round(sel.rotation);
    renderAll();
});

document.getElementById('scaleR')?.addEventListener('input', function () {
    const sel = selectedLayer();
    if (!sel) return;
    sel.scale = parseFloat(this.value) / 100;
    document.getElementById('scaleVal').textContent = this.value;
    renderAll();
});

document.getElementById('opR')?.addEventListener('input', function () {
    const sel = selectedLayer();
    if (!sel) return;
    sel.opacity = parseFloat(this.value) / 100;
    document.getElementById('opVal').textContent = this.value;
    renderAll();
});

document.getElementById('blendSel')?.addEventListener('change', function () {
    const sel = selectedLayer();
    if (!sel) return;
    sel.blend = this.value;
    renderAll();
});

// Checkout
document.getElementById('closeCheckoutBtn')?.addEventListener('click', () => checkoutDialog.close());
checkoutForm?.addEventListener('submit', submitOrder);

// Canvas interactions
canvas.addEventListener('mousedown', startDrag);
canvas.addEventListener('mousemove', moveDrag);
window.addEventListener('mouseup', endDrag);
canvas.addEventListener('touchstart', startDrag, { passive: false });
canvas.addEventListener('touchmove', moveDrag, { passive: false });
window.addEventListener('touchend', endDrag);

// ---- INIT ----
resizeCanvas();
positionZoneHint();
loadProductMockup();
initColorDots();
updatePanels();

// Expose for debugging
window.mainCanvas = canvas;
window.layers = state.layers;
window.addImage = addImage;
window.addText = addText;
window.addShape = addShape;
window.renderAll = renderAll;
window.exportFull = exportFull;
window.exportPrintOnly = exportPrintOnly;
