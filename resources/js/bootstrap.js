// Axios se carga desde el layout (CDN) para que esté disponible antes de los scripts en línea.
// Aquí solo configuramos defaults e interceptor CSRF.
if (typeof window.axios !== 'undefined') {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    window.axios.defaults.headers.common['Accept'] = 'application/json';
    window.axios.interceptors.request.use(function (config) {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) config.headers['X-CSRF-TOKEN'] = meta.getAttribute('content');
        return config;
    });
}
