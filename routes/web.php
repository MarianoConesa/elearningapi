<?php

use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ServerRequestInterface;
use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config\Config;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

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
