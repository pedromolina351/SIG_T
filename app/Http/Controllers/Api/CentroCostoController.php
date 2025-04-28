<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCentroCostoRequest;
use App\Http\Requests\UpdateCentroCostoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentroCostoController extends Controller
{
    public function obtenerCentroCosto(Request $request){
        try{
            $centroCostoID = $request->query('centroCostoID');
            $resultados = DB::select('EXEC [dbo].[sps_ObtenerCentroCosto] @centroCostoID = :centroCostoID', [
                'centroCostoID' => $centroCostoID ?? null
            ]);

            if (empty($resultados)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron centros de costo.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Centros de costo obtenidos correctamente.',
                'data' => $resultados
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los centros de costo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function crearCentroCosto(CreateCentroCostoRequest $request)
    {
        try {
            $validated = $request->validated();
    
            DB::statement('EXEC dbo.spi_InsertarCentroCosto 
                @codcentroCosto = :codcentroCosto,
                @nombreCentroCosto = :nombreCentroCosto,
                @abreviaturaCentro = :abreviaturaCentro,
                @descripcionCentro = :descripcionCentro,
                @estado = :estado,
                @usuarioRegistro = :usuarioRegistro', [
                'codcentroCosto' => $validated['codcentroCosto'],
                'nombreCentroCosto' => $validated['nombreCentroCosto'],
                'abreviaturaCentro' => $validated['abreviaturaCentro'],
                'descripcionCentro' => $validated['descripcionCentro'],
                'estado' => $validated['estado'],
                'usuarioRegistro' => $validated['usuarioRegistro'],
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Centro de costo creado exitosamente.',
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
                'message' => 'Error al crear el centro de costo: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function eliminarCentroCosto($centroCostoID){
        try{
            $centroCostoExist = DB::table('CentroCosto')->where('centroCostoID', $centroCostoID)->exists();

            if (!$centroCostoExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'El centro de costo no existe.',
                ], 404);
            }

            DB::statement('EXEC dbo.spd_EliminarCentroCosto @centroCostoID = :centroCostoID', [
                'centroCostoID' => $centroCostoID
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Centro de costo eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el centro de costo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarCentroCosto(UpdateCentroCostoRequest $request){
        try {
            $validated = $request->validated();
    
            $centroCostoExist = DB::table('CentroCosto')->where('centroCostoID', $validated['centroCostoID'])->exists();
    
            if (!$centroCostoExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'El centro de costo no existe.',
                ], 404);
            }
    
            DB::statement('EXEC dbo.spu_ActualizarCentroCosto 
                @centroCostoID = :centroCostoID,
                @codcentroCosto = :codcentroCosto,
                @nombreCentroCosto = :nombreCentroCosto,
                @abreviaturaCentro = :abreviaturaCentro,
                @descripcionCentroCosto = :descripcionCentroCosto,
                @estado = :estado,
                @usuarioRegistro = :usuarioRegistro', [
                'centroCostoID' => $validated['centroCostoID'],
                'codcentroCosto' => $validated['codcentroCosto'],
                'nombreCentroCosto' => $validated['nombreCentroCosto'],
                'abreviaturaCentro' => $validated['abreviaturaCentro'],
                'descripcionCentroCosto' => $validated['descripcionCentroCosto'],
                'estado' => $validated['estado'],
                'usuarioRegistro' => $validated['usuarioRegistro'],
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Centro de costo actualizado correctamente.',
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
                'message' => 'Error al actualizar el centro de costo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
