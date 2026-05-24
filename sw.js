// Minimal service worker — cache login shell only; never intercept BK reports or POST.
self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open('sw-cache').then(function (cache) {
      return cache.add('index.php').catch(function () {});
    })
  );
});

function shouldBypassServiceWorker(url) {
  var path = url.pathname;
  if (path.indexOf('/bk/') !== -1) {
    return true;
  }
  if (/print_|report_query_timing|comparison_report|progress_report/i.test(path)) {
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
  event.respondWith(
    caches.match(event.request).then(function (response) {
      return response || fetch(event.request);
    })
  );
});
