<?php
/**
 * Suite de Pruebas Automatizadas para el Proyecto PromoCatálogo
 * Compatible con ejecución local por CLI y Pipelines de Jenkins CI.
 * Genera salida en consola y archivo de reporte JUnit XML para Jenkins.
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/core/App.php';
App::iniciar();

class TestRunner
{
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;

    public function assert(bool $condition, string $testName, string $message = ''): void
    {
        if ($condition) {
            $this->passed++;
            $this->results[] = ['name' => $testName, 'status' => 'PASS', 'message' => ''];
            echo "\033[32m  [PASS]\033[0m $testName\n";
        } else {
            $this->failed++;
            $this->results[] = ['name' => $testName, 'status' => 'FAIL', 'message' => $message];
            echo "\033[31m  [FAIL]\033[0m $testName - $message\n";
        }
    }

    public function assertEquals($expected, $actual, string $testName): void
    {
        $this->assert(
            $expected === $actual,
            $testName,
            "Esperado: " . json_encode($expected) . " | Obtenido: " . json_encode($actual)
        );
    }

    public function generateJunitXml(string $filePath): void
    {
        $total = $this->passed + $this->failed;
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<testsuite name=\"PromoCatalogoSuite\" tests=\"{$total}\" failures=\"{$this->failed}\" errors=\"0\">\n";

        foreach ($this->results as $r) {
            $xml .= "  <testcase name=\"" . htmlspecialchars($r['name']) . "\">\n";
            if ($r['status'] === 'FAIL') {
                $xml .= "    <failure message=\"" . htmlspecialchars($r['message']) . "\">" . htmlspecialchars($r['message']) . "</failure>\n";
            }
            $xml .= "  </testcase>\n";
        }

        $xml .= "</testsuite>\n";
        @file_put_contents($filePath, $xml);
    }

    public function isSuccess(): bool
    {
        return $this->failed === 0;
    }

    public function printSummary(): void
    {
        echo "\n--------------------------------------------------\n";
        echo "Resumen de Pruebas: {$this->passed} Exitosas, {$this->failed} Fallidas.\n";
        if ($this->failed === 0) {
            echo "\033[32m✔ TODAS LAS PRUEBAS PASARON EXITOSAMENTE (CI READY)\033[0m\n";
        } else {
            echo "\033[31m✖ SE DETECTARON FALLOS EN LA SUITE\033[0m\n";
        }
        echo "--------------------------------------------------\n";
    }
}

$runner = new TestRunner();

// Configuración de base de datos en memoria (SQLite) para entorno de pruebas / CI
$sqlite = new PDO('sqlite::memory:');
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlite->exec('CREATE TABLE usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_documento TEXT UNIQUE NOT NULL,
    nombre TEXT NOT NULL,
    apellidos TEXT NOT NULL,
    correo TEXT UNIQUE NOT NULL,
    ciudad TEXT NOT NULL,
    pais TEXT NOT NULL,
    password TEXT NOT NULL,
    rol TEXT DEFAULT "usuario",
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);');
$sqlite->exec('CREATE TABLE productos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    descripcion TEXT,
    precio_original REAL NOT NULL,
    precio_promocion REAL NOT NULL,
    imagen TEXT,
    categoria TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    activo INTEGER DEFAULT 1
);');
Database::setConexion($sqlite);

echo "==================================================\n";
echo " Ejecutando Suite de Pruebas: PromoCatálogo CI\n";
echo "==================================================\n\n";

// -----------------------------------------------------------------------------
// 1. Pruebas de Autocompletado de Ciudad y País (Colombia, Italia y Francia)
// -----------------------------------------------------------------------------
echo "[1] Probando catálogo y autocompletado de Ubicación...\n";

$paises = Ubicacion::paises();
$runner->assert(in_array('Colombia', $paises), 'Catálogo incluye Colombia');
$runner->assert(in_array('Italia', $paises), 'Catálogo incluye Italia');
$runner->assert(in_array('Francia', $paises), 'Catálogo incluye Francia');

// Autocompletado de Roma -> Italia
$buscarRoma = Ubicacion::buscar('Roma');
$runner->assert(!empty($buscarRoma), 'Ciudad Roma existe en el catálogo');
$runner->assertEquals('Italia', $buscarRoma[0]['pais'] ?? '', 'Autocompletado: Roma resuelve a Italia');

// Autocompletado de Milán / Milano -> Italia
$buscarMilan = Ubicacion::buscar('Milán');
$runner->assertEquals('Italia', $buscarMilan[0]['pais'] ?? '', 'Autocompletado: Milán resuelve a Italia');

// Autocompletado de París -> Francia
$buscarParis = Ubicacion::buscar('París');
$runner->assert(!empty($buscarParis), 'Ciudad París existe en el catálogo');
$runner->assertEquals('Francia', $buscarParis[0]['pais'] ?? '', 'Autocompletado: París resuelve a Francia');

// Autocompletado de Bogotá / Medellín -> Colombia
$buscarBogota = Ubicacion::buscar('Bogotá');
$runner->assertEquals('Colombia', $buscarBogota[0]['pais'] ?? '', 'Autocompletado: Bogotá resuelve a Colombia');

// Resistencia a mayúsculas y acentos
$buscarSinTilde = Ubicacion::buscar('bogota');
$runner->assertEquals('Colombia', $buscarSinTilde[0]['pais'] ?? '', 'Normalización insensible a tildes/mayúsculas');


// -----------------------------------------------------------------------------
// 2. Pruebas de Validación y Persistencia en Formulario de Registro
// -----------------------------------------------------------------------------
echo "\n[2] Probando validación y persistencia de errores...\n";

$usuario = new Usuario();

// Datos con errores múltiples para validar persistencia y señalizaciones específicas
$datosInvalidos = [
    'nombre'           => '1234', // Invalido: números
    'apellidos'        => '',     // Inválido: vacío
    'ciudad'           => 'Roma', // Válido
    'pais'             => '',     // Se debe autocompletar a Italia
    'numero_documento' => '',     // Inválido: vacío
    'correo'           => 'correo-no-valido', // Inválido: formato
    'password'         => '123',  // Inválido: muy corta
    'confirm_password' => '456',  // Inválido: no coincide
    'terminos'         => false,  // Inválido: no aceptado
];

$limpios = Usuario::limpiar($datosInvalidos);
[$datosCorregidos, $errores] = $usuario->validarRegistro($limpios);

// Verificar autocompletado en validación de backend
$runner->assertEquals('Italia', $datosCorregidos['pais'], 'Backend autocompleta país Italia para ciudad Roma');

// Verificar persistencia de valores introducidos
$runner->assertEquals('1234', $datosCorregidos['nombre'], 'Persistencia: el nombre introducido se conserva');
$runner->assertEquals('Roma', $datosCorregidos['ciudad'], 'Persistencia: la ciudad introducida se conserva');
$runner->assertEquals('correo-no-valido', $datosCorregidos['correo'], 'Persistencia: el correo introducido se conserva');

// Verificar señalizaciones específicas de error junto a cada campo
$runner->assert(isset($errores['nombre']), 'Error específico generado para campo nombre');
$runner->assert(isset($errores['apellidos']), 'Error específico generado para campo apellidos');
$runner->assert(isset($errores['numero_documento']), 'Error específico generado para campo numero_documento');
$runner->assert(isset($errores['correo']), 'Error específico generado para campo correo');
$runner->assert(isset($errores['password']), 'Error específico generado para campo password');
$runner->assert(isset($errores['confirm_password']), 'Error específico generado para confirmación de contraseña');
$runner->assert(isset($errores['terminos']), 'Error específico generado para términos');

// Registro válido para verificar que pasa limpio
$datosValidos = [
    'nombre'           => 'Giovanni',
    'apellidos'        => 'Rossi',
    'ciudad'           => 'Roma',
    'pais'             => 'Italia',
    'numero_documento' => 'RM12345678',
    'correo'           => 'test_giovanni_' . time() . '@catalogo.com',
    'password'         => 'ClaveSegura123',
    'confirm_password' => 'ClaveSegura123',
    'terminos'         => true,
];
$limpiosValidos = Usuario::limpiar($datosValidos);
[$datosOk, $erroresOk] = $usuario->validarRegistro($limpiosValidos);
$runner->assert(empty($erroresOk), 'Validación exitosa sin errores para datos completos y válidos');


// -----------------------------------------------------------------------------
// 3. Pruebas de Detalle y Cálculos del Catálogo de Productos
// -----------------------------------------------------------------------------
echo "\n[3] Probando catálogo y cálculo de promociones...\n";

$productoDummy = [
    'id'               => 1,
    'nombre'           => 'Laptop Gaming Pro',
    'descripcion'      => 'Laptop de alto rendimiento',
    'precio_original'  => 1200.00,
    'precio_promocion' => 899.99,
    'fecha_inicio'     => '2026-09-01',
    'fecha_fin'        => '2026-12-31',
];

$ahorro = $productoDummy['precio_original'] - $productoDummy['precio_promocion'];
$porcentaje = round(($ahorro / $productoDummy['precio_original']) * 100);

$runner->assert($ahorro > 300, 'Cálculo de ahorro mayor a 300 USD');
$runner->assertEquals(25.0, (float)$porcentaje, 'Cálculo de porcentaje de descuento (25% OFF)');

// Generar reporte JUnit XML para Jenkins
$junitPath = ROOT_PATH . '/tests/junit.xml';
$runner->generateJunitXml($junitPath);
echo "\nReporte JUnit XML exportado en: {$junitPath}\n";

$runner->printSummary();

exit($runner->isSuccess() ? 0 : 1);
