<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAsientoContableRequest;
use App\Http\Requests\CreateMovimientoContableRequest;
use App\Http\Requests\UpdateMonedaRequest;
use App\Http\Requests\UpdateMovimientoRequest;
use App\Http\Requests\UpdateTipoTransaccionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientosContablesController extends Controller
{
    public function obtenerAsientosContables(Request $request)
    {
        try {
            $params = [];
            $sql = 'EXEC MovimientosContables.sps_ObtenerAsientosContables';

            $bindings = [];

            // Lista de posibles parámetros y cómo llamarlos
            $availableParams = [
                'FechaInicio' => 'datetime',
                'FechaFin' => 'datetime',
                'Estado' => 'string',
                'IdUsuarioCreador' => 'integer',
                'IdPeriodo' => 'integer',
            ];

            // Construimos los parámetros dinámicos si están presentes
            foreach ($availableParams as $key => $type) {
                if ($request->filled($key)) {
                    $sql .= " @$key = :$key,";
                    $bindings[$key] = $request->input($key);
                }
            }

            // Quitamos la última coma si existen parámetros
            $sql = rtrim($sql, ',');

            // Ejecutar consulta
            $result = DB::select($sql, $bindings);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron asientos contables.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asientos contables obtenidos correctamente.',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los asientos contables: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function crearAsientoContable(CreateAsientoContableRequest $request)
    {
        try {
            $validated = $request->validated();

            $result = DB::select('EXEC MovimientosContables.spi_CrearAsientoContable 
                @Descripcion = :descripcion,
                @Fecha = :fecha,
                @IdPeriodo = :id_periodo,
                @IdUsuarioCreador = :id_usuario_creador,
                @IdUsuarioAprobador = :id_usuario_aprobador', [
                'descripcion' => $validated['descripcion'] ?? null,
                'fecha' => $validated['fecha'] ?? null,
                'id_periodo' => $validated['id_periodo'],
                'id_usuario_creador' => $validated['id_usuario_creador'],
                'id_usuario_aprobador' => $validated['id_usuario_aprobador']
            ]);

            foreach ($request->listado_imagenes as $imagen) {
                DB::statement('EXEC MovimientosContables.spi_AgregarImagenAsientoContable 
                    @IdAsiento = :id_asiento,
                    @ubicacionImagen = :ubicacionImagen', [
                    'id_asiento' => $result[0]->id_asiento,
                    'ubicacionImagen' => $imagen['ubicacionImagen']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asiento contable creado correctamente.',
                'id_asiento' => $result[0]->id_asiento ?? null,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el asiento contable.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function anularAsientoContable(Request $request)
    {
        try {
            // Validar campos obligatorios
            $validated = $request->validate([
                'id_asiento' => 'required|integer',
                'motivo_anulacion' => 'required|string|max:255',
                'id_usuario_aprobador' => 'required|integer',
            ]);

            // Ejecutar el SP
            $result = DB::select('EXEC MovimientosContables.spd_AnularAsientoContable 
                @IdAsiento = :id_asiento, 
                @MotivoAnulacion = :motivo_anulacion, 
                @IdUsuarioAprobador = :id_usuario_aprobador', [
                'id_asiento' => $validated['id_asiento'],
                'motivo_anulacion' => $validated['motivo_anulacion'],
                'id_usuario_aprobador' => $validated['id_usuario_aprobador']
            ]);

            return response()->json([
                'success' => true,
                'message' => $result[0]->mensaje ?? 'Asiento contable anulado correctamente.',
                'id_asiento' => $result[0]->id_asiento ?? null,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al anular el asiento contable.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reactivarAsientoContable(Request $request)
    {
        try {
            // Validar campos
            $validated = $request->validate([
                'id_asiento' => 'required|integer',
                'id_usuario_aprobador' => 'required|integer',
            ]);

            // Verificar si el asiento contable existe y no está anulado
            $asiento = DB::select('EXEC MovimientosContables.spi_ObtenerAsientoContablePorId @IdAsiento = :id_asiento', [
                'id_asiento' => $validated['id_asiento'],
            ]);
            if (empty($asiento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El asiento contable no existe o ya está anulado.',
                ], 404);
            }
            // Ejecutar el SP
            $result = DB::select('EXEC [MovimientosContables].[spu_ReactivarAsientoContable]
                @IdAsiento = :id_asiento, 
                @IdUsuarioAprobador = :id_usuario_aprobador', [
                'id_asiento' => $validated['id_asiento'],
                'id_usuario_aprobador' => $validated['id_usuario_aprobador']
            ]);

            return response()->json([
                'success' => true,
                'message' => $result[0]->mensaje ?? 'Asiento contable reactivado correctamente.',
                'id_asiento' => $result[0]->id_asiento ?? null,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar el asiento contable.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function crearMovimientoContable(Request $request)
    {
        try {
            // Validar estructura general
            $validated = $request->validate([
                'id_asiento' => 'required|integer',
                'movimientos' => 'required|array|min:1',
                'movimientos.*.id_cuenta' => 'required|integer',
                'movimientos.*.id_centro' => 'required|integer',
                'movimientos.*.descripcion' => 'required|string|max:255',
                'movimientos.*.fecha_transaccion' => 'nullable|date',
                'movimientos.*.debito' => 'nullable|numeric|min:0',
                'movimientos.*.credito' => 'nullable|numeric|min:0',
                'movimientos.*.id_tipo_transaccion' => 'required|integer',
                'movimientos.*.referencia' => 'nullable|string|max:50',
            ]);

            $idAsiento = $validated['id_asiento'];
            $movimientos = $validated['movimientos'];
            $idsInsertados = [];

            foreach ($movimientos as $movimiento) {
                $resultado = DB::select('EXEC MovimientosContables.spi_CrearMovimientoContable 
                    @IdAsiento = :id_asiento,
                    @IdCuenta = :id_cuenta,
                    @IdCentro = :id_centro,
                    @Descripcion = :descripcion,
                    @FechaTransaccion = :fecha_transaccion,
                    @Debito = :debito,
                    @Credito = :credito,
                    @IdTipoTransaccion = :id_tipo_transaccion,
                    @Referencia = :referencia', [
                    'id_asiento' => $idAsiento,
                    'id_cuenta' => $movimiento['id_cuenta'],
                    'id_centro' => $movimiento['id_centro'],
                    'descripcion' => $movimiento['descripcion'],
                    'fecha_transaccion' => $movimiento['fecha_transaccion'] ?? null,
                    'debito' => $movimiento['debito'] ?? 0,
                    'credito' => $movimiento['credito'] ?? 0,
                    'id_tipo_transaccion' => $movimiento['id_tipo_transaccion'],
                    'referencia' => $movimiento['referencia'] ?? null,
                ]);

                $idsInsertados[] = $resultado[0]->id_movimiento ?? null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Movimientos contables creados exitosamente.',
                'movimientos_insertados' => $idsInsertados,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear los movimientos contables.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function obtenerTiposTransaccion()
    {
        try {
            $result = DB::select('EXEC MovimientosContables.[spi_ObtenerTiposTransaccion]');

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron tipos de transacción.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tipos de transacción obtenidos correctamente.',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de transacción: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function crearTipoTransaccion(Request $request)
    {
        try {
            $validated = $request->validate([
                'Nombre' => 'required|string|max:100',
                'Estado' => 'required|string|max:20',
            ]);

            DB::statement('EXEC [MovimientosContables].[spi_CrearTipoTransaccion]
                @Nombre = :Nombre,
                @Estado = :Estado', [
                'Nombre' => $validated['Nombre'],
                'Estado' => $validated['Estado'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de transacción creado exitosamente.',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de transacción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerAsientoContablePorID($id_asiento)
    {
        try {
            $result = DB::select('EXEC MovimientosContables.spi_ObtenerAsientoContablePorId @IdAsiento = :id_asiento', [
                'id_asiento' => $id_asiento,
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el asiento contable.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asiento contable obtenido correctamente.',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el asiento contable: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerMovimientosContables(Request $request)
    {
        try {
            // Extraer los posibles filtros desde el request
            $idAsiento = $request->input('id_asiento');
            $idCuenta = $request->input('id_cuenta');
            $fechaDesde = $request->input('fecha_desde');
            $fechaHasta = $request->input('fecha_hasta');

            // Ejecutar el SP pasando solo los parámetros relevantes
            $result = DB::select('EXEC MovimientosContables.spi_ObtenerMovimientosContables 
                @IdAsiento = :id_asiento, 
                @IdCuenta = :id_cuenta, 
                @FechaDesde = :fecha_desde, 
                @FechaHasta = :fecha_hasta', [
                'id_asiento' => $idAsiento,
                'id_cuenta' => $idCuenta,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Movimientos contables obtenidos correctamente.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los movimientos contables.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarMovimientoContable(UpdateMovimientoRequest $request)
    {
        try {
            $validated = $request->validated();

            $result = DB::select('EXEC MovimientosContables.spi_ActualizarMovimientoContable
                @IdMovimiento = :id_movimiento,
                @Descripcion = :descripcion,
                @FechaTransaccion = :fecha_transaccion,
                @Debito = :debito,
                @Credito = :credito,
                @Referencia = :referencia', [
                'id_movimiento' => $validated['id_movimiento'],
                'descripcion' => $validated['descripcion'] ?? null,
                'fecha_transaccion' => $validated['fecha_transaccion'] ?? null,
                'debito' => $validated['debito'] ?? null,
                'credito' => $validated['credito'] ?? null,
                'referencia' => $validated['referencia'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => $result[0]->mensaje ?? 'Actualización realizada con éxito.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el movimiento contable.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarMovimientoContable($idMovimiento)
    {
        try {
            $result = DB::select('EXEC MovimientosContables.spi_EliminarMovimientoContable @IdMovimiento = :id_movimiento', [
                'id_movimiento' => $idMovimiento,
            ]);

            return response()->json([
                'success' => true,
                'message' => $result[0]->mensaje ?? 'Movimiento contable eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el movimiento contable: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarTipoTransaccion(UpdateTipoTransaccionRequest $request)
    {
        try {
            $validated = $request->validated();

            $result = DB::select('EXEC MovimientosContables.spi_ActualizarTipoTransaccion 
            @IdTipoTransaccion = :id_tipo_transaccion,
            @Nombre = :nombre,
            @Estado = :estado', [
                'id_tipo_transaccion' => $validated['id_tipo_transaccion'],
                'nombre' => $validated['nombre'] ?? null,
                'estado' => $validated['estado'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => $result[0]->mensaje ?? 'Actualización realizada con éxito.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de transacción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function eliminarTipoTransaccion($idTipoTransaccion){
        try {
            $result = DB::select('EXEC [MovimientosContables].[spi_EliminarTipoTransaccion] @IdTipoTransaccion = :id_tipo_transaccion', [
                'id_tipo_transaccion' => $idTipoTransaccion,
            ]);

            return response()->json([
                'success' => true,
                'message' => $result[0]->mensaje ?? 'Tipo de transacción eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de transacción: ' . $e->getMessage(),
            ], 500);
        }
    }
}
