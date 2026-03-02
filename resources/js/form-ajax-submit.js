/**
 * Formularios con clase .form-ajax-submit se envían por fetch (sin recarga).
 * El servidor debe devolver JSON: { success, message, redirect } o { success: false, message }.
 * En éxito: se muestra toast y se redirige a redirect (si existe).
 */
(function () {
    function init() {
        document.querySelectorAll('form.form-ajax-submit').forEach(function (form) {
            if (form.dataset.ajaxBound) return;
            form.dataset.ajaxBound = '1';
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const confirmMsg = form.getAttribute('data-confirm');
                if (confirmMsg && !window.confirm(confirmMsg)) return;
                const submitBtn = form.querySelector('[type="submit"]');
                const originalText = submitBtn?.innerHTML;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    if (submitBtn.dataset.loadingText) submitBtn.innerHTML = submitBtn.dataset.loadingText;
                }
                const fd = new FormData(form);
                const url = form.getAttribute('action') || window.location.href;
                const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'post').toUpperCase();
                const opts = {
                    method: method,
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                };
                fetch(url, opts)
                    .then(function (res) {
                        return res.json().then(function (data) {
                            if (!res.ok && res.status === 422 && data.errors) {
                                const msg = typeof data.message === 'string' ? data.message : Object.values(data.errors).flat().join(' ');
                                return { ok: false, status: res.status, data: { success: false, message: msg || 'Errores de validación.' } };
                            }
                            return { ok: res.ok, status: res.status, data: data };
                        }).catch(function () {
                            return { ok: false, status: res.status, data: { success: false, message: 'Error en la respuesta del servidor.' } };
                        });
                    })
                    .then(function (result) {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            if (originalText) submitBtn.innerHTML = originalText;
                        }
                        const data = result.data || {};
                        const msg = data.message || (result.ok ? 'Guardado correctamente.' : 'Error al procesar.');
                        if (result.ok && data.success !== false) {
                            if (typeof window.showToast === 'function') window.showToast(msg, 'success');
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        } else {
                            if (typeof window.showToast === 'function') window.showToast(msg, 'error');
                        }
                    })
                    .catch(function (err) {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            if (originalText) submitBtn.innerHTML = originalText;
                        }
                        if (typeof window.showToast === 'function') window.showToast('Error de conexión. Intente de nuevo.', 'error');
                    });
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    document.addEventListener('turbo:render', init);
})();
