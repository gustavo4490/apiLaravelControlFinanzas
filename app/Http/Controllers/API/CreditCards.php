<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardarTarjetaRequest;
use App\Models\CreditCard;
use Illuminate\Http\Request;

class CreditCards extends Controller
{
    /*
     * obtener todas las tarjetas que tenga el usuario autenticado
     */
    public function index()
    {
        try {
            // Obtener el ID del usuario autenticado
            $userId = auth()->user()->id;

            // Obtener las tarjetas de crédito del usuario autenticado
            $tarjetas = CreditCard::where('idusuario', $userId)->get();

            // Verificar si se encontraron tarjetas de crédito
            if ($tarjetas->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron tarjetas de crédito para este usuario.'
                ], 404);
            }

            return response()->json($tarjetas, 200);
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
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
