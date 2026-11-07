<?php $rutaActual = $_GET['r'] ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require APP_PATH . '/views/partials/head.php'; ?>
</head>
<body class="app-body">
    <a class="visually-hidden-focusable skip-link" href="#contenido">Saltar al contenido</a>

    <nav class="navbar navbar-expand-md app-nav sticky-top">
        <div class="container">
            <a class="navbar-brand brand" href="<?= url('catalogo') ?>">
                <?php require APP_PATH . '/views/partials/brand.php'; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Abrir menú">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto ms-md-4 gap-md-1">
                    <li class="nav-item">
                        <a class="nav-link <?= $rutaActual === 'catalogo' ? 'active' : '' ?>" href="<?= url('catalogo') ?>">
                            <i class="bi bi-grid me-1"></i>Catálogo
                        </a>
                    </li>
                    <?php if (Session::esAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= str_starts_with($rutaActual, 'admin') ? 'active' : '' ?>" href="<?= url('admin') ?>">
                                <i class="bi bi-speedometer2 me-1"></i>Panel admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="dropdown">
                    <button class="user-chip dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar"><?= e(mb_strtoupper(mb_substr($usuario['nombre'], 0, 1))) ?></span>
                        <span class="d-flex flex-column text-start lh-sm">
                            <span class="fw-semibold"><?= e($usuario['nombre']) ?></span>
                            <small class="text-secondary"><?= $usuario['rol'] === 'admin' ? 'Administrador' : 'Cliente' ?></small>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text small text-secondary"><?= e($usuario['correo']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?= url('logout') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5" id="contenido">
        <?php require APP_PATH . '/views/partials/flash.php'; ?>
        <?= $contenido ?>
    </main>

    <footer class="app-footer">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>© <?= date('Y') ?> <?= e(App::config('app')['nombre']) ?> · <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>v1.2.0 CI/CD Jenkins</span></span>
            <span class="text-secondary small">Precios en USD · Colombia, Italia y Francia · Promociones sujetas a disponibilidad</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php foreach ($scripts ?? [] as $script): ?>
        <script src="<?= asset($script) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
