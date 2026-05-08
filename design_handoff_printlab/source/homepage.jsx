// homepage.jsx — главная страница PrintLab

function TopBar() {
  return (
    <div className="pl-topbar">
      <div className="pl-container" style={{ display: 'flex', alignItems: 'center', width: '100%' }}>
        <span className="pl-city">📍 Москва</span>
        <span style={{ width: 24 }}></span>
        <a href="#">Доставка</a>
        <a href="#">Оплата</a>
        <a href="#">Гарантия</a>
        <a href="#">Помощь</a>
        <a href="#">Партнёрам</a>
        <span className="pl-spacer"></span>
        <span className="pl-phone">8 800 555-35-35</span>
        <a href="#">Войти</a>
      </div>
    </div>
  );
}

function Header({ onConstructorClick, cartCount = 3 }) {
  return (
    <header className="pl-header">
      <div className="pl-container pl-header-row">
        <a href="#" className="pl-logo">
          <span className="pl-logo-mark">P</span>
          <span className="pl-logo-text">Print<span>Lab</span></span>
        </a>
        <div className="pl-search">
          <input placeholder="Найти принт, футболку, мем..." />
          <button>Найти</button>
        </div>
        <div className="pl-header-actions">
          <div className="pl-header-action">
            {Icon.user}
            <span>Профиль</span>
          </div>
          <div className="pl-header-action">
            {Icon.heart}
            <span>Избранное</span>
            <span className="pl-badge">7</span>
          </div>
          <div className="pl-header-action">
            {Icon.cart}
            <span>Корзина</span>
            <span className="pl-badge">{cartCount}</span>
          </div>
        </div>
      </div>
    </header>
  );
}

function Nav({ onConstructorClick }) {
  const items = [
    { name: 'Конструктор', tag: 'HIT', onClick: onConstructorClick },
    { name: 'Футболки' },
    { name: 'Худи' },
    { name: 'Свитшоты' },
    { name: 'Лонгсливы' },
    { name: 'Мемы', tag: 'NEW' },
    { name: 'Парные' },
    { name: 'Детям' },
    { name: 'Распродажа' },
  ];
  return (
    <div className="pl-nav">
      <div className="pl-container pl-nav-row">
        <div className="pl-nav-cat">
          {Icon.menu}
          КАТАЛОГ
        </div>
        <ul>
          {items.map((it, i) => (
            <li key={i} onClick={it.onClick} className={it.name === 'Конструктор' ? 'active' : ''}>
              {it.name}
              {it.tag && <span className="pl-nav-tag">{it.tag}</span>}
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}

function Hero({ onConstructorClick }) {
  return (
    <div className="pl-container">
      <div className="pl-hero">
        <div className="pl-hero-main">
          <div className="pl-hero-content">
            <span className="pl-hero-badge">Скидка −40% до конца недели</span>
            <h1>Создай <em>свою</em><br/>футболку за 2 минуты</h1>
            <p>Любой принт, текст или фото на твоей футболке. Печать в день заказа, доставка по всей России.</p>
            <button className="pl-hero-cta" onClick={onConstructorClick}>
              Открыть конструктор {Icon.arrow}
            </button>
          </div>
          <div className="pl-hero-shirt">
            <ShirtSVG color="#ffffff">
              <g transform="translate(140, 180)">
                <text x="60" y="50" textAnchor="middle" fontFamily="Impact" fontSize="34" fill="#e30613">МОЯ</text>
                <text x="60" y="90" textAnchor="middle" fontFamily="Impact" fontSize="34" fill="#111">ФУТБОЛКА</text>
              </g>
            </ShirtSVG>
          </div>
        </div>
        <div className="pl-hero-side">
          <div className="pl-hero-card red">
            <div>
              <h3>Парные<br/>футболки</h3>
              <p>Для двоих от 1290 ₽</p>
            </div>
            <div className="pl-hero-card-foot">Смотреть →</div>
          </div>
          <div className="pl-hero-card dark">
            <div>
              <h3>Худи<br/>премиум</h3>
              <p>Плотная ткань 320 г/м²</p>
            </div>
            <div className="pl-hero-card-foot">Каталог →</div>
          </div>
        </div>
      </div>
    </div>
  );
}

function Categories() {
  const cats = [
    { name: 'Футболки муж.', from: 'от 690 ₽', color: '#1f3a8a' },
    { name: 'Футболки жен.', from: 'от 690 ₽', color: '#e9789e' },
    { name: 'Худи', from: 'от 2390 ₽', color: '#1a1a1a' },
    { name: 'Свитшоты', from: 'от 1890 ₽', color: '#7c4a2a' },
    { name: 'Лонгсливы', from: 'от 1190 ₽', color: '#1e6b3a' },
    { name: 'Детское', from: 'от 590 ₽', color: '#ffd400' },
  ];
  return (
    <div className="pl-container pl-section">
      <div className="pl-section-head">
        <h2>Каталог <em>товаров</em></h2>
        <a className="pl-link" href="#">Все категории →</a>
      </div>
      <div className="pl-cats">
        {cats.map((c, i) => (
          <div className="pl-cat" key={i}>
            <div className="pl-cat-img">
              <ShirtSVG color={c.color} style={{ width: '100%', height: '100%' }}/>
            </div>
            <div className="pl-cat-name">{c.name}</div>
            <div className="pl-cat-from">{c.from}</div>
          </div>
        ))}
      </div>
    </div>
  );
}

function ProductCard({ p }) {
  const [fav, setFav] = React.useState(p.fav);
  return (
    <div className="pl-card">
      <div className="pl-card-img">
        <div className="pl-card-tags">
          {p.tags?.map((t, i) => <span key={i} className={`pl-card-tag ${t.k}`}>{t.l}</span>)}
        </div>
        <button className={`pl-card-fav ${fav ? 'active' : ''}`} onClick={(e) => { e.stopPropagation(); setFav(!fav); }}>
          {fav ? Icon.heartFilled : Icon.heart}
        </button>
        <ShirtSVG color={p.shirtColor} style={{ width: '100%', height: '100%' }}>
          <g transform={`translate(${p.printX || 130}, ${p.printY || 180})`}>
            <foreignObject x="0" y="0" width="140" height="140">
              <div style={{ width: '140px', height: '140px' }}>
                {p.print}
              </div>
            </foreignObject>
          </g>
        </ShirtSVG>
      </div>
      <div className="pl-card-body">
        <div className="pl-card-name">{p.name}</div>
        <div className="pl-card-rating">
          <span className="pl-stars">★★★★★</span>
          <span>{p.rating} · {p.sales}+ продано</span>
        </div>
        <div className="pl-card-price-row">
          <span className={`pl-card-price ${p.priceOld ? 'sale' : ''}`}>{p.price} ₽</span>
          {p.priceOld && <span className="pl-card-price-old">{p.priceOld} ₽</span>}
        </div>
        <div className="pl-card-colors">
          {p.colors?.map((c, i) => <span key={i} className="pl-card-color" style={{ background: c }}/>)}
        </div>
      </div>
    </div>
  );
}

function TopSales() {
  const products = [
    { name: 'Футболка "Космонавт-вайб" белая', price: 890, priceOld: 1490, rating: 4.9, sales: 2400, fav: false,
      shirtColor: '#ffffff', print: PRINTS[0].svg, tags: [{k:'sale', l:'-40%'}, {k:'hot', l:'хит'}],
      colors: ['#ffffff','#1a1a1a','#1f3a8a','#e9789e'] },
    { name: 'Худи оверсайз с принтом "NYET"', price: 2890, priceOld: 3990, rating: 4.8, sales: 1200, fav: true,
      shirtColor: '#1a1a1a', print: PRINTS[2].svg, tags: [{k:'sale', l:'-28%'}],
      colors: ['#1a1a1a','#9ca3a3','#16213e'] },
    { name: 'Футболка с пиксель-сердцем', price: 990, rating: 5.0, sales: 800, fav: false,
      shirtColor: '#ffd400', print: PRINTS[3].svg, tags: [{k:'new', l:'new'}],
      colors: ['#ffd400','#ffffff','#1a1a1a'] },
    { name: 'Свитшот "Cat-meme" жёлтый', price: 1990, rating: 4.7, sales: 540, fav: false,
      shirtColor: '#ffffff', print: PRINTS[4].svg, tags: [{k:'hot', l:'хит'}],
      colors: ['#ffffff','#1a1a1a','#e9789e'] },
    { name: 'Футболка "РОССИЯ" триколор', price: 790, priceOld: 990, rating: 4.9, sales: 3100, fav: false,
      shirtColor: '#ffffff', print: PRINTS[1].svg, tags: [{k:'sale', l:'-20%'}],
      colors: ['#ffffff','#1a1a1a','#c91e1e'] },
  ];
  return (
    <div className="pl-container pl-section">
      <div className="pl-section-head">
        <h2>ТОП <em>продаж</em></h2>
        <a className="pl-link" href="#">Все хиты →</a>
      </div>
      <div className="pl-grid">
        {products.map((p, i) => <ProductCard key={i} p={p}/>)}
      </div>
    </div>
  );
}

function PromoStrip({ onConstructorClick }) {
  return (
    <div className="pl-container">
      <div className="pl-promo">
        <div className="pl-promo-icon">{Icon.shapes}</div>
        <div className="pl-promo-text">
          <h3>Создай уникальный принт прямо сейчас</h3>
          <p>Загрузи фото, добавь текст или выбери из 5000+ готовых дизайнов. Печать от 1 шт.</p>
        </div>
        <button className="pl-promo-cta" onClick={onConstructorClick}>В конструктор →</button>
      </div>
    </div>
  );
}

function PopularPrints() {
  const tags = ['Все', 'Мемы', 'Поп-культура', 'Игры', 'Аниме', 'Музыка', 'Спорт', 'Города'];
  const [active, setActive] = React.useState('Все');
  return (
    <div className="pl-container pl-section">
      <div className="pl-section-head">
        <h2>Популярные <em>принты</em></h2>
        <a className="pl-link" href="#">Все принты (5000+) →</a>
      </div>
      <div style={{ display: 'flex', gap: 6, marginBottom: 14, flexWrap: 'wrap' }}>
        {tags.map(t => (
          <button key={t} onClick={() => setActive(t)}
            className="pl-c-tag" style={active === t ? { background: '#111', color: 'white' } : {}}>
            {t}
          </button>
        ))}
      </div>
      <div className="pl-prints">
        {PRINTS.concat(PRINTS).slice(0, 16).map((p, i) => (
          <div className="pl-print" key={i}>
            <div style={{ width: '70%', height: '70%' }}>{p.svg}</div>
            <div className="pl-print-label">{p.name}</div>
          </div>
        ))}
      </div>
    </div>
  );
}

function Benefits() {
  const items = [
    { icon: Icon.truck, t: 'Доставка по РФ', s: 'От 1 дня · СДЭК, Почта, курьер' },
    { icon: Icon.ruble, t: 'Печать от 1 шт.', s: 'Без минимального заказа' },
    { icon: Icon.shield, t: 'Гарантия качества', s: 'Не подошло — вернём деньги' },
    { icon: Icon.shapes, t: '5000+ дизайнов', s: 'Готовые принты + свои' },
  ];
  return (
    <div className="pl-container pl-section">
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 14 }}>
        {items.map((it, i) => (
          <div key={i} style={{ background: '#fafafa', borderRadius: 8, padding: 18, display: 'flex', gap: 14, alignItems: 'flex-start' }}>
            <div style={{ width: 40, height: 40, background: 'white', borderRadius: 6, display: 'grid', placeItems: 'center', color: '#e30613', flexShrink: 0 }}>{it.icon}</div>
            <div>
              <div style={{ fontWeight: 800, fontSize: 14, marginBottom: 2 }}>{it.t}</div>
              <div style={{ fontSize: 12, color: '#777' }}>{it.s}</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function Footer() {
  return (
    <footer className="pl-footer">
      <div className="pl-container">
        <div className="pl-footer-cols">
          <div>
            <h4>Каталог</h4>
            <ul><li>Футболки</li><li>Худи</li><li>Свитшоты</li><li>Лонгсливы</li><li>Детям</li></ul>
          </div>
          <div>
            <h4>Помощь</h4>
            <ul><li>Доставка</li><li>Оплата</li><li>Возврат</li><li>FAQ</li><li>Контакты</li></ul>
          </div>
          <div>
            <h4>Партнёрам</h4>
            <ul><li>Оптом</li><li>Сотрудничество</li><li>Реферальная</li><li>Художникам</li></ul>
          </div>
          <div>
            <h4>О нас</h4>
            <ul><li>Компания</li><li>Производство</li><li>Отзывы</li><li>Блог</li></ul>
          </div>
          <div>
            <h4>Подписка</h4>
            <p style={{ fontSize: 12, color: '#999', margin: '0 0 10px' }}>Скидка 15% на первый заказ для новых подписчиков</p>
            <div style={{ display: 'flex', gap: 6 }}>
              <input placeholder="email@example.com" style={{ flex: 1, padding: '9px 12px', border: 0, borderRadius: 4, fontSize: 12 }}/>
              <button style={{ background: '#e30613', color: 'white', border: 0, padding: '0 14px', borderRadius: 4, fontWeight: 700, cursor: 'pointer', fontSize: 12 }}>OK</button>
            </div>
          </div>
        </div>
        <div className="pl-footer-bottom">
          <span>© 2026 PrintLab. Все права защищены.</span>
          <span>Политика конфиденциальности · Оферта</span>
        </div>
      </div>
    </footer>
  );
}

function Homepage({ onConstructorClick }) {
  return (
    <div data-screen-label="Главная страница">
      <TopBar/>
      <Header onConstructorClick={onConstructorClick}/>
      <Nav onConstructorClick={onConstructorClick}/>
      <Hero onConstructorClick={onConstructorClick}/>
      <Categories/>
      <TopSales/>
      <PromoStrip onConstructorClick={onConstructorClick}/>
      <PopularPrints/>
      <Benefits/>
      <Footer/>
    </div>
  );
}

window.Homepage = Homepage;
window.TopBar = TopBar;
window.Header = Header;
window.Nav = Nav;
