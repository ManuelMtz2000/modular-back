<?php

use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\UserController;
use App\Models\User;
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

Route::get('/auth/siiau/{codigo}/{password}', [UserController::class, 'siiau']);
Route::post('/auth/login', [UserController::class, 'login']);
Route::post('/auth/login/siiau', [UserController::class, 'loginSiiau']);
Route::post('/auth/login/siiau/verificar', [UserController::class, 'verificarSiiau']);
Route::put('/users/update/contrasenia/{id}', [UserController::class, 'nuevaContra']);
Route::put('/users/update/datos/{id}', [UserController::class, 'editDatos']);
Route::put('/users/update/foto/{id}', [UserController::class, 'editFoto']);
Route::post('users/verificar/{id}', [UserController::class, 'verificar']);
Route::get('/users/validate-user/{id}', [UserController::class, 'validarStatus']);
Route::get('/publicaciones/get/{id}', [PublicacionesController::class, 'getByUser']);
Route::post('/publicaciones/reportar', [PublicacionesController::class, 'reportar']);
Route::post('/publicaciones/reclamar', [PublicacionesController::class, 'reclamar']);
Route::put('/publicaciones/cerrar/{id}', [PublicacionesController::class, 'cerrarPublicacion']);
Route::post('/publicaciones/busqueda', [PublicacionesController::class, 'search']);
Route::post('/publicaciones/busqueda-inteligente', [PublicacionesController::class, 'busquedaInteligente']);
Route::get('/ayuda', [UserController::class, 'ayuda']);

Route::resource('users', UserController::class);
Route::resource('publicaciones', PublicacionesController::class);