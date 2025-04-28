<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meta;
use Illuminate\Support\Facades\DB;

class MetaController extends Controller
{
    public function getAllMetas(){
        try{
            $resultados = DB::select('EXEC sp_GetAll_poa_t_metas_an_ods');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las metas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMetasByObjetivo($codigo_objetivo){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_metas_an_odsXobjetivo_an_ods ?', [$codigo_objetivo]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las metas: ' . $e->getMessage(),
            ], 500);
        }
    }
}
