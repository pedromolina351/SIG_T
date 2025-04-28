<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClienteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación aplicadas a la solicitud.
     */
    public function rules(): array
    {
        return [
            'personaID' => 'required|integer|exists:RegistroPersonas,personaID',
            'agremiadoID' => 'nullable|integer|exists:Agremiados,agremiadoID',
            'tipoClienteID' => 'required|integer|exists:TiposCliente,tipoClienteID',
            'departamentoID' => 'required|integer|exists:Departamentos,departamentoID',
            'municipioID' => 'required|integer|exists:Municipios,municipioID',
            'estadoID' => 'sometimes|nullable|integer|in:0,1',
            'nombre_comercial' => 'sometimes|nullable|string|max:255'
        ];
    }

    /**
     * Mensajes de error personalizados para cada regla de validación.
     */
    public function messages(): array
    {
        return [
            'personaID.required' => 'El ID de persona es obligatorio.',
            'personaID.exists' => 'El ID de persona no existe en la base de datos.',
            'agremiadoID.exists' => 'El ID de agremiado no existe en la base de datos.',
            'tipoClienteID.required' => 'El tipo de cliente es obligatorio.',
            'tipoClienteID.exists' => 'El tipo de cliente no existe en la base de datos.',
            'departamentoID.required' => 'El departamento es obligatorio.',
            'departamentoID.exists' => 'El departamento no existe en la base de datos.',
            'municipioID.required' => 'El municipio es obligatorio.',
            'municipioID.exists' => 'El municipio no existe en la base de datos.',
            'estadoID.in' => 'El estado solo puede ser 0 (Inactivo) o 1 (Activo).',
            'nombre_comercial.max' => 'El nombre comercial no puede exceder los 255 caracteres.'
        ];
    }
}
