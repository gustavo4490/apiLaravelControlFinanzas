<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarTarjetaRequest;
use App\Http\Requests\GuardarTarjetaRequest;
use App\Models\CreditCard;
use Illuminate\Http\Request;

class CreditCards extends Controller
{
    /*
     * obtener todas las tarjetas que tenga el usuario autenticado
     */
    public function index(string $tipo)
    {
        try {
            if (is_null($tipo)) {
                return response()->json([
                    'message' => 'El parámetro tipo es requerido.',
                    'numero' => 0
                ], 200);
            }
            // Obtener el ID del usuario autenticado
            $userId = auth()->user()->id;

            // Obtener las tarjetas de crédito del usuario autenticado

            $tarjetas = CreditCard::where('idusuario', $userId)
                ->where('tipo', $tipo)
                ->orderBy('id', 'desc')
                ->get();

            // Verificar si se encontraron tarjetas de crédito
            if ($tarjetas->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron tarjetas de crédito para este usuario.',
                    'numero' => 0
                ], 200);
            }

            return response()->json([
                'tarjetas' => $tarjetas,
                'numero' => 1
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las tarjetas de crédito.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GuardarTarjetaRequest $request)
    {
        try {
            // Obtener los datos validados
            $data = $request->validated();

            // Obtener el ID del usuario autenticado
            $userId = auth()->user()->id;

            // Establecer el campo 'idusuario' con el ID del usuario autenticado
            $data['idusuario'] = $userId;

            // Crear un nuevo registro en la base de datos
            CreditCard::create($data);

            // Devolver una respuesta exitosa con un mensaje personalizado
            return response()->json([
                'res' => true,
                'msg' => 'Tarjeta de crédito almacenada con éxito.',
            ], 201);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver una respuesta de error
            return response()->json([
                'res' => false,
                'msg' => 'Hubo un error al almacenar la tarjeta de crédito.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Validar que el ID de la tarjeta sea numérico
        if (!is_numeric($id)) {
            return response()->json([
                'error' => 'El ID de la tarjeta de crédito es requerido y debe ser un número válido.'
            ], 400);
        }

        // Encuentra la tarjeta de crédito asociada al usuario
        try {
            $creditCard = CreditCard::where('id', $id)
                ->where('idusuario', auth()->id())
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'La tarjeta de crédito no existe o no pertenece al usuario autenticado.'
            ], 404);
        }

        // Respuesta con los datos de la tarjeta de crédito
        return response()->json([
            'creditCard' => $creditCard,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function updatePartial(ActualizarTarjetaRequest $request, $id)
    {
        // Validar que el ID de la tarjeta sea numérico
        if (!is_numeric($id)) {
            return response()->json([
                'error' => 'El ID de la tarjeta de crédito es requerido y debe ser un número válido.'
            ], 400);
        }

        // Obtén el usuario autenticado
        $user = auth()->user();

        // Encuentra la tarjeta de crédito asociada al usuario
        $creditCard = CreditCard::where('id', $id)->where('idusuario', $user->id)->first();

        if (!$creditCard) {
            return response()->json([
                'error' => 'La tarjeta de crédito no existe o no pertenece al usuario autenticado.'
            ], 404);
        }

        // Valida los datos de la solicitud
        $data = $request->validated();

        // Actualiza solo los campos enviados en la solicitud
        $creditCard->update($data);

        // Respuesta con los datos actualizados
        return response()->json([
            'message' => 'Tarjeta actualizada con éxito.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            return response()->json([
                'error' => 'El ID de la tarjeta de crédito es requerido y debe ser un número válido.'
            ], 400);
        }

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Buscar la tarjeta de crédito que pertenece al usuario
        $creditCard = CreditCard::where('id', $id)->where('idusuario', $user->id)->first();

        // Validar si la tarjeta existe y pertenece al usuario
        if (!$creditCard) {
            return response()->json([
                'error' => 'La tarjeta de crédito no existe o no pertenece al usuario autenticado.'
            ], 404);
        }

        // Eliminar la tarjeta
        $creditCard->delete();

        return response()->json([
            'message' => 'Tarjeta de crédito eliminada correctamente.'
        ]);
    }
}
