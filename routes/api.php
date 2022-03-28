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

/*Route::get('/fotos_p/{filename}', function ($filename)
{
    $file = \Illuminate\Support\Facades\Storage::get("fotos_p/$filename");
    dd($file);
    return response($file, 200)->header('Content-Type', 'image/jpeg');
});*/

Route::resource('users', UserController::class);
Route::resource('publicaciones', PublicacionesController::class);