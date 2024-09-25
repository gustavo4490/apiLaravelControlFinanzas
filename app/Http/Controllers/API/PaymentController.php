<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardarPagoRequest;
use App\Models\CreditCard;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function registrarPago(GuardarPagoRequest $request, $id)
    {
        // Validar que el ID de la tarjeta sea numérico
        if (!is_numeric($id)) {
            return response()->json([
                'error' => 'El ID de la tarjeta de crédito es requerido y debe ser un número válido.'
            ], 400);
        }

        // Obtener el ID del usuario autenticado
        $userId = auth()->user()->id;

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Validar que la tarjeta pertenece al usuario autenticado
            $tarjeta = CreditCard::where('id', $id)->where('idusuario', $userId)->first();

            // Si la tarjeta no pertenece al usuario o no existe, devolver un error
            if (!$tarjeta) {
                return response()->json(['error' => 'No tienes acceso a esta tarjeta'], 403);
            }

            $validated = $request->validated();

            // Establece valores por defecto
            $validated['id_tarjeta'] = $tarjeta->id;
            // Crea un nuevo pago
            Payment::create($validated);

            $nuevoSaldo = $tarjeta->saldo - $validated['cantidad'];
            $tarjeta->saldo = $nuevoSaldo;
            $tarjeta->save();

            // Confirmar la transacción
            DB::commit();

            // Retornar la respuesta de éxito
            return response()->json([
                'message' => 'pago registrado correctamente y saldo actualizado'
            ], 201);
        } catch (\Exception $e) {
            // Revertir los cambios en caso de error
            DB::rollBack();
            // Retornar un error
            return response()->json(['error' => 'Ocurrió un error al registrar el gasto: ' . $e->getMessage()], 500);
        }
    }
}
