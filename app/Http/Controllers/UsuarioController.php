<?php

namespace App\Http\Controllers;

use App\Models\Escenario;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Listado de todos los usuarios
     *
     * Lista los usuarios registrados en el sistema junto con su rol.
     *
     *@return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $usuarios = User::with('role')->search($request['query'])->orderBy('name')->get();
        // enviar los parametros de esta forma para que el datatable del front los pueda leer sin problemas
        return response()->json([
            'list' => $usuarios,
            'total' => $usuarios->count(),
        ]);
    }

    /**
     * Guardar un nuevo usuario
     *
     * Registra un nuevo usuario en el sistema con su respectivo rol y credenciales seguras.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // creacion de prueba
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'sometimes|string|min:5',
            'password_confirmation' => 'sometimes|required_with:password|same:password',
        ]);

        User::create($data);

        return response()->json([
            'message' => 'Usuario credo exitoasamente!'
        ]);
    }

    /**
     * Mostrar informacion de un usuario
     *
     * Muestra los detalles de un usuario específico identificado por su ID.
     *
     *  @return App\Models\User;
     */
    public function show(User $usuario)
    {
        return $usuario;
    }

    /**
     * Actualizar informacion de un usuario
     *
     * Actualiza la información de un usuario existente, incluyendo nombre, correo u otros atributos permitidos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'sometimes|nullable|string|min:5',
            'password_confirmation' => 'sometimes|required_with:password|same:password',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $usuario->update($data);
        return response()->json(['message' => 'Usuario actualizado exitosamente!']);
    }

    /**
     * Eliminar un usuario
     *
     * Elimina un usuario del sistema junto con sus relaciones asociadas, si las hubiera.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}
