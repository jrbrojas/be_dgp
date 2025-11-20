<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Obtener todos los roles
     */
    public function index()
    {
        $roles = Role::get();
        return response()->json([
            'list' => $roles
        ]);
    }
}
