/**
 * Registro:
 *  - Autocompletado accesible de ciudad (Colombia y España)
 *  - Si la ciudad es conocida, el país se completa y se BLOQUEA
 *  - Documento adaptado al país (Cédula / DNI-NIE)
 *  - Validación en vivo de nombre y contraseña
 */
(function () {
    'use strict';

    var datos = JSON.parse(document.getElementById('datos-ubicacion').textContent);
    var PAISES = datos.paises;
    var CIUDADES = datos.ciudades.map(function (c) {
        c.clave = normalizar(c.ciudad);
        return c;
    });

    var form = document.getElementById('form-registro');
    var ciudad = document.getElementById('ciudad');
    var lista = document.getElementById('lista-ciudades');
    var paisHidden = document.getElementById('pais');
    var grupoPais = document.getElementById('pais-grupo');
    var radios = Array.prototype.slice.call(grupoPais.querySelectorAll('input[type=radio]'));
    var notaBloqueo = document.getElementById('pais-bloqueado');
    var notaBloqueoTexto = document.getElementById('pais-bloqueado-texto');
    var paisError = document.getElementById('pais-error');
    var doc = document.getElementById('numero_documento');

    var sugerencias = [];
    var activo = -1;
    var paisAutomatico = false; // true si el país lo puso el autocompletado
    var seleccion = null;       // última ciudad elegida de la lista (resuelve ciudades repetidas)

    radios.forEach(function (r) { r.required = true; });

    /* ================= Utilidades ================= */

    function normalizar(texto) {
        return (texto || '').normalize('NFD').replace(/[̀-ͯ]/g, '')
            .toLowerCase().trim().replace(/\s+/g, ' ');
    }

    function escapar(t) {
        var d = document.createElement('div');
        d.textContent = t;
        return d.innerHTML;
    }

    function resaltar(texto, consulta) {
        var i = normalizar(texto).indexOf(consulta);
        if (!consulta || i < 0) return escapar(texto);
        return escapar(texto.slice(0, i)) + '<mark>' + escapar(texto.slice(i, i + consulta.length)) + '</mark>' +
            escapar(texto.slice(i + consulta.length));
    }

    function codigo(pais) { return PAISES[pais].codigo.toLowerCase(); }

    /* ================= País: bloquear / desbloquear ================= */

    function marcarPais(pais) {
        paisHidden.value = pais || '';
        radios.forEach(function (r) { r.checked = r.value === pais; });
        if (pais) ocultarErrorPais();
        actualizarDocumento();
    }

    function bloquearPais(info) {
        marcarPais(info.pais);
        paisAutomatico = true;
        radios.forEach(function (r) { r.disabled = true; });
        grupoPais.classList.add('is-locked');
        notaBloqueoTexto.textContent = 'País completado según la ciudad: ' + info.ciudad + ' (' + info.region + ').';
        notaBloqueo.classList.remove('d-none');
    }

    function desbloquearPais(limpiar) {
        var estabaBloqueado = grupoPais.classList.contains('is-locked');
        radios.forEach(function (r) { r.disabled = false; });
        grupoPais.classList.remove('is-locked');
        notaBloqueo.classList.add('d-none');
        if (limpiar && estabaBloqueado && paisAutomatico) marcarPais('');
        paisAutomatico = false;
    }

    function ocultarErrorPais() {
        paisError.classList.add('d-none');
        grupoPais.classList.remove('is-invalid');
    }

    function mostrarErrorPais(texto) {
        paisError.textContent = texto;
        paisError.classList.remove('d-none');
        grupoPais.classList.add('is-invalid');
    }

    radios.forEach(function (r) {
        r.addEventListener('change', function () { marcarPais(r.value); });
    });

    document.getElementById('cambiar-ciudad').addEventListener('click', function () {
        ciudad.value = '';
        seleccion = null;
        desbloquearPais(true);
        ciudad.focus();
    });

    /* ================= Evaluar ciudad escrita ================= */

    function evaluarCiudad(alSalir) {
        var clave = normalizar(ciudad.value);
        var coincidencias = CIUDADES.filter(function (c) { return c.clave === clave; });

        if (seleccion && seleccion.clave === clave) {
            bloquearPais(seleccion);
        } else if (coincidencias.length === 1) {
            if (alSalir) ciudad.value = coincidencias[0].ciudad; // nombre con tildes correctas
            bloquearPais(coincidencias[0]);
        } else if (coincidencias.length > 1) {
            // Ej: Cartagena (Colombia y España): el usuario debe elegir
            desbloquearPais(true);
            var paises = coincidencias.map(function (c) { return c.pais; });
            if (paises.indexOf(paisHidden.value) === -1) {
                marcarPais('');
                if (alSalir) mostrarErrorPais(coincidencias[0].ciudad + ' existe en ' + paises.join(' y ') + '. Elige el país.');
            }
        } else {
            desbloquearPais(true);
        }
    }

    /* ================= Lista de sugerencias (combobox) ================= */

    function buscar(consulta) {
        if (!consulta) return [];
        var empiezan = [], contienen = [];
        CIUDADES.forEach(function (c) {
            var i = c.clave.indexOf(consulta);
            if (i === 0) empiezan.push(c);
            else if (i > 0) contienen.push(c);
        });
        return empiezan.concat(contienen).slice(0, 8);
    }

    function pintar() {
        var consulta = normalizar(ciudad.value);
        sugerencias = buscar(consulta);
        activo = -1;

        if (!consulta) return cerrar();

        if (!sugerencias.length) {
            lista.innerHTML = '<li class="combo-empty">No encontramos "' + escapar(ciudad.value) +
                '". Puedes continuar y elegir el país manualmente.</li>';
        } else {
            lista.innerHTML = sugerencias.map(function (c, i) {
                return '<li class="combo-item" role="option" id="opcion-' + i + '" data-i="' + i + '" aria-selected="false">' +
                    '<span class="flag flag-' + codigo(c.pais) + '" aria-hidden="true"></span>' +
                    '<span><span class="combo-city">' + resaltar(c.ciudad, consulta) + '</span><br>' +
                    '<span class="combo-region">' + escapar(c.region) + ', ' + escapar(c.pais) + '</span></span></li>';
            }).join('');
        }
        lista.hidden = false;
        ciudad.setAttribute('aria-expanded', 'true');
    }

    function cerrar() {
        lista.hidden = true;
        ciudad.setAttribute('aria-expanded', 'false');
        ciudad.removeAttribute('aria-activedescendant');
        activo = -1;
    }

    function marcarActivo(i) {
        var items = lista.querySelectorAll('.combo-item');
        if (!items.length) return;
        activo = (i + items.length) % items.length;
        items.forEach(function (el, j) { el.setAttribute('aria-selected', j === activo ? 'true' : 'false'); });
        ciudad.setAttribute('aria-activedescendant', 'opcion-' + activo);
        items[activo].scrollIntoView({ block: 'nearest' });
    }

    function elegir(i) {
        var c = sugerencias[i];
        if (!c) return;
        ciudad.value = c.ciudad;
        ciudad.classList.remove('is-invalid');
        seleccion = c;
        bloquearPais(c); // elección explícita: incluso para ciudades repetidas
        cerrar();
        doc.focus();
    }

    ciudad.addEventListener('input', function () {
        pintar();
        evaluarCiudad(false);
    });

    ciudad.addEventListener('focus', function () { if (ciudad.value && !grupoPais.classList.contains('is-locked')) pintar(); });

    ciudad.addEventListener('keydown', function (ev) {
        if (lista.hidden && (ev.key === 'ArrowDown' || ev.key === 'ArrowUp')) { pintar(); }
        switch (ev.key) {
            case 'ArrowDown': ev.preventDefault(); marcarActivo(activo + 1); break;
            case 'ArrowUp':   ev.preventDefault(); marcarActivo(activo - 1); break;
            case 'Enter':
                if (!lista.hidden && activo >= 0) { ev.preventDefault(); elegir(activo); }
                else if (!lista.hidden && sugerencias.length === 1) { ev.preventDefault(); elegir(0); }
                break;
            case 'Escape': cerrar(); break;
            case 'Tab':
                if (!lista.hidden && activo >= 0) elegir(activo);
                break;
        }
    });

    ciudad.addEventListener('blur', function () {
        setTimeout(function () {
            cerrar();
            if (!grupoPais.classList.contains('is-locked') || paisAutomatico) evaluarCiudad(true);
        }, 120);
    });

    // mousedown evita que el input pierda el foco antes de elegir
    lista.addEventListener('mousedown', function (ev) {
        var item = ev.target.closest('.combo-item');
        if (item) { ev.preventDefault(); elegir(+item.dataset.i); }
    });

    /* ================= Documento según país ================= */

    function actualizarDocumento() {
        var info = PAISES[paisHidden.value];
        var label = document.getElementById('doc-label');
        var ayuda = document.getElementById('doc-ayuda');
        if (info) {
            label.textContent = info.documento;
            ayuda.textContent = info.documento_ayuda;
            if (paisHidden.value === 'Colombia') {
                doc.placeholder = 'Ej: 1012345678';
                doc.inputMode = 'numeric';
            } else if (paisHidden.value === 'Italia') {
                doc.placeholder = 'Ej: RSSMRA85M01H501Z o RM1234567';
                doc.inputMode = 'text';
            } else if (paisHidden.value === 'Francia') {
                doc.placeholder = 'Ej: 123456789012';
                doc.inputMode = 'text';
            } else {
                doc.placeholder = 'Ej: Documento de identidad';
                doc.inputMode = 'text';
            }
        } else {
            label.textContent = 'Número de documento';
            ayuda.textContent = 'El tipo de documento depende del país.';
            doc.placeholder = 'Primero indica tu ciudad o país';
            doc.inputMode = 'text';
        }
        validarDocumento();
    }

    function validarDocumento() {
        var info = PAISES[paisHidden.value];
        var valor = doc.value.replace(/[\s.\-]/g, '').toUpperCase();
        var msg = '';
        if (!valor) msg = 'Escribe tu número de documento.';
        else if (!info) msg = 'Primero indica tu ciudad o país.';
        else if (!new RegExp(info.documento_patron).test(valor)) msg = info.documento + ' no válido. ' + info.documento_ayuda;
        doc.setCustomValidity(msg);
        if (msg) document.getElementById('doc-error').textContent = msg;
    }

    doc.addEventListener('input', function () {
        if (paisHidden.value === 'Italia' || paisHidden.value === 'Francia' || paisHidden.value === 'España') {
            var pos = doc.selectionStart;
            doc.value = doc.value.toUpperCase();
            doc.setSelectionRange(pos, pos);
        }
        validarDocumento();
    });

    /* ================= Nombre y apellidos ================= */

    var soloLetras = /^[\p{L}' -]{2,60}$/u;
    document.querySelectorAll('[data-solo-letras]').forEach(function (input) {
        var feedback = input.parentNode.querySelector('.invalid-feedback');
        function validar() {
            var v = input.value.trim();
            var msg = !v ? (input.id === 'nombre' ? 'Escribe tu nombre.' : 'Escribe tus apellidos.')
                : soloLetras.test(v) ? '' : 'Usa solo letras (mínimo 2).';
            input.setCustomValidity(msg);
            if (msg && !input.classList.contains('is-invalid')) feedback.textContent = msg;
        }
        input.addEventListener('input', validar);
        validar();
    });

    /* ================= Correo ================= */

    var correo = document.getElementById('correo');
    correo.addEventListener('input', function () {
        var fb = correo.parentNode.querySelector('.invalid-feedback');
        if (!correo.classList.contains('is-invalid')) {
            fb.textContent = correo.value ? 'El correo no tiene un formato válido.' : 'Escribe tu correo electrónico.';
        }
    });

    /* ================= Contraseña ================= */

    var pass = document.getElementById('password');
    var confirmar = document.getElementById('confirm_password');
    var barra = document.getElementById('strength-fill');

    function validarPassword() {
        var v = pass.value;
        var reglas = {
            largo: v.length >= 8,
            letra: /[A-Za-z]/.test(v),
            numero: /\d/.test(v),
            coincide: v.length > 0 && v === confirmar.value
        };
        Object.keys(reglas).forEach(function (k) {
            var li = document.querySelector('[data-regla="' + k + '"]');
            li.classList.toggle('ok', reglas[k]);
            li.querySelector('i').className = reglas[k] ? 'bi bi-check-circle-fill' : 'bi bi-circle';
        });

        var puntos = (reglas.largo ? 1 : 0) + (reglas.letra ? 1 : 0) + (reglas.numero ? 1 : 0) +
            (/[^A-Za-z0-9]/.test(v) ? 1 : 0) + (v.length >= 12 ? 1 : 0);
        var colores = ['#c0262d', '#c0262d', '#d97706', '#d97706', '#12805c', '#12805c'];
        barra.style.width = (v ? Math.max(puntos, 1) * 20 : 0) + '%';
        barra.style.background = colores[puntos];

        pass.setCustomValidity(reglas.largo && reglas.letra && reglas.numero ? '' : 'Contraseña débil');
        confirmar.setCustomValidity(confirmar.value && confirmar.value === v ? '' : 'Las contraseñas no coinciden.');
    }
    pass.addEventListener('input', validarPassword);
    confirmar.addEventListener('input', validarPassword);

    /* ================= Envío ================= */

    form.validarExtra = function () {
        validarDocumento();
        validarPassword();
        if (!paisHidden.value) mostrarErrorPais('Selecciona Colombia, Italia o Francia.');
    };

    /* ================= Estado inicial (si el servidor devolvió datos) ================= */

    if (ciudad.value) {
        var paisPrevio = paisHidden.value;
        evaluarCiudad(true);
        if (!grupoPais.classList.contains('is-locked') && paisPrevio && PAISES[paisPrevio]) marcarPais(paisPrevio);
    } else {
        actualizarDocumento();
    }
    validarPassword();

    var resumen = document.getElementById('resumen-errores');
    if (resumen) resumen.focus();
})();
