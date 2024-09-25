<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuardarPagoRequest extends FormRequest
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
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'nullable|string|max:255',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que el id_tarjeta sea un número válido
            if (!is_numeric($this->route('id'))) {
                $validator->errors()->add('id', 'El id debe ser un número.');
            }
        });
    }


    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha del pago es obligatoria.',
            'fecha.date' => 'La fecha debe tener un formato válido (por ejemplo: YYYY-MM-DD).',
            'cantidad.required' => 'La cantidad del pago es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un valor numérico.',
            'cantidad.min' => 'La cantidad debe ser mayor o igual a 0.',
            'motivo.string' => 'El motivo debe ser una cadena de texto.',
            'motivo.max' => 'El motivo no puede tener más de 255 caracteres.',
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
