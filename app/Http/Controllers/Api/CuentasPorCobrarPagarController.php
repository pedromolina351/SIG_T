<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCuentaPorCobrarRequest;
use App\Http\Requests\CreateCuentaPorPagarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentasPorCobrarPagarController extends Controller
{
    public function obtenerCuentasPorCobrar(Request $request)
    {
        try {
            // Recoge los parámetros, aunque vengan vacíos
            $params = [
                'id_contraparte'      => $request->input('id_contraparte'),
                'tipo_contraparte'    => $request->input('tipo_contraparte'),
                'estado'              => $request->input('estado'),
                'fuente'              => $request->input('fuente'),
                'id_centro_origen'    => $request->input('id_centro_origen'),
                'id_centro_destino'   => $request->input('id_centro_destino'),
                'fecha_desde'         => $request->input('fecha_desde'),
                'fecha_hasta'         => $request->input('fecha_hasta'),
            ];

            // Ejecutar SP (pasan los 8 parámetros en orden)
            $result = DB::select(
                'EXEC CuentasPorCobrarPagar.sp_ObtenerCuentasPorCobrar 
                @id_contraparte = :id_contraparte,
                @tipo_contraparte = :tipo_contraparte,
                @estado = :estado,
                @fuente = :fuente,
                @id_centro_origen = :id_centro_origen,
                @id_centro_destino = :id_centro_destino,
                @fecha_desde = :fecha_desde,
                @fecha_hasta = :fecha_hasta',
                [
                    'id_contraparte' => $params['id_contraparte'] ?? null,
                    'tipo_contraparte' => $params['tipo_contraparte'] ?? null,
                    'estado' => $params['estado'] ?? null,
                    'fuente' => $params['fuente'] ?? null,
                    'id_centro_origen' => $params['id_centro_origen'] ?? null,
                    'id_centro_destino' => $params['id_centro_destino'] ?? null,
                    'fecha_desde' => $params['fecha_desde'] ?? null,
                    'fecha_hasta' => $params['fecha_hasta'] ?? null
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Cuentas por cobrar obtenidas con éxito.',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuentas por cobrar.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerCuentasPorPagar(Request $request)
    {
        try {
            $params = [
                'id_contraparte'      => $request->input('id_contraparte'),
                'tipo_contraparte'    => $request->input('tipo_contraparte'),
                'estado'              => $request->input('estado'),
                'fuente'              => $request->input('fuente'),
                'id_centro_origen'    => $request->input('id_centro_origen'),
                'id_centro_destino'   => $request->input('id_centro_destino'),
                'fecha_desde'         => $request->input('fecha_desde'),
                'fecha_hasta'         => $request->input('fecha_hasta'),
            ];

            $result = DB::select('EXEC CuentasPorCobrarPagar.sp_ObtenerCuentasPorPagar 
            @id_contraparte = :id_contraparte,
            @tipo_contraparte = :tipo_contraparte,
            @estado = :estado,
            @fuente = :fuente,
            @id_centro_origen = :id_centro_origen,
            @id_centro_destino = :id_centro_destino,
            @fecha_desde = :fecha_desde,
            @fecha_hasta = :fecha_hasta', $params);

            return response()->json([
                'success' => true,
                'message' => 'Cuentas por pagar obtenidas correctamente.',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuentas por pagar.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerContrapartesPorTipo($tipo_contraparte)
    {
        // Mapeo de tipos válidos con claves "normalizadas" para comparación
        $tiposValidos = [
            'cliente' => 'Cliente',
            'proveedor' => 'Proveedor',
            'agremiado' => 'Agremiado',
            'centrodecosto' => 'Centro de Costo',
        ];

        // Eliminar espacios, guiones, subguiones, y convertir a minúsculas
        $tipoKey = strtolower(str_replace(['-', '_', ' '], '', $tipo_contraparte));

        if (!array_key_exists($tipoKey, $tiposValidos)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de contraparte no válido. Tipos válidos: Cliente, Proveedor, Agremiado, Centro de Costo.',
            ], 400);
        }

        $tipoNormalizado = $tiposValidos[$tipoKey];

        try {
            $result = DB::select('EXEC CuentasPorCobrarPagar.sp_ObtenerContrapartesPorTipo @tipo_contraparte = :tipo', [
                'tipo' => $tipoNormalizado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contrapartes obtenidas correctamente.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las contrapartes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function crearCuentaPorCobrar(CreateCuentaPorCobrarRequest $request)
    {
        try {
            $validated = $request->validated();
    
            $result = DB::select('EXEC CuentasPorCobrarPagar.sp_CrearCuentaPorCobrar
                @id_contraparte = :id_contraparte,
                @tipo_contraparte = :tipo_contraparte,
                @id_centro_origen = :id_centro_origen,
                @id_centro_destino = :id_centro_destino,
                @fuente = :fuente,
                @concepto = :concepto,
                @id_asiento = :id_asiento,
                @fecha_emision = :fecha_emision,
                @fecha_vencimiento = :fecha_vencimiento,
                @monto_total = :monto_total,
                @monto_pagado = :monto_pagado,
                @estado = :estado,
                @id_factura = :id_factura,
                @id_prestamo = :id_prestamo,
                @id_cxp = :id_cxp,
                @plazo_meses = :plazo_meses,
                @frecuencia_pago = :frecuencia_pago,
                @interes_aplicado = :interes_aplicado,
                @monto_cuota = :monto_cuota,
                @referencia = :referencia', [
    
                'id_contraparte' => $validated['id_contraparte'] ?? null,
                'tipo_contraparte' => $validated['tipo_contraparte'],
                'id_centro_origen' => $validated['id_centro_origen'],
                'id_centro_destino' => $validated['id_centro_destino'] ?? null,
                'fuente' => $validated['fuente'],
                'concepto' => $validated['concepto'],
                'id_asiento' => $validated['id_asiento'] ?? null,
                'fecha_emision' => $validated['fecha_emision'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'monto_total' => $validated['monto_total'],
                'monto_pagado' => $validated['monto_pagado'] ?? 0,
                'estado' => $validated['estado'],
                'id_factura' => $validated['id_factura'] ?? null,
                'id_prestamo' => $validated['id_prestamo'] ?? null,
                'id_cxp' => $validated['id_cxp'] ?? null,
                'plazo_meses' => $validated['plazo_meses'] ?? null,
                'frecuencia_pago' => $validated['frecuencia_pago'] ?? null,
                'interes_aplicado' => $validated['interes_aplicado'] ?? null,
                'monto_cuota' => $validated['monto_cuota'] ?? null,
                'referencia' => $validated['referencia'] ?? null,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Cuenta por cobrar creada correctamente.',
                'id_cxc' => $result[0]->id_cxc ?? null
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cuenta por cobrar.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
