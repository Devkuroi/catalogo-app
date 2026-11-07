<!DOCTYPE html>
<html lang="es">
<head>
    <?php require APP_PATH . '/views/partials/head.php'; ?>
</head>
<body class="auth-body">
    <div class="auth-shell">
        <aside class="auth-aside" aria-hidden="true">
            <a href="<?= url() ?>" class="brand brand-light" tabindex="-1">
                <?php require APP_PATH . '/views/partials/brand.php'; ?>
            </a>

            <div class="auth-aside-copy">
                <p class="eyebrow">Ofertas de temporada</p>
                <h2>Tecnología a <span class="text-accent">precio de promoción</span>.</h2>
                <p class="muted-light">Laptops, audio, fotografía y wearables con precios de promoción por tiempo limitado.</p>
            </div>

            <div class="aside-tickets">
                <div class="ticket"><i class="bi bi-laptop"></i><span>Electrónica</span></div>
                <div class="ticket ticket-alt"><i class="bi bi-headphones"></i><span>Accesorios</span></div>
                <div class="ticket"><i class="bi bi-camera"></i><span>Fotografía</span></div>
                <div class="ticket ticket-alt"><i class="bi bi-smartwatch"></i><span>Wearables</span></div>
            </div>

            <p class="aside-foot"><span class="flag flag-co"></span> Colombia &nbsp;·&nbsp; <span class="flag flag-it"></span> Italia &nbsp;·&nbsp; <span class="flag flag-fr"></span> Francia</p>
        </aside>

        <main class="auth-main" id="contenido">
            <div class="auth-topbar d-lg-none">
                <a href="<?= url() ?>" class="brand"><?php require APP_PATH . '/views/partials/brand.php'; ?></a>
            </div>
            <div class="auth-content">
                <?php require APP_PATH . '/views/partials/flash.php'; ?>
                <?= $contenido ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php foreach ($scripts ?? [] as $script): ?>
        <script src="<?= asset($script) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
