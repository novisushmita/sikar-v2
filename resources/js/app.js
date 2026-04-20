import './bootstrap';
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
  apiKey: "AIzaSyB_5ifKdVu6GOHImKXAUG-T3AntMqLqGmA",
  authDomain: "sikar-a9a0d.firebaseapp.com",
  projectId: "sikar-a9a0d",
  storageBucket: "sikar-a9a0d.firebasestorage.app",
  messagingSenderId: "53317808040",
  appId: "1:53317808040:web:d93a9dd998a4c2604df79d"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Saat web TERBUKA - tampilkan notif via service worker
// onMessage(messaging, (payload) => {
//   console.log('Message received (foreground): ', payload);
  
//   if (document.visibilityState === 'visible') {
//     const { title, body } = payload.notification;
//     navigator.serviceWorker.ready.then((registration) => {
//       registration.showNotification(title, {
//         body: body,
//         icon: '/images/logo_pge_192.png',
//         vibrate: [200, 100, 200]
//       });
//     });
//   }
// });


function sendTokenToServer(token) {
  var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  let formData = new FormData();
  formData.append('web_token', token);
  fetch('/tokenweb', {
    headers: { 'X-CSRF-TOKEN': csrf },
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  }).then(res => {
    console.log('Status:', res.status);
  });
}

function requestPermission() {
  Notification.requestPermission().then((permission) => {
    if (permission === 'granted') {
      console.log('Notification permission granted.');
    } else {
      alert("Silakan izinkan notifikasi untuk mendapatkan notifikasi terbaru");
    }
  });
}

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js', { scope: '/' })
    .then((swRegistration) => {
      console.log('Service worker registration succeeded:', swRegistration);

      const sw = swRegistration.installing || swRegistration.waiting || swRegistration.active;

      if (swRegistration.active) {
        getTokenFCM(swRegistration);
      } else {
        sw.addEventListener('statechange', (e) => {
          if (e.target.state === 'activated') {
            getTokenFCM(swRegistration);
          }
        });
      }
    }).catch((err) => {
      console.log('Service worker registration failed:', err);
    });
}

function getTokenFCM(swRegistration) {
  getToken(messaging, {
    vapidKey: 'BKo5Cu1-VGrVoZinh51B3Xy4OI4ZH2tSA0UMmSWrQiG4j0x-Y-ygi7rFJSqik679iuxcZuDRDPFKmkrnz7M4nKM',
    serviceWorkerRegistration: swRegistration
  }).then((currentToken) => {
    if (currentToken) {
      console.log('FCM Token:', currentToken);
      sendTokenToServer(currentToken);
    } else {
      requestPermission();
      console.log('No registration token available.');
    } 
  }).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
  });
}