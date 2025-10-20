/* public/firebase-messaging-sw.js */

// Use compat builds for simplicity across all devices
importScripts('https://www.gstatic.com/firebasejs/9.6.11/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.11/firebase-messaging-compat.js');

// Your Firebase config
firebase.initializeApp({
    apiKey: "AIzaSyAZCa6o-DPX4NWxjZJlBIzKnjrlDz3l7YM",
    authDomain: "flettonchatbot.firebaseapp.com",
    projectId: "flettonchatbot",
    storageBucket: "flettonchatbot.appspot.com",
    messagingSenderId: "691698478961",
    appId: "1:691698478961:web:293a80ed82b837e32210d0",
    measurementId: "G-E6G35MR752"
});

const messaging = firebase.messaging();

// ✅ Background message (Android & Desktop)
messaging.onBackgroundMessage(function (payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const n = payload.notification || {};
    const d = payload.data || {};

    const title = n.title || d.title || 'Notification';
    const options = {
        body: n.body || d.body || '',
        icon: n.icon || d.icon || '/logo.png',
        data: {
            url: (payload.webpush?.fcm_options?.link) || d.click_action || '/'
        }
    };

    return self.registration.showNotification(title, options);
});

// ✅ iOS-compatible notification click (PWA)
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const target = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: "window", includeUncontrolled: true }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(target);
            }
        })
    );
});
