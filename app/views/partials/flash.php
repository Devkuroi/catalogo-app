<?php if (!empty($flash)):
    $iconos = ['success' => 'check-circle-fill', 'danger' => 'exclamation-octagon-fill', 'warning' => 'exclamation-triangle-fill', 'info' => 'info-circle-fill'];
?>
    <div class="alert alert-<?= e($flash['tipo']) ?> alert-dismissible fade show d-flex align-items-start gap-2 flash" role="alert">
        <i class="bi bi-<?= $iconos[$flash['tipo']] ?? 'info-circle-fill' ?> mt-1" aria-hidden="true"></i>
        <div><?= e($flash['mensaje']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>
