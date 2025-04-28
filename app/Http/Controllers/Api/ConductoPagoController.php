<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateConductoPagoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConductoPagoController extends Controller
{
    public function crearConductoPago(CreateConductoPagoRequest $request){
        try {
            $validated = $request->validated();

            DB::statement('EXEC dbo.spi_InsertarConductoPago 
                @descripcionConducto = :descripcionConducto,
                @estado = :estado,
                @usuarioRegistro = :usuarioRegistro', [
                'descripcionConducto' => $validated['descripcionConducto'],
                'estado' => $validated['estado'],
                'usuarioRegistro' => $validated['usuarioRegistro'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conducto de pago creado exitosamente.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el conducto de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerConductos(Request $request){
        try {
            $conductoID = $request->query('conductoID');
            $resultados = DB::select('EXEC [dbo].[sps_ConsultarConductoPago] @conductoID = :conductoID', [
                'conductoID' => $conductoID ?? null
            ]);

            if (empty($resultados)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron conductos de pago.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Conductos de pago obtenidos correctamente.',
                'data' => $resultados
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los conductos de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarConductoPago(Request $request){
        try {
            $conductoID = $request->input('conductoID');
            $descripcionConducto = $request->input('descripcionConducto');
            $estado = $request->input('estado');
            $usuarioRegistro = $request->input('usuarioRegistro');

            DB::statement('EXEC dbo.spu_ActualizarConductoPago 
                @conductoID = :conductoID,
                @descripcionConducto = :descripcionConducto,
                @estado = :estado,
                @usuarioRegistro = :usuarioRegistro', [
                'conductoID' => $conductoID,
                'descripcionConducto' => $descripcionConducto,
                'estado' => $estado,
                'usuarioRegistro' => $usuarioRegistro,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conducto de pago actualizado exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el conducto de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarConductoPago($conductoID){
        try {
            DB::statement('EXEC dbo.sp_EliminarConductoPago @conductoID = :conductoID', [
                'conductoID' => $conductoID
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conducto de pago eliminado exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el conducto de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

}
