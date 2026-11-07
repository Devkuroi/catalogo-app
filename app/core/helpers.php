<?php
/**
 * Funciones de ayuda disponibles en controladores y vistas.
 */

/** Escapa texto para imprimirlo en HTML de forma segura. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Genera la URL de una ruta: url('login') => index.php?r=login */
function url(string $ruta = '', array $params = []): string
{
    $query = $ruta !== '' ? ['r' => $ruta] + $params : $params;
    return 'index.php' . ($query ? '?' . http_build_query($query) : '');
}

/** Ruta pública de un recurso estático con versión para evitar caché antigua. */
function asset(string $ruta): string
{
    $archivo = ROOT_PATH . '/assets/' . $ruta;
    $version = is_file($archivo) ? filemtime($archivo) : 1;
    return 'assets/' . $ruta . '?v=' . $version;
}

/** Campo oculto con el token CSRF. */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Session::csrfToken()) . '">';
}

/** Icono de Bootstrap Icons según el tipo de producto (no hay imágenes reales). */
function icono_producto(array $p): string
{
    $mapa = [
        'laptop' => 'laptop', 'smartphone' => 'phone', 'teléfono' => 'phone', 'auricular' => 'headphones',
        'smartwatch' => 'smartwatch', 'reloj' => 'smartwatch', 'cámara' => 'camera', 'tablet' => 'tablet',
    ];
    $nombre = mb_strtolower($p['nombre'] ?? '');
    foreach ($mapa as $clave => $icono) {
        if (str_contains($nombre, $clave)) {
            return $icono;
        }
    }
    return 'bag';
}

/** Formatea un precio. */
function precio($valor): string
{
    return '$' . number_format((float) $valor, 2, ',', '.');
}
