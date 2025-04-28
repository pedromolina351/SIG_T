<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Log;

class UpdateCuentaContableRequest extends FormRequest
{
    public function authorize()
    
    {
        return true;
    }

    public function rules()
    {
        return [
            'cuentaID' => 'required|integer|exists:CuentasContables,cuentaID',
            'codigoCuentaID' => 'nullable|string|max:50|unique:CuentasContables,codigoCuentaID,' . $this->cuentaID . ',cuentaID',
            'nombre' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string|max:255',
            'nivel' => 'nullable|integer|min:1|max:7',
            'id_cuenta_padre' => 'nullable|integer|exists:CuentasContables,cuentaID',
            'centroCostoID' => 'nullable|integer|exists:CentroCosto,centroCostoID',
            'modalidad' => 'nullable|string|in:Cuentas de Orden,De Resultado,De Ajuste,Transitoria,Auxiliar,Operativa,Sumaria,Principal',
            'codigo_sar' => 'nullable|string|max:20',
            'tipo' => 'nullable|string|in:Transaccional,Sumaria',
            'naturaleza' => 'nullable|string|in:Acreedora,Deudora',
            'categoria' => 'nullable|string|in:Gasto,Costo,Ingreso,Patrimonio,Pasivo,Activo',
            'id_moneda' => 'nullable|integer|exists:Moneda,id_moneda',
            'estado' => 'nullable|boolean',
        ];
    }
}
