<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index()
    {
        return Formulario::all();
    }

    public function show(Formulario $entidad)
    {
        return $entidad;
    }

    /*
    public function store(FormularioStoreRequest $request)
    {
        $data = $request->all();
        $entidad = new Formulario();
        $entidad->fill($data);
        $entidad->save();
        return $entidad;
    }


    public function update(FormularioStoreRequest $request, Formulario $entidad)
    {
        $entidad->update($request->all());
        return $entidad;
    }

    public function destroy(Formulario $entidad)
    {
        $entidad->delete();
        return $entidad;
    }
    */
}
