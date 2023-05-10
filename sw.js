// Define the cache name and files to cache
const CACHE_NAME = 'jspt-v06';
const urlsToCache = [
  '/',
  '/index.php',
  '/assets/css/main.css',
  '/assets/js/app.js'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', (event) => {
  const shouldNotBeCached = [
    '/dashboard',
    '/account',
    '/list',
    '/aboutus',
    '/badgets',
    '/contitutionalact',
    '/donations',
    '/error',
    '/gbadgets',
    '/forgot',
    '/new',
    '/news',
    '/recover',
    '/statutes',
    '/search'
  ];

  const requestURL = new URL(event.request.url);
  if (!shouldNotBeCached.some(url => requestURL.pathname.endsWith(url))) {
    event.respondWith(
      caches.match(event.request)
        .then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }

          return fetch(event.request)
            .then((response) => {
              if (!response || response.status !== 200 || response.type !== 'basic') {
                return response;
              }

              const responseToCache = response.clone();

              caches.open(CACHE_NAME)
                .then((cache) => {
                  cache.put(event.request, responseToCache);
                });

              return response;
            });
        })
    );
  }
});

self.addEventListener('activate', (event) => {
  const cacheWhitelist = [CACHE_NAME];

  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

//



importScripts('https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/9.21.0/firebase-messaging.js');


firebase.initializeApp({
    apiKey: "AIzaSyAwniByf6z00j8HOyj0bIkhim4CQNGOsks",
    authDomain: "youngdreamersfortalaigua-01.firebaseapp.com",
    projectId: "youngdreamersfortalaigua-01",
    storageBucket: "youngdreamersfortalaigua-01.appspot.com",
    messagingSenderId: "505716996899",
    appId: "1:505716996899:web:560ff6434ee2ab2841aa96",
    measurementId: "G-G124TRGM7Y"
});

// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();

// Handle background message
messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: '/firebase-logo.png'
  };

  self.registration.showNotification(notificationTitle,
    notificationOptions);
});

// Handle notification click event
self.addEventListener('notificationclick', function(event) {
  console.log('On notification click: ', event.notification.tag);
  // Android doesn’t close the notification when you click on it
  // See: http://crbug.com/463146
  event.notification.close();

  // This looks to see if the current is already open and
  // focuses if it is
  event.waitUntil(clients.matchAll({
    type: "window"
  }).then(function(clientList) {
    for (let i = 0; i < clientList.length; i++) {
      const client = clientList[i];
      if (client.url === '/' && 'focus' in client)
        return client.focus();
    }
    if (clients.openWindow) {
      return clients.openWindow('/');
    }
  }));
});
