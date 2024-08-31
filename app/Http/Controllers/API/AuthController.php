<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardarUsuarioRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;



class AuthController extends Controller
{
    /*
     * Registrar nuevo usuario.
     */
    public function create(GuardarUsuarioRequest $request)
    {
        DB::beginTransaction();
        try {
            // Obtiene los datos validados del request
            $validated = $request->validated();

            // Establece valores por defecto
            $validated['rol'] = 'user';
            $validated['estado'] = '1';
            // encriptar la contraseña 
            $validated['password'] = Hash::make($validated['password']);
            // Crea un nuevo usuario
            $user = User::create($validated);

            // Confirmar la transacción
            DB::commit();

            // Responde con éxito
            return response()->json([
                'res' => true,
                'msg' => 'Usuario almacenado correctamente',
                'data' => $user->email
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {

            DB::rollBack();
            // Maneja cualquier excepción
            return response()->json([
                'res' => false,
                'msg' => 'Error al almacenar el usuario: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
