<?php

use App\Http\Controllers\API\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UserController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Socialite\Facades\Socialite;

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            $user = $request->user();
            $user->fullName = $user->nombre . ' ' . $user->apellidos;
            return $user;
        });
    });

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

    Route::prefix('user')->group(function () {
        Route::get('/initialInfo', [UserController::class, 'getInitialInfo'])->middleware('auth:sanctum');
        Route::post('followCourse', [UserController::class, 'followCourse'])->middleware('auth:sanctum');
        Route::post('endCourse', [UserController::class, 'endCourse'])->middleware('auth:sanctum');
        Route::post('unfollowCourse', [UserController::class, 'unfollowCourse'])->middleware('auth:sanctum');
    });

    Route::prefix('images')->group(function () {
        Route::get('initialImages', [ImageController::class, 'getInitialImg']);
    });

    Route::prefix('courses')->group(function () {
        Route::post('create', [CourseController::class, 'createCourse'])->middleware('auth:sanctum');
        Route::get('getAll', [CourseController::class, 'getAllCourses']);
        Route::post('getById', [CourseController::class, 'getCourseById']);
        Route::get('getOwned', [CourseController::class, 'getOwnedCourses'])->middleware('auth:sanctum');
        Route::get('getFollowed', [CourseController::class, 'getFollowedCourses'])->middleware('auth:sanctum');
        Route::get('getLiked', [CourseController::class, 'getLikedCourses'])->middleware('auth:sanctum');
        Route::get('getEnded', [CourseController::class, 'getEndedCourses'])->middleware('auth:sanctum');
    });

    Route::get('getCategories', [CategoryController::class, 'getCategories']);


    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
