// constructor.jsx — рабочий конструктор PrintLab

const { useState, useRef, useEffect, useCallback } = React;

// ====== Главный конструктор ======
function Constructor({ onBack }) {
  const [tab, setTab] = useState('clipart'); // clipart | upload | text | products
  const [productType, setProductType] = useState('tshirt'); // tshirt | hoodie | longsleeve
  const [shirtColorId, setShirtColorId] = useState('white');
  const [view, setView] = useState('front'); // front | back
  const [size, setSize] = useState('M');
  const [qty, setQty] = useState(1);

  // элементы на принте: { id, type:'image'|'text', x, y, w, h, rotate, ... }
  const [elements, setElements] = useState([]);
  const [selected, setSelected] = useState(null);
  const [history, setHistory] = useState([[]]);
  const [historyIdx, setHistoryIdx] = useState(0);

  const shirtColor = SHIRT_COLORS.find(c => c.id === shirtColorId);

  const pushHistory = (newEls) => {
    const next = history.slice(0, historyIdx + 1);
    next.push(newEls);
    setHistory(next);
    setHistoryIdx(next.length - 1);
  };

  const updateElements = (newEls) => {
    setElements(newEls);
    pushHistory(newEls);
  };

  const undo = () => {
    if (historyIdx > 0) {
      setHistoryIdx(historyIdx - 1);
      setElements(history[historyIdx - 1]);
    }
  };
  const redo = () => {
    if (historyIdx < history.length - 1) {
      setHistoryIdx(historyIdx + 1);
      setElements(history[historyIdx + 1]);
    }
  };

  const addPrint = (print) => {
    const newEl = {
      id: 'el_' + Date.now(),
      type: 'image',
      printId: print.id,
      svg: print.svg,
      x: 25, y: 25, w: 50, h: 50, // в процентах от print-area
      rotate: 0,
    };
    updateElements([...elements, newEl]);
    setSelected(newEl.id);
  };

  const addText = (text) => {
    const newEl = {
      id: 'el_' + Date.now(),
      type: 'text',
      text: text || 'ВАШ ТЕКСТ',
      x: 15, y: 35, w: 70, h: 18,
      rotate: 0,
      fontFamily: FONTS[0].id,
      color: '#000000',
      bold: true,
    };
    updateElements([...elements, newEl]);
    setSelected(newEl.id);
  };

  const updateElement = (id, patch) => {
    const next = elements.map(el => el.id === id ? { ...el, ...patch } : el);
    setElements(next);
  };
  const commitElementChange = (id, patch) => {
    const next = elements.map(el => el.id === id ? { ...el, ...patch } : el);
    updateElements(next);
  };
  const deleteElement = (id) => {
    updateElements(elements.filter(el => el.id !== id));
    setSelected(null);
  };

  const onUpload = (file) => {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      const newEl = {
        id: 'el_' + Date.now(),
        type: 'image',
        imgSrc: e.target.result,
        x: 20, y: 20, w: 60, h: 60,
        rotate: 0,
      };
      updateElements([...elements, newEl]);
      setSelected(newEl.id);
      setTab('clipart');
    };
    reader.readAsDataURL(file);
  };

  // цена
  const basePrice = productType === 'tshirt' ? 690 : productType === 'hoodie' ? 2390 : 1190;
  const printSurcharge = elements.length > 0 ? 200 : 0;
  const price = basePrice + printSurcharge;
  const priceOld = Math.round(price * 1.4);

  return (
    <div className="pl-c-page" data-screen-label="Конструктор">
      <ConstructorHeader onBack={onBack}/>
      <div className="pl-constructor">
        {/* LEFT: tools */}
        <div className="pl-c-toolbar">
          <div className="pl-c-tabs">
            <button className={`pl-c-tab ${tab==='clipart'?'active':''}`} onClick={() => setTab('clipart')}>
              {Icon.clipart} Принты
            </button>
            <button className={`pl-c-tab ${tab==='upload'?'active':''}`} onClick={() => setTab('upload')}>
              {Icon.upload} Фото
            </button>
            <button className={`pl-c-tab ${tab==='text'?'active':''}`} onClick={() => setTab('text')}>
              {Icon.text} Текст
            </button>
            <button className={`pl-c-tab ${tab==='shapes'?'active':''}`} onClick={() => setTab('shapes')}>
              {Icon.shapes} Фигуры
            </button>
          </div>
          <div className="pl-c-panel">
            {tab === 'clipart' && <ClipartPanel onAdd={addPrint}/>}
            {tab === 'upload' && <UploadPanel onUpload={onUpload}/>}
            {tab === 'text' && <TextPanel
              onAddText={addText}
              selected={elements.find(e => e.id === selected && e.type === 'text')}
              onUpdate={(p) => commitElementChange(selected, p)}
            />}
            {tab === 'shapes' && <ShapesPanel onAdd={addPrint}/>}
          </div>
        </div>

        {/* CENTER: stage */}
        <div className="pl-c-stage">
          <div className="pl-c-stage-top">
            <div className="pl-c-views">
              <button className={`pl-c-view ${view==='front'?'active':''}`} onClick={() => setView('front')}>Перёд</button>
              <button className={`pl-c-view ${view==='back'?'active':''}`} onClick={() => setView('back')}>Спина</button>
            </div>
            <div className="pl-c-stage-actions">
              <button className="pl-c-iconbtn" onClick={undo} disabled={historyIdx===0} title="Отменить">{Icon.undo}</button>
              <button className="pl-c-iconbtn" onClick={redo} disabled={historyIdx===history.length-1} title="Повторить">{Icon.redo}</button>
              <button className="pl-c-iconbtn" title="Очистить" onClick={() => { updateElements([]); setSelected(null); }}>×</button>
            </div>
          </div>
          <Canvas
            productType={productType}
            shirtColor={shirtColor}
            view={view}
            elements={elements}
            selected={selected}
            setSelected={setSelected}
            updateElement={updateElement}
            commitElementChange={commitElementChange}
            deleteElement={deleteElement}
          />
          <ColorBar
            colors={SHIRT_COLORS}
            selected={shirtColorId}
            onChange={setShirtColorId}
          />
        </div>

        {/* RIGHT: product config */}
        <div className="pl-c-side">
          <div className="pl-c-side-head">
            <h3>Твоя {productType === 'tshirt' ? 'футболка' : productType === 'hoodie' ? 'худи' : 'лонгслив'}</h3>
            <div className="pl-c-side-sub">Готова к печати · 1-2 дня</div>
          </div>
          <div className="pl-c-side-body">
            <div className="pl-c-section-title">Тип изделия</div>
            <div className="pl-c-product-types">
              {[
                { id: 'tshirt', name: 'Футболка', from: 690 },
                { id: 'hoodie', name: 'Худи', from: 2390 },
                { id: 'longsleeve', name: 'Лонгслив', from: 1190 },
              ].map(t => (
                <button key={t.id} className={`pl-c-product-type ${productType===t.id?'active':''}`} onClick={() => setProductType(t.id)}>
                  <div style={{ width: 28, height: 32 }}>
                    {t.id === 'hoodie'
                      ? <HoodieSVG color="#ddd" style={{ width: '100%', height: '100%' }}/>
                      : <ShirtSVG color="#ddd" style={{ width: '100%', height: '100%' }}/>}
                  </div>
                  {t.name}
                  <span style={{ fontSize: 10, color: '#999', fontWeight: 500 }}>от {t.from} ₽</span>
                </button>
              ))}
            </div>

            <div className="pl-c-section-title">Размер</div>
            <div className="pl-c-sizes">
              {['XS','S','M','L','XL','XXL'].map(s => (
                <button key={s} className={`pl-c-size ${size===s?'active':''}`} onClick={() => setSize(s)}>{s}</button>
              ))}
            </div>

            <div className="pl-c-section-title">Количество</div>
            <div className="pl-c-qty">
              <button onClick={() => setQty(Math.max(1, qty-1))}>−</button>
              <span>{qty}</span>
              <button onClick={() => setQty(qty+1)}>+</button>
            </div>

            <div className="pl-c-section-title">Состав</div>
            <div className="pl-c-meta" style={{ flexDirection: 'column', alignItems: 'flex-start', gap: 6, marginBottom: 16 }}>
              <div>· 100% хлопок, плотность 180 г/м²</div>
              <div>· Прямой крой, унисекс</div>
              <div>· Цветостойкая печать DTG</div>
            </div>

            <div className="pl-c-section-title">Доставка</div>
            <div className="pl-c-meta">
              <span className="pl-c-meta-item">{Icon.truck} 1-3 дня по РФ</span>
              <span className="pl-c-meta-item">{Icon.shield} 14 дней на возврат</span>
            </div>
          </div>
          <div className="pl-c-side-foot">
            <div className="pl-c-price-row">
              <div>
                <span className="pl-c-price">{(price * qty).toLocaleString('ru')} ₽</span>
                <span className="pl-c-price-old" style={{ marginLeft: 8 }}>{(priceOld * qty).toLocaleString('ru')} ₽</span>
              </div>
              <span style={{ background: '#e30613', color: 'white', fontSize: 11, fontWeight: 800, padding: '3px 7px', borderRadius: 3 }}>
                −{Math.round((1 - price/priceOld) * 100)}%
              </span>
            </div>
            <button className="pl-c-buy">Купить в 1 клик</button>
            <button className="pl-c-cart-add">Добавить в корзину</button>
          </div>
        </div>
      </div>
    </div>
  );
}

