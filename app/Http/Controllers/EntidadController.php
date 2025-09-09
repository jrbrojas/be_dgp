<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Http\Requests\EntidadStoreRequest;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    public function index()
    {
        return Entidad::all();
    }

    public function store(Request $request)
    {
        $data = $request->json();
        $entidad = new Entidad();
        $entidad->fill($data);
        $entidad->save();
        return $entidad;
    }
}
