// Service worker v3 — cache login page only; never intercept app module PHP pages.
var CACHE_NAME = 'sw-cache-v3';

self.addEventListener('install', function (event) {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.add('index.php').catch(function () {});
    })
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys.filter(function (k) { return k !== CACHE_NAME; }).map(function (k) {
          return caches.delete(k);
        })
      );
    }).then(function () {
      return self.clients.claim();
    })
  );
});

function shouldBypassServiceWorker(url) {
  var path = url.pathname;
  if (/\.php$/i.test(path)) {
    return true;
  }
  if (/\/(bk|dr|hr|fr|sm|mm|lab|pharmecy)\//i.test(path) || /\/fr\//i.test(path)) {
    return true;
  }
  if (/print_|report_query_timing|comparison_report|progress_report|gynae_registeration/i.test(path)) {
    return true;
  }
  return false;
}

self.addEventListener('fetch', function (event) {
  if (event.request.method !== 'GET') {
    return;
  }
  var url = new URL(event.request.url);
  if (shouldBypassServiceWorker(url)) {
    return;
  }
  var path = url.pathname.replace(/\/+$/, '') || '/';
  if (path !== '/index.php' && path !== '/') {
    return;
  }
  event.respondWith(
    caches.match(event.request).then(function (response) {
      return response || fetch(event.request);
    })
  );
});
