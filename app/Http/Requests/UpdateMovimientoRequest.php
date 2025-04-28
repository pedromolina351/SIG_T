<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovimientoRequest extends FormRequest
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
            'id_movimiento' => 'required|integer',
            'descripcion' => 'nullable|string|max:255',
            'fecha_transaccion' => 'nullable|date',
            'debito' => 'nullable|numeric|min:0',
            'credito' => 'nullable|numeric|min:0',
            'referencia' => 'nullable|string|max:50',
        ];
    }

}
