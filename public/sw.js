// Service Worker - Klinik Indosehat 2003
const CACHE_NAME = 'indosehat-mcu-v1.1.0';
const STATIC_ASSETS = [
  '/',
  '/favicon.ico',
  '/manifest.webmanifest',
  '/images/logo/logo-klinik-indosehat-2003.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Hanya tangani GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Jangan cache rute administratif /admin
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/livewire')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Cache respons yang valid
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        // Fallback ke cache jika offline
        return caches.match(event.request);
      })
  );
});
