const thirtyDays = 30 * 24 * 60 * 60;
workbox.core.skipWaiting();
workbox.core.clientsClaim();

workbox.routing.registerRoute(
    '/',
    new workbox.strategies.StaleWhileRevalidate()
);

workbox.routing.registerRoute(
    new RegExp('\\.html$'),
    new workbox.strategies.NetworkFirst()
);

workbox.routing.registerRoute(
    new RegExp('/.\\.js$'),
    new workbox.strategies.StaleWhileRevalidate()
);

workbox.routing.registerRoute(
    /\/media\/catalog.*\.(?:png|gif|jpg|jpeg|svg)$/,
    new workbox.strategies.StaleWhileRevalidate({
        cacheName: 'catalog',
        plugins: [
            new workbox.expiration.Plugin({
                maxEntries: 60,
                maxAgeSeconds: thirtyDays // 30 Days
            })
        ]
    })
);

workbox.routing.registerRoute(
    /\.(?:png|gif|jpg|jpeg|svg)$/,
    new workbox.strategies.CacheFirst({
        cacheName: 'images',
        plugins: [
            new workbox.expiration.Plugin({
                maxEntries: 60,
                maxAgeSeconds: thirtyDays // 30 Days
            })
        ]
    })
);

workbox.precaching.precacheAndRoute(self.__precacheManifest || []);

// This "catch" handler is triggered when any of the other routes fail to
// generate a response.

// TODO: Add fallbacks

importScripts('/simistatic/config.js');
self.addEventListener('push', function(event) {
    var apiPath = SMCONFIGS.notification_api+ 'pwadevices/message?endpoint=';
    event.waitUntil(
        registration.pushManager.getSubscription()
            .then(function(subscription) {
                if (!subscription || !subscription.endpoint) {
                    throw new Error();
                }
                apiPath = apiPath + encodeURI(subscription.endpoint);
                return fetch(apiPath)
                    .then(function(response) {
                        if (response.status !== 200){
                            console.log("Problem Occurred:"+response.status);
                            throw new Error();
                        }
                        return response.json();
                    })
                    .then(function(data) {
                        data = data[0]
                        if (data.status == 0) {
                            console.error('The API returned an error.', data.error.message);
                            throw new Error();
                        }
                        //console.log(data);
                        var options = {};
                        var title = '';
                        var icon = data.notification.logo_icon ? data.notification.logo_icon : './icons/siminia_circle_512.png';
                        if (data.notification.notice_title){

                            title = data.notification.notice_title;
                            var message = data.notification.notice_content;
                            var url = '/';
                            if (data.notification.notice_url) {
                                url = data.notification.notice_url;
                                if(data.notification.hasOwnProperty('pwa_url') && data.notification.pwa_url){
                                    url = data.notification.pwa_url + '/' + data.notification.notice_url;
                                }
                            }
                            console.log(url);
                            if (data.notification.image_url){
                                options['image'] = data.notification.image_url;
                            }
                            var data = {
                                url: url
                            };
                            options = {
                                body : message,
                                icon: icon,
                                data: data
                            };
                        } else {
                            title = 'New Notification';
                            options = {
                                icon: icon,
                                badge: './icons/siminia_circle_512.png',
                                data: {
                                    url: "/"
                                }
                            };
                        }

                        return self.registration.showNotification(title, options);
                    })
                    .catch(function(err) {
                        console.log(err);
                        return self.registration.showNotification('New Notification', {
                            icon: './icons/siminia_circle_512.png',
                            badge: './icons/siminia_circle_512.png',
                            data: {
                                url: "/"
                            }
                        });
                    });
            })
    );
});
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var url = event.notification.data.url;
    event.waitUntil(
        clients.matchAll({
            type: 'window'
        })
            .then(function(windowClients) {
                for (var i = 0; i < windowClients.length; i++) {
                    var client = windowClients[i];
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});


// check version PWA
if (window.SMCONFIGS) {
    var headers = new Headers({
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        // 'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
        // 'Access-Control-Allow-Headers': 'X-Requested-With,content-type',
        // 'Access-Control-Allow-Credentials': true,
    });
    var init = {cache: 'default', mode: 'cors',headers};
    init['method'] = 'GET';
    var api = window.SMCONFIGS.notification_api + "pwadevices/config"
    var _request = new Request(api, init);
    fetch(_request)
        .then(function (response) {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok');
        })
        .then(function (data) {
            if(data && data.pwa && data.pwa.hasOwnProperty('pwa_studio_client_ver_number') && data.pwa.pwa_studio_client_ver_number && localStorage){
                var pwa_build_time = localStorage.getItem("CLIENT_VER");
                if(!pwa_build_time || pwa_build_time === null){
                    localStorage.setItem("CLIENT_VER",data.pwa.pwa_studio_client_ver_number);
                }else{
                    if(data.pwa.pwa_studio_client_ver_number !== pwa_build_time) {
                        sessionStorage.clear();
                        localStorage.setItem("CLIENT_VER",data.pwa.pwa_studio_client_ver_number);
                        unregister();
                        if(caches){
                            caches.keys().then(function(names) {
                                for (var name of names)
                                    if(name.indexOf('sw-precache') >= 0){
                                        caches.delete(name);
                                    }
                            });
                            window.location.reload();
                        }
                    }
                }
            }
        }).catch((error) => {
        //alert(error.toString());
        console.error(error);
    });
}