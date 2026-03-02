/**
 * Toast global: showToast(message, type)
 * type: 'success' | 'error' | 'warning' | 'info'
 */
(function () {
    const DURATION = 4500;

    function getContainer() {
        let el = document.getElementById('toast-container');
        if (!el) {
            el = document.createElement('div');
            el.id = 'toast-container';
            el.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none';
            el.setAttribute('aria-live', 'polite');
            document.body.appendChild(el);
        }
        return el;
    }

    const typeStyles = {
        success: 'border-green-500/80 bg-green-50 dark:bg-green-900/30 dark:border-green-500/50 text-green-800 dark:text-green-200 shadow-lg',
        error: 'border-red-500/80 bg-red-50 dark:bg-red-900/30 dark:border-red-500/50 text-red-800 dark:text-red-200 shadow-lg',
        warning: 'border-amber-500/80 bg-amber-50 dark:bg-amber-900/30 dark:border-amber-500/50 text-amber-800 dark:text-amber-200 shadow-lg',
        info: 'border-blue-500/80 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-500/50 text-blue-800 dark:text-blue-200 shadow-lg',
    };

    const icons = {
        success: '<svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        warning: '<svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
    };

    function showToast(message, type = 'info') {
        if (!message) return;
        const container = getContainer();
        const style = typeStyles[type] || typeStyles.info;
        const icon = icons[type] || icons.info;
        const div = document.createElement('div');
        div.className = 'pointer-events-auto flex items-start gap-3 rounded-lg border p-3 ' + style + ' animate-toast-in';
        div.setAttribute('role', 'alert');
        div.innerHTML = '<span class="flex-shrink-0">' + icon + '</span><p class="text-sm font-medium flex-1">' + escapeHtml(String(message)) + '</p>';
        container.appendChild(div);

        const remove = () => {
            div.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => {
                if (div.parentNode) div.parentNode.removeChild(div);
            }, 200);
        };

        const t = setTimeout(remove, DURATION);
        div.addEventListener('click', () => {
            clearTimeout(t);
            remove();
        });
    }

    function escapeHtml(text) {
        const span = document.createElement('span');
        span.textContent = text;
        return span.innerHTML;
    }

    window.showToast = showToast;

    function showFlashToasts() {
        document.querySelectorAll('.flash-toast').forEach(function (flash) {
            const type = (flash.getAttribute('data-type') || 'info');
            const msg = flash.getAttribute('data-msg') || '';
            if (msg) showToast(msg, type);
            flash.remove();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showFlashToasts);
    } else {
        showFlashToasts();
    }
})();
