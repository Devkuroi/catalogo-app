<?php
/**
 * Catálogo de productos en promoción (usuarios autenticados).
 */
class CatalogoController extends Controller
{
    public function index(): void
    {
        $this->requiereLogin();

        $productos  = (new Producto())->vigentes();
        $categorias = array_values(array_unique(array_filter(array_column($productos, 'categoria'))));
        sort($categorias);

        $this->render('catalogo/index', [
            'titulo'     => 'Catálogo',
            'productos'  => $productos,
            'categorias' => $categorias,
        ]);
    }
}
