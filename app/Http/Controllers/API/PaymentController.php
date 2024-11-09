<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarPagoRequest;
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


            if ($tarjeta->tipo == 'billeteraEfectivo') {
                $nuevoSaldo = $tarjeta->saldo + $validated['cantidad'];
            } else {
                $nuevoSaldo = $tarjeta->saldo - $validated['cantidad'];
            }
            if ($nuevoSaldo < 0) {
                return response()->json([
                    'message' => 'No puedes abonar más del saldo disponible en la tarjeta.'
                ], 200);
            }

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

    public function eliminarPago(string $id, string $tipo)
    {

        // Validar que el ID de la tarjeta sea numérico
        if (!is_numeric($id)) {
            return response()->json([
                'error' => 'El ID del gasto es requerido y debe ser un número válido.'
            ], 400);
        }

        // Obtener el ID del usuario autenticado
        $userId = auth()->user()->id;

        DB::beginTransaction();

        try {

            // Obtener el pago con el id proporcionado
            $pago = Payment::where('id', $id)->first();

            // Verificar si el pago existe
            if (!$pago) {
                return response()->json(['error' => 'Pago no encontrado'], 404);
            }

            $cantidadEliminar = $pago->cantidad;

            // Obtener el id de la tarjeta asociada al gasto
            $idTarjetaAsociada = $pago->id_tarjeta;

            // Validar que la tarjeta pertenece al usuario autenticado
            $tarjeta = CreditCard::where('id', $idTarjetaAsociada)->where('idusuario', $userId)->first();

            // Si la tarjeta no pertenece al usuario o no existe, devolver un error
            if (!$tarjeta) {
                return response()->json(['error' => 'No tienes acceso a esta tarjeta'], 403);
            }

            $pago->delete();

            if ($tipo == 'billeteraEfectivo') {
                $nuevoSaldo = $tarjeta->saldo - $cantidadEliminar;
            } else if ($tipo == 'tarjetaCredito') {
                $nuevoSaldo = $tarjeta->saldo + $cantidadEliminar;
            }
            // restar el monto del pago
            $tarjeta->saldo = $nuevoSaldo;
            $tarjeta->save();

            DB::commit();

            return response()->json([
                'message' => 'Pago eliminado correctamente y saldo actualizado.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ocurrió un error al eliminar el pago: ' . $e->getMessage()], 500);
        }
    }

    public function actualizarPago(ActualizarPagoRequest $request, $id, string $tipo)
    {

        // Validar que el ID de la tarjeta sea numérico
        if (!is_numeric($id)) {
            return response()->json([
                'error' => 'El ID del gasto es requerido y debe ser un número válido.'
            ], 400);
        }

        // Obtener el ID del usuario autenticado
        $userId = auth()->user()->id;

        DB::beginTransaction();

        try {

            // Obtener el pago con el id proporcionado
            $pago = Payment::where('id', $id)->first();

            // Verificar si el pago existe
            if (!$pago) {
                return response()->json(['error' => 'Pago no encontrado'], 404);
            }

            $cantidadOriginal = $pago->cantidad;

            // Obtener el id de la tarjeta asociada al gasto
            $idTarjetaAsociada = $pago->id_tarjeta;

            // Validar que la tarjeta pertenece al usuario autenticado
            $tarjeta = CreditCard::where('id', $idTarjetaAsociada)->where('idusuario', $userId)->first();

            // Si la tarjeta no pertenece al usuario o no existe, devolver un error
            if (!$tarjeta) {
                return response()->json(['error' => 'No tienes acceso a esta tarjeta'], 403);
            }

            $data = $request->validated();

            // Obtener la nueva cantidad del gasto
            $nuevaCantidad = $data['cantidad'] ?? null;

            if (!is_null($nuevaCantidad) && $nuevaCantidad != $cantidadOriginal) {

                if ($tipo == 'billeteraEfectivo') {
                    // Si la nueva cantidad es mayor que la cantidad original, restamos la diferencia al saldo
                    if ($nuevaCantidad > $cantidadOriginal) {
                        $diferencia = $nuevaCantidad - $cantidadOriginal;
                        $tarjeta->saldo += $diferencia;
                    }

                    // Si la nueva cantidad es menor que la cantidad original, sumamos la diferencia al saldo
                    else if ($nuevaCantidad < $cantidadOriginal) {
                        $diferencia = $cantidadOriginal - $nuevaCantidad;
                        $tarjeta->saldo -= $diferencia;
                    }
                } else if ($tipo == 'tarjetaCredito') {
                    // Si la nueva cantidad es mayor que la cantidad original, restamos la diferencia al saldo
                    if ($nuevaCantidad > $cantidadOriginal) {
                        $diferencia = $nuevaCantidad - $cantidadOriginal;
                        $tarjeta->saldo -= $diferencia;
                    }

                    // Si la nueva cantidad es menor que la cantidad original, sumamos la diferencia al saldo
                    else if ($nuevaCantidad < $cantidadOriginal) {
                        $diferencia = $cantidadOriginal - $nuevaCantidad;
                        $tarjeta->saldo += $diferencia;
                    }
                }

                // Guardar los cambios en la tarjeta
                $tarjeta->save();
            }

            $pago->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Pago actualizado correctamente y saldo actualizado.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ocurrió un error al actualizar el Pago: ' . $e->getMessage()], 500);
        }
    }
}