function ConstructorHeader({ onBack }) {
  return (
    <div style={{ display: 'flex', alignItems: 'center', height: 56, background: 'white', borderBottom: '1px solid #e5e5e5', padding: '0 20px', gap: 14 }}>
      <button onClick={onBack} style={{ background: 'transparent', border: 0, fontSize: 14, fontWeight: 600, color: '#777', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 6 }}>
        ← На главную
      </button>
      <div style={{ width: 1, height: 28, background: '#e5e5e5' }}/>
      <a className="pl-logo" style={{ fontSize: 20 }}>
        <span className="pl-logo-mark" style={{ width: 28, height: 28, fontSize: 16 }}>P</span>
        <span className="pl-logo-text">Print<span>Lab</span></span>
      </a>
      <div style={{ marginLeft: 14, fontSize: 13, fontWeight: 600, color: '#444' }}>Конструктор</div>
      <div style={{ flex: 1 }}/>
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 12, color: '#777' }}>
        <span style={{ width: 8, height: 8, borderRadius: '50%', background: '#1ea54a' }}/>
        Авто-сохранение в облако
      </div>
      <button style={{ background: 'white', border: '1.5px solid #e5e5e5', padding: '8px 14px', borderRadius: 6, fontSize: 13, fontWeight: 700, cursor: 'pointer' }}>Поделиться</button>
    </div>
  );
}

// ====== ПАНЕЛИ ======
function ClipartPanel({ onAdd }) {
  const [tag, setTag] = useState('Все');
  const tags = ['Все', 'Мемы', 'Поп-культура', 'Игры', 'Аниме', 'Музыка', 'Спорт', 'Россия'];
  return (
    <>
      <input className="pl-c-search" placeholder="Найти принт..."/>
      <div className="pl-c-tags">
        {tags.map(t => (
          <button key={t} className={`pl-c-tag ${t===tag?'active':''}`} onClick={() => setTag(t)}>{t}</button>
        ))}
      </div>
      <div className="pl-c-section-title">Популярные · {PRINTS.length}</div>
      <div className="pl-c-clipart-grid">
        {PRINTS.map((p, i) => (
          <div key={i} className="pl-c-clipart" onClick={() => onAdd(p)} title={p.name}>
            <div style={{ width: '78%', height: '78%' }}>{p.svg}</div>
          </div>
        ))}
      </div>
    </>
  );
}

function UploadPanel({ onUpload }) {
  const inputRef = useRef();
  return (
    <>
      <div className="pl-c-upload" onClick={() => inputRef.current?.click()}>
        {Icon.upload}
        <div className="pl-c-upload-title">Перетащи или загрузи фото</div>
        <div className="pl-c-upload-hint">PNG, JPG, SVG · до 20 МБ</div>
        <input ref={inputRef} type="file" accept="image/*" style={{ display: 'none' }}
          onChange={(e) => onUpload(e.target.files[0])}/>
      </div>
      <div style={{ marginTop: 16, fontSize: 12, color: '#777', lineHeight: 1.5 }}>
        💡 Лучшее качество печати: разрешение 300 dpi и выше. Прозрачный фон — PNG.
      </div>
      <div className="pl-c-section-title" style={{ marginTop: 22 }}>Мои загрузки</div>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 6 }}>
        {[1,2,3].map(i => (
          <div key={i} style={{ aspectRatio: 1, background: '#f4f4f4', borderRadius: 6, display: 'grid', placeItems: 'center', color: '#bbb', fontSize: 22 }}>+</div>
        ))}
      </div>
    </>
  );
}

