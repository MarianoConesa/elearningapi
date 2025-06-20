<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\Image;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
        try{
        // Validación de datos
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'profilePic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048'
        ]);

        // Crear nuevo usuario
        $user = User::create((array)$request->except('profilePic'));
        if ($request->hasFile('profilePic')) {
            $imagePath = $request->file('profilePic')->store('profile_pics', 'public');
            $newImg = Image::create(['name' => $request->username . 'profile_pic',
                           'file' => $imagePath,]);
            $user->update(['profilePic' => $newImg->id]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registro exitoso. Revisa tu correo para verificar tu cuenta.'
        ]);

        }catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el perfil',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inicio de sesión de usuarios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Intento de inicio de sesión
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Debes verificar tu correo antes de iniciar sesión.'
                ], 403);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
                'user' => $user
            ]);
        }

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Error en el inicio de sesión',
            'error' => $e->getMessage(),
        ], 500);
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
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }

    public function deleteUser(Request $request)
    {
        try{
            $user = $request->user();
            User::findOrFail($user->id)->delete();
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        }catch (Exception  $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function forgotPassword(Request $request)
{
    try{
        $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->email)->first();

    // Generar nueva contraseña aleatoria
    $newPassword = Str::random(10);

    // Actualizar en la base de datos
    $user->password = Hash::make($newPassword);
    $user->save();

    // Enviar correo
    Mail::to($user->email)->send(new PasswordResetMail($newPassword, $user));

    return response()->json(['message' => 'Se ha enviado un correo con la nueva contraseña.']);
    }catch (Exception  $e){
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
