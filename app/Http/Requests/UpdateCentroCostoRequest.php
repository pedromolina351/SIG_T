<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCentroCostoRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'centroCostoID' => 'required|numeric|min:1',
            'codcentroCosto' => 'required|numeric|min:1',
            'nombreCentroCosto' => 'required|string|max:255',
            'abreviaturaCentro' => 'required|string|max:15',
            'descripcionCentroCosto' => 'required|string|max:255',
            'estado' => 'required|boolean',
            'usuarioRegistro' => 'required|integer|max:30',
        ];
    }
}
