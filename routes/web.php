<?php

use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ServerRequestInterface;
use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config\Config;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Hash inválido.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'El correo ya estaba verificado.']);
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/');
    return redirect()->away("{$frontendUrl}/emailVerified");
})->name('verification.verify');

// Route::post('/email/resend', function (Request $request) {
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json(['message' => 'Usuario no encontrado'], 404);
    //     }

    //     if ($user->hasVerifiedEmail()) {
    //         return response()->json(['message' => 'Este email ya está verificado']);
    //     }

    //     $user->sendEmailVerificationNotification();

    //     return response()->json(['message' => 'Correo de verificación reenviado']);
    // });

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->stateless()->redirect();
});

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate(
        ['google_id' => $googleUser->getId()],
        [
            'username' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'email_verified_at' => now(),
            'profilePic' => $googleUser->getAvatar(),
        ]
    );

    $token = $user->createToken('api-token')->plainTextToken;

    $frontendUrl = rtrim(env('APP_FRONTEND', 'http://localhost:5173'), '/');
    return redirect()->away("{$frontendUrl}/loginSuccess?token={$token}");
});
