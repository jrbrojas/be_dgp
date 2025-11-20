<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Login de usuario
     *
     * Autentica credenciales válidas y devuelve un token JWT para acceder a los endpoints protegidos de la API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $token = $request->authenticate();
        return $this->respondWithToken($token);
    }

    /**
     * Usuario autenticado (perfil)
     *
     * Devuelve la información del usuario autenticado junto con su rol y datos básicos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        /** @var \App\Models\User */
        return response()->json(auth()->user()->load('role'));
    }

    /**
     * Cerrar sesión
     *
     * Cierra la sesión del usuario autenticado e invalida el token JWT activo.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * Refrescar token
     *
     * Genera un nuevo token JWT reemplazando al anterior sin necesidad de volver a iniciar sesión.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            /** @var \App\Models\User */
            'user' => Auth::user()->load('role'),
            'status' => 'success',
        ]);

    }
}
