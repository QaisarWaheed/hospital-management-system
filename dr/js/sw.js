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
			return caches.match("/404.html");
		})
	);
});

