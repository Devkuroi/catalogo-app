<?php $scripts = ['js/admin.js']; ?>

<header class="page-head">
    <div>
        <p class="eyebrow eyebrow-dark mb-1">Administración</p>
        <h1 class="page-title">Panel de control</h1>
        <p class="text-secondary mb-0">Usuarios registrados y estado de las promociones.</p>
    </div>
    <a href="<?= url('catalogo') ?>" class="btn btn-outline-ink"><i class="bi bi-eye me-1"></i>Ver catálogo</a>
</header>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat"><i class="bi bi-people"></i><span class="stat-num"><?= $stats['usuarios'] ?></span><span class="stat-label">Usuarios</span></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat"><span class="flag flag-co flag-lg"></span><span class="stat-num"><?= $stats['colombia'] ?></span><span class="stat-label">Colombia</span></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat"><span class="flag flag-es flag-lg"></span><span class="stat-num"><?= $stats['espana'] ?></span><span class="stat-label">España</span></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat"><i class="bi bi-tags"></i><span class="stat-num"><?= $stats['vigentes'] ?><small>/<?= $stats['productos'] ?></small></span><span class="stat-label">Promos visibles</span></div>
    </div>
</div>

<div class="panel">
    <ul class="nav nav-underline panel-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tab === 'usuarios' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-usuarios" type="button" role="tab">
                <i class="bi bi-people me-1"></i>Usuarios <span class="count"><?= $stats['usuarios'] ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tab === 'productos' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-productos" type="button" role="tab">
                <i class="bi bi-box-seam me-1"></i>Productos <span class="count"><?= $stats['productos'] ?></span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Usuarios -->
        <section class="tab-pane fade <?= $tab === 'usuarios' ? 'show active' : '' ?>" id="tab-usuarios" role="tabpanel">
            <div class="panel-tools">
                <div class="input-icon">
                    <i class="bi bi-search"></i>
                    <input type="search" class="form-control" id="filtro-usuarios" placeholder="Buscar por nombre, correo o ciudad…" aria-label="Buscar usuarios">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle data-table" id="tabla-usuarios">
                    <thead>
                        <tr><th>Usuario</th><th>Documento</th><th>Ubicación</th><th>Rol</th><th>Registro</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-sm"><?= e(mb_strtoupper(mb_substr($u['nombre'], 0, 1))) ?></span>
                                        <div class="lh-sm">
                                            <div class="fw-semibold"><?= e($u['nombre'] . ' ' . $u['apellidos']) ?></div>
                                            <small class="text-secondary"><?= e($u['correo']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><code><?= e($u['numero_documento']) ?></code></td>
                                <td>
                                    <span class="flag flag-<?= $u['pais'] === 'España' ? 'es' : 'co' ?>"></span>
                                    <?= e($u['ciudad']) ?>, <span class="text-secondary"><?= e($u['pais']) ?></span>
                                </td>
                                <td>
                                    <span class="role-pill role-<?= e($u['rol']) ?>"><?= $u['rol'] === 'admin' ? 'Admin' : 'Usuario' ?></span>
                                </td>
                                <td class="text-secondary small"><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Productos -->
        <section class="tab-pane fade <?= $tab === 'productos' ? 'show active' : '' ?>" id="tab-productos" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle data-table">
                    <thead>
                        <tr><th>Producto</th><th>Precio</th><th>Vigencia</th><th>Estado</th><th class="text-end">Acción</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p):
                            if (!$p['activo'])              { $estado = ['Oculto', 'muted']; }
                            elseif ($p['dias_restantes'] < 0) { $estado = ['Vencido', 'danger']; }
                            else                              { $estado = ['Visible', 'success']; }
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mini-icon"><i class="bi bi-<?= icono_producto($p) ?>"></i></span>
                                        <div class="lh-sm">
                                            <div class="fw-semibold"><?= e($p['nombre']) ?></div>
                                            <small class="text-secondary"><?= e($p['categoria']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= precio($p['precio_promocion']) ?></div>
                                    <small class="text-secondary text-decoration-line-through"><?= precio($p['precio_original']) ?></small>
                                    <small class="text-success fw-semibold">-<?= $p['descuento'] ?>%</small>
                                </td>
                                <td class="small"><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?> – <?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></td>
                                <td><span class="status status-<?= $estado[1] ?>"><?= $estado[0] ?></span></td>
                                <td class="text-end">
                                    <form method="POST" action="<?= url('admin/producto') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $p['activo'] ? 'btn-outline-secondary' : 'btn-ink' ?>">
                                            <i class="bi bi-<?= $p['activo'] ? 'eye-slash' : 'eye' ?> me-1"></i><?= $p['activo'] ? 'Ocultar' : 'Publicar' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
