/**
 * Panel admin: filtro rápido de la tabla de usuarios.
 */
(function () {
    'use strict';

    var filtro = document.getElementById('filtro-usuarios');
    var filas = document.querySelectorAll('#tabla-usuarios tbody tr');

    function normalizar(t) {
        return (t || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    if (filtro) {
        filtro.addEventListener('input', function () {
            var q = normalizar(filtro.value.trim());
            filas.forEach(function (tr) {
                tr.classList.toggle('d-none', q && normalizar(tr.textContent).indexOf(q) === -1);
            });
        });
    }
})();
