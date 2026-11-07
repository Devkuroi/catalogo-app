/**
 * Utilidades comunes de formularios:
 *  - Mostrar/ocultar contraseña
 *  - Validación con estilos de Bootstrap
 *  - Estado de "enviando" en el botón (evita doble envío)
 */
(function () {
    'use strict';

    // Mostrar / ocultar contraseña
    document.querySelectorAll('.pass-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            var visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            btn.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
            btn.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
            input.focus();
        });
    });

    // Al editar un campo marcado por el servidor, quitar el error
    document.querySelectorAll('.form-control.is-invalid, .form-check-input.is-invalid').forEach(function (el) {
        el.addEventListener('input', function () { el.classList.remove('is-invalid'); }, { once: true });
        el.addEventListener('change', function () { el.classList.remove('is-invalid'); }, { once: true });
    });

    // Validación al enviar
    document.querySelectorAll('form.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (typeof form.validarExtra === 'function') form.validarExtra();

            if (!form.checkValidity()) {
                ev.preventDefault();
                ev.stopPropagation();
                form.classList.add('was-validated');
                var primero = form.querySelector(':invalid:not(fieldset)');
                if (primero) {
                    primero.focus({ preventScroll: true });
                    primero.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            var btn = form.querySelector('.btn-submit');
            if (btn) {
                btn.disabled = true;
                btn.querySelector('.spinner-border').classList.remove('d-none');
                btn.querySelector('.btn-label').textContent = 'Procesando…';
            }
        });
    });
})();
