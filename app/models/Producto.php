<?php
/**
 * Modelo de productos en promoción.
 */
class Producto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conexion();
    }

    /** Productos activos cuya promoción sigue vigente. */
    public function vigentes(): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM productos
             WHERE activo = 1 AND fecha_fin >= :hoy
             ORDER BY fecha_fin ASC'
        );
        $stmt->execute([':hoy' => date('Y-m-d')]);
        return array_map([$this, 'enriquecer'], $stmt->fetchAll());
    }

    public function todos(): array
    {
        $stmt = $this->db->query('SELECT * FROM productos ORDER BY id ASC');
        return array_map([$this, 'enriquecer'], $stmt->fetchAll());
    }

    public function cambiarEstado(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE productos SET activo = 1 - activo WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM productos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ? $this->enriquecer($fila) : null;
    }

    /** Agrega campos calculados que necesitan las vistas. */
    private function enriquecer(array $p): array
    {
        $original = (float) $p['precio_original'];
        $promo    = (float) $p['precio_promocion'];

        $p['activo']     = (bool) $p['activo'];
        $p['descuento']  = $original > 0 ? (int) round(($original - $promo) / $original * 100) : 0;
        $p['ahorro']     = $original - $promo;
        $hoy = new DateTimeImmutable('today');
        $fin = new DateTimeImmutable($p['fecha_fin']);
        $p['dias_restantes'] = $fin >= $hoy ? (int) $hoy->diff($fin)->days : -1;
        $p['vigente'] = $p['activo'] && $p['dias_restantes'] >= 0;
        return $p;
    }
}
