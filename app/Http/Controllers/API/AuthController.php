<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardarUsuarioRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;




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


    public function login(LoginRequest $request)
    {
        // Verifica si las credenciales son válidas
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'errors' => ['No autorizado']
            ], 401);
        }

        // Obtén el usuario autenticado
        $user = Auth::user();

        // Genera un token de acceso personal
        $token = $user->createToken('API TOKEN')->plainTextToken;

        // Retorna la respuesta con el token
        return response()->json([
            'status' => true,
            'message' => 'Usuario logueado correctamente',
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'type' => $user->rol,
            ],
            'token' => $token
        ], 200);
    }

    public function logout()
    {

        return  'salir';
        // auth()->user()->tokens()->delete();
        // return response()->json([
        //     'status' => true,
        //     'message' => 'Sesión cerrada correctamente'
        // ], 200);
    }
}