function TextPanel({ onAddText, selected, onUpdate }) {
  const [draft, setDraft] = useState('');
  return (
    <>
      <div className="pl-c-section-title">{selected ? 'Редактировать текст' : 'Добавить текст'}</div>
      <textarea className="pl-c-textarea"
        placeholder="Например: «BEST DAD EVER» или «МЕМ»"
        value={selected ? selected.text : draft}
        onChange={(e) => selected ? onUpdate({ text: e.target.value }) : setDraft(e.target.value)}/>
      {!selected && (
        <button onClick={() => { onAddText(draft || 'ВАШ ТЕКСТ'); setDraft(''); }}
          style={{ marginTop: 10, width: '100%', background: '#111', color: 'white', border: 0, padding: 11, borderRadius: 6, fontWeight: 700, cursor: 'pointer', fontSize: 13 }}>
          Добавить на футболку
        </button>
      )}
      <div className="pl-c-section-title" style={{ marginTop: 18 }}>Шрифт</div>
      <div className="pl-c-fonts">
        {FONTS.map(f => (
          <button key={f.id} className={`pl-c-font ${selected?.fontFamily === f.id ? 'active' : ''}`}
            style={{ fontFamily: f.css, fontWeight: f.weight }}
            onClick={() => selected && onUpdate({ fontFamily: f.id })}>
            Аа
          </button>
        ))}
      </div>
      {selected && (
        <>
          <div className="pl-c-section-title" style={{ marginTop: 18 }}>Цвет</div>
          <div className="pl-c-color-grid">
            {['#000000','#ffffff','#e30613','#ffd400','#1ea54a','#1f3a8a','#7c4a2a','#e9789e','#ff7a00','#6e3aa7','#444444','#16213e'].map(c => (
              <div key={c} className={`pl-c-color ${selected.color === c ? 'active' : ''}`}
                style={{ background: c }} onClick={() => onUpdate({ color: c })}/>
            ))}
          </div>
        </>
      )}
    </>
  );
}

