<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpedienteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Retornamos true porque asumimos que el RoleMiddleware 
        // ya se encargó de verificar que quien entra aquí es un Alumno
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplicarán a la petición.
     */
    public function rules(): array
    {
        return [
            // Reglas para los campos de texto
            'titulo'      => ['required', 'string', 'max:255'],
            'tipo'        => ['required', 'string', 'in:cuantitativo,cualitativo,mixto'],

            // El asesor puede ser opcional al inicio, pero si lo envían, debe existir en la BD
            'asesor_id'   => ['nullable', 'integer', 'exists:persona,id'],

            // Regla crítica (Feature 2): Archivo requerido, solo PDF, máximo 10MB (10240 KB)
            'archivo_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    /**
     * Mensajes de error personalizados 
     */
    public function messages(): array
    {
        return [
            'titulo.required'      => 'El título del proyecto es obligatorio.',
            'titulo.max'           => 'El título es demasiado largo (máximo 255 caracteres).',

            'tipo.required'        => 'Debe seleccionar el tipo de investigación.',
            'tipo.in'              => 'El tipo de investigación seleccionado no es válido.',

            'asesor_id.exists'     => 'El asesor seleccionado no se encuentra registrado en el sistema.',

            'archivo_pdf.required' => 'Es obligatorio adjuntar el documento de su proyecto.',
            'archivo_pdf.file'     => 'Hubo un error al subir el archivo.',
            'archivo_pdf.mimes'    => 'El documento adjunto debe ser estrictamente un archivo PDF.',
            'archivo_pdf.max'      => 'El archivo PDF es muy pesado. El tamaño máximo permitido es 10MB.',
        ];
    }
}
