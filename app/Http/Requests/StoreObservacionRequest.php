<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObservacionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'expediente_id' => 'required|exists:expediente,id',
            'tipoobservacion_id' => 'nullable',
            'descripcion' => 'required|string|min:20',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'descripcion.min' => 'La descripción de la observación debe tener al menos 20 caracteres.',
        ];
    }
}
