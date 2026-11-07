<?php
/**
 * Conexión PDO única (patrón Singleton).
 */
class Database
{
    private static ?PDO $conexion = null;

    public static function setConexion(?PDO $conexion): void
    {
        self::$conexion = $conexion;
    }

    public static function conexion(): PDO
    {
        if (self::$conexion === null) {
            $cfg = App::config('db');
            $dsn = $cfg['dsn'] ?: sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $cfg['host'],
                $cfg['nombre'],
                $cfg['charset']
            );

            self::$conexion = new PDO($dsn, $cfg['usuario'], $cfg['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$conexion;
    }
}
