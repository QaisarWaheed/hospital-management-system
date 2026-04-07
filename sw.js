// On install - caching the application shell
self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open('sw-cache').then(function(cache) {
      // cache any static files that make up the application shell
      return cache.add('index.php');
    })
  );
});

// On network request
self.addEventListener('fetch', function(event) {
  event.respondWith(
    // Try the cache
    caches.match(event.request).then(function(response) {
      //If response found return it, else fetch again
      return response || fetch(event.request);
    })
  );
});

self.addEventListener('fetch', evt => {
	evt. respondwith(
		caches.match(evt.request).then(cacheRes => {
			return cacheRes || fetch(evt.request).then(fetchRes => {
				return caches.open(dynamicCacheName).then(cache => {
				cache.put(evt.request.url, fetchRes.clone());
				limitCacheSize(dynamicCacheName, 15);
				return fetchRes;
				})
			});
		}).catch(() => {
			return caches.match("/404.php");
		})
	);
});
