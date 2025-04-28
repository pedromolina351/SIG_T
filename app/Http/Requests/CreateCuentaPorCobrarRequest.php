<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCuentaPorCobrarRequest extends FormRequest
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
            'id_contraparte' => 'required|integer',
            'tipo_contraparte' => 'required|string|in:Cliente,Proveedor,Empleado,Agremiado,Centro de Costo',
            'id_centro_origen' => 'required|integer',
            'id_centro_destino' => 'nullable|integer',
            'fuente' => 'required|string|in:Venta,Cuota,Préstamo,Multa,Convenio,Otros',
            'concepto' => 'required|string|max:255',
            'id_asiento' => 'nullable|integer',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'monto_total' => 'required|numeric|min:0',
            'monto_pagado' => 'nullable|numeric|min:0',
            'estado' => 'required|string|in:Pendiente,Pagado,Vencido',
            'id_factura' => 'nullable|integer',
            'id_prestamo' => 'nullable|integer',
            'id_cxp' => 'nullable|integer',
            'plazo_meses' => 'nullable|integer',
            'frecuencia_pago' => 'nullable|string|in:Mensual,Bimestral,Trimestral',
            'interes_aplicado' => 'nullable|numeric',
            'monto_cuota' => 'nullable|numeric',
            'referencia' => 'nullable|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'id_contraparte.required' => 'El campo id_contraparte es obligatorio.',
            'tipo_contraparte.required' => 'El campo tipo_contraparte es obligatorio.',
            'tipo_contraparte.in' => 'El campo tipo_contraparte debe ser uno de los siguientes: Cliente, Proveedor, Empleado, Agremiado, Centro de Costo.',
            'id_centro_origen.required' => 'El campo id_centro_origen es obligatorio.',
            'fuente.required' => 'El campo fuente es obligatorio.',
            'fuente.in' => 'El campo fuente debe ser uno de los siguientes: Venta, Cuota, Préstamo, Multa, Convenio, Otros.',
            'concepto.required' => 'El campo concepto es obligatorio.',  
            'fecha_vencimiento.required' => 'El campo fecha_vencimiento es obligatorio.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de emisión.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser uno de los siguientes: Pendiente, Pagado, Vencido.',
            'frecuencia_pago.in' => 'El campo frecuencia_pago debe ser uno de los siguientes: Mensual, Bimestral, Trimestral.',
            
        ];
    }
    
}
