<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMonedaRequest;
use App\Http\Requests\UpdateMonedaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonedaController extends Controller
{
    public function obtenerMoneda(Request $request)
    {
        try {
            $monedaID = $request->query('monedaID');
            $resultados = DB::select('EXEC [dbo].[sps_ObtenerMoneda] @monedaID = :monedaID', [
                'monedaID' => $monedaID ?? null
            ]);

            if (empty($resultados)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron monedas.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Monedas obtenidas correctamente.',
                'data' => $resultados
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las monedas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function crearMoneda(CreateMonedaRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::statement('EXEC dbo.spi_InsertarMoneda 
                @nombreMoneda = :nombreMoneda,
                @descReducida = :descReducida,
                @simboloMoneda = :simboloMoneda,
                @usuarioRegistro = :usuarioRegistro', [
                'nombreMoneda' => $validated['nombreMoneda'],
                'descReducida' => $validated['descReducida'] ?? null,
                'simboloMoneda' => $validated['simboloMoneda'] ?? null,
                'usuarioRegistro' => $validated['usuarioRegistro'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Moneda creada exitosamente.',
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
                'message' => 'Error al crear la moneda: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function eliminarMoneda($monedaID)
    {
        try {
            $monedaExists = DB::table('Moneda')->where('monedaID', $monedaID)->exists();

            if (!$monedaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El id de la moneda especificada no existe.',
                ], 404);
            }

            DB::statement('EXEC dbo.spd_EliminarMoneda @monedaID = :monedaID', [
                'monedaID' => $monedaID
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Moneda eliminada correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la moneda: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarMoneda(UpdateMonedaRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();

            // Ejecutar el procedimiento almacenado para actualizar la moneda
            DB::statement('EXEC dbo.spu_ActualizarMoneda 
            @monedaID = :monedaID,
            @nombreMoneda = :nombreMoneda,
            @descReducida = :descReducida,
            @simboloMoneda = :simboloMoneda,
            @usuarioRegistro = :usuarioRegistro', [
                'monedaID' => $validated['monedaID'],
                'nombreMoneda' => $validated['nombreMoneda'],
                'descReducida' => $validated['descReducida'] ?? null,
                'simboloMoneda' => $validated['simboloMoneda'] ?? null,
                'usuarioRegistro' => $validated['usuarioRegistro'],
            ]);

            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Moneda actualizada exitosamente.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejo de errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la moneda: ' . $e->getMessage(),
            ], 500);
        }
    }
}
