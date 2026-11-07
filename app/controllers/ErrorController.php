<?php
/**
 * Páginas de error.
 */
class ErrorController extends Controller
{
    public function noEncontrado(): void
    {
        $this->render('errors/error', [
            'titulo'  => 'Página no encontrada',
            'codigo'  => 404,
            'mensaje' => 'La página que buscas no existe o fue movida.',
        ], 'auth');
    }

    public function baseDatos(): void
    {
        $this->render('errors/error', [
            'titulo'  => 'Error de conexión',
            'codigo'  => 500,
            'mensaje' => 'No pudimos conectar con la base de datos. Verifica que MySQL esté encendido en XAMPP y que hayas importado sql/database.sql.',
        ], 'auth');
    }
}
