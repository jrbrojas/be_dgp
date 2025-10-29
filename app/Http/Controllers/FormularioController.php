<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index()
    {
        $formularios = Formulario::get();
        return response()->json($formularios);
    }

    public function show(Formulario $formulario)
    {
        return $formulario;
    }
}
