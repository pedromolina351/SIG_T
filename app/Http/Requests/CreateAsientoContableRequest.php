<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAsientoContableRequest extends FormRequest
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
    public function rules()
    {
        return [
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'id_periodo' => 'required|integer',
            'id_usuario_creador' => 'required|integer|exists:Usuarios,usuarioID',
            'id_usuario_aprobador' => 'required|integer|exists:Usuarios,usuarioID',
            'listado_imagenes' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede exceder los 255 caracteres.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'id_periodo.required' => 'El ID del periodo es obligatorio.',
            'id_periodo.integer' => 'El ID del periodo debe ser un número entero.',
            'id_usuario_creador.required' => 'El ID del usuario creador es obligatorio.',
            'id_usuario_creador.string' => 'El ID del usuario creador debe ser una cadena de texto.',
            'id_usuario_creador.exists' => 'El ID del usuario creador no existe en la base de datos.',
            'id_usuario_aprobador.required' => 'El ID del usuario aprobador es obligatorio.',
            'id_usuario_aprobador.string' => 'El ID del usuario aprobador debe ser una cadena de texto.',
            'id_usuario_aprobador.exists' => 'El ID del usuario aprobador no existe en la base de datos.',
        ];
    }


}
