import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.private(`App.Models.User.${window.App.userId}`)
    .notification(function (e){
        writeNotify(e)

        const notifySound = new Audio('/storage/sound/notification.mp3');
        notifySound.play()
    })

function writeNotify(eventNotify)
{
    const parent = $('#navbarNotify');

    if (!parent.find('b.count').length) {
        parent.append(`<span class="position-absolute bottom-10 start-0 translate-middle badge rounded-pill bg-danger">
           <b class="count">1</b>
            </span>`)
    } else {
        let count = parent.find('b.count').text();
        parent.find('b.count').text(++count);
    }

    let navItemParent = parent.parent('.nav-item')
    let listNotify = navItemParent.find('.dropdown-menu')

    if (navItemParent.find('.notify-is-empty').length) {
        listNotify.empty()
    }

    listNotify.append(`<div class="dropdown-item">${eventNotify.message}</div>`)
}