<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
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
            'codigo_cliente' => 'nullable|string|max:20',
            'personaID' => 'nullable|integer',
            'agremiadoID' => 'nullable|integer',
            'tipoClienteID' => 'nullable|integer',
            'departamentoID' => 'nullable|integer',
            'municipioID' => 'nullable|integer',
            'estadoID' => 'nullable|integer',
            'nombre_comercial' => 'nullable|string|max:255',
            'nroIdentificacion' => 'nullable|string|max:20',
            'nombre' => 'nullable|string|max:100',
            'apellido1' => 'nullable|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'apellido3' => 'nullable|string|max:50',
            'sexo' => 'nullable|string|max:1',
            'direccion' => 'nullable|string|max:150',
            'telefono1' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:50',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,webp',
        ];
    }


}