function ShapesPanel({ onAdd }) {
  const shapes = [
    { id: 'circle', svg: <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#e30613"/></svg> },
    { id: 'square', svg: <svg viewBox="0 0 100 100"><rect x="15" y="15" width="70" height="70" fill="#111"/></svg> },
    { id: 'triangle', svg: <svg viewBox="0 0 100 100"><polygon points="50,15 85,80 15,80" fill="#ffd400"/></svg> },
    { id: 'star', svg: <svg viewBox="0 0 100 100"><polygon points="50,10 61,40 92,40 67,58 76,90 50,72 24,90 33,58 8,40 39,40" fill="#1ea54a"/></svg> },
    { id: 'heart', svg: <svg viewBox="0 0 100 100"><path d="M50 85 C20 60 10 40 25 25 C40 15 50 30 50 30 C50 30 60 15 75 25 C90 40 80 60 50 85 Z" fill="#e9789e"/></svg> },
    { id: 'lightning', svg: <svg viewBox="0 0 100 100"><polygon points="55,10 25,55 45,55 35,90 75,40 55,40" fill="#ff7a00"/></svg> },
  ];
  return (
    <>
      <div className="pl-c-section-title">Базовые фигуры</div>
      <div className="pl-c-clipart-grid">
        {shapes.map(s => (
          <div key={s.id} className="pl-c-clipart" onClick={() => onAdd({ id: s.id, svg: s.svg })}>
            <div style={{ width: '70%', height: '70%' }}>{s.svg}</div>
          </div>
        ))}
      </div>
    </>
  );
}

// ====== CANVAS ======
function Canvas({ productType, shirtColor, view, elements, selected, setSelected, updateElement, commitElementChange, deleteElement }) {
  const printAreaRef = useRef();

  return (
    <div className="pl-c-canvas-wrap" onMouseDown={(e) => {
      if (e.target.closest('.pl-c-element')) return;
      setSelected(null);
    }}>
      <div className="pl-c-canvas">
        <div className="pl-c-shirt">
          {productType === 'hoodie'
            ? <HoodieSVG color={shirtColor.hex} style={{ width: '100%', height: '100%' }}/>
            : <ShirtSVG color={shirtColor.hex} style={{ width: '100%', height: '100%' }}/>}
        </div>
        <div ref={printAreaRef} className={`pl-c-print-area ${selected ? 'hide-border' : ''}`}>
          {elements.map(el => (
            <CanvasElement
              key={el.id}
              el={el}
              isSelected={selected === el.id}
              onSelect={() => setSelected(el.id)}
              onUpdate={(p) => updateElement(el.id, p)}
              onCommit={(p) => commitElementChange(el.id, p)}
              onDelete={() => deleteElement(el.id)}
              container={printAreaRef.current}
            />
          ))}
          {elements.length === 0 && (
            <div style={{ position: 'absolute', inset: 0, display: 'grid', placeItems: 'center', color: 'rgba(0,0,0,0.3)', fontSize: 13, fontWeight: 600, pointerEvents: 'none' }}>
              ЗОНА ПЕЧАТИ · перетащи сюда дизайн
            </div>
          )}
        </div>
        {/* Угловой бейдж */}
        <div style={{ position: 'absolute', bottom: -34, left: '50%', transform: 'translateX(-50%)', fontSize: 11, color: '#999', fontWeight: 600, whiteSpace: 'nowrap' }}>
          {view === 'front' ? 'Вид спереди' : 'Вид сзади'} · зона печати 30×40 см
        </div>
      </div>
    </div>
  );
}

function CanvasElement({ el, isSelected, onSelect, onUpdate, onCommit, onDelete, container }) {
  const [drag, setDrag] = useState(null); // { type: 'move'|'resize', startX, startY, origEl }

  useEffect(() => {
    if (!drag) return;
    const move = (e) => {
      if (!container) return;
      const rect = container.getBoundingClientRect();
      const dx = ((e.clientX - drag.startX) / rect.width) * 100;
      const dy = ((e.clientY - drag.startY) / rect.height) * 100;
      if (drag.type === 'move') {
        onUpdate({
          x: Math.max(-10, Math.min(110 - drag.origEl.w, drag.origEl.x + dx)),
          y: Math.max(-10, Math.min(110 - drag.origEl.h, drag.origEl.y + dy)),
        });
      } else if (drag.type === 'resize') {
        const dir = drag.dir;
        let { x, y, w, h } = drag.origEl;
        if (dir.includes('r')) w = Math.max(8, drag.origEl.w + dx);
        if (dir.includes('l')) { w = Math.max(8, drag.origEl.w - dx); x = drag.origEl.x + (drag.origEl.w - w); }
        if (dir.includes('b')) h = Math.max(8, drag.origEl.h + dy);
        if (dir.includes('t')) { h = Math.max(8, drag.origEl.h - dy); y = drag.origEl.y + (drag.origEl.h - h); }
        // proportional for image
        if (el.type === 'image') {
          const ratio = drag.origEl.w / drag.origEl.h;
          if (Math.abs(dx) > Math.abs(dy)) { h = w / ratio; if (dir.includes('t')) y = drag.origEl.y + (drag.origEl.h - h); }
          else { w = h * ratio; if (dir.includes('l')) x = drag.origEl.x + (drag.origEl.w - w); }
        }
        onUpdate({ x, y, w, h });
      }
    };
    const up = () => {
      if (drag) onCommit({}); // commit history snapshot
      setDrag(null);
    };
    window.addEventListener('mousemove', move);
    window.addEventListener('mouseup', up);
    return () => {
      window.removeEventListener('mousemove', move);
      window.removeEventListener('mouseup', up);
    };
  }, [drag]);

  const startDrag = (e, type, dir) => {
    e.stopPropagation();
    e.preventDefault();
    onSelect();
    setDrag({ type, dir, startX: e.clientX, startY: e.clientY, origEl: { ...el } });
  };

  const style = {
    left: `${el.x}%`,
    top: `${el.y}%`,
    width: `${el.w}%`,
    height: `${el.h}%`,
    transform: el.rotate ? `rotate(${el.rotate}deg)` : undefined,
  };

  return (
    <div className={`pl-c-element ${isSelected ? 'selected' : ''}`} style={style}
      onMouseDown={(e) => startDrag(e, 'move')} onClick={(e) => { e.stopPropagation(); onSelect(); }}>
      {el.type === 'image' && el.svg && (
        <div style={{ width: '100%', height: '100%', pointerEvents: 'none' }}>{el.svg}</div>
      )}
      {el.type === 'image' && el.imgSrc && (
        <img src={el.imgSrc} style={{ width: '100%', height: '100%', objectFit: 'contain', pointerEvents: 'none' }}/>
      )}
      {el.type === 'text' && (
        <div style={{
          width: '100%', height: '100%',
          display: 'grid', placeItems: 'center',
          fontFamily: FONTS.find(f => f.id === el.fontFamily)?.css || 'sans-serif',
          fontWeight: el.bold ? 800 : 500,
          color: el.color,
          fontSize: `${el.h * 0.6}px`,
          textAlign: 'center',
          lineHeight: 1,
          pointerEvents: 'none',
          whiteSpace: 'pre-wrap',
          overflow: 'hidden',
          letterSpacing: '0.01em',
        }}>{el.text}</div>
      )}
      {isSelected && (
        <>
          <div className="pl-c-element-handle tl" onMouseDown={(e) => startDrag(e, 'resize', 'tl')}/>
          <div className="pl-c-element-handle tr" onMouseDown={(e) => startDrag(e, 'resize', 'tr')}/>
          <div className="pl-c-element-handle bl" onMouseDown={(e) => startDrag(e, 'resize', 'bl')}/>
          <div className="pl-c-element-handle br" onMouseDown={(e) => startDrag(e, 'resize', 'br')}/>
          <button className="pl-c-element-handle del" onClick={(e) => { e.stopPropagation(); onDelete(); }}>×</button>
        </>
      )}
    </div>
  );
}

function ColorBar({ colors, selected, onChange }) {
  return (
    <div style={{ background: 'white', borderTop: '1px solid #e5e5e5', padding: '14px 20px', display: 'flex', alignItems: 'center', gap: 14 }}>
      <div style={{ fontSize: 12, fontWeight: 700, color: '#777', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Цвет:</div>
      <div style={{ display: 'flex', gap: 8, flex: 1 }}>
        {colors.map(c => (
          <button key={c.id} title={c.name} onClick={() => onChange(c.id)}
            style={{
              width: 30, height: 30, borderRadius: '50%',
              background: c.hex,
              border: selected === c.id ? '2px solid #e30613' : c.isLight ? '1.5px solid #d0d0d0' : '1.5px solid transparent',
              boxShadow: selected === c.id ? '0 0 0 2px white inset' : 'none',
              cursor: 'pointer', padding: 0,
            }}/>
        ))}
      </div>
      <div style={{ fontSize: 12, color: '#777' }}>
        {colors.find(c => c.id === selected)?.name}
      </div>
    </div>
  );
}

window.Constructor = Constructor;
