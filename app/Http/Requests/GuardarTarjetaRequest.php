<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuardarTarjetaRequest extends FormRequest
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
            'name' => 'required|string|max:75',
            'saldo' => 'sometimes|numeric', // 'sometimes' para que no sea obligatorio
            'icono' => 'required|string|max:75',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la tarjeta es obligatorio.',
            'name.string' => 'El nombre de la tarjeta debe ser una cadena de texto.',
            'name.max' => 'El nombre de la tarjeta no debe exceder los 75 caracteres.',

            'saldo.numeric' => 'El saldo debe ser un número válido.',

            'icono.required' => 'El ícono de la tarjeta es obligatorio.',
            'icono.string' => 'El ícono debe ser una cadena de texto.',
            'icono.max' => 'El ícono no debe exceder los 75 caracteres.',
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
