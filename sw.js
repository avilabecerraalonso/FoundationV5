// Define the cache name and files to cache
const CACHE_NAME = 'jspt-v05';
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


importScripts("https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/9.21.0/firebase-messaging.js");


const firebaseConfig = {
    apiKey: "AIzaSyAwniByf6z00j8HOyj0bIkhim4CQNGOsks",
    authDomain: "youngdreamersfortalaigua-01.firebaseapp.com",
    projectId: "youngdreamersfortalaigua-01",
    storageBucket: "youngdreamersfortalaigua-01.appspot.com",
    messagingSenderId: "505716996899",
    appId: "1:505716996899:web:560ff6434ee2ab2841aa96",
    measurementId: "G-G124TRGM7Y"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Get the messaging instance
const messaging = getMessaging(app);

self.addEventListener("push", async (event) => {
  try {
    if ("Notification" in self) {
      // Request permission to show notifications
      const permission = await Notification.requestPermission();
      if (permission === "granted") {
        // Get the registration token
        const currentToken = await getToken(messaging, {
          vapidKey:
            "BKlKTCq5MEuyR3yhY86AkNwetwBJLdNlXRZVks-A0in_hknX1QFvLhOG48XZLO1E6LGqeHjJBXZUYg_phkBFsSU",
          serviceWorkerRegistration: self.registration,
        });
        console.log("Token:", currentToken);
      } else if (permission === "denied") {
        console.log("Permission for notifications was denied");
      } else if (permission === "default") {
        console.log("The permission request was dismissed by the user");
      }
    } else {
      console.log("Notifications are not available");
    }
  } catch (error) {
    if (error.code === "messaging/permission-blocked") {
      console.log("The permission for notifications was blocked");
    } else {
      console.log("Error while subscribing to notifications:", error);
    }
  }
});

// Handle incoming messages
messaging.onMessage((payload) => {
  console.log("[Service Worker] Received message:", payload);

  const { title, body, icon } = payload.notification;

  self.registration.showNotification(title, {
    body,
    icon,
  });
});

if ('firebase' in self && 'messaging' in firebase) {
  firebase.messaging().getToken({vapidKey: 'BKlKTCq5MEuyR3yhY86AkNwetwBJLdNlXRZVks-A0in_hknX1QFvLhOG48XZLO1E6LGqeHjJBXZUYg_phkBFsSU'}).then((currentToken) => {
    if (currentToken) {
      console.log('FCM token:', currentToken);
    } else {
      console.log('No FCM token available.');
    }
  }).catch((error) => {
    console.log('An error occurred while retrieving the FCM token:', error);
  });
} else {
  console.log('Firebase Messaging is not available');
}


// Handle notification clicks
self.addEventListener("notificationclick", (event) => {
  console.log("[Service Worker] Notification click Received.");

  event.notification.close();

  event.waitUntil(clients.openWindow("https://youngdreamersfortalaigua.org"));
});
