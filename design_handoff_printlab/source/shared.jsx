// shared.jsx — общие SVG, мокапы футболок, утилиты для PrintLab

// === Иконки ===
const Icon = {
  search: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>,
  user: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
  heart: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"/></svg>,
  heartFilled: <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"/></svg>,
  cart: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>,
  menu: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>,
  pin: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M12 22s-8-7-8-13a8 8 0 0 1 16 0c0 6-8 13-8 13Z"/><circle cx="12" cy="9" r="3"/></svg>,
  arrow: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>,
  star: <svg viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.9 7.5.6-5.7 5 1.7 7.4L12 18l-6.5 3.9 1.7-7.4-5.7-5L9 8.9Z"/></svg>,
  clipart: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>,
  upload: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></svg>,
  text: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8"><path d="M4 7V5h16v2M9 5v14M15 19h-6"/></svg>,
  shapes: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8"><circle cx="6.5" cy="6.5" r="3.5"/><rect x="13" y="3" width="8" height="8"/><path d="m6.5 13 4.5 8H2Z"/></svg>,
  rotate: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>,
  undo: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/></svg>,
  redo: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 15-6.7L21 13"/></svg>,
  zoom: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M8 11h6M11 8v6"/></svg>,
  truck: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="1" y="6" width="14" height="11" rx="1"/><path d="M15 9h4l3 4v4h-7"/><circle cx="6" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>,
  ruble: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 21V4h5a4 4 0 1 1 0 8H6m0 4h10"/></svg>,
  shield: <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M12 2 4 6v6c0 5 3.5 8.5 8 10 4.5-1.5 8-5 8-10V6Z"/></svg>,
};

// === SVG-мокап футболки (вид спереди) ===
function ShirtSVG({ color = '#ffffff', children, style }) {
  return (
    <svg viewBox="0 0 400 480" style={style} xmlns="http://www.w3.org/2000/svg">
      <defs>
        <filter id="shirtShade" x="-10%" y="-10%" width="120%" height="120%">
          <feGaussianBlur stdDeviation="2"/>
        </filter>
      </defs>
      {/* основная форма */}
      <path
        d="M120 50 L80 70 L40 130 L70 170 L100 150 L100 440 Q100 460 120 460 L280 460 Q300 460 300 440 L300 150 L330 170 L360 130 L320 70 L280 50 Q260 80 200 80 Q140 80 120 50 Z"
        fill={color}
        stroke="rgba(0,0,0,0.15)"
        strokeWidth="1.5"
      />
      {/* воротник */}
      <path d="M155 50 Q200 90 245 50 Q230 75 200 75 Q170 75 155 50 Z" fill="rgba(0,0,0,0.08)" />
      {/* теневые складки */}
      <path d="M105 160 Q110 280 105 440" fill="none" stroke="rgba(0,0,0,0.06)" strokeWidth="6" strokeLinecap="round"/>
      <path d="M295 160 Q290 280 295 440" fill="none" stroke="rgba(0,0,0,0.06)" strokeWidth="6" strokeLinecap="round"/>
      <path d="M120 55 L100 90" fill="none" stroke="rgba(0,0,0,0.08)" strokeWidth="3"/>
      <path d="M280 55 L300 90" fill="none" stroke="rgba(0,0,0,0.08)" strokeWidth="3"/>
      {children}
    </svg>
  );
}

function HoodieSVG({ color = '#ffffff', children, style }) {
  return (
    <svg viewBox="0 0 400 480" style={style} xmlns="http://www.w3.org/2000/svg">
      {/* основная форма с карманом */}
      <path
        d="M120 60 L70 90 L30 160 L70 200 L95 180 L95 440 Q95 460 115 460 L285 460 Q305 460 305 440 L305 180 L330 200 L370 160 L330 90 L280 60 Q270 75 250 80 L250 130 Q200 145 150 130 L150 80 Q130 75 120 60 Z"
        fill={color}
        stroke="rgba(0,0,0,0.15)"
        strokeWidth="1.5"
      />
      {/* капюшон */}
      <path d="M150 80 Q200 130 250 80 Q230 100 200 105 Q170 100 150 80 Z" fill="rgba(0,0,0,0.15)"/>
      {/* шнурки */}
      <line x1="190" y1="105" x2="188" y2="160" stroke="rgba(0,0,0,0.4)" strokeWidth="2"/>
      <line x1="210" y1="105" x2="212" y2="160" stroke="rgba(0,0,0,0.4)" strokeWidth="2"/>
      {/* карман-кенгуру */}
      <path d="M140 320 L260 320 L270 400 L130 400 Z" fill="none" stroke="rgba(0,0,0,0.18)" strokeWidth="1.5"/>
      {/* складки */}
      <path d="M100 200 Q105 320 100 440" fill="none" stroke="rgba(0,0,0,0.06)" strokeWidth="6" strokeLinecap="round"/>
      <path d="M300 200 Q295 320 300 440" fill="none" stroke="rgba(0,0,0,0.06)" strokeWidth="6" strokeLinecap="round"/>
      {children}
    </svg>
  );
}

// === Готовые принты (SVG, без внешних ассетов) ===
const PRINTS = [
  // 0 — космонавт
  {
    id: 'astronaut', name: 'Космонавт-вайб', tag: 'POP',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="42" r="22" fill="#fff" stroke="#111" strokeWidth="3"/>
        <ellipse cx="50" cy="42" rx="14" ry="14" fill="#111"/>
        <ellipse cx="46" cy="38" rx="3" ry="5" fill="#fff" opacity="0.7"/>
        <rect x="32" y="62" width="36" height="22" rx="4" fill="#fff" stroke="#111" strokeWidth="3"/>
        <rect x="40" y="68" width="6" height="10" fill="#e30613"/>
        <rect x="54" y="68" width="6" height="10" fill="#1ea54a"/>
        <line x1="38" y1="22" x2="34" y2="14" stroke="#ffd400" strokeWidth="3" strokeLinecap="round"/>
        <line x1="62" y1="22" x2="66" y2="14" stroke="#ffd400" strokeWidth="3" strokeLinecap="round"/>
      </svg>
    ),
  },
  // 1 — RUSSIA
  {
    id: 'russia', name: 'РОССИЯ', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <text x="50" y="55" textAnchor="middle" fontFamily="Arial Black, sans-serif" fontSize="18" fontWeight="900" fill="#e30613">РОССИЯ</text>
        <rect x="20" y="58" width="60" height="6" fill="#fff" stroke="#111" strokeWidth="1"/>
        <rect x="20" y="64" width="60" height="6" fill="#0039a6"/>
        <rect x="20" y="70" width="60" height="6" fill="#e30613"/>
      </svg>
    ),
  },
  // 2 — НЕТ
  {
    id: 'nyet', name: 'NYET', tag: 'HIT',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <rect x="10" y="35" width="80" height="30" fill="#111" transform="rotate(-4 50 50)"/>
        <text x="50" y="58" textAnchor="middle" fontFamily="Impact, sans-serif" fontSize="28" fill="#ffd400" transform="rotate(-4 50 50)">НЕТ</text>
      </svg>
    ),
  },
  // 3 — пиксель-сердце
  {
    id: 'pixelheart', name: 'Pixel love', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        {[
          [3,2],[4,2],[6,2],[7,2],
          [2,3],[3,3],[4,3],[5,3],[6,3],[7,3],[8,3],
          [2,4],[3,4],[4,4],[5,4],[6,4],[7,4],[8,4],
          [3,5],[4,5],[5,5],[6,5],[7,5],
          [4,6],[5,6],[6,6],
          [5,7]
        ].map(([x,y],i) => <rect key={i} x={20+x*7} y={15+y*7} width="7" height="7" fill="#e30613"/>)}
      </svg>
    ),
  },
  // 4 — кот мем
  {
    id: 'cat', name: 'Cat-meme', tag: 'NEW',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="55" r="28" fill="#ffd400"/>
        <polygon points="28,38 35,18 45,32" fill="#ffd400"/>
        <polygon points="72,38 65,18 55,32" fill="#ffd400"/>
        <circle cx="42" cy="52" r="3" fill="#111"/>
        <circle cx="58" cy="52" r="3" fill="#111"/>
        <path d="M44 64 Q50 70 56 64" stroke="#111" strokeWidth="2" fill="none"/>
        <line x1="32" y1="60" x2="22" y2="58" stroke="#111" strokeWidth="1.5"/>
        <line x1="32" y1="63" x2="22" y2="64" stroke="#111" strokeWidth="1.5"/>
        <line x1="68" y1="60" x2="78" y2="58" stroke="#111" strokeWidth="1.5"/>
        <line x1="68" y1="63" x2="78" y2="64" stroke="#111" strokeWidth="1.5"/>
      </svg>
    ),
  },
  // 5 — keep calm
  {
    id: 'keepcalm', name: 'KEEP CHILL', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <rect x="15" y="15" width="70" height="70" fill="#111"/>
        <polygon points="50,22 53,28 50,32 47,28" fill="#fff"/>
        <text x="50" y="48" textAnchor="middle" fontFamily="Arial, sans-serif" fontSize="11" fontWeight="700" fill="#fff">KEEP</text>
        <text x="50" y="60" textAnchor="middle" fontFamily="Arial, sans-serif" fontSize="11" fontWeight="700" fill="#fff">CHILL</text>
        <text x="50" y="74" textAnchor="middle" fontFamily="Arial, sans-serif" fontSize="7" fill="#fff">AND VIBE ON</text>
      </svg>
    ),
  },
  // 6 — звезда панк
  {
    id: 'star', name: 'Punk Star', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <polygon points="50,10 61,40 92,40 67,58 76,90 50,72 24,90 33,58 8,40 39,40" fill="#e30613" stroke="#111" strokeWidth="3"/>
      </svg>
    ),
  },
  // 7 — gigachad
  {
    id: 'chad', name: 'CHAD', tag: 'TOP',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <text x="50" y="40" textAnchor="middle" fontFamily="Impact" fontSize="28" fill="#111">CHAD</text>
        <text x="50" y="70" textAnchor="middle" fontFamily="Impact" fontSize="14" fill="#888">МОДА · МОЩЬ</text>
        <line x1="20" y1="48" x2="80" y2="48" stroke="#e30613" strokeWidth="2"/>
      </svg>
    ),
  },
  // 8 — pizza
  {
    id: 'pizza', name: 'Pizza Lover', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <polygon points="20,80 80,80 50,20" fill="#ffd400" stroke="#111" strokeWidth="2"/>
        <polygon points="25,75 75,75 50,30" fill="#e30613"/>
        <circle cx="42" cy="55" r="4" fill="#fff"/>
        <circle cx="55" cy="48" r="4" fill="#fff"/>
        <circle cx="50" cy="65" r="4" fill="#fff"/>
        <circle cx="60" cy="62" r="3" fill="#1ea54a"/>
      </svg>
    ),
  },
  // 9 — рамка для текста
  {
    id: 'frame', name: 'Свой текст', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <rect x="15" y="35" width="70" height="30" fill="none" stroke="#111" strokeWidth="2" strokeDasharray="3 3"/>
        <text x="50" y="55" textAnchor="middle" fontFamily="Arial" fontSize="9" fill="#888">ВАШ ТЕКСТ</text>
      </svg>
    ),
  },
  // 10 — banana
  {
    id: 'banana', name: 'Banana', tag: '',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <path d="M25 70 Q35 30 75 25 Q72 35 60 38 Q40 50 35 75 Z" fill="#ffd400" stroke="#111" strokeWidth="2.5"/>
        <path d="M75 25 L80 18" stroke="#111" strokeWidth="2.5" strokeLinecap="round"/>
      </svg>
    ),
  },
  // 11 — ape
  {
    id: 'ape', name: 'NFT Ape', tag: 'NEW',
    svg: (
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="32" fill="#7c4a2a"/>
        <ellipse cx="50" cy="62" rx="20" ry="18" fill="#d8a878"/>
        <circle cx="42" cy="48" r="5" fill="#111"/>
        <circle cx="58" cy="48" r="5" fill="#111"/>
        <ellipse cx="50" cy="68" rx="6" ry="3" fill="#111"/>
      </svg>
    ),
  },
];

// === Цвета футболок ===
const SHIRT_COLORS = [
  { id: 'white', name: 'Белый', hex: '#ffffff', isLight: true },
  { id: 'black', name: 'Чёрный', hex: '#1a1a1a' },
  { id: 'gray',  name: 'Серый',  hex: '#9ca3a3', isLight: true },
  { id: 'red',   name: 'Красный',hex: '#c91e1e' },
  { id: 'blue',  name: 'Синий',  hex: '#1f3a8a' },
  { id: 'navy',  name: 'Тёмно-синий', hex: '#16213e' },
  { id: 'green', name: 'Зелёный',hex: '#1e6b3a' },
  { id: 'yellow',name: 'Жёлтый', hex: '#f3c613', isLight: true },
  { id: 'pink',  name: 'Розовый',hex: '#e9789e', isLight: true },
  { id: 'purple',name: 'Сирень', hex: '#6e3aa7' },
  { id: 'orange',name: 'Оранж',  hex: '#e8732a' },
  { id: 'beige', name: 'Беж',    hex: '#d6c5a8', isLight: true },
];

// === Шрифты ===
const FONTS = [
  { id: 'manrope', name: 'Manrope', css: '"Manrope", sans-serif', weight: 800 },
  { id: 'impact',  name: 'Impact',  css: 'Impact, sans-serif',     weight: 400 },
  { id: 'serif',   name: 'Serif',   css: 'Georgia, serif',         weight: 700 },
  { id: 'mono',    name: 'Mono',    css: '"JetBrains Mono", monospace', weight: 700 },
  { id: 'press',   name: 'Press',   css: '"Press Start 2P", monospace', weight: 400 },
  { id: 'caveat',  name: 'Caveat',  css: '"Caveat", cursive',     weight: 700 },
];

// Экспорт в window
Object.assign(window, { Icon, ShirtSVG, HoodieSVG, PRINTS, SHIRT_COLORS, FONTS });
