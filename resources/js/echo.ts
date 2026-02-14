import Echo from 'laravel-echo'
const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: 'localhost',
  wsPort: 8080, // TODO: colocar no .env
  forceTLS: false,
  withCredentials: true,
  enabledTransports: ['ws', 'wss'],
});

export default echo
