var CACHE_NAME = 'maison-nord-v1';
var STATIC_ASSETS = [
  '/maison-nord/',
  '/maison-nord/index.php',
  '/maison-nord/boutique.php',
  '/maison-nord/assets/css/styles.css',
  '/maison-nord/assets/js/app.js',
  '/maison-nord/assets/js/cart.js',
  '/maison-nord/assets/js/products.php',
  '/maison-nord/assets/icons/icon-192.svg',
  '/maison-nord/assets/icons/icon-512.svg',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'
];

/* Install: pre-cache static shell */
self.addEventListener('install', function (e) {
  e.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(STATIC_ASSETS);
    }).then(function () {
      return self.skipWaiting();
    })
  );
});

/* Activate: clear old caches */
self.addEventListener('activate', function (e) {
  e.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys.filter(function (k) { return k !== CACHE_NAME; })
          .map(function (k) { return caches.delete(k); })
      );
    }).then(function () {
      return self.clients.claim();
    })
  );
});

/* Fetch: stale-while-revalidate for same-origin; network-first for API */
self.addEventListener('fetch', function (e) {
  var url = new URL(e.request.url);

  /* Skip non-GET and admin/api requests — always fresh */
  if (e.request.method !== 'GET') return;
  if (url.pathname.indexOf('/maison-nord/admin/') === 0) return;
  if (url.pathname.indexOf('/maison-nord/api/')   === 0) return;

  /* API-like PHP pages that need fresh data */
  var dynamicPages = [
    'mes-commandes.php', 'profil.php', 'checkout.php',
    'checkout/confirmation.php', 'checkout/callback.php',
    'login.php', 'register.php'
  ];
  var isDynamic = dynamicPages.some(function (p) {
    return url.pathname.indexOf(p) !== -1;
  });

  if (isDynamic) {
    /* Network first, fall back to cache */
    e.respondWith(
      fetch(e.request).then(function (res) {
        return res;
      }).catch(function () {
        return caches.match(e.request);
      })
    );
    return;
  }

  /* Stale-while-revalidate for everything else */
  e.respondWith(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.match(e.request).then(function (cached) {
        var networkFetch = fetch(e.request).then(function (res) {
          if (res && res.status === 200) {
            cache.put(e.request, res.clone());
          }
          return res;
        });
        return cached || networkFetch;
      });
    })
  );
});
