import axios from 'axios';
window.axios = axios;

window.axios.defaults.baseURL = document.querySelector('meta[name="app-url"]')?.content;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Interceptor para inyectar automáticamente el ID del usuario autenticado en todas las solicitudes
window.axios.interceptors.request.use((config) => {
    try {
        const userStr = localStorage.getItem('auth_user');
        if (userStr) {
            const user = JSON.parse(userStr);
            if (user && user.id_usuario) {
                config.headers['X-User-Id'] = user.id_usuario;
                config.headers['X-User-Role'] = user.rol;
            }
        }
    } catch (e) {}
    return config;
}, (error) => {
    return Promise.reject(error);
});
