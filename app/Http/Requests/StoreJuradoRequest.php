<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJuradoRequest extends FormRequest
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
            'jurados' => 'required|array|size:3',
            'jurados.*' => 'required|integer|exists:persona,id',
        ];
    }

    public function messages(): array
    {
        return [
            'jurados.size' => 'Se deben asignar exactamente 3 jurados.',
            'jurados.*.exists' => 'Uno de los jurados seleccionados no existe.',
        ];
    }
}
