<?php
/**
 * Controlador base: renderizado de vistas, redirecciones y filtros de acceso.
 */
abstract class Controller
{
    /**
     * Renderiza una vista dentro de un layout.
     * @param string $vista   Ruta relativa en app/views (ej: 'auth/login')
     * @param array  $datos   Variables disponibles en la vista
     * @param string $layout  Layout en app/views/layouts
     */
    protected function render(string $vista, array $datos = [], string $layout = 'main'): void
    {
        extract($datos, EXTR_SKIP);
        $flash = Session::obtenerFlash();
        $usuario = Session::usuario();

        ob_start();
        require APP_PATH . "/views/$vista.php";
        $contenido = ob_get_clean();

        require APP_PATH . "/views/layouts/$layout.php";
    }

    protected function redirect(string $ruta, array $params = []): void
    {
        header('Location: ' . url($ruta, $params));
        exit;
    }

    protected function esPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** Corta la petición si el token CSRF no es válido. */
    protected function verificarCsrf(string $rutaRetorno): void
    {
        if (!Session::csrfValido($_POST['_csrf'] ?? null)) {
            Session::flash('danger', 'Tu sesión expiró. Vuelve a intentarlo.');
            $this->redirect($rutaRetorno);
        }
    }

    protected function requiereLogin(): void
    {
        if (!Session::usuario()) {
            Session::flash('warning', 'Inicia sesión para continuar.');
            $this->redirect('login');
        }
    }

    protected function requiereAdmin(): void
    {
        $this->requiereLogin();
        if (!Session::esAdmin()) {
            Session::flash('danger', 'No tienes permisos para acceder a esa sección.');
            $this->redirect('catalogo');
        }
    }

    protected function soloInvitados(): void
    {
        if (Session::usuario()) {
            $this->redirect(Session::esAdmin() ? 'admin' : 'catalogo');
        }
    }
}
