import fs from 'node:fs';
import path from 'node:path';
import sharp from 'sharp';
import gifenc from 'gifenc';
import hme from 'h264-mp4-encoder';

const { GIFEncoder, quantize, applyPalette } = gifenc;

const root = process.cwd();
const outDir = path.join(root, 'public', 'marketing');
const width = 540;
const height = 960;
const fps = 8;
const duration = 40;
const totalFrames = fps * duration;

const tshirtPath = path.join(root, 'public/mockups/tshirts/black-front.png');
const printPaths = [
  '03f4e977-13b1-48bf-9311-a09eee4b16a3.png',
  '1411d81d-f18c-4984-aeab-0ddd3de8950e.png',
  '2d3cfac1-0be1-40db-9e9a-c801242b3426.png',
  '72508e0b-335a-42cc-8c3e-935b33f15a2c.png',
].map((file) => path.join(root, 'storage/app/public/generated-prints', file));

fs.mkdirSync(outDir, { recursive: true });

const tshirt = await sharp(tshirtPath).resize(420, 420, { fit: 'contain' }).png().toBuffer();
const prints = await Promise.all(
  printPaths.map((file) => sharp(file).resize(150, 150, { fit: 'cover' }).png().toBuffer()),
);

function esc(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');
}

function ease(x) {
  return 1 - Math.pow(1 - Math.max(0, Math.min(1, x)), 3);
}

function line(text, x, y, size, fill = '#ffffff', weight = 800, anchor = 'middle') {
  return `<text x="${x}" y="${y}" text-anchor="${anchor}" font-family="Arial, Helvetica, sans-serif" font-size="${size}" font-weight="${weight}" fill="${fill}">${esc(text)}</text>`;
}

function pill(text, x, y, w, h, fill = '#ffffff', color = '#111111') {
  return `
    <rect x="${x}" y="${y}" width="${w}" height="${h}" rx="18" fill="${fill}" opacity="0.96"/>
    ${line(text, x + w / 2, y + h / 2 + 9, 25, color, 800)}
  `;
}

function sceneFor(t) {
  if (t < 5) return 'hook';
  if (t < 12) return 'product';
  if (t < 22) return 'tutorial';
  if (t < 32) return 'examples';
  return 'cta';
}

function overlaySvg(t, frame) {
  const scene = sceneFor(t);
  const pulse = 1 + Math.sin(frame / 7) * 0.025;
  const bgShift = Math.sin(frame / 18) * 22;

  let body = `
    <defs>
      <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#12131a"/>
        <stop offset="45%" stop-color="#e50914"/>
        <stop offset="100%" stop-color="#111827"/>
      </linearGradient>
      <radialGradient id="glow" cx="50%" cy="30%" r="55%">
        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.22"/>
        <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
      </radialGradient>
    </defs>
    <rect width="540" height="960" fill="url(#bg)"/>
    <circle cx="${145 + bgShift}" cy="170" r="210" fill="url(#glow)"/>
    <circle cx="${430 - bgShift}" cy="710" r="220" fill="url(#glow)"/>
    <rect x="28" y="28" width="484" height="904" rx="34" fill="#0b0c10" opacity="0.30"/>
    ${line('PrintLab', 270, 76, 34, '#ffffff', 900)}
  `;

  if (scene === 'hook') {
    body += `
      ${line('Хочешь свой', 270, 145, 42, '#ffffff', 900)}
      ${line('anime-print?', 270, 195, 52, '#ffffff', 900)}
      ${line('Футболка или кружка с твоей идеей', 270, 238, 25, '#f8fafc', 700)}
      <g transform="translate(60 305) scale(${pulse})">
        <rect x="0" y="0" width="420" height="420" rx="34" fill="#ffffff" opacity="0.10"/>
      </g>
      ${pill('AI-дизайн за пару минут', 77, 765, 386, 58)}
      ${line('Макет перед печатью', 270, 855, 28, '#ffffff', 800)}
    `;
  } else if (scene === 'product') {
    const p = ease((t - 5) / 7);
    body += `
      ${line('Как заказать?', 270, 145, 48, '#ffffff', 900)}
      ${pill('1. Выбери товар', 82, 205, 376, 58)}
      ${pill('2. Напиши идею', 82, 280, 376, 58, p > 0.35 ? '#ffffff' : '#ffffff99')}
      ${pill('3. Получи макет', 82, 355, 376, 58, p > 0.7 ? '#ffffff' : '#ffffff99')}
      ${line('Anime-style, chibi, cyber, samurai vibe', 270, 802, 25, '#ffffff', 800)}
      ${line('без сложного дизайнера', 270, 842, 26, '#ffffff', 800)}
    `;
  } else if (scene === 'tutorial') {
    const dots = '.'.repeat((Math.floor(t * 2) % 3) + 1);
    body += `
      ${line('Пишешь идею', 270, 145, 46, '#ffffff', 900)}
      <rect x="58" y="205" width="424" height="140" rx="22" fill="#ffffff" opacity="0.96"/>
      ${line('cyber anime samurai', 270, 262, 31, '#111827', 900)}
      ${line('+ мой ник: KIRA', 270, 306, 30, '#111827', 900)}
      ${pill(`AI создает принт${dots}`, 82, 382, 376, 58)}
      ${line('Потом открываешь конструктор', 270, 801, 25, '#ffffff', 800)}
      ${line('и оставляешь заявку', 270, 840, 30, '#ffffff', 900)}
    `;
  } else if (scene === 'examples') {
    body += `
      ${line('Выбери свой стиль', 270, 140, 45, '#ffffff', 900)}
      ${line('Оригинальный персонаж, ник или аватарка', 270, 187, 24, '#ffffff', 700)}
      <rect x="58" y="685" width="424" height="132" rx="22" fill="#ffffff" opacity="0.94"/>
      ${line('Загрузи картинку', 270, 740, 32, '#111827', 900)}
      ${line('или создай через AI', 270, 784, 30, '#111827', 900)}
      ${line('Мы проверим макет перед печатью', 270, 858, 25, '#ffffff', 800)}
    `;
  } else {
    body += `
      ${line('Скидка для группы', 270, 160, 43, '#ffffff', 900)}
      <rect x="72" y="215" width="396" height="132" rx="28" fill="#ffffff"/>
      ${line('ANIME15', 270, 300, 56, '#e50914', 900)}
      ${line('Создай дизайн по ссылке в посте', 270, 420, 32, '#ffffff', 900)}
      ${line('или напиши нам в Telegram', 270, 464, 28, '#ffffff', 800)}
      ${pill('Футболки / кружки / AI-принты', 57, 765, 426, 58)}
      ${line('PrintLab', 270, 870, 44, '#ffffff', 900)}
    `;
  }

  return Buffer.from(`<svg width="540" height="960" viewBox="0 0 540 960" xmlns="http://www.w3.org/2000/svg">${body}</svg>`);
}

