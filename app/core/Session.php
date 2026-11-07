<?php
/**
 * Manejo de sesión, mensajes flash, datos "old" de formularios y token CSRF.
 */
class Session
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }
    }

    /* ---------- Usuario autenticado ---------- */

    public static function login(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int) $usuario['id'],
            'nombre' => $usuario['nombre'] . ' ' . $usuario['apellidos'],
            'correo' => $usuario['correo'],
            'rol'    => $usuario['rol'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function esAdmin(): bool
    {
        return (self::usuario()['rol'] ?? null) === 'admin';
    }

    /* ---------- Mensajes flash ---------- */

    public static function flash(string $tipo, string $mensaje): void
    {
        $_SESSION['_flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
    }

    public static function obtenerFlash(): ?array
    {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return $flash;
    }

    /* ---------- Datos de formulario tras un error (patrón PRG) ---------- */

    public static function guardarFormulario(array $old, array $errores): void
    {
        $_SESSION['_old'] = $old;
        $_SESSION['_errores'] = $errores;
    }

    public static function obtenerFormulario(): array
    {
        $datos = [$_SESSION['_old'] ?? [], $_SESSION['_errores'] ?? []];
        unset($_SESSION['_old'], $_SESSION['_errores']);
        return $datos;
    }

    /* ---------- CSRF ---------- */

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function csrfValido(?string $token): bool
    {
        return is_string($token) && hash_equals(self::csrfToken(), $token);
    }
}
