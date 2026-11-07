/**
 * Login: rellenar con credenciales demo y aviso de Bloq Mayús.
 */
(function () {
    'use strict';

    var correo = document.getElementById('correo');
    var password = document.getElementById('password');
    var tarjetas = document.querySelectorAll('.js-demo');

    tarjetas.forEach(function (card) {
        card.addEventListener('click', function () {
            correo.value = card.dataset.correo;
            password.value = card.dataset.password;
            tarjetas.forEach(function (c) { c.classList.remove('is-selected'); });
            card.classList.add('is-selected');
            document.getElementById('form-login').classList.remove('was-validated');
            document.querySelector('#form-login .btn-submit').focus();
        });
    });

    var aviso = document.getElementById('caps');
    function revisarMayus(ev) {
        if (ev.getModifierState) aviso.classList.toggle('d-none', !ev.getModifierState('CapsLock'));
    }
    password.addEventListener('keyup', revisarMayus);
    password.addEventListener('keydown', revisarMayus);
    password.addEventListener('blur', function () { aviso.classList.add('d-none'); });
})();
