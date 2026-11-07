<?php $scripts = ['js/catalogo.js']; ?>

<header class="page-head">
    <div>
        <p class="eyebrow eyebrow-dark mb-1">Promociones vigentes</p>
        <h1 class="page-title">Hola, <?= e(explode(' ', $usuario['nombre'])[0]) ?> 👋</h1>
        <p class="text-secondary mb-0">
            <?= count($productos) ?> <?= count($productos) === 1 ? 'oferta disponible' : 'ofertas disponibles' ?> ahora mismo.
        </p>
    </div>
</header>

<?php if (empty($productos)): ?>
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h2>No hay promociones activas</h2>
        <p>Vuelve pronto: publicamos nuevas ofertas cada semana.</p>
    </div>
<?php else: ?>

    <div class="toolbar" role="search">
        <div class="input-icon flex-grow-1">
            <i class="bi bi-search"></i>
            <input type="search" class="form-control" id="buscar" placeholder="Buscar producto…" aria-label="Buscar producto">
        </div>
        <div class="chips" role="group" aria-label="Filtrar por categoría">
            <button type="button" class="chip active" data-categoria="">Todas</button>
            <?php foreach ($categorias as $cat): ?>
                <button type="button" class="chip" data-categoria="<?= e($cat) ?>"><?= e($cat) ?></button>
            <?php endforeach; ?>
        </div>
        <select class="form-select toolbar-sort" id="ordenar" aria-label="Ordenar">
            <option value="fin">Terminan antes</option>
            <option value="descuento">Mayor descuento</option>
            <option value="precio-asc">Precio: menor a mayor</option>
            <option value="precio-desc">Precio: mayor a menor</option>
        </select>
    </div>

    <div class="row g-4" id="grid">
        <?php foreach ($productos as $i => $p):
            $urgente = $p['dias_restantes'] <= 7;
        ?>
            <div class="col-sm-6 col-lg-4 producto"
                 data-nombre="<?= e(mb_strtolower($p['nombre'] . ' ' . $p['descripcion'])) ?>"
                 data-categoria="<?= e($p['categoria']) ?>"
                 data-descuento="<?= (int) $p['descuento'] ?>"
                 data-precio="<?= (float) $p['precio_promocion'] ?>"
                 data-fin="<?= (int) $p['dias_restantes'] ?>"
                 data-orden="<?= $i ?>">
                <article class="product-card h-100">
                    <div class="product-visual visual-<?= $i % 4 ?>">
                        <i class="bi bi-<?= icono_producto($p) ?>" aria-hidden="true"></i>
                        <span class="discount-badge">-<?= (int) $p['descuento'] ?>%</span>
                    </div>
                    <div class="product-body">
                        <span class="product-cat"><?= e($p['categoria']) ?></span>
                        <h2 class="product-name"><?= e($p['nombre']) ?></h2>
                        <p class="product-desc"><?= e($p['descripcion']) ?></p>

                        <div class="price-row">
                            <span class="price-now"><?= precio($p['precio_promocion']) ?></span>
                            <span class="price-old"><?= precio($p['precio_original']) ?></span>
                        </div>
                        <p class="saving">Ahorras <?= precio($p['ahorro']) ?></p>

                        <div class="product-foot">
                            <span class="deadline<?= $urgente ? ' deadline-urgent' : '' ?>">
                                <i class="bi bi-clock"></i>
                                <?= $p['dias_restantes'] === 0 ? 'Termina hoy' : 'Quedan ' . $p['dias_restantes'] . ($p['dias_restantes'] === 1 ? ' día' : ' días') ?>
                            </span>
                            <button type="button" class="btn btn-sm btn-ink btn-ver-detalles" data-bs-toggle="modal" data-bs-target="#detalle-<?= (int) $p['id'] ?>">
                                <i class="bi bi-eye me-1"></i>Ver detalles
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modales de detalle de cada producto (ubicados fuera de la grilla para evitar conflictos de z-index y filtros) -->
    <?php foreach ($productos as $i => $p):
        $urgente = $p['dias_restantes'] <= 7;
    ?>
        <div class="modal fade modal-detalle-producto" id="detalle-<?= (int) $p['id'] ?>" tabindex="-1" aria-labelledby="detalle-titulo-<?= (int) $p['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="product-visual visual-<?= $i % 4 ?> modal-visual position-relative">
                        <i class="bi bi-<?= icono_producto($p) ?>" aria-hidden="true"></i>
                        <span class="discount-badge position-absolute top-0 start-0 m-3">-<?= (int) $p['descuento'] ?>% OFF</span>
                        <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="product-cat badge bg-secondary-subtle text-secondary-emphasis px-3 py-2 rounded-pill"><?= e($p['categoria']) ?></span>
                            <span class="badge <?= $urgente ? 'bg-danger' : 'bg-success' ?>-subtle text-<?= $urgente ? 'danger' : 'success' ?>-emphasis px-3 py-2 rounded-pill">
                                <i class="bi <?= $urgente ? 'bi-exclamation-triangle' : 'bi-check-circle' ?> me-1"></i>
                                <?= $urgente ? '¡Oferta por tiempo limitado!' : 'Promoción activa' ?>
                            </span>
                        </div>
                        <h2 class="h3 fw-bold mt-2 mb-3" id="detalle-titulo-<?= (int) $p['id'] ?>"><?= e($p['nombre']) ?></h2>
                        <p class="text-secondary lead fs-6 mb-4"><?= e($p['descripcion']) ?></p>

                        <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                            <div class="row g-3 text-center text-sm-start">
                                <div class="col-sm-4">
                                    <div class="text-muted small">Precio Promoción</div>
                                    <div class="h3 text-success fw-bold mb-0"><?= precio($p['precio_promocion']) ?></div>
                                </div>
                                <div class="col-sm-4 border-sm-start">
                                    <div class="text-muted small">Precio Regular</div>
                                    <div class="h4 text-decoration-line-through text-muted mb-0"><?= precio($p['precio_original']) ?></div>
                                </div>
                                <div class="col-sm-4 border-sm-start">
                                    <div class="text-muted small">Ahorro Total</div>
                                    <div class="h4 text-primary fw-bold mb-0"><?= precio($p['ahorro']) ?> <span class="fs-6 fw-normal">(<?= (int) $p['descuento'] ?>%)</span></div>
                                </div>
                            </div>
                        </div>

                        <dl class="detail-list mb-4">
                            <div>
                                <dt><i class="bi bi-calendar-event me-2 text-primary"></i>Periodo de vigencia</dt>
                                <dd><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?> hasta el <?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></dd>
                            </div>
                            <div>
                                <dt><i class="bi bi-hourglass-split me-2 text-warning"></i>Disponibilidad restante</dt>
                                <dd class="fw-semibold <?= $urgente ? 'text-danger' : 'text-success' ?>">
                                    <?= $p['dias_restantes'] === 0 ? '¡Termina hoy mismo!' : $p['dias_restantes'] . ' días restantes' ?>
                                </dd>
                            </div>
                            <div>
                                <dt><i class="bi bi-shield-check me-2 text-success"></i>Garantía del producto</dt>
                                <dd>Garantía directa de 12 meses y envío con seguro incluido.</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" onclick="alert('¡Producto agregado al carrito de promociones!')">
                            <i class="bi bi-bag-check me-2"></i>Aprovechar oferta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="empty-state d-none" id="sin-resultados">
        <i class="bi bi-search"></i>
        <h2>Sin resultados</h2>
        <p>Prueba con otra búsqueda o categoría.</p>
        <button type="button" class="btn btn-outline-ink btn-sm" id="limpiar-filtros">Limpiar filtros</button>
    </div>
<?php endif; ?>
