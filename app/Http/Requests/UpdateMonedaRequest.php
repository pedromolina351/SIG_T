<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonedaRequest extends FormRequest
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
             'monedaID' => 'required|integer|exists:Moneda,monedaID',
             'nombreMoneda' => 'required|string|max:60',
             'descReducida' => 'nullable|string|max:10',
             'simboloMoneda' => 'nullable|string|max:5',
             'usuarioRegistro' => 'required|integer|max:30',
         ];
     }
}
