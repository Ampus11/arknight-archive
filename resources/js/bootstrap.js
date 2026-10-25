// Import this file to bootstrap your application's JavaScript dependencies.
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
