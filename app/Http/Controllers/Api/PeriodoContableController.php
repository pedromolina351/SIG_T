<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoContableController extends Controller
{
    public function crearPeriodoContable(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombrePeriodo' => 'required|string|max:50',
                'fecha_inicio'  => 'required|date',
                'fecha_fin'     => 'required|date',
                'estado'        => 'nullable|boolean',
                'fecha_cierre'  => 'nullable|date',
            ]);

            DB::statement('EXEC PeriodoContable.spi_InsertarPeriodoContable
                @nombrePeriodo = :nombrePeriodo,
                @fecha_inicio = :fecha_inicio,
                @fecha_fin = :fecha_fin,
                @estado = :estado,
                @fecha_cierre = :fecha_cierre', [
                'nombrePeriodo' => $validated['nombrePeriodo'],
                'fecha_inicio'  => $validated['fecha_inicio'],
                'fecha_fin'     => $validated['fecha_fin'],
                'estado'        => $validated['estado'] ?? 1,
                'fecha_cierre'  => $validated['fecha_cierre']
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Periodo contable creado exitosamente.',
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el periodo contable.', 'error' => $e->getMessage()], 500);
        }
    }

    public function obtenerPeriodosContables()
    {
        try {
            $periodos = DB::select('EXEC PeriodoContable.sps_ObtenerPeriodoContable');
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Moneda creada exitosamente.',
                    'data'    => $periodos
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al obtener los periodos contables.', 'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerPeriodoContablePorID($periodoID)
    {
        try {
            $periodo = DB::select('EXEC PeriodoContable.sps_ObtenerPeriodoContablePorID @periodoID = :periodoID', ['periodoID' => $periodoID]);

            if (empty($periodo)) {
                return response()->json(['success' => false, 'message' => 'Periodo contable no encontrado.'], 404);
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Moneda creada exitosamente.',
                    'data'    => $periodo
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener el periodo contable.', 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizarPeriodoContable(Request $request)
    {
        try {
            $validated = $request->validate([
                'periodoID'    => 'required|integer',
                'nombrePeriodo' => 'nullable|string|max:50',
                'fecha_inicio'  => 'nullable|date',
                'fecha_fin'     => 'nullable|date',
                'estado'        => 'nullable|boolean',
                'fecha_cierre'  => 'nullable|date',
            ]);

            DB::statement('EXEC PeriodoContable.spu_ActualizarPeriodoContable
                @periodoID = :periodoID,
                @nombrePeriodo = :nombrePeriodo,
                @fecha_inicio = :fecha_inicio,
                @fecha_fin = :fecha_fin,
                @estado = :estado,
                @fecha_cierre = :fecha_cierre', [
                'periodoID'    => $validated['periodoID'],
                'nombrePeriodo' => $validated['nombrePeriodo'],
                'fecha_inicio'  => $validated['fecha_inicio'],
                'fecha_fin'     => $validated['fecha_fin'],
                'estado'        => $validated['estado'],
                'fecha_cierre'  => $validated['fecha_cierre'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Periodo contable actualizado correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el periodo contable.', 'error' => $e->getMessage()], 500);
        }
    }

    public function cerrarPeriodoContable(Request $request)
    {
        try {
            $validated = $request->validate([
                'periodoID' => 'required|integer',
            ]);

            DB::statement('EXEC PeriodoContable.spu_CambiarEstadoPeriodoContable
                @periodoID = :periodoID, @accion = 0', [
                'periodoID' => $validated['periodoID'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Periodo contable cerrado correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cerrar el periodo contable.', 'error' => $e->getMessage()], 500);
        }
    }

    public function abrirPeriodoContable(Request $request)
    {
        try {
            $validated = $request->validate([
                'periodoID' => 'required|integer',
            ]);

            DB::statement('EXEC PeriodoContable.spu_CambiarEstadoPeriodoContable
                @periodoID = :periodoID, @accion = 1', [
                'periodoID' => $validated['periodoID'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Periodo contable abierto correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al abrir el periodo contable.', 'error' => $e->getMessage()], 500);
        }
    }

    public function eliminarPeriodoContable($periodoID)
    {
        try {
            DB::statement('EXEC PeriodoContable.spd_EliminarPeriodoContable @periodoID = :periodoID', [
                'periodoID' => $periodoID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Periodo contable eliminado correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el periodo contable.', 'error' => $e->getMessage()], 500);
        }
    }
}
