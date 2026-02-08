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

window.Echo.channel('question.vote')
    .listen('Broadcast\\Question\\Vote', function (e){
        console.log('question', e)
    })

// window.Echo.channel('comment.vote')
//     .listen('Broadcast\\Comment\\Vote', function (e){
//         console.log('comment', e)
//     })

window.Echo.private(`comment.vote.${window.App.userId}`)
    .notification( (e) => {
        console.log('comment', e)
    })