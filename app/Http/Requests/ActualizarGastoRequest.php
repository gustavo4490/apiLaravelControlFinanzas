<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActualizarGastoRequest extends FormRequest
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
            'fecha' => 'nullable|date',
            'empresa' => 'nullable|string|max:255',
            'cantidad' => 'nullable|numeric|min:0',
            'detalle' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'fecha.date' => 'El campo fecha debe ser una fecha válida.',
            'empresa.string' => 'El campo empresa debe ser un texto válido.',
            'empresa.max' => 'El nombre de la empresa no debe exceder los 255 caracteres.',
            'cantidad.numeric' => 'El campo cantidad debe ser un número.',
            'cantidad.min' => 'La cantidad debe ser al menos 0.',
            'detalle.string' => 'El campo detalle debe ser un texto válido.',
            'detalle.max' => 'El detalle no debe exceder los 255 caracteres.',
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
