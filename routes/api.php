<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('v1/auth/register', [AuthController::class, 'create']);
Route::post('v1/auth/login', [AuthController::class, 'login']);

// Rutas protegidas

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('v1/auth/logout', [AuthController::class, 'logout']);
});