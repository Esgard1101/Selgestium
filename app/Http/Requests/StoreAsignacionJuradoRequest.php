<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreAsignacionJuradoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expediente_id' => 'required|integer|exists:expediente,id',
            'presidente_id' => 'required|integer|exists:persona,id',
            'secretario_id' => 'required|integer|exists:persona,id',
            'vocal_id' => 'required|integer|exists:persona,id',
            'numero_resolucion' => 'required|string|max:100',
            'fecha_emision' => 'required|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $ids = [
                $this->input('presidente_id'),
                $this->input('secretario_id'),
                $this->input('vocal_id')
            ];

            // 3 jurados distintos
            if (count(array_unique($ids)) !== 3) {
                $validator->errors()->add('jurados', 'Los jurados deben ser personas distintas.');
            }

            // Ninguno puede ser el asesor del expediente
            $expediente = DB::table('expediente')->where('id', $this->input('expediente_id'))->first();
            if ($expediente && in_array($expediente->asesor_id, $ids)) {
                $validator->errors()->add('jurados', 'El docente que es asesor del expediente no puede ser asignado como jurado.');
            }

            // Existen en persona con rol jurado (rol_id = 8)
            foreach ($ids as $id) {
                $esJurado = DB::table('rolpersona')
                    ->where('persona_id', $id)
                    ->where('rol_id', 8)
                    ->exists();

                if (!$esJurado) {
                    $validator->errors()->add('jurados', "La persona con ID {$id} no tiene el rol de Jurado.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'presidente_id.required' => 'El presidente es obligatorio.',
            'secretario_id.required' => 'El secretario es obligatorio.',
            'vocal_id.required' => 'El vocal es obligatorio.',
            'numero_resolucion.required' => 'El número de resolución es obligatorio.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
        ];
    }
}
