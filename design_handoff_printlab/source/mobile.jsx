// mobile.jsx — мобильная версия PrintLab

const { useState: useStateM, useRef: useRefM, useEffect: useEffectM } = React;

// ====== ОБЩИЕ МОБИЛЬНЫЕ ИКОНКИ ======
const MIcon = {
  back: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="m15 18-6-6 6-6"/></svg>,
  close: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>,
  burger: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>,
  search: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>,
  cart: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>,
  heart: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"/></svg>,
  user: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
  home: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="m3 11 9-8 9 8v10a2 2 0 0 1-2 2h-4v-7H10v7H6a2 2 0 0 1-2-2V11Z"/></svg>,
  catalog: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>,
  plus: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M12 5v14M5 12h14"/></svg>,
  filter: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 6h18M6 12h12M10 18h4"/></svg>,
};

// ====== СТИЛИ ВНУТРИ КОМПОНЕНТА ======
const M_CSS = `
.m-app { width: 100%; height: 100%; background: #f4f4f4; overflow: hidden; display: flex; flex-direction: column; font-family: 'Manrope', sans-serif; }
.m-scroll { flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding-bottom: 80px; }

/* ====== HEADER ====== */
.m-header {
  background: white;
  padding: 8px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid #eee;
  flex-shrink: 0;
}
.m-iconbtn {
  width: 36px; height: 36px;
  display: grid; place-items: center;
  background: transparent; border: 0;
  color: #111; cursor: pointer;
  position: relative;
  flex-shrink: 0;
}
.m-iconbtn svg { width: 22px; height: 22px; }
.m-search {
  flex: 1;
  height: 36px;
  background: #f4f4f4;
  border-radius: 8px;
  display: flex;
  align-items: center;
  padding: 0 10px;
  gap: 6px;
  color: #888;
  font-size: 13px;
}
.m-cart-badge {
  position: absolute; top: 4px; right: 2px;
  background: #e30613; color: white;
  font-size: 9px; font-weight: 800;
  border-radius: 999px;
  min-width: 14px; height: 14px;
  display: grid; place-items: center;
  padding: 0 3px;
}
.m-logo {
  display: flex; align-items: center; gap: 6px;
  font-weight: 900; font-size: 18px;
}
.m-logo-mark {
  width: 26px; height: 26px;
  background: #e30613; color: white;
  border-radius: 5px;
  display: grid; place-items: center;
  font-size: 14px; font-weight: 900;
  transform: rotate(-4deg);
}
.m-logo-text span { color: #e30613; }

/* ====== HERO ====== */
.m-hero {
  margin: 12px 14px 0;
  border-radius: 12px;
  background: linear-gradient(120deg, #ffeb3b 0%, #ffd400 60%, #ffb703 100%);
  padding: 18px 16px;
  position: relative;
  overflow: hidden;
  min-height: 180px;
}
.m-hero-badge {
  display: inline-block;
  background: #e30613; color: white;
  font-size: 10px; font-weight: 800;
  padding: 4px 8px;
  border-radius: 3px;
  margin-bottom: 10px;
  letter-spacing: 0.04em;
}
.m-hero h1 {
  font-size: 24px; font-weight: 900;
  margin: 0 0 8px;
  line-height: 1.05; letter-spacing: -0.02em;
  max-width: 65%;
  color: #111;
}
.m-hero h1 em {
  background: #e30613; color: white;
  font-style: normal; padding: 0 5px;
  display: inline-block; transform: rotate(-2deg);
}
.m-hero p { margin: 0 0 12px; font-size: 12px; max-width: 65%; color: #1a1a1a; }
.m-hero-btn {
  background: #111; color: white;
  border: 0; padding: 10px 16px;
  border-radius: 6px;
  font-size: 12px; font-weight: 800;
  text-transform: uppercase;
  cursor: pointer;
}
.m-hero-img {
  position: absolute;
  right: -10px; top: 50%;
  transform: translateY(-50%) rotate(8deg);
  width: 130px; height: 150px;
}

/* ====== SECTION ====== */
.m-section { margin-top: 22px; }
.m-section-head {
  padding: 0 14px;
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  margin-bottom: 10px;
}
.m-section-head h2 { margin: 0; font-size: 18px; font-weight: 900; letter-spacing: -0.01em; }
.m-section-head h2 em { font-style: normal; color: #e30613; }
.m-section-head .m-link { font-size: 12px; color: #777; font-weight: 600; }

/* ====== HORIZONTAL SCROLLERS ====== */
.m-hscroll {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding: 0 14px;
  scroll-snap-type: x proximity;
  -webkit-overflow-scrolling: touch;
}
.m-hscroll::-webkit-scrollbar { display: none; }
.m-hscroll > * { scroll-snap-align: start; flex-shrink: 0; }

/* Categories */
.m-cat-card {
  width: 92px;
  background: white;
  border-radius: 10px;
  padding: 10px 6px;
  text-align: center;
  border: 1px solid #eee;
}
.m-cat-card-img {
  width: 70px; height: 70px;
  margin: 0 auto;
  display: grid; place-items: center;
}
.m-cat-card-name { font-size: 11px; font-weight: 700; }
.m-cat-card-from { font-size: 10px; color: #888; margin-top: 2px; }

/* Product card */
.m-card {
  width: 156px;
  background: white;
  border-radius: 10px;
  border: 1px solid #eee;
  overflow: hidden;
  position: relative;
}
.m-card-img {
  background: #f4f4f4;
  aspect-ratio: 1;
  position: relative;
}
.m-card-tag {
  position: absolute; top: 6px; left: 6px;
  font-size: 9px; font-weight: 800;
  background: #e30613; color: white;
  padding: 2px 6px; border-radius: 3px;
  text-transform: uppercase;
}
.m-card-fav {
  position: absolute; top: 6px; right: 6px;
  width: 26px; height: 26px;
  border-radius: 50%; background: white;
  border: 0; display: grid; place-items: center;
  color: #aaa;
}
.m-card-fav.active { color: #e30613; }
.m-card-body { padding: 8px 10px 10px; }
.m-card-name {
  font-size: 11px; font-weight: 600;
  line-height: 1.2;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 27px;
  margin-bottom: 4px;
}
.m-card-price-row { display: flex; align-items: baseline; gap: 6px; }
.m-card-price { font-size: 15px; font-weight: 900; color: #e30613; }
.m-card-price-old { font-size: 11px; color: #999; text-decoration: line-through; }

/* Prints */
.m-prints-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 6px;
  padding: 0 14px;
}
.m-print {
  aspect-ratio: 1;
  background: white;
  border-radius: 8px;
  border: 1px solid #eee;
  display: grid; place-items: center;
  overflow: hidden;
}
.m-print > div { width: 70%; height: 70%; }

/* ====== TABBAR ====== */
.m-tabbar {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  background: white;
  border-top: 1px solid #eee;
  height: 64px;
  display: flex;
  padding-bottom: 4px;
  z-index: 10;
}
.m-tab {
  flex: 1;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 2px;
  background: transparent; border: 0;
  font-size: 10px; color: #999; font-weight: 600;
  cursor: pointer;
  padding-top: 8px;
}
.m-tab svg { width: 22px; height: 22px; }
.m-tab.active { color: #e30613; }
.m-tab.fab {
  position: relative;
}
.m-tab.fab .m-fab-btn {
  width: 48px; height: 48px;
  background: #e30613; color: white;
  border-radius: 50%;
  display: grid; place-items: center;
  margin-top: -18px;
  box-shadow: 0 6px 16px rgba(227,6,19,0.4);
}
.m-tab.fab .m-fab-btn svg { width: 26px; height: 26px; }

/* ====== CONSTRUCTOR MOBILE ====== */
.m-c {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #f4f4f4;
  overflow: hidden;
  position: relative;
}
.m-c-header {
  padding: 8px 10px;
  background: white;
  display: flex;
  align-items: center;
  gap: 6px;
  border-bottom: 1px solid #eee;
  flex-shrink: 0;
}
.m-c-header h1 { margin: 0; font-size: 15px; font-weight: 800; }
.m-c-stage {
  flex: 1;
  display: grid;
  place-items: center;
  position: relative;
  overflow: hidden;
  padding: 16px;
}
.m-c-canvas {
  position: relative;
  width: 280px; height: 340px;
  user-select: none;
}
.m-c-views {
  position: absolute;
  top: 14px; left: 50%; transform: translateX(-50%);
  display: flex; gap: 3px;
  background: rgba(255,255,255,0.95);
  border-radius: 999px;
  padding: 3px;
  font-size: 11px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.m-c-view {
  background: transparent; border: 0;
  padding: 5px 12px;
  border-radius: 999px;
  font-weight: 700; color: #777;
  font-size: 11px;
}
.m-c-view.active { background: #111; color: white; }

.m-c-actions {
  position: absolute;
  top: 14px; right: 14px;
  display: flex; flex-direction: column; gap: 6px;
}
.m-c-action {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: white;
  border: 0; color: #777;
  display: grid; place-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.m-c-action svg { width: 16px; height: 16px; }

.m-c-tools {
  background: white;
  border-top: 1px solid #eee;
  flex-shrink: 0;
}
.m-c-tab-bar {
  display: flex;
  border-bottom: 1px solid #eee;
}
.m-c-tab-btn {
  flex: 1;
  background: transparent; border: 0;
  padding: 10px 4px 8px;
  font-size: 10px;
  color: #777;
  font-weight: 600;
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  position: relative;
}
.m-c-tab-btn svg { width: 20px; height: 20px; }
.m-c-tab-btn.active { color: #e30613; }
.m-c-tab-btn.active::after {
  content: '';
  position: absolute;
  bottom: -1px; left: 25%; right: 25%;
  height: 2px; background: #e30613;
}

.m-c-panel {
  padding: 12px 14px;
  height: 180px;
  overflow-y: auto;
}
.m-c-panel h4 {
  margin: 0 0 10px;
  font-size: 11px;
  text-transform: uppercase;
  color: #777;
  letter-spacing: 0.05em;
  font-weight: 800;
}
.m-c-clipart-row {
  display: flex; gap: 8px;
  overflow-x: auto;
  padding-bottom: 8px;
}
.m-c-clipart-row::-webkit-scrollbar { display: none; }
.m-c-clipart-item {
  width: 64px; height: 64px;
  background: #f4f4f4;
  border-radius: 8px;
  border: 1px solid #eee;
  flex-shrink: 0;
  display: grid; place-items: center;
}
.m-c-clipart-item > div { width: 70%; height: 70%; }

.m-c-color-row {
  display: flex; gap: 8px;
  overflow-x: auto;
  padding-bottom: 4px;
}
.m-c-color-row::-webkit-scrollbar { display: none; }
.m-c-color-dot {
  width: 32px; height: 32px;
  border-radius: 50%;
  border: 2px solid #e5e5e5;
  flex-shrink: 0;
  cursor: pointer;
}
.m-c-color-dot.active { border-color: #e30613; box-shadow: 0 0 0 2px white inset; }

.m-c-product-row {
  display: flex; gap: 8px;
}
.m-c-product-pill {
  flex: 1;
  border: 1.5px solid #e5e5e5;
  border-radius: 8px;
  background: white;
  padding: 8px 6px;
  font-size: 10px;
  font-weight: 700;
  display: flex; flex-direction: column; align-items: center; gap: 2px;
}
.m-c-product-pill.active { border-color: #e30613; color: #e30613; background: #fff5f5; }
.m-c-product-pill > div { width: 24px; height: 28px; }

.m-c-foot {
  background: white;
  border-top: 1px solid #eee;
  padding: 10px 14px;
  display: flex; align-items: center; gap: 10px;
  flex-shrink: 0;
}
.m-c-price-block { flex: 1; }
.m-c-price-block .m-c-price { font-size: 18px; font-weight: 900; }
.m-c-price-block .m-c-price-old { font-size: 11px; color: #999; text-decoration: line-through; margin-left: 6px; }
.m-c-price-block .m-c-price-sub { font-size: 10px; color: #888; }
.m-c-buy {
  background: #e30613; color: white;
  border: 0; padding: 12px 18px;
  border-radius: 8px;
  font-weight: 800; font-size: 13px;
  cursor: pointer;
  text-transform: uppercase;
}

.m-c-element {
  position: absolute;
  cursor: move;
  user-select: none;
}
.m-c-element.selected {
  outline: 1.5px solid #e30613;
  outline-offset: 2px;
}
`;

