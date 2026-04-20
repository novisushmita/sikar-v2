// ===================================
// FIREBASE MESSAGING (BACKGROUND NOTIF)
// ===================================
importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyB_5ifKdVu6GOHImKXAUG-T3AntMqLqGmA",
    authDomain: "sikar-a9a0d.firebaseapp.com",
    projectId: "sikar-a9a0d",
    storageBucket: "sikar-a9a0d.firebasestorage.app",
    messagingSenderId: "53317808040",
    appId: "1:53317808040:web:d93a9dd998a4c2604df79d"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log('[sw.js] Background message received: ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/logo_pge_192.png',
        vibrate: [200, 100, 200]
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    //Otomatis pakai URL apapun yang sedang dipakai (localhost atau ngrok)
    const urlToOpen = self.registration.scope;
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                for (const client of clientList) {
                    if (client.url.startsWith(self.registration.scope) && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// ===================================
// PWA OFFLINE CACHING
// ===================================
const filesToCache = ['/', '/offline.html'];

const preLoad = function() {
    return caches.open("offline").then(function(cache) {
        return cache.addAll(filesToCache);
    });
};

self.addEventListener("install", function(event) {
    event.waitUntil(preLoad());
    self.skipWaiting(); // ← tambah ini supaya langsung aktif
});

self.addEventListener("activate", function(event) {
    event.waitUntil(clients.claim()); // ← tambah ini
});

const checkResponse = function(request) {
    return new Promise(function(fulfill, reject) {
        fetch(request).then(function(response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject();
            }
        }, reject);
    });
};

const addToCache = function(request) {
    if (!request.url.startsWith('http')) {
        return Promise.resolve();
    }
    return caches.open("offline").then(function(cache) {
        return fetch(request).then(function(response) {
            return cache.put(request, response);
        });
    });
};

const returnFromCache = function(request) {
    return caches.open("offline").then(function(cache) {
        return cache.match(request).then(function(matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

self.addEventListener("fetch", function(event) {
    event.respondWith(checkResponse(event.request).catch(function() {
        return returnFromCache(event.request);
    }));
    if (!event.request.url.startsWith('http')) {
        event.waitUntil(addToCache(event.request));
    }
});