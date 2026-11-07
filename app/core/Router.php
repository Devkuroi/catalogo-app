<?php
/**
 * Enrutador simple basado en el parámetro ?r=
 * No requiere mod_rewrite, funciona directamente en XAMPP.
 */
class Router
{
    private array $rutas = [];

    public function agregar(string $ruta, string $controlador, string $accion): self
    {
        $this->rutas[$ruta] = [$controlador, $accion];
        return $this;
    }

    public function despachar(string $ruta): void
    {
        $ruta = trim($ruta, '/');

        if (!isset($this->rutas[$ruta])) {
            http_response_code(404);
            (new ErrorController())->noEncontrado();
            return;
        }

        [$controlador, $accion] = $this->rutas[$ruta];

        try {
            (new $controlador())->$accion();
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            http_response_code(500);
            (new ErrorController())->baseDatos();
        }
    }
}
