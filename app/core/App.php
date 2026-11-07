<?php
/**
 * Arranque de la aplicación: carga configuración, autoload y sesión.
 */
class App
{
    private static array $config = [];

    public static function iniciar(): void
    {
        self::$config = require APP_PATH . '/config/config.php';
        date_default_timezone_set(self::$config['app']['zona_horaria']);

        spl_autoload_register(function (string $clase) {
            foreach (['core', 'models', 'controllers'] as $carpeta) {
                $archivo = APP_PATH . "/$carpeta/$clase.php";
                if (is_file($archivo)) {
                    require_once $archivo;
                    return;
                }
            }
        });

        require_once APP_PATH . '/core/helpers.php';
        Session::iniciar();
    }

    public static function config(string $clave)
    {
        return self::$config[$clave] ?? null;
    }
}
