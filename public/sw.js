const CACHE_NAME = 'printlab-pwa-v1';
const STATIC_ASSETS = [
  '/manifest.webmanifest',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/icons/apple-touch-icon.png',
  '/mockups/tshirts/white-front.png',
  '/mockups/tshirts/black-front.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .catch(() => undefined)
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
    ))
  );
  self.clients.claim();
});

function isUnsafeRequest(request) {
  const url = new URL(request.url);

  return request.method !== 'GET'
    || url.pathname.startsWith('/api/')
    || url.pathname.includes('/auth/')
    || url.pathname.includes('/order-requests')
    || url.pathname.includes('/constructor/');
}

async function networkFirst(request) {
  const cache = await caches.open(CACHE_NAME);

  try {
    const response = await fetch(request);

    if (response.ok && request.method === 'GET') {
      cache.put(request, response.clone());
    }

    return response;
  } catch (error) {
    const cached = await cache.match(request);

    if (cached) {
      return cached;
    }

    throw error;
  }
}

self.addEventListener('fetch', (event) => {
  const { request } = event;

  if (isUnsafeRequest(request)) {
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(networkFirst(request));
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => cached || networkFirst(request))
  );
});
