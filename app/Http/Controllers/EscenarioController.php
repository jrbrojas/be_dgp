<?php

namespace App\Http\Controllers;

use App\Models\Escenario;
use Illuminate\Http\Request;

class EscenarioController extends Controller
{
    public function index()
    {
        return Escenario::all();
    }

    public function show(Escenario $entidad)
    {
        return $entidad;
    }

    public function store(EscenarioStoreRequest $request)
    {
        $data = $request->all();
        $entidad = new Escenario();
        $entidad->fill($data);
        $entidad->save();
        return $entidad;
    }

    public function update(EscenarioStoreRequest $request, Escenario $entidad)
    {
        $entidad->update($request->all());
        return $entidad;
    }

    public function destroy(Escenario $entidad)
    {
        $entidad->delete();
        return $entidad;
    }
}
