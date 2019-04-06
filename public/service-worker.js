var CACHE = 'games-gameandme-fr';

// use 'addEventListener' instead of 'onMessage' syntax, it's javascript ninja recommandation
self.addEventListener('install', function(evt) {
	console.log('The service worker is beeing installed');
})


self.addEventListener('fetch', function(evt) {
	//console.log('The service worker intercept the following network\'s request : ', evt.request.url);

	evt.respondWith(
		fromNetwork(evt.request, 3000)
			.catch(function(){
				return fromCache(evt.request);
			})
	);
})


/**
* Handle a request and try it if return error or delay it's too big return cache
*/
function fromNetwork(request, timeout) {
	return new Promise(function (fulfill, reject) {
		var timeoutID = setTimeout(reject, timeout);

		fetch(request).then(function(response){
			//console.log('Network response received', response);
			clearTimeout(timeoutID);
			fulfill(response);

			updateCache(request, response.clone());

		}, reject);
	});
}

/**
* Handle a request and try to return a cache response
*/
function fromCache(request) {
	// console.log('REQUEST THE CACHE');
	return caches.open(CACHE) // just reopen the correct cache
		.then(function(cache) {
			// console.log(request);
			return cache.match(request)
				.then(function (matching){ // result of the match
					// console.log('matching', matching);
					return matching || Promise.reject('no match');
				});
		});
}

/**
* Handle a request and network response and update cache
*/
function updateCache(request, response) {
	// console.log('UPDATE THE CACHE');
	caches.open(CACHE) // just reopen the correct cache
		.then(function(cache) {
			cache.put(request.url, response);
		});
}


/**
* Supprime le cache (sinon on avait des pb lors du reload de la home)
*/
self.addEventListener('activate', function(e) {
  console.log('[ServiceWorker] Activate');
  e.waitUntil(
    caches.keys().then(function(keyList) {
      return Promise.all(keyList.map(function(key) {
        if (key == CACHE ) {
          console.log('[ServiceWorker] Removing old cache', key);
          return caches.delete(key);
        }
      }));
    })
  );
  
  return self.clients.claim();
});

/*
// On install, cache some resource.
self.addEventListener('install', function(evt) {
  console.log('The service worker is being installed.');
  // Open a cache and use `addAll()` with an array of assets to add all of them
  // to the cache. Ask the service worker to keep installing until the
  // returning promise resolves.
  evt.waitUntil(caches.open(CACHE).then(function (cache) {
    cache.addAll([
	  '/favicon.ico',
	  '/images/uk.png',
	  '/images/fr.png',
	  '/images/loader.gif',
	  '/fonts/fontawesome-webfont.eot',
	  '/fonts/fontawesome-webfont.svg',
	  '/fonts/fontawesome-webfont.ttf',
	  '/fonts/fontawesome-webfont.woff',
	  '/fonts/fontawesome-webfont.woff2',
	  '/fonts/FontAwesome.otf',
	  '/images/logo.png',
	  '/css/app.css',
	  '/css/styles.css',
	  '/css/font-awesome.min.css',
	  '/css/jquery-ui.min.css',
	  '/css/jquery.dataTables.min.css',
	  '/js/app.js',
	  '/js/jquery-ui.js',
	  '/js/jquery.ui.datepicker-fr.js',
	  '/js/jquery.dataTables.min.js',
	  '/js/utils.js',
	  '/js/sw.js',
	  '/js/highcharts/highcharts.js'
    ]);
  }));
});


self.addEventListener('activate', function(e) {
  console.log('[ServiceWorker] Activate');
  e.waitUntil(
    caches.keys().then(function(keyList) {
      return Promise.all(keyList.map(function(key) {
        if (key !== cacheName && key !== dataCacheName) {
          console.log('[ServiceWorker] Removing old cache', key);
          return caches.delete(key);
        }
      }));
    })
  );
  
  return self.clients.claim();
});

// On fetch, use cache but update the entry with the latest contents
// from the server.
self.addEventListener('fetch', function(evt) {
  console.log('[Service Worker] Fetch', evt.request.url);
  
   evt.respondWith(
	  caches.match(evt.request).then(function(response) {
		return response || fetch(evt.request);
	  })
   );
});
*/
