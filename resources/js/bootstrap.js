import axios from 'axios';
import { router } from '@inertiajs/vue3';
window.axios = axios;
// const token = '10|Qn9ZdJufJ5htNcOnl971uFYLfOn2eYHHLSVzU1Rtc546c47c';
// window.axios.defaults.headers.common['Authorization'] = token ? `Bearer ${token}` : null;
//window.axios.defaults.baseURL = 'http://192.168.0.103:9000'
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
//window.axios.defaults.withCredentials = true;
// axios.defaults.withXSRFToken = true;

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            router.visit(route('login'));
        }

        return Promise.reject(error);
    }
);
