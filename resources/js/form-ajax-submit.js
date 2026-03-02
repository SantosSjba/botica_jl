/**
 * Formularios con clase .form-ajax-submit se envían por Axios (sin recarga).
 * El servidor debe devolver JSON: { success, message, redirect } o { success: false, message }.
 * En éxito: se muestra toast y se redirige a redirect (si existe) tras un breve retraso.
 */
(function () {
    const REDIRECT_DELAY_MS = 1200;

    function getAxios() {
        if (typeof window.axios !== 'undefined') return window.axios;
        return null;
    }

    function init() {
        var axios = getAxios();
        if (!axios) return;

        document.querySelectorAll('form.form-ajax-submit').forEach(function (form) {
            if (form.dataset.ajaxBound) return;
            form.dataset.ajaxBound = '1';

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var confirmMsg = form.getAttribute('data-confirm');
                if (confirmMsg && !window.confirm(confirmMsg)) return;

                var submitBtn = form.querySelector('[type="submit"]');
                var originalText = submitBtn ? submitBtn.innerHTML : null;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    if (submitBtn.dataset.loadingText) submitBtn.innerHTML = submitBtn.dataset.loadingText;
                }

                var url = form.getAttribute('action') || window.location.href;
                var fd = new FormData(form);

                // Siempre POST: _method (PUT/PATCH/DELETE) va en el body; Laravel lo interpreta.
                axios.post(url, fd)
                    .then(function (res) {
                        var data = res.data || {};
                        var msg = data.message || 'Guardado correctamente.';
                        if (data.success !== false && data.redirect) {
                            if (typeof window.showToast === 'function') window.showToast(msg, 'success');
                            setTimeout(function () {
                                window.location.href = data.redirect;
                            }, REDIRECT_DELAY_MS);
                        } else if (data.success !== false) {
                            if (typeof window.showToast === 'function') window.showToast(msg, 'success');
                        } else {
                            if (typeof window.showToast === 'function') window.showToast(msg || 'Error al procesar.', 'error');
                        }
                    })
                    .catch(function (err) {
                        var data = err.response && err.response.data ? err.response.data : {};
                        var msg = data.message || 'Error al procesar.';
                        if (err.response && err.response.status === 422 && data.errors) {
                            msg = typeof data.message === 'string' ? data.message : Object.values(data.errors).flat().join(' ');
                        }
                        if (typeof window.showToast === 'function') window.showToast(msg, 'error');
                    })
                    .finally(function () {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            if (originalText) submitBtn.innerHTML = originalText;
                        }
                    });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
