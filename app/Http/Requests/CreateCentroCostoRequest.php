<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCentroCostoRequest extends FormRequest
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
            'codcentroCosto' => 'required|numeric|min:1',
            'nombreCentroCosto' => 'required|string|max:255',
            'abreviaturaCentro' => 'required|string|max:15',
            'descripcionCentro' => 'required|string|max:255',
            'estado' => 'required|boolean',
            'usuarioRegistro' => 'required|integer|max:30',
        ];
    }
}
