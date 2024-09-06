<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActualizarTarjetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'saldo' => 'sometimes|numeric|min:0',
            'icono' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => 'El nombre es opcional pero, si se proporciona, debe ser una cadena de texto y no exceder los 255 caracteres.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',

            'saldo.sometimes' => 'El saldo es opcional pero, si se proporciona, debe ser un número y no puede ser menor que 0.',
            'saldo.numeric' => 'El saldo debe ser un número.',
            'saldo.min' => 'El saldo no puede ser menor que 0.',

            'icono.sometimes' => 'El icono es opcional pero, si se proporciona, debe ser una cadena de texto y no exceder los 255 caracteres.',
            'icono.string' => 'El icono debe ser una cadena de texto.',
            'icono.max' => 'El icono no puede exceder los 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'res' => false,
            'msg' => $validator->errors(),
        ], 422));
    }
}
