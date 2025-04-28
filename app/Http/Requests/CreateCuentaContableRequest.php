<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCuentaContableRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigoCuentaID' => 'required|string|max:50|unique:CuentasContables,codigoCuentaID',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:255',
            'nivel' => 'required|integer|min:1|max:7',
            'id_cuenta_padre' => 'nullable|integer|exists:CuentasContables,cuentaID',
            'centroCostoID' => 'required|integer|exists:CentroCosto,centroCostoID',
            'modalidad' => 'required|string|in:Cuentas de Orden,De Resultado,De Ajuste,Transitoria,Auxiliar,Operativa,Sumaria,Principal',
            'codigo_sar' => 'nullable|string|max:20',
            'tipo' => 'required|string|in:Transaccional,Sumaria',
            'naturaleza' => 'required|string|in:Acreedora,Deudora',
            'categoria' => 'required|string|in:Gasto,Costo,Ingreso,Patrimonio,Pasivo,Activo',
            'id_moneda' => 'required|integer|exists:Moneda,id_moneda',
            'estado' => 'nullable|boolean',
        ];
    }
}
