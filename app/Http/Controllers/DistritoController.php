<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use Illuminate\Http\Request;

class DistritoController extends Controller
{
    public function index(Request $request)
    {
        return Distrito::query()
            ->when($request->get('departamento_id'), function ($query, $id) {
                $query->where('department_id', $id);
            })
            ->when($request->get('provincia_id'), function ($query, $id) {
                $query->where('province_id', $id);
            })
            ->get();
    }
}