if (typeof document !== 'undefined' && !document.getElementById('m-css')) {
  const s = document.createElement('style');
  s.id = 'm-css';
  s.textContent = M_CSS;
  document.head.appendChild(s);
}

// ====== HOME PAGE (mobile) ======
function MHomepage({ onConstructorClick, onConstructorClickFromTab }) {
  const [tab, setTab] = useStateM('home');
  return (
    <div className="m-app" data-screen-label="Mobile · Главная">
      <div className="m-header">
        <button className="m-iconbtn">{MIcon.burger}</button>
        <div className="m-logo">
          <span className="m-logo-mark">P</span>
          <span className="m-logo-text">Print<span>Lab</span></span>
        </div>
        <div style={{ flex: 1 }}/>
        <button className="m-iconbtn">{MIcon.heart}</button>
        <button className="m-iconbtn">{MIcon.cart}<span className="m-cart-badge">3</span></button>
      </div>

      <div className="m-scroll">
        {/* search */}
        <div style={{ padding: '10px 14px 0' }}>
          <div className="m-search">
            {MIcon.search}
            <span>Найти принт, мем, футболку...</span>
          </div>
        </div>

        {/* HERO */}
        <div className="m-hero">
          <span className="m-hero-badge">−40% ДО ВОСКРЕСЕНЬЯ</span>
          <h1>Свой<br/><em>принт</em><br/>за 2 мин</h1>
          <p>Любая идея на твоей футболке</p>
          <button className="m-hero-btn" onClick={onConstructorClick}>В КОНСТРУКТОР →</button>
          <div className="m-hero-img">
            <ShirtSVG color="#ffffff">
              <g transform="translate(140, 200)">
                <text x="60" y="40" textAnchor="middle" fontFamily="Impact" fontSize="38" fill="#e30613">МОЯ</text>
                <text x="60" y="80" textAnchor="middle" fontFamily="Impact" fontSize="32" fill="#111">ФУТБОЛКА</text>
              </g>
            </ShirtSVG>
          </div>
        </div>

        {/* CATEGORIES */}
        <div className="m-section">
          <div className="m-section-head">
            <h2>Категории</h2>
            <span className="m-link">все →</span>
          </div>
          <div className="m-hscroll">
            {[
              { name: 'Футболки', from: 690, color: '#1f3a8a' },
              { name: 'Худи', from: 2390, color: '#1a1a1a' },
              { name: 'Свитшоты', from: 1890, color: '#7c4a2a' },
              { name: 'Лонгсливы', from: 1190, color: '#1e6b3a' },
              { name: 'Детское', from: 590, color: '#ffd400' },
              { name: 'Парные', from: 1290, color: '#e9789e' },
            ].map((c, i) => (
              <div className="m-cat-card" key={i}>
                <div className="m-cat-card-img">
                  <ShirtSVG color={c.color} style={{ width: '100%', height: '100%' }}/>
                </div>
                <div className="m-cat-card-name">{c.name}</div>
                <div className="m-cat-card-from">от {c.from} ₽</div>
              </div>
            ))}
          </div>
        </div>

        {/* TOP SALES */}
        <div className="m-section">
          <div className="m-section-head">
            <h2>ТОП <em>продаж</em></h2>
            <span className="m-link">все →</span>
          </div>
          <div className="m-hscroll">
            {[
              { name: 'Футболка "Космонавт-вайб"', price: 890, old: 1490, color: '#fff', print: 0, tag: '−40%' },
              { name: 'Худи "NYET" чёрное', price: 2890, old: 3990, color: '#1a1a1a', print: 2, tag: '−28%' },
              { name: 'Футболка "Pixel love"', price: 990, old: null, color: '#ffd400', print: 3, tag: 'NEW' },
              { name: 'Свитшот "Cat-meme"', price: 1990, old: null, color: '#fff', print: 4, tag: 'ХИТ' },
              { name: 'Футболка "РОССИЯ"', price: 790, old: 990, color: '#fff', print: 1, tag: '−20%' },
            ].map((p, i) => (
              <div className="m-card" key={i}>
                <div className="m-card-img">
                  <span className="m-card-tag">{p.tag}</span>
                  <button className="m-card-fav">{MIcon.heart}</button>
                  <ShirtSVG color={p.color} style={{ width: '100%', height: '100%' }}>
                    <g transform="translate(130, 180)">
                      <foreignObject x="0" y="0" width="140" height="140">
                        <div style={{ width: '140px', height: '140px' }}>{PRINTS[p.print].svg}</div>
                      </foreignObject>
                    </g>
                  </ShirtSVG>
                </div>
                <div className="m-card-body">
                  <div className="m-card-name">{p.name}</div>
                  <div className="m-card-price-row">
                    <span className="m-card-price">{p.price} ₽</span>
                    {p.old && <span className="m-card-price-old">{p.old} ₽</span>}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* PROMO BANNER */}
        <div style={{ margin: '22px 14px 0', background: 'linear-gradient(90deg, #111 0%, #2c2c2c 100%)', color: 'white', borderRadius: 12, padding: 14, display: 'flex', alignItems: 'center', gap: 12 }}>
          <div style={{ width: 40, height: 40, background: '#e30613', borderRadius: '50%', display: 'grid', placeItems: 'center', flexShrink: 0 }}>{Icon.shapes}</div>
          <div style={{ flex: 1 }}>
            <div style={{ fontWeight: 800, fontSize: 13 }}>Создай свой принт</div>
            <div style={{ fontSize: 11, opacity: 0.7 }}>5000+ дизайнов, фото, текст</div>
          </div>
          <button onClick={onConstructorClick} style={{ background: '#ffd400', color: '#111', border: 0, padding: '8px 12px', borderRadius: 6, fontSize: 11, fontWeight: 800 }}>ОК</button>
        </div>

        {/* POPULAR PRINTS */}
        <div className="m-section">
          <div className="m-section-head">
            <h2>Популярные <em>принты</em></h2>
            <span className="m-link">5000+ →</span>
          </div>
          <div className="m-prints-grid">
            {PRINTS.slice(0, 8).map((p, i) => (
              <div className="m-print" key={i}>
                <div>{p.svg}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* TABBAR */}
      <div className="m-tabbar">
        <button className={`m-tab ${tab==='home'?'active':''}`} onClick={() => setTab('home')}>
          {MIcon.home}<span>Главная</span>
        </button>
        <button className={`m-tab ${tab==='catalog'?'active':''}`} onClick={() => setTab('catalog')}>
          {MIcon.catalog}<span>Каталог</span>
        </button>
        <button className="m-tab fab" onClick={onConstructorClickFromTab || onConstructorClick}>
          <span className="m-fab-btn">{MIcon.plus}</span>
          <span style={{ marginTop: 2 }}>Создать</span>
        </button>
        <button className={`m-tab ${tab==='fav'?'active':''}`} onClick={() => setTab('fav')}>
          {MIcon.heart}<span>Избранное</span>
        </button>
        <button className={`m-tab ${tab==='profile'?'active':''}`} onClick={() => setTab('profile')}>
          {MIcon.user}<span>Профиль</span>
        </button>
      </div>
    </div>
  );
}

// ====== CONSTRUCTOR MOBILE ======
function MConstructor({ onBack }) {
  const [tab, setTab] = useStateM('clipart');
  const [shirtColorId, setShirtColorId] = useStateM('white');
  const [productType, setProductType] = useStateM('tshirt');
  const [view, setView] = useStateM('front');
  const [elements, setElements] = useStateM([]);
  const [selected, setSelected] = useStateM(null);
  const shirtColor = SHIRT_COLORS.find(c => c.id === shirtColorId);
  const printAreaRef = useRefM();

  const addPrint = (p) => {
    const el = { id: 'el_' + Date.now(), type: 'image', svg: p.svg, x: 25, y: 25, w: 50, h: 50 };
    setElements([...elements, el]);
    setSelected(el.id);
  };
  const addText = (text) => {
    const el = { id: 'el_' + Date.now(), type: 'text', text, x: 15, y: 35, w: 70, h: 20, fontFamily: 'manrope', color: '#000', bold: true };
    setElements([...elements, el]);
    setSelected(el.id);
  };

  const basePrice = productType === 'tshirt' ? 690 : productType === 'hoodie' ? 2390 : 1190;
  const price = basePrice + (elements.length > 0 ? 200 : 0);
  const priceOld = Math.round(price * 1.4);

  return (
    <div className="m-app" data-screen-label="Mobile · Конструктор">
      <div className="m-c-header">
        <button className="m-iconbtn" onClick={onBack}>{MIcon.back}</button>
        <h1>Конструктор</h1>
        <div style={{ flex: 1 }}/>
        <button className="m-iconbtn" style={{ fontSize: 11, fontWeight: 700, color: '#777', width: 'auto', padding: '0 10px' }}>Поделиться</button>
      </div>

      {/* STAGE */}
      <div className="m-c-stage">
        <div className="m-c-views">
          <button className={`m-c-view ${view==='front'?'active':''}`} onClick={() => setView('front')}>Перёд</button>
          <button className={`m-c-view ${view==='back'?'active':''}`} onClick={() => setView('back')}>Спина</button>
        </div>
        <div className="m-c-actions">
          <button className="m-c-action">{Icon.undo}</button>
          <button className="m-c-action">{Icon.redo}</button>
          <button className="m-c-action" onClick={() => { setElements([]); setSelected(null); }}>×</button>
        </div>
        <div className="m-c-canvas">
          {productType === 'hoodie'
            ? <HoodieSVG color={shirtColor.hex} style={{ width: '100%', height: '100%' }}/>
            : <ShirtSVG color={shirtColor.hex} style={{ width: '100%', height: '100%' }}/>}
          <div ref={printAreaRef} style={{ position: 'absolute', left: '27%', top: '22%', width: '46%', height: '50%', border: selected ? 'none' : '1.5px dashed rgba(0,0,0,0.18)', borderRadius: 4 }}>
            {elements.length === 0 && (
              <div style={{ position: 'absolute', inset: 0, display: 'grid', placeItems: 'center', color: 'rgba(0,0,0,0.3)', fontSize: 9, fontWeight: 600, textAlign: 'center', padding: 4, lineHeight: 1.3 }}>
                ЗОНА ПЕЧАТИ<br/>30×40 см
              </div>
            )}
            {elements.map(el => (
              <MCanvasEl key={el.id} el={el} isSelected={selected === el.id}
                onSelect={() => setSelected(el.id)}
                onUpdate={(p) => setElements(elements.map(e => e.id === el.id ? { ...e, ...p } : e))}
                onDelete={() => { setElements(elements.filter(e => e.id !== el.id)); setSelected(null); }}
                container={printAreaRef.current}/>
            ))}
          </div>
        </div>
      </div>

      {/* TOOLS */}
      <div className="m-c-tools">
        <div className="m-c-tab-bar">
          <button className={`m-c-tab-btn ${tab==='clipart'?'active':''}`} onClick={() => setTab('clipart')}>{Icon.clipart}<span>Принты</span></button>
          <button className={`m-c-tab-btn ${tab==='text'?'active':''}`} onClick={() => setTab('text')}>{Icon.text}<span>Текст</span></button>
          <button className={`m-c-tab-btn ${tab==='upload'?'active':''}`} onClick={() => setTab('upload')}>{Icon.upload}<span>Фото</span></button>
          <button className={`m-c-tab-btn ${tab==='color'?'active':''}`} onClick={() => setTab('color')}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>
            <span>Цвет</span>
          </button>
          <button className={`m-c-tab-btn ${tab==='product'?'active':''}`} onClick={() => setTab('product')}>{Icon.shapes}<span>Тип</span></button>
        </div>
        <div className="m-c-panel">
          {tab === 'clipart' && (
            <>
              <h4>Готовые принты · {PRINTS.length}</h4>
              <div className="m-c-clipart-row">
                {PRINTS.map((p, i) => (
                  <div className="m-c-clipart-item" key={i} onClick={() => addPrint(p)}>
                    <div>{p.svg}</div>
                  </div>
                ))}
              </div>
            </>
          )}
          {tab === 'text' && (
            <>
              <h4>Добавить текст</h4>
              <input placeholder="Например: МЕМ"
                onKeyDown={(e) => { if (e.key === 'Enter' && e.target.value) { addText(e.target.value); e.target.value = ''; } }}
                style={{ width: '100%', height: 40, border: '1px solid #ddd', borderRadius: 8, padding: '0 12px', fontSize: 14, marginBottom: 10, outline: 'none' }}/>
              <div style={{ display: 'flex', gap: 6, overflowX: 'auto' }}>
                {FONTS.map(f => (
                  <button key={f.id} onClick={() => selected && setElements(elements.map(e => e.id === selected ? { ...e, fontFamily: f.id } : e))}
                    style={{ flexShrink: 0, padding: '8px 14px', border: '1.5px solid #e5e5e5', borderRadius: 8, background: 'white', fontFamily: f.css, fontWeight: f.weight, fontSize: 14, cursor: 'pointer' }}>
                    Аа
                  </button>
                ))}
              </div>
            </>
          )}
          {tab === 'upload' && (
            <>
              <h4>Загрузить фото</h4>
              <div style={{ border: '2px dashed #d0d0d0', borderRadius: 10, padding: '24px 16px', textAlign: 'center' }}>
                <div style={{ width: 36, height: 36, margin: '0 auto 6px', color: '#999' }}>{Icon.upload}</div>
                <div style={{ fontSize: 13, fontWeight: 700 }}>Загрузи фото</div>
                <div style={{ fontSize: 11, color: '#888', marginTop: 2 }}>PNG, JPG · до 20 МБ</div>
              </div>
            </>
          )}
          {tab === 'color' && (
            <>
              <h4>Цвет {productType === 'hoodie' ? 'худи' : 'футболки'}</h4>
              <div className="m-c-color-row">
                {SHIRT_COLORS.map(c => (
                  <div key={c.id} title={c.name} onClick={() => setShirtColorId(c.id)}
                    className={`m-c-color-dot ${shirtColorId === c.id ? 'active' : ''}`}
                    style={{ background: c.hex, borderColor: shirtColorId === c.id ? '#e30613' : (c.isLight ? '#d0d0d0' : '#222') }}/>
                ))}
              </div>
              <div style={{ marginTop: 8, fontSize: 11, color: '#888' }}>
                {SHIRT_COLORS.find(c => c.id === shirtColorId)?.name}
              </div>
            </>
          )}
          {tab === 'product' && (
            <>
              <h4>Тип изделия</h4>
              <div className="m-c-product-row">
                {[
                  { id: 'tshirt', name: 'Футболка', from: 690 },
                  { id: 'hoodie', name: 'Худи', from: 2390 },
                  { id: 'longsleeve', name: 'Лонгслив', from: 1190 },
                ].map(t => (
                  <button key={t.id} className={`m-c-product-pill ${productType === t.id ? 'active' : ''}`} onClick={() => setProductType(t.id)}>
                    <div>{t.id === 'hoodie' ? <HoodieSVG color="#ddd" style={{ width: '100%', height: '100%' }}/> : <ShirtSVG color="#ddd" style={{ width: '100%', height: '100%' }}/>}</div>
                    {t.name}
                    <span style={{ fontSize: 9, color: '#999', fontWeight: 500 }}>от {t.from} ₽</span>
                  </button>
                ))}
              </div>
              <h4 style={{ marginTop: 14 }}>Размер</h4>
              <div style={{ display: 'flex', gap: 4 }}>
                {['XS','S','M','L','XL','XXL'].map(s => (
                  <button key={s} style={{ flex: 1, padding: '8px 0', border: '1.5px solid #e5e5e5', borderRadius: 6, background: s==='M' ? '#111' : 'white', color: s==='M' ? 'white' : '#111', fontSize: 11, fontWeight: 700, cursor: 'pointer' }}>{s}</button>
                ))}
              </div>
            </>
          )}
        </div>
      </div>

      {/* FOOT */}
      <div className="m-c-foot">
        <div className="m-c-price-block">
          <div>
            <span className="m-c-price">{price.toLocaleString('ru')} ₽</span>
            <span className="m-c-price-old">{priceOld.toLocaleString('ru')} ₽</span>
          </div>
          <div className="m-c-price-sub">Доставка 1-3 дня · M, белый</div>
        </div>
        <button className="m-c-buy">В корзину</button>
      </div>
    </div>
  );
}

function MCanvasEl({ el, isSelected, onSelect, onUpdate, onDelete, container }) {
  const [drag, setDrag] = useStateM(null);
  useEffectM(() => {
    if (!drag) return;
    const move = (e) => {
      const touch = e.touches?.[0] || e;
      if (!container) return;
      const rect = container.getBoundingClientRect();
      const dx = ((touch.clientX - drag.startX) / rect.width) * 100;
      const dy = ((touch.clientY - drag.startY) / rect.height) * 100;
      onUpdate({
        x: Math.max(-10, Math.min(110 - drag.origEl.w, drag.origEl.x + dx)),
        y: Math.max(-10, Math.min(110 - drag.origEl.h, drag.origEl.y + dy)),
      });
    };
    const up = () => setDrag(null);
    window.addEventListener('mousemove', move);
    window.addEventListener('mouseup', up);
    window.addEventListener('touchmove', move);
    window.addEventListener('touchend', up);
    return () => {
      window.removeEventListener('mousemove', move);
      window.removeEventListener('mouseup', up);
      window.removeEventListener('touchmove', move);
      window.removeEventListener('touchend', up);
    };
  }, [drag]);

  const start = (e) => {
    e.stopPropagation();
    e.preventDefault();
    onSelect();
    const t = e.touches?.[0] || e;
    setDrag({ startX: t.clientX, startY: t.clientY, origEl: { ...el } });
  };

  return (
    <div className={`m-c-element ${isSelected ? 'selected' : ''}`} style={{
      left: `${el.x}%`, top: `${el.y}%`, width: `${el.w}%`, height: `${el.h}%`,
    }} onMouseDown={start} onTouchStart={start}>
      {el.type === 'image' && <div style={{ width: '100%', height: '100%', pointerEvents: 'none' }}>{el.svg}</div>}
      {el.type === 'text' && (
        <div style={{
          width: '100%', height: '100%',
          display: 'grid', placeItems: 'center',
          fontFamily: FONTS.find(f => f.id === el.fontFamily)?.css || 'sans-serif',
          fontWeight: el.bold ? 800 : 500,
          color: el.color, fontSize: `${el.h * 0.5}px`,
          textAlign: 'center', lineHeight: 1, pointerEvents: 'none',
          whiteSpace: 'pre-wrap', overflow: 'hidden',
        }}>{el.text}</div>
      )}
      {isSelected && (
        <button onClick={(e) => { e.stopPropagation(); onDelete(); }}
          style={{ position: 'absolute', top: -28, right: -8, width: 22, height: 22, borderRadius: '50%', background: '#e30613', color: 'white', border: 0, fontSize: 14, fontWeight: 800, cursor: 'pointer' }}>×</button>
      )}
    </div>
  );
}

// Wrapper that holds nav state inside a single artboard
function MobileApp({ initial = 'home' }) {
  const [view, setView] = useStateM(initial);
  if (view === 'constructor') return <MConstructor onBack={() => setView('home')}/>;
  return <MHomepage onConstructorClick={() => setView('constructor')} onConstructorClickFromTab={() => setView('constructor')}/>;
}

Object.assign(window, { MHomepage, MConstructor, MobileApp });
