<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function(){
    // Route::prefix('auth')->middleware('throttle:auth')->group(function(){
    //     Route::post('register', [UserController::class, 'register']);
    //     Route::post('login',    [UserController::class, 'login']);
    // });
    Route::prefix('auth')->group(function(){
        Route::post('register', [UserController::class, 'register']);
        Route::post('login',    [UserController::class, 'login'])->middleware('throttle:10,1');
    });
    
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [UserController::class, 'logout']);
        Route::apiResource('posts', PostController::class)->names('v1.posts');
    });
});

Route::prefix('v2')->group(function(){
    Route::apiResource('posts', PostController::class)->names('v2.posts');
});
