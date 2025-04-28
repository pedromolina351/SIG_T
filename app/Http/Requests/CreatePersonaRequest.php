<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePersonaRequest extends FormRequest
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
            'nombre' => 'required|string|max:100',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'apellido3' => 'nullable|string|max:50',
            'tipoPersona' => 'required|string', 
            'tipoID' => 'required|integer',
            'nroIdentificacion' => 'required|string|max:20|unique:RegistroPersonas,nroIdentificacion',
            'fechaNacimiento' => 'required|date',
            'fechaConstitucion' => 'nullable|date',
            'sexo' => 'required|string|in:M,F,O', // M: Masculino, F: Femenino, O: Otro
            'direccion' => 'nullable|string|max:150',
            'telefono1' => 'nullable|string|max:50',
            'telefono2' => 'nullable|string|max:50',
            'estadoID' => 'nullable|integer',
            'email' => 'nullable|email|max:50|unique:RegistroPersonas,email',
            'usuarioRegistro' => 'required|integer|max:30',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido1.required' => 'El primer apellido es obligatorio.',
            'tipoPersona.in' => 'El tipo de persona debe ser F (Física) o J (Jurídica).',
            'sexo.in' => 'El sexo debe ser M (Masculino), F (Femenino) o O (Otro).',
            'nroIdentificacion.unique' => 'El número de identificación ya está registrado.',
            'email.unique' => 'El correo electrónico ya está registrado.',
        ];
    }
}
