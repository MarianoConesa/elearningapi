<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registro de usuarios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validación de datos
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Crear nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generar token
        $token = User::find($user->id)->createToken('api-token')->plainTextToken;

        return response()->json(['message' => 'Usuario registrado correctamente', 'token' => $token]);
    }

    /**
     * Inicio de sesión de usuarios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
       // Intento de inicio de sesión
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Usuario autenticado, generar token
            $user = Auth::user();
            $userModel = User::findOrFail($user->id);
            $token = $userModel->createToken('api-token')->plainTextToken;

            return response()->json(['message' => 'Inicio de sesión exitoso', 'token' => $token]);
        } else {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
    }

    /**
     * Cierre de sesión de usuarios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revocar el token del usuario autenticado
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}