function compositesFor(t) {
  const scene = sceneFor(t);
  const layers = [{ input: overlaySvg(t, Math.round(t * fps)), top: 0, left: 0 }];

  if (scene === 'hook') {
    layers.push({ input: tshirt, left: 60, top: 300 });
    layers.push({ input: prints[0], left: 195, top: 442 });
  }

  if (scene === 'product') {
    layers.push({ input: tshirt, left: 60, top: 440 });
    layers.push({ input: prints[1], left: 195, top: 582 });
  }

  if (scene === 'tutorial') {
    layers.push({ input: tshirt, left: 60, top: 465 });
    layers.push({ input: prints[2], left: 195, top: 607 });
  }

  if (scene === 'examples') {
    const y = 260;
    layers.push({ input: prints[0], left: 82, top: y });
    layers.push({ input: prints[1], left: 308, top: y });
    layers.push({ input: prints[2], left: 82, top: y + 185 });
    layers.push({ input: prints[3], left: 308, top: y + 185 });
  }

  if (scene === 'cta') {
    layers.push({ input: tshirt, left: 60, top: 500 });
    layers.push({ input: prints[3], left: 195, top: 642 });
  }

  return layers;
}

const gif = GIFEncoder();
const encoder = await hme.createH264MP4Encoder();
encoder.width = width;
encoder.height = height;
encoder.frameRate = fps;
encoder.kbps = 1800;
encoder.speed = 4;
encoder.groupOfPictures = fps * 2;
encoder.outputFilename = 'anime-print-ad.mp4';
encoder.initialize();

for (let frame = 0; frame < totalFrames; frame += 1) {
  const t = frame / fps;
  const raw = await sharp({
    create: {
      width,
      height,
      channels: 4,
      background: '#111827',
    },
  })
    .composite(compositesFor(t))
    .raw()
    .toBuffer();

  encoder.addFrameRgba(raw);

  const palette = quantize(raw, 256);
  const indexed = applyPalette(raw, palette);
  gif.writeFrame(indexed, width, height, { palette, delay: 1000 / fps });

  if (frame === 0) {
    await sharp(raw, { raw: { width, height, channels: 4 } })
      .jpeg({ quality: 90 })
      .toFile(path.join(outDir, 'anime-print-ad-poster.jpg'));
  }
}

gif.finish();
fs.writeFileSync(path.join(outDir, 'anime-print-ad.gif'), Buffer.from(gif.bytes()));

encoder.finalize();
fs.writeFileSync(path.join(outDir, 'anime-print-ad.mp4'), Buffer.from(encoder.FS.readFile(encoder.outputFilename)));
encoder.delete();

console.log(`Rendered ${path.join(outDir, 'anime-print-ad.gif')}`);
console.log(`Rendered ${path.join(outDir, 'anime-print-ad.mp4')}`);
