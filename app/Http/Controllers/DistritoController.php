<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use Illuminate\Http\Request;

class DistritoController extends Controller
{
    /**
     * Obtener todos los distritos
     */
    public function index(Request $request)
    {
        return Distrito::when($request->get('departamento_id'), function ($query, $id) {
                $query->where('department_id', $id);
            })
            ->when($request->get('provincia_id'), function ($query, $id) {
                $query->where('province_id', $id);
            })
            ->get();
    }
}
