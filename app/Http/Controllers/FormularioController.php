<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    /**
     * Obtener la lista de formularios
     *
     * Devuelve el catálogo de formularios disponibles para la creación de escenarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $formularios = Formulario::get();
        return response()->json([
            'list' => $formularios,
        ]);
    }

    /**
     * Mostrar la información de un formulario
     *
     * Muestra el detalle de un formulario específico y su configuración asociada.
     *
     * @return Formulario
     */
    public function show(Formulario $formulario)
    {
        return $formulario;
    }
}
