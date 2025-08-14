import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const echoOptions = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['websocket', 'ws', 'wss'],
};
const pusherOptions = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['websocket', 'ws', 'wss'],
    cluster: ''
};
const echo = new Echo({...echoOptions, ...{client: new Pusher(pusherOptions.key, pusherOptions)}});

export default echo;
// curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket"  -H "Host: localhost" -H "Origin:http://localhost" "http://127.0.0.1:8080"
// curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" -H "Sec-WebSocket-Key: IxA5/OSZd4AJO6brdOcpNA==" -H "Sec-WebSocket-Version: 13" -H "Host: 127.0.0.1:8000" -H "Origin:http://127.0.0.1:8000" "http://127.0.0.1:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false"
