<?php $scripts = ['js/form-utils.js', 'js/login.js']; ?>

<header class="mb-4">
    <h1 class="auth-title">Inicia sesión</h1>
    <p class="text-secondary mb-0">Accede para ver las promociones vigentes.</p>
</header>

<?php if (!empty($cuentasDemo)): ?>
    <section class="demo-box mb-4" aria-labelledby="demo-titulo">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 id="demo-titulo" class="demo-title"><i class="bi bi-key me-1"></i>Credenciales de prueba</h2>
            <span class="badge rounded-pill text-bg-warning-subtle border border-warning-subtle">Modo demo</span>
        </div>
        <p class="small text-secondary mb-3">Toca una cuenta para rellenar el formulario.</p>

        <div class="demo-grid">
            <?php foreach ($cuentasDemo as $c): ?>
                <button type="button" class="demo-card js-demo"
                        data-correo="<?= e($c['correo']) ?>" data-password="<?= e($c['password']) ?>">
                    <span class="demo-role demo-role-<?= e($c['rol']) ?>">
                        <i class="bi bi-<?= $c['rol'] === 'admin' ? 'shield-lock' : 'person' ?>"></i>
                        <?= e($c['etiqueta']) ?>
                    </span>
                    <span class="demo-line"><i class="bi bi-envelope"></i><code><?= e($c['correo']) ?></code></span>
                    <span class="demo-line"><i class="bi bi-lock"></i><code><?= e($c['password']) ?></code></span>
                    <span class="demo-use">Usar esta cuenta <i class="bi bi-arrow-right"></i></span>
                </button>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($errores['general'])): ?>
    <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-exclamation-octagon-fill mt-1"></i><div><?= e($errores['general']) ?></div>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('login') ?>" class="needs-validation" novalidate id="form-login">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico</label>
        <div class="input-icon">
            <i class="bi bi-envelope"></i>
            <input type="email" class="form-control form-control-lg" id="correo" name="correo"
                   value="<?= e($old['correo'] ?? '') ?>" placeholder="tucorreo@ejemplo.com"
                   autocomplete="email" required <?= empty($old['correo']) ? 'autofocus' : '' ?>>
            <div class="invalid-feedback">Escribe un correo válido.</div>
        </div>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-icon">
            <i class="bi bi-lock"></i>
            <input type="password" class="form-control form-control-lg has-toggle" id="password" name="password"
                   placeholder="Tu contraseña" autocomplete="current-password" required
                   <?= !empty($old['correo']) ? 'autofocus' : '' ?>>
            <button type="button" class="pass-toggle" data-target="password" aria-label="Mostrar contraseña">
                <i class="bi bi-eye"></i>
            </button>
            <div class="invalid-feedback">Escribe tu contraseña.</div>
        </div>
        <p class="caps-warning small mt-2 mb-0 d-none" id="caps"><i class="bi bi-capslock-fill me-1"></i>Bloq Mayús está activado</p>
    </div>

    <button type="submit" class="btn btn-accent btn-lg w-100 btn-submit">
        <span class="btn-label">Entrar</span>
        <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
    </button>
</form>

<p class="text-center mt-4 mb-0 text-secondary">
    ¿Aún no tienes cuenta? <a href="<?= url('registro') ?>" class="link-strong">Regístrate gratis</a>
</p>
