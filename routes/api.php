<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CreditCards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('v1/auth/register', [AuthController::class, 'create']);
Route::post('v1/auth/login', [AuthController::class, 'login']);



// Rutas protegidas

Route::middleware(['auth:sanctum'])->group(function () {
    // cerrar sesion
    Route::post('v1/auth/logout', [AuthController::class, 'logout']);

    // solo el usuario administrador podra eliminar a otros usuarios
    Route::delete('v1/delete/user/{email}', [AuthController::class, 'deleteByEmail']);
    
    // registrar tarjetas de credito
    Route::resource('v1/creditCard', CreditCards::class);
});