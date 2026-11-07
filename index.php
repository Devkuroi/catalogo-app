<?php
/**
 * Front Controller: único punto de entrada de la aplicación.
 * Todas las peticiones pasan por aquí: index.php?r=<ruta>
 */
define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');

require_once APP_PATH . '/core/App.php';
App::iniciar();

$router = new Router();
$router
    ->agregar('',                 'HomeController',     'index')
    ->agregar('login',            'AuthController',     'login')
    ->agregar('registro',         'AuthController',     'registro')
    ->agregar('logout',           'AuthController',     'logout')
    ->agregar('catalogo',         'CatalogoController', 'index')
    ->agregar('admin',            'AdminController',    'index')
    ->agregar('admin/producto',   'AdminController',    'cambiarEstadoProducto');

$router->despachar($_GET['r'] ?? '');
