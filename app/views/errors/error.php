<section class="text-center py-4">
    <div class="error-code"><?= (int) $codigo ?></div>
    <h1 class="auth-title mb-2"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-4"><?= e($mensaje) ?></p>
    <a href="<?= url() ?>" class="btn btn-accent"><i class="bi bi-house me-1"></i>Volver al inicio</a>
</section>
