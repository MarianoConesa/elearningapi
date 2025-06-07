<?php

use App\Http\Controllers\API\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VideoController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Socialite\Facades\Socialite;

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/initialInfo', [UserController::class, 'getInitialInfo']);
            Route::post('followCourse', [UserController::class, 'followCourse']);
            Route::post('endCourse', [UserController::class, 'endCourse']);
            Route::post('unfollowCourse', [UserController::class, 'unfollowCourse']);
            Route::post('updateUser', [UserController::class, 'updateUser']);
            Route::post('updatePassword', [UserController::class, 'updatePassword']);
        });
    });
    Route::get('user/getUserInfo/{id}', [UserController::class, 'getForeignUserInfo']);

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
        Route::get('getByUserId/{id}', [CourseController::class, 'getCoursesByUserId']);
        Route::post('search', [CourseController::class, 'searchCourses']);
    });

    Route::get('/videos/stream/{filename}', [VideoController::class, 'stream'])->name('api.videostream');

    Route::get('getCategories', [CategoryController::class, 'getCategories']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/createComment', [CommentController::class, 'createComment']);
        Route::post('/updateComment/{id}', [CommentController::class, 'updateComment']);
        Route::delete('/removeComment/{id}', [CommentController::class, 'removeComment']);
    });

    Route::get('/getComments/{courseId}', [CommentController::class, 'getComments']);


    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgotPassword', [AuthController::class, 'forgotPassword']);
