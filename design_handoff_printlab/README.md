# Handoff: PrintLab — конструктор принтов для футболок

## Overview
PrintLab — это маркетплейс печати на одежде в духе vsemayki.ru с упором на молодёжную аудиторию (16–25 лет, мемы, поп-культура). Дизайн-пакет содержит:

- **Главную страницу** в классическом стиле (красные акценты, плотная вёрстка, маркетинговые баннеры)
- **Альтернативный streetwear-вариант главной** (тёмная тема, ограниченные дропы)
- **Рабочий конструктор** с drag-and-drop, добавлением принтов, текста, фото и сменой цвета изделия
- **Мобильную версию** обоих экранов (адаптировано под iPhone, есть нижний tabbar)

## About the Design Files
Файлы в этом пакете — **дизайн-референсы, созданные в HTML/JSX**: интерактивные прототипы, которые показывают желаемый внешний вид и поведение. Это **не production-код** для прямого копирования.

Задача: **воспроизвести эти дизайны в существующей кодовой базе целевого проекта** (React/Next.js, Vue/Nuxt, SvelteKit и т. д.), используя её устоявшиеся паттерны, библиотеки компонентов, систему стилей. Если проект только начинается — выбрать наиболее подходящий стек и реализовать дизайн там.

Inline JSX в `source/*.jsx` использует Babel Standalone и глобальные переменные через `window` — это формат прототипа. В реальном проекте это надо переписать как нормальные ES-модули с импортами, типизацией (TypeScript), и компонентами вашей UI-библиотеки.

## Fidelity
**High-fidelity (hi-fi).** Все цвета, шрифты, отступы, иконки, типографика — финальные. Воспроизводите пиксель-в-пиксель, используя инструменты вашего кодстека (например, Tailwind, CSS Modules, styled-components).

## Tech Stack Recommendations (если стек не задан)
- **Frontend**: Next.js 14+ (App Router) или Vite + React 18
- **Styling**: Tailwind CSS с кастомными токенами (см. ниже) или CSS Modules
- **State**: Zustand или React Context для конструктора; React Query для каталога
- **Drag & Drop**: `@dnd-kit/core` или собственная реализация на pointer events (как в прототипе)
- **Конструктор-canvas**: для production лучше переписать на Konva.js или Fabric.js — текущая реализация на абсолютно-позиционированных DIV хороша как референс UX, но не масштабируется под сложные эффекты (поворот, экспорт PNG)

## Design Tokens

### Цвета
```
--red:        #e30613   /* primary, акценты, CTA */
--red-dark:   #c00510   /* hover state */
--red-light:  #ff2233   /* streetwear вариант */
--yellow:     #ffd400   /* hero, sale-теги */
--black:      #111111   /* основной текст, тёмные кнопки */
--gray-900:   #1a1a1a   /* топбар, футер */
--gray-700:   #4a4a4a   /* вторичный текст */
--gray-500:   #8a8a8a   /* подписи, плейсхолдеры */
--gray-300:   #d0d0d0   /* бордеры */
--gray-200:   #e5e5e5   /* разделители */
--gray-100:   #f4f4f4   /* фон секций */
--gray-50:    #fafafa   /* мягкий фон карточек */
--white:      #ffffff
--green:      #1ea54a   /* NEW-теги, статусы */
--orange:     #ff7a00   /* рейтинги */
```

### Цвета футболок (12 шт)
Белый `#ffffff`, Чёрный `#1a1a1a`, Серый `#9ca3a3`, Красный `#c91e1e`, Синий `#1f3a8a`, Тёмно-синий `#16213e`, Зелёный `#1e6b3a`, Жёлтый `#f3c613`, Розовый `#e9789e`, Сирень `#6e3aa7`, Оранж `#e8732a`, Беж `#d6c5a8`.

### Типографика
- **Шрифт интерфейса**: Manrope (Google Fonts), веса 400/500/600/700/800/900
- **Дополнительные** (для конструктора-текста): Impact, Georgia, JetBrains Mono, Press Start 2P, Caveat
- Шкала: `11/12/13/14/16/18/20/26/44px`
- Заголовки секций: 26px / weight 900 / letter-spacing −0.01em
- Hero h1: 44px / weight 900 / letter-spacing −0.02em
- Body: 14px / weight 400 / line-height 1.4

### Spacing scale
`4 / 6 / 8 / 10 / 12 / 14 / 16 / 18 / 22 / 26 / 32 / 36 / 50 px`

### Border radius
- кнопки: `4px` (резкие, marketplace-стиль)
- карточки товаров: `8px`
- панели/модалки: `8px`
- pills/теги: `3–4px` или `999px` (полностью круглые)
- иконки-кружки: `50%`

### Shadows
- Карточка hover: `0 6px 24px rgba(0,0,0,0.1)`
- Промо-блок: `0 12px 24px rgba(0,0,0,0.12)` (через filter: drop-shadow на SVG)
- FAB кнопка mobile: `0 6px 16px rgba(227,6,19,0.4)`

