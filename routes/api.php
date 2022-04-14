<?php

use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [UserController::class, 'login']);
Route::put('/users/update/contrasenia/{id}', [UserController::class, 'nuevaContra']);
Route::put('/users/update/datos/{id}', [UserController::class, 'editDatos']);
Route::put('/users/update/foto/{id}', [UserController::class, 'editFoto']);
Route::get('/publicaciones/get/{id}', [PublicacionesController::class, 'getByUser']);
Route::post('/publicaciones/reportar', [PublicacionesController::class, 'reportar']);
Route::post('/publicaciones/reclamar', [PublicacionesController::class, 'reclamar']);
Route::put('/publicaciones/cerrar/{id}', [PublicacionesController::class, 'cerrarPublicacion']);

Route::resource('users', UserController::class);
Route::resource('publicaciones', PublicacionesController::class);