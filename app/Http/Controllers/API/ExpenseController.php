<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
   
    public function detalleGastosPorTarjeta(string $id)
{
    // Obtener el ID del usuario autenticado
    $userId = auth()->user()->id;

    // Buscar la tarjeta con el ID proporcionado y asegurarse de que pertenece al usuario autenticado
    $tarjeta = CreditCard::where('id', $id)->where('idusuario', $userId)->first();

    // Si la tarjeta no pertenece al usuario o no existe, devolver un error
    if (!$tarjeta) {
        return response()->json(['error' => 'No tienes acceso a esta tarjeta'], 403);
    }

    // Obtener los gastos asociados a la tarjeta
    $gastos = Expense::where('id_tarjeta', $id)->get();

    // Retornar la información de la tarjeta y los gastos
    return response()->json([
        'tarjeta' => $tarjeta,
        'gastos' => $gastos,
    ]);
}

}
