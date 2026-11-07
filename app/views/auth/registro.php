<?php
$scripts = ['js/form-utils.js', 'js/registro.js'];

// Ayudantes de la vista para errores por campo
$invalido = fn(string $campo) => isset($errores[$campo]) ? ' is-invalid' : '';
$mensaje  = fn(string $campo, string $porDefecto) => e($errores[$campo] ?? $porDefecto);
$valor    = fn(string $campo) => e($old[$campo] ?? '');
$paisOld  = $old['pais'] ?? '';
?>

<header class="mb-4">
    <h1 class="auth-title">Crea tu cuenta</h1>
    <p class="text-secondary mb-0">Te toma menos de un minuto. Disponible para Colombia, Italia y Francia.</p>
</header>

<?php if (!empty($errores)): ?>
    <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert" tabindex="-1" id="resumen-errores">
        <i class="bi bi-exclamation-octagon-fill mt-1"></i>
        <div>Revisa <?= count($errores) === 1 ? 'el campo marcado' : 'los ' . count($errores) . ' campos marcados' ?> en rojo.</div>
    </div>
<?php endif; ?>

<script type="application/json" id="datos-ubicacion"><?= json_encode(
    ['paises' => $paises, 'ciudades' => $ciudades],
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?></script>

<form method="POST" action="<?= url('registro') ?>" class="needs-validation" novalidate id="form-registro">
    <?= csrf_field() ?>

    <!-- 1. Datos personales -->
    <fieldset class="form-section">
        <legend><span class="step">1</span>Datos personales</legend>
        <div class="row g-3">
            <div class="col-sm-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control<?= $invalido('nombre') ?>" id="nombre" name="nombre"
                       value="<?= $valor('nombre') ?>" autocomplete="given-name" required minlength="2" maxlength="60"
                       data-solo-letras placeholder="Ej: María">
                <div class="invalid-feedback"><?= $mensaje('nombre', 'Usa solo letras (mínimo 2).') ?></div>
            </div>
            <div class="col-sm-6">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control<?= $invalido('apellidos') ?>" id="apellidos" name="apellidos"
                       value="<?= $valor('apellidos') ?>" autocomplete="family-name" required minlength="2" maxlength="60"
                       data-solo-letras placeholder="Ej: Gómez Ruiz">
                <div class="invalid-feedback"><?= $mensaje('apellidos', 'Usa solo letras (mínimo 2).') ?></div>
            </div>
        </div>
    </fieldset>

    <!-- 2. Ubicación -->
    <fieldset class="form-section">
        <legend><span class="step">2</span>Ubicación</legend>
        <div class="row g-3">
            <div class="col-12">
                <label for="ciudad" class="form-label">Ciudad</label>
                <div class="combo" id="combo-ciudad">
                    <div class="input-icon">
                        <i class="bi bi-geo-alt"></i>
                        <input type="text" class="form-control<?= $invalido('ciudad') ?>" id="ciudad" name="ciudad"
                               value="<?= $valor('ciudad') ?>" required minlength="2" maxlength="100"
                               placeholder="Empieza a escribir: Bogotá, Roma, París, Medellín…"
                               role="combobox" aria-autocomplete="list" aria-expanded="false"
                               aria-controls="lista-ciudades" aria-describedby="ciudad-ayuda" autocomplete="off">
                        <div class="invalid-feedback"><?= $mensaje('ciudad', 'Escribe tu ciudad.') ?></div>
                    </div>
                    <ul class="combo-list" id="lista-ciudades" role="listbox" aria-label="Ciudades sugeridas" hidden></ul>
                </div>
                <div class="form-text" id="ciudad-ayuda">Al elegir una ciudad conocida, el país se completa solo.</div>
            </div>

            <div class="col-12">
                <span class="form-label d-block" id="pais-label">País</span>
                <input type="hidden" name="pais" id="pais" value="<?= e($paisOld) ?>">
                <div class="country-picker<?= isset($errores['pais']) ? ' is-invalid' : '' ?>" role="radiogroup"
                     aria-labelledby="pais-label" id="pais-grupo">
                    <?php foreach ($paises as $nombrePais => $info): ?>
                        <label class="country-option">
                            <input type="radio" name="pais_opcion" value="<?= e($nombrePais) ?>"
                                   <?= $paisOld === $nombrePais ? 'checked' : '' ?>>
                            <span class="country-card">
                                <span class="flag flag-<?= strtolower($info['codigo']) ?>" aria-hidden="true"></span>
                                <span class="country-name"><?= e($nombrePais) ?></span>
                                <i class="bi bi-lock-fill country-lock" aria-hidden="true"></i>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="lock-note d-none" id="pais-bloqueado" aria-live="polite">
                    <i class="bi bi-lock-fill"></i>
                    <span id="pais-bloqueado-texto">País detectado automáticamente.</span>
                    <button type="button" class="btn btn-link btn-sm p-0 ms-auto" id="cambiar-ciudad">Cambiar ciudad</button>
                </div>
                <div class="invalid-feedback d-block<?= isset($errores['pais']) ? '' : ' d-none' ?>" id="pais-error">
                    <?= $mensaje('pais', 'Selecciona Colombia, Italia o Francia.') ?>
                </div>
            </div>
        </div>
    </fieldset>

    <!-- 3. Identificación -->
    <fieldset class="form-section">
        <legend><span class="step">3</span>Identificación</legend>
        <label for="numero_documento" class="form-label">
            <span id="doc-label">Número de documento</span>
        </label>
        <div class="input-icon">
            <i class="bi bi-person-vcard"></i>
            <input type="text" class="form-control<?= $invalido('numero_documento') ?>" id="numero_documento"
                   name="numero_documento" value="<?= $valor('numero_documento') ?>" required maxlength="20"
                   placeholder="Primero indica tu ciudad o país" aria-describedby="doc-ayuda" autocomplete="off">
            <div class="invalid-feedback" id="doc-error"><?= $mensaje('numero_documento', 'Número de documento no válido.') ?></div>
        </div>
        <div class="form-text" id="doc-ayuda">El tipo de documento depende del país.</div>
    </fieldset>

    <!-- 4. Cuenta -->
    <fieldset class="form-section">
        <legend><span class="step">4</span>Datos de acceso</legend>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input type="email" class="form-control<?= $invalido('correo') ?>" id="correo" name="correo"
                       value="<?= $valor('correo') ?>" autocomplete="email" required maxlength="150"
                       placeholder="tucorreo@ejemplo.com">
                <div class="invalid-feedback"><?= $mensaje('correo', 'Escribe un correo válido.') ?></div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="form-control has-toggle<?= $invalido('password') ?>" id="password"
                           name="password" autocomplete="new-password" required minlength="8"
                           aria-describedby="pass-reglas">
                    <button type="button" class="pass-toggle" data-target="password" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                    <div class="invalid-feedback"><?= $mensaje('password', 'Mínimo 8 caracteres, con al menos una letra y un número.') ?></div>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="form-control has-toggle<?= $invalido('confirm_password') ?>"
                           id="confirm_password" name="confirm_password" autocomplete="new-password" required>
                    <button type="button" class="pass-toggle" data-target="confirm_password" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                    <div class="invalid-feedback"><?= $mensaje('confirm_password', 'Las contraseñas no coinciden.') ?></div>
                </div>
            </div>
        </div>

        <div class="strength mt-3" aria-live="polite">
            <div class="strength-bar"><span id="strength-fill"></span></div>
            <ul class="pass-rules" id="pass-reglas">
                <li data-regla="largo"><i class="bi bi-circle"></i>8 caracteres o más</li>
                <li data-regla="letra"><i class="bi bi-circle"></i>Una letra</li>
                <li data-regla="numero"><i class="bi bi-circle"></i>Un número</li>
                <li data-regla="coincide"><i class="bi bi-circle"></i>Las contraseñas coinciden</li>
            </ul>
        </div>
    </fieldset>

    <div class="form-check mb-4">
        <input class="form-check-input<?= $invalido('terminos') ?>" type="checkbox" id="terminos" name="terminos" value="1" required
               <?= !empty($old['terminos']) ? 'checked' : '' ?>>
        <label class="form-check-label small" for="terminos">
            Acepto el tratamiento de mis datos personales para gestionar mi cuenta.
        </label>
        <div class="invalid-feedback"><?= $mensaje('terminos', 'Debes aceptar el tratamiento de datos.') ?></div>
    </div>

    <button type="submit" class="btn btn-accent btn-lg w-100 btn-submit">
        <span class="btn-label">Crear cuenta</span>
        <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
    </button>
</form>

<p class="text-center mt-4 mb-0 text-secondary">
    ¿Ya tienes cuenta? <a href="<?= url('login') ?>" class="link-strong">Inicia sesión</a>
</p>
