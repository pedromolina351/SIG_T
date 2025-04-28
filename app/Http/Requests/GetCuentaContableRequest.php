<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCuentaContableRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "cuentaID" => ["required", "integer", "exists:CuentasContables,cuentaID"]
        ];
    }
}
