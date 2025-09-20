<?php

namespace App\Http\Controllers;

use App\Models\Escenario;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::with('roles')->get();
        // eviar los parametros de esta forma para que el datatable del front los pueda leer sin problemas
        return response()->json([
            'list' => $usuarios,
            'total' => $usuarios->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // creacion de prueba
        $data = $request->validate([
            'role' => 'required|exists:roles,name',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);
        $data['password'] = '12345';
        $usuario = User::create($data);
        $usuario->assignRole($data['role']);

        return response()->json([
            'message' => 'Usuario credo exitoasamente!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        return $usuario;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'role' => 'required|exists:roles,name',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
        ]);
        $usuario->update($data);
        $usuario->syncRoles($data['role']);
        return response()->json(['message' => 'Usuario actualizado exitosamente!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}