## Screens / Views

### 1. Главная страница (desktop, 1280px)
**Файл-референс:** `source/homepage.jsx` + `source/styles.css`

Структура сверху вниз:
1. **TopBar** (32px, чёрный) — город, ссылки на сервис, телефон, «Войти»
2. **Header** (~70px, белый) — логотип, поисковая строка с красной обводкой 2px и красной кнопкой «Найти», иконки Профиль/Избранное/Корзина (с красными бейджами счётчиков)
3. **Nav** (44px, красный фон) — вертикальная плашка КАТАЛОГ + горизонтальный список категорий с тегами HIT/NEW (жёлтые)
4. **Hero** (grid 1fr / 280px) — большой жёлтый баннер с акцией (h1 с фрагментом в красной плашке `<em>`, изображение футболки повёрнуто на 8°) + два сайд-баннера (красный + чёрный)
5. **Категории** (grid 6 колонок) — карточки с SVG-мокапами футболок разных цветов
6. **Топ продаж** (grid 5 колонок) — карточки товаров: SVG-футболка с принтом, теги (sale/hot/new), кнопка «избранное», название (max 2 строки), рейтинг звёздами, цена + старая цена, точки доступных цветов
7. **Promo strip** — горизонтальный чёрный CTA-блок на конструктор
8. **Популярные принты** — фильтр-теги (Все/Мемы/Поп-культура/...) + grid 8×N миниатюр
9. **Benefits** (grid 4) — иконка + заголовок + подпись (доставка, печать от 1 шт., гарантия, 5000+ дизайнов)
10. **Footer** (тёмный) — 5 колонок: каталог, помощь, партнёрам, о нас, подписка с email-полем

### 2. Конструктор (desktop)
**Файл-референс:** `source/constructor.jsx`

Layout: **3 колонки** (`280px / 1fr / 320px`), полная высота вьюпорта.

**Левая панель — инструменты:**
- 4 вкладки: Принты / Фото / Текст / Фигуры (иконка сверху, лейбл снизу, активная — красная с подчёркиванием)
- Принты: поиск + чипы-фильтры + grid 3 колонки (12 готовых SVG-принтов: космонавт, NYET, Pixel love, Cat-meme, Pizza, NFT Ape и др.)
- Фото: drop-zone с пунктирной рамкой + история загрузок
- Текст: textarea + кнопка «Добавить» + 6 шрифтов (превью «Аа») + палитра 12 цветов

**Центр — холст:**
- Топбар: переключатель Перёд/Спина (segmented control), undo/redo/clear (34×34 иконки)
- Канвас 480×580: SVG-футболка с тенью + зона печати (пунктирная рамка, 46% × 50% относительно футболки)
- Элементы: drag (move/resize углами/удаление красной кнопкой ×); selected = красная outline 1.5px
- Внизу: панель с 12 цветными кружками для смены цвета изделия

**Правая панель — конфигуратор:**
- Тип изделия: 3 пилюли (Футболка / Худи / Лонгслив) с мини-SVG и ценами «от»
- Размер: grid 6 кнопок XS–XXL (активная — чёрная)
- Количество: −/N/+
- Состав, доставка, гарантия (информация)
- **Sticky footer:**
  - Цена крупная 28px + старая цена + бейдж скидки красный
  - Кнопка «Купить в 1 клик» (красная, full-width)
  - Кнопка «Добавить в корзину» (white outline)

### 3. Streetwear главная (тёмная альтернатива)
**Файл-референс:** `source/PrintLab.html` (`HomeDark` компонент)

То же что главная, но:
- Фон `#0d0d0d`, текст белый
- Hero — чёрный с красной обводкой 2px, заголовок 50px «NO LIMITS»
- Жёлтый бейдж «STREETWEAR DROP»
- Карточки товаров на тёмном фоне, секция «Тренды этой недели» 8×1 с номерами #1–#8 в углу

### 4. Мобильная главная
**Файл-референс:** `source/mobile.jsx` (`MHomepage`)

Внутри iPhone-фрейма (402×874):
- Header: hamburger / лого / ❤️ / 🛒 (бейдж счётчика)
- Псевдо-search-bar (плейсхолдер)
- Hero компактный (180px, повёрнутая футболка справа за пределами 65% ширины контента)
- Горизонтальные скроллы с snap для категорий и топа продаж (по 156px карточки)
- Промо-баннер чёрный с жёлтой кнопкой «ОК»
- Принты grid 4×2
- **Bottom tabbar (64px)** с 5 кнопками; центральная — красный круглый FAB 48×48 «+» (поднят над таббаром на −18px), запускает конструктор

### 5. Мобильный конструктор
**Файл-референс:** `source/mobile.jsx` (`MConstructor`)

Сверху вниз:
1. Header: ←/название/Поделиться
2. Stage: футболка 280×340 центрирована, переключатель видов (pill) сверху, undo/redo/clear как круглые кнопки справа
3. Tools: 5 вкладок (Принты/Текст/Фото/Цвет/Тип), панель ~180px
4. Sticky footer: цена + кнопка «В корзину»

