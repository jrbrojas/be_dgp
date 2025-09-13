<?php

namespace App\Http\Controllers;

use App\Models\Escenario;
use App\Models\PlantillaA;
use Illuminate\Http\Request;

class PlantillaAController extends Controller
{
    public function index()
    {
        //
    }

    public function show(PlantillaA $plantillaA)
    {
        //
    }

    public function update(Request $request, PlantillaA $plantillaA)
    {
        //
    }

    public function destroy(PlantillaA $plantillaA)
    {
        //
    }

    public function download(Request $request, Escenario $escenario)
    {
        dd($escenario);
    }
}
