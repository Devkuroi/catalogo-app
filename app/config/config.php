<?php
/**
 * Configuración general de la aplicación.
 * Cambia aquí los datos de conexión según tu entorno (XAMPP por defecto).
 */
return [
    'app' => [
        'nombre'    => 'PromoCatálogo',
        // Muestra las credenciales de prueba en el login.
        // ⚠️ Poner en false antes de publicar en producción.
        'modo_demo' => true,
        'zona_horaria' => 'America/Bogota',
    ],

    'db' => [
        'host'     => 'localhost',
        'nombre'   => 'catalogo_db',
        'usuario'  => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
        // Opcional: DSN completo (útil para pruebas). Si se define, ignora host/nombre.
        'dsn'      => getenv('CATALOGO_DSN') ?: null,
    ],

    // Cuentas de acceso de prueba (se muestran en el login mientras modo_demo = true)
    'cuentas_demo' => [
        [
            'rol'       => 'admin',
            'etiqueta'  => 'Administrador',
            'correo'    => 'admin@catalogo.com',
            'password'  => 'Admin123*',
            'documento' => '1000000001',
            'nombre'    => 'Ana',
            'apellidos' => 'Administradora',
            'ciudad'    => 'Cali',
            'pais'      => 'Colombia',
        ],
        [
            'rol'       => 'usuario',
            'etiqueta'  => 'Usuario',
            'correo'    => 'usuario@catalogo.com',
            'password'  => 'Usuario123*',
            'documento' => 'RM12345678',
            'nombre'    => 'Marco',
            'apellidos' => 'Rossi',
            'ciudad'    => 'Roma',
            'pais'      => 'Italia',
        ],
    ],
];
