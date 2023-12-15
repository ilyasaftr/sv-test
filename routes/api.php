<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

Route::get('/users', [\App\Http\Controllers\UserController::class, 'me']);
Route::patch('/users', [\App\Http\Controllers\UserController::class, 'update']);

Route::apiResource('/categories', \App\Http\Controllers\CategoryController::class);
Route::apiResource('/products', \App\Http\Controllers\ProductController::class);
Route::apiResource('/products/{id}/reviews', \App\Http\Controllers\ReviewController::class);

// custom
Route::get('/ping', [\App\Http\Controllers\PingController::class, 'ping']);
