<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVerificacionCreditosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // autorización vía middleware verificar.permiso
    }

    public function rules(): array
    {
        return [
            'expediente_id' => ['required', 'integer', 'exists:expediente,id'],
            'creditos'      => ['required', 'integer', 'min:0', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'expediente_id.required' => 'Debes seleccionar un expediente.',
            'expediente_id.exists'   => 'El expediente seleccionado no existe.',
            'creditos.required'      => 'El número de créditos es obligatorio.',
            'creditos.integer'       => 'Los créditos deben ser un número entero.',
            'creditos.min'           => 'Los créditos no pueden ser negativos.',
            'creditos.max'           => 'El valor máximo permitido es 300 créditos.',
        ];
    }
}
