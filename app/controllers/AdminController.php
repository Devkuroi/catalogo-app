<?php
/**
 * Panel del administrador: usuarios registrados y gestión de promociones.
 */
class AdminController extends Controller
{
    public function index(): void
    {
        $this->requiereAdmin();

        $usuarios  = (new Usuario())->todos();
        $productos = (new Producto())->todos();
        $porPais   = (new Usuario())->totalesPorPais();

        $this->render('admin/index', [
            'titulo'    => 'Panel de administración',
            'usuarios'  => $usuarios,
            'productos' => $productos,
            'stats'     => [
                'usuarios'  => count($usuarios),
                'colombia'  => (int) ($porPais['Colombia'] ?? 0),
                'espana'    => (int) ($porPais['España'] ?? 0),
                'vigentes'  => count(array_filter($productos, fn($p) => $p['vigente'])),
                'productos' => count($productos),
            ],
            'tab' => ($_GET['tab'] ?? '') === 'productos' ? 'productos' : 'usuarios',
        ]);
    }

    public function cambiarEstadoProducto(): void
    {
        $this->requiereAdmin();
        if (!$this->esPost()) {
            $this->redirect('admin');
        }
        $this->verificarCsrf('admin');

        $modelo = new Producto();
        $id = (int) ($_POST['id'] ?? 0);

        if ($modelo->cambiarEstado($id)) {
            $p = $modelo->buscarPorId($id);
            Session::flash('success', '"' . $p['nombre'] . '" ahora está ' . ($p['activo'] ? 'activo' : 'oculto') . ' en el catálogo.');
        } else {
            Session::flash('danger', 'No se encontró el producto.');
        }
        $this->redirect('admin', ['tab' => 'productos']);
    }
}
