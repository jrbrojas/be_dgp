<?php

namespace App\Http\Controllers;

use App\Models\UbigeoPeruProvince;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function index(Request $request)
    {
        return UbigeoPeruProvince::query()
            ->when($request->get('departamento_id'), function ($query, $id) {
                $query->where('department_id', $id);
            })
            ->get();
    }
}
