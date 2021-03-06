// [Working example](/serviceworker-cookbook/offline-fallback/).

self.addEventListener('install', function(event) {
    // Put `offline.html` page into cache
    var offlineRequest = new Request('offline.html');
    event.waitUntil(
        fetch(offlineRequest).then(function(response) {
            return caches.open('offline').then(function(cache) {
                return cache.put(offlineRequest, response);
            });
        })
    );
});

self.addEventListener('fetch', function(event) {
    // Only fall back for HTML documents.
    var request = event.request;
    //
    if (request.method === 'GET' && request.headers.get('accept').includes('text/html')) {
        // `fetch()` will use the cache when possible, to this examples
        // depends on cache-busting URL parameter to avoid the cache.
        event.respondWith(
            fetch(request).catch(function(error) {
                // `fetch()` throws an exception when the server is unreachable but not
                // for valid HTTP responses, even `4xx` or `5xx` range.

                return caches.open('offline').then(function(cache) {
                    return cache.match('offline.html');
                });
            })
        );
    }
    // Any other handlers come here. Without calls to `event.respondWith()` the
    // request will be handled without the ServiceWorker.
});
