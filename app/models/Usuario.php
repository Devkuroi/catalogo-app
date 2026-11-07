<?php
/**
 * Modelo de usuarios: consultas, reglas de validación del registro y cuentas demo.
 */
class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conexion();
    }

    /* ================= Consultas ================= */

    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => mb_strtolower(trim($correo))]);
        return $stmt->fetch() ?: null;
    }

    public function existe(string $campo, string $valor): bool
    {
        if (!in_array($campo, ['correo', 'numero_documento'], true)) {
            throw new InvalidArgumentException('Campo no permitido');
        }
        $stmt = $this->db->prepare("SELECT 1 FROM usuarios WHERE $campo = :valor LIMIT 1");
        $stmt->execute([':valor' => $valor]);
        return (bool) $stmt->fetchColumn();
    }

    public function crear(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (numero_documento, nombre, apellidos, correo, ciudad, pais, password, rol)
             VALUES (:documento, :nombre, :apellidos, :correo, :ciudad, :pais, :password, :rol)'
        );
        $stmt->execute([
            ':documento' => $d['numero_documento'],
            ':nombre'    => $d['nombre'],
            ':apellidos' => $d['apellidos'],
            ':correo'    => $d['correo'],
            ':ciudad'    => $d['ciudad'],
            ':pais'      => $d['pais'],
            ':password'  => password_hash($d['password'], PASSWORD_DEFAULT),
            ':rol'       => $d['rol'] ?? 'usuario',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function todos(): array
    {
        return $this->db->query(
            'SELECT id, numero_documento, nombre, apellidos, correo, ciudad, pais, rol, fecha_registro
             FROM usuarios ORDER BY fecha_registro DESC, id DESC'
        )->fetchAll();
    }

    public function totalesPorPais(): array
    {
        $filas = $this->db->query('SELECT pais, COUNT(*) AS total FROM usuarios GROUP BY pais')->fetchAll();
        return array_column($filas, 'total', 'pais');
    }

    /**
     * Garantiza que existan las cuentas de prueba definidas en config.php.
     * Así las credenciales que se muestran en el login siempre funcionan.
     */
    public function asegurarCuentasDemo(array $cuentas): void
    {
        foreach ($cuentas as $c) {
            if (!$this->buscarPorCorreo($c['correo']) && !$this->existe('numero_documento', $c['documento'])) {
                $this->crear([
                    'numero_documento' => $c['documento'],
                    'nombre'    => $c['nombre'],
                    'apellidos' => $c['apellidos'],
                    'correo'    => $c['correo'],
                    'ciudad'    => $c['ciudad'],
                    'pais'      => $c['pais'],
                    'password'  => $c['password'],
                    'rol'       => $c['rol'],
                ]);
            }
        }
    }

    /* ================= Reglas de negocio ================= */

    /** Limpia y normaliza los datos que llegan del formulario de registro. */
    public static function limpiar(array $entrada): array
    {
        $txt = fn($k) => preg_replace('/\s+/', ' ', trim((string) ($entrada[$k] ?? '')));
        return [
            'nombre'           => $txt('nombre'),
            'apellidos'        => $txt('apellidos'),
            'ciudad'           => $txt('ciudad'),
            'pais'             => $txt('pais'),
            'numero_documento' => strtoupper(preg_replace('/[\s.\-]/', '', (string) ($entrada['numero_documento'] ?? ''))),
            'correo'           => mb_strtolower(trim((string) ($entrada['correo'] ?? ''))),
            'password'         => (string) ($entrada['password'] ?? ''),
            'confirm_password' => (string) ($entrada['confirm_password'] ?? ''),
            'terminos'         => !empty($entrada['terminos']),
        ];
    }

    /**
     * Valida el registro. Devuelve [datosCorregidos, errores por campo].
     * Si la ciudad es conocida y pertenece a un solo país, el país se fija automáticamente.
     */
    public function validarRegistro(array $d): array
    {
        $errores = [];
        $soloLetras = "/^[\p{L}' -]{2,60}$/u";

        if ($d['nombre'] === '')                      $errores['nombre'] = 'Escribe tu nombre.';
        elseif (!preg_match($soloLetras, $d['nombre'])) $errores['nombre'] = 'Usa solo letras (mínimo 2).';

        if ($d['apellidos'] === '')                      $errores['apellidos'] = 'Escribe tus apellidos.';
        elseif (!preg_match($soloLetras, $d['apellidos'])) $errores['apellidos'] = 'Usa solo letras (mínimo 2).';

        // ---- Ciudad y país ----
        $coincidencias = $d['ciudad'] !== '' ? Ubicacion::buscar($d['ciudad']) : [];
        $paisesCiudad = array_column($coincidencias, 'pais');

        if ($d['ciudad'] === '') {
            $errores['ciudad'] = 'Escribe tu ciudad.';
        } elseif (count($coincidencias) === 1) {
            // Ciudad conocida en un solo país: se autocompleta y bloquea
            $d['ciudad'] = $coincidencias[0]['ciudad'];
            $d['pais']   = $coincidencias[0]['pais'];
        } elseif (count($coincidencias) > 1) {
            $d['ciudad'] = $coincidencias[0]['ciudad'];
            if (!in_array($d['pais'], $paisesCiudad, true)) {
                $errores['pais'] = 'Esta ciudad existe en ' . implode(' y ', $paisesCiudad) . '. Elige el país.';
            }
        } elseif (mb_strlen($d['ciudad']) < 2) {
            $errores['ciudad'] = 'El nombre de la ciudad es muy corto.';
        }

        if (!isset($errores['pais']) && !Ubicacion::esPaisValido($d['pais'])) {
            $errores['pais'] = 'Selecciona Colombia, Italia o Francia.';
        }

        // ---- Documento (depende del país) ----
        if ($d['numero_documento'] === '') {
            $errores['numero_documento'] = 'Escribe tu número de documento.';
        } elseif (Ubicacion::esPaisValido($d['pais'])) {
            $info = Ubicacion::PAISES[$d['pais']];
            if (!preg_match('/' . $info['documento_patron'] . '/', $d['numero_documento'])) {
                $errores['numero_documento'] = $info['documento'] . ' no válido. ' . $info['documento_ayuda'];
            }
        }
        if (!isset($errores['numero_documento']) && $d['numero_documento'] !== ''
            && $this->existe('numero_documento', $d['numero_documento'])) {
            $errores['numero_documento'] = 'Este documento ya está registrado.';
        }

        // ---- Correo ----
        if ($d['correo'] === '') {
            $errores['correo'] = 'Escribe tu correo electrónico.';
        } elseif (!filter_var($d['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'El correo no tiene un formato válido.';
        } elseif ($this->existe('correo', $d['correo'])) {
            $errores['correo'] = 'Ya existe una cuenta con este correo.';
        }

        // ---- Contraseña ----
        if (strlen($d['password']) < 8 || !preg_match('/[A-Za-z]/', $d['password']) || !preg_match('/\d/', $d['password'])) {
            $errores['password'] = 'Mínimo 8 caracteres, con al menos una letra y un número.';
        }
        if ($d['confirm_password'] === '' || $d['password'] !== $d['confirm_password']) {
            $errores['confirm_password'] = 'Las contraseñas no coinciden.';
        }

        if (!$d['terminos']) {
            $errores['terminos'] = 'Debes aceptar el tratamiento de datos.';
        }

        return [$d, $errores];
    }
}
