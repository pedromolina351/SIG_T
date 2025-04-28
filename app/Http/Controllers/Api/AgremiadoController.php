<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCommentRequest;


class AgremiadoController extends Controller {
    public function getAffiliatesLicenseNumber() {
        try {
            $results = DB::select('EXEC sps_ConsultarColegiaciones');
    
            $data = is_array($results) ? $results : [];
    
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    
}
