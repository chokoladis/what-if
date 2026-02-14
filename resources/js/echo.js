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
        // добавить в шапку
        const parent = $('#navbarNotify');

        let count = parent.find('b.count').text();
        parent.find('b.count').text(count++);
        console.log(parent.find('b.count'), count)
        // todo check for not notify
        const listNotify = $('#navbarNotify + .dropdown-menu');
        console.log(listNotify)
        listNotify.append(`
            <div class="dropdown-item">${e.message}</div>
        `)
        console.log('question', e)
    })

// window.Echo.private(`comment.vote.${window.App.userId}`)
//     .notification( (e) => {
//         console.log('comment', e)
//     })