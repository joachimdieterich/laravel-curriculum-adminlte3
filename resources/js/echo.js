import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

let echo = {};

const shouldConnectToWS = import.meta.env.VITE_WEBSOCKET_APP_ACTIVE == true;
if (shouldConnectToWS === true) {
    window.Pusher = Pusher;

    const echoOptions = {
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['websocket', 'ws', 'wss'],
        csrfToken: document.head.querySelector('meta[name="csrf-token"]'),
        enableLogging: true, // Aktiviert Logging
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')
            }
        }
    };
    echo = new Echo(
        {
            ...echoOptions,
            ...{
                Pusher
            }
        }
    );
}

export default echo;