Touch-события: drag через `touchstart/touchmove/touchend` (см. `MCanvasEl`).

## Interactions & Behavior

### Конструктор — состояние
```ts
type Element =
  | { id: string; type: 'image'; svg?: ReactNode; imgSrc?: string;
      x: number; y: number; w: number; h: number; rotate?: number }
  | { id: string; type: 'text'; text: string;
      x: number; y: number; w: number; h: number;
      fontFamily: string; color: string; bold: boolean };

interface ConstructorState {
  productType: 'tshirt' | 'hoodie' | 'longsleeve';
  shirtColorId: string;
  view: 'front' | 'back';
  size: 'XS' | 'S' | 'M' | 'L' | 'XL' | 'XXL';
  qty: number;
  elements: Element[];
  selected: string | null;
  history: Element[][];   // для undo/redo
  historyIdx: number;
}
```

Координаты `x/y/w/h` — **в процентах от print-area** (зоны печати), не пиксели. Это обеспечивает корректный экспорт принта на изделие любого размера.

### Drag/Resize logic
- Pointer down на элементе → `setDrag({ type: 'move', startX, startY, origEl })`
- Глобальный mousemove → пересчитать `x/y` в процентах от размера контейнера
- mouseup → коммит в history
- Resize: 4 угловых хендла (`tl/tr/bl/br`), для image — пропорциональный resize (сохранение aspect ratio)

### Цена
```ts
basePrice = { tshirt: 690, hoodie: 2390, longsleeve: 1190 }[productType];
printSurcharge = elements.length > 0 ? 200 : 0;
price = basePrice + printSurcharge;
priceOld = Math.round(price * 1.4);  // показывается как зачёркнутая
```

### Анимации
- Карточки товара hover: `transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,0.1);` 150ms
- Категории hover: `border-color: red; transform: translateY(-2px);` 150ms
- Tab transitions: opacity/colour 120ms
- Mobile horizontal scroll: `scroll-snap-type: x proximity`
- DC artboards drag: cubic-bezier(.2,.7,.3,1) 180ms

## Assets
**Все ассеты — inline SVG в коде**, никаких внешних файлов:
- 12 SVG-принтов (PRINTS в `shared.jsx`) — в production замените на реальную библиотеку клипарта (минимум сотня вариантов, лучше тысячи; рекомендую брать у себя на сервере + теговую систему)
- SVG-мокапы футболки и худи (`ShirtSVG`, `HoodieSVG` в `shared.jsx`) — для production обычно нужны photoreal mockups (PNG/WebP с placeholder-зонами через CSS mask или canvas composition)
- Иконки — собственный `Icon` объект, можно заменить на Lucide React, Heroicons или подобное

## Files in this bundle
- `source/PrintLab.html` — точка входа, оркестрирует все артборды через DesignCanvas
- `source/styles.css` — все CSS-токены и стили (≈800 строк, можно разбить по компонентам)
- `source/shared.jsx` — иконки, SVG-мокапы, каталог принтов, цвета, шрифты
- `source/homepage.jsx` — компоненты главной desktop (TopBar, Header, Nav, Hero, Categories, ProductCard, TopSales, PromoStrip, PopularPrints, Benefits, Footer)
- `source/constructor.jsx` — desktop-конструктор (Constructor, ClipartPanel, UploadPanel, TextPanel, ShapesPanel, Canvas, CanvasElement, ColorBar)
- `source/mobile.jsx` — мобильные версии (MHomepage, MConstructor, MCanvasEl, MobileApp)
- `source/design-canvas.jsx`, `source/ios-frame.jsx` — служебные обёртки прототипа, в production не нужны

## Запуск референса
Откройте `source/PrintLab.html` в браузере. Все артборды (5 шт) рендерятся на одном холсте — можно панорамировать/зумировать колесом мыши, любой артборд раскрывается на полный экран кнопкой ⤢.

## Рекомендации по реализации
1. **Начните с design tokens** — настройте Tailwind theme или CSS variables по списку выше
2. **Соберите атомы**: Button (primary red, secondary outline, dark), Input, Tag/Badge, Card, IconButton
3. **Затем компоненты каталога**: ProductCard, CategoryCard, PrintTile
4. **Layout-компоненты**: TopBar, Header (с поиском), Nav, Footer — переиспользуются на всех страницах
5. **Главная** — собирается из компонентов выше
6. **Конструктор** — самая сложная часть, советую:
   - Вынести canvas-логику в отдельный хук `useCanvas()` или Zustand store
   - Использовать Konva.js для рендера (даст экспорт в PNG, поворот, фильтры из коробки)
   - Сохранять состояние конструктора в URL (для шеринга) и localStorage (для авто-сохранения)
7. **Mobile** — отдельные роуты или адаптивные компоненты с media queries; bottom tabbar реализуется через `position: fixed` + safe-area insets

Удачи!
