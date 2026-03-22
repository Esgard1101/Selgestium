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

    public function rules(): array
    {
        return [
            'expediente_id' => 'required|integer|exists:expediente,id',
            'jurado_id' => 'required|integer|exists:persona,id',
            'ronda' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'tipo_veredicto' => 'required|string|in:aprobado,observado',
        ];
    }
}
