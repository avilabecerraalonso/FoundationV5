importScripts('https://www.gstatic.com/firebasejs/9.21.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.21.0/firebase-messaging-compat.js');


try {
  firebase.initializeApp({
    apiKey: "AIzaSyAwniByf6z00j8HOyj0bIkhim4CQNGOsks",
    authDomain: "youngdreamersfortalaigua-01.firebaseapp.com",
    projectId: "youngdreamersfortalaigua-01",
    storageBucket: "youngdreamersfortalaigua-01.appspot.com",
    messagingSenderId: "505716996899",
    appId: "1:505716996899:web:560ff6434ee2ab2841aa96",
    measurementId: "G-G124TRGM7Y"
  });
} catch (error) {
  console.log('Error initializing Firebase:', error);
}

// Request permission to show notifications and subscribe the user
self.addEventListener('push', async (event) => {
  try {
    if ('Notification' in self) {
      // Request permission to show notifications
      const permission = await Notification.requestPermission();
      if (permission === 'granted') {
        if ('firebase' in self && 'messaging' in firebase) {
          // Get the registration token
          const currentToken = await firebase.messaging().getToken({
            vapidKey: 'BBKlKTCq5MEuyR3yhY86AkNwetwBJLdNlXRZVks-A0in_hknX1QFvLhOG48XZLO1E6LGqeHjJBXZUYg_phkBFsSU',
            serviceWorkerRegistration: self.registration
          });
          console.log('Token:', currentToken);
        } else {
          console.log('Firebase Messaging is not available');
        }
      } else if (permission === 'denied') {
        console.log('Permission for notifications was denied');
      } else if (permission === 'default') {
        console.log('The permission request was dismissed by the user');
      }
    } else {
      console.log('Notifications are not available');
    }
  } catch (error) {
    if (error.code === 'messaging/permission-blocked') {
      console.log('The permission for notifications was blocked');
    } else {
      console.log('Error while subscribing to notifications:', error);
    }
  }
});

// Handle incoming messages
if ('firebase' in self && 'messaging' in firebase) {
  firebase.messaging().onMessage((payload) => {
    console.log('[Service Worker] Received message:', payload);

    const { title, body, icon } = payload.notification;

    self.registration.showNotification(title, {
      body,
      icon,
    });
  });
} else {
  console.log('Firebase Messaging is not available');
}

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  console.log('[Service Worker] Notification click Received.');

  event.notification.close();

  event.waitUntil(clients.openWindow('https://youngdreamersfortalaigua.org'));
});

firebase.messaging().getToken().then(function (currentToken) {
  if (currentToken) {
    console.log('FCM token:', currentToken);
    // Send the token to your server or save it for later use
  } else {
    console.log('No FCM token available.');
  }
}).catch(function (error) {
  console.log('An error occurred while retrieving the FCM token:', error);
});
