<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    /**
     * Obtener la lista de formularios
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
     */
    public function show(Formulario $formulario)
    {
        return $formulario;
    }
}
