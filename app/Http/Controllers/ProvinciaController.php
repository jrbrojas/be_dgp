<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\UbigeoPeruProvince;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function index(Request $request)
    {
        return Provincia::when($request->get('departamento_id'), function ($query, $id) {
                $query->where('departmento_id', $id);
            })
            ->get();
    }
}
