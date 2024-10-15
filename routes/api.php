<?php

use App\Http\Controllers\API\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UserController;

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            $user = $request->user();
            $user->fullName = $user->nombre . ' ' . $user->apellidos;
            return $user;
        });
    });

    Route::prefix('user')->group(function () {
        Route::get('/initialInfo', [UserController::class, 'getInitialInfo'])->middleware('auth:sanctum');
    });

    Route::prefix('images')->group(function () {
        Route::get('initialImages', [ImageController::class, 'getInitialImg']);
    });

    Route::get('getCategories', [CategoryController::class, 'getCategories']);


    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
