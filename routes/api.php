<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CreditCards;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('v1/auth/register', [AuthController::class, 'create']);
Route::post('v1/auth/login', [AuthController::class, 'login']);



// Rutas protegidas por autenticacion

// Route::middleware(['auth:sanctum'])->group(function () {
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {


    // cerrar sesion
    Route::post('v1/auth/logout', [AuthController::class, 'logout']);

    // solo el usuario administrador podra eliminar a otros usuarios
    Route::delete('v1/delete/user/{email}', [AuthController::class, 'deleteByEmail']);

    // Ruta adicional para PATCH
    Route::patch('v1/creditCard/{id}', [CreditCards::class, 'updatePartial']);
    // mostrar todas las tarjetas de credito o carteras en base el tipo
    Route::get('v1/creditCard/{tipo}', [CreditCards::class, 'index']);
    // mostrar detalle de la tarjeta de credito gastos y pagos
    Route::get('v1/creditCard/detalle/{id}', [CreditCards::class, 'obtenerDetalleGastoYPago']);

    Route::resource('v1/creditCard', CreditCards::class);


    // Ver el datalle de gasto de una tarjeta de credito
    Route::get('v1/expenseCreditCard/{id}', [ExpenseController::class, 'detalleGastosPorTarjeta']);
    Route::post('v1/expenseCreditCard/{id}', [ExpenseController::class, 'registrarGasto']);
    Route::delete('v1/expenseCreditCard/{id}', [ExpenseController::class, 'eliminarGasto']);
    Route::patch('v1/expenseCreditCard/{id}', [ExpenseController::class, 'actualizarGasto']);

    // pagos tarjeta de credito 
    Route::post('v1/paymentCreditCard/{id}', [PaymentController::class, 'registrarPago']);
    Route::delete('v1/paymentCreditCard/{id}', [PaymentController::class, 'eliminarPago']);
    Route::patch('v1/paymentCreditCard/{id}', [PaymentController::class, 'actualizarPago']);
});
