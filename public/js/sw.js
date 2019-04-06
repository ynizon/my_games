/* Enregistrement du service worker */

if ('serviceWorker' in navigator) {
  navigator.serviceWorker
    .register('/service-worker.js')
    .then(function(reg) {
      // suivre l'état de l'enregistrement du Service Worker : `installing`, `waiting`, `active`
	  console.log('Service Worker Registered');
    });
}