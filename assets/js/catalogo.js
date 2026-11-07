/**
 * Catálogo: búsqueda, filtro por categoría y ordenamiento en el cliente.
 */
(function () {
    'use strict';

    var grid = document.getElementById('grid');
    if (!grid) return;

    var buscar = document.getElementById('buscar');
    var ordenar = document.getElementById('ordenar');
    var chips = document.querySelectorAll('.chip');
    var vacio = document.getElementById('sin-resultados');
    var items = Array.prototype.slice.call(grid.querySelectorAll('.producto'));
    var categoria = '';

    function normalizar(t) {
        return (t || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
    }

    function aplicar() {
        var q = normalizar(buscar.value);
        var visibles = 0;

        items.forEach(function (el) {
            var ok = (!categoria || el.dataset.categoria === categoria) &&
                     (!q || normalizar(el.dataset.nombre).indexOf(q) !== -1);
            el.classList.toggle('d-none', !ok);
            if (ok) visibles++;
        });

        var criterio = ordenar.value;
        items.slice().sort(function (a, b) {
            var A = a.dataset, B = b.dataset;
            switch (criterio) {
                case 'descuento':   return B.descuento - A.descuento;
                case 'precio-asc':  return A.precio - B.precio;
                case 'precio-desc': return B.precio - A.precio;
                default:            return A.fin - B.fin || A.orden - B.orden;
            }
        }).forEach(function (el) { grid.appendChild(el); });

        vacio.classList.toggle('d-none', visibles > 0);
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) { c.classList.remove('active'); c.setAttribute('aria-pressed', 'false'); });
            chip.classList.add('active');
            chip.setAttribute('aria-pressed', 'true');
            categoria = chip.dataset.categoria;
            aplicar();
        });
    });

    buscar.addEventListener('input', aplicar);
    ordenar.addEventListener('change', aplicar);

    document.getElementById('limpiar-filtros').addEventListener('click', function () {
        buscar.value = '';
        chips[0].click();
        buscar.focus();
    });

    /* ================= Manejo robusto de botones 'Ver detalles' ================= */
    document.querySelectorAll('.btn-ver-detalles').forEach(function (btn) {
        btn.addEventListener('click', function (ev) {
            var targetSelector = btn.getAttribute('data-bs-target');
            if (!targetSelector) return;
            var modalEl = document.querySelector(targetSelector);
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            }
        });
    });
})();
