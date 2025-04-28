<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCuentaContableRequest;
use Illuminate\Http\Request;
use App\Http\Requests\CreateCuentaContableRequest;
use App\Http\Requests\GetCuentaContableRequest;
use App\Services\CuentaContableService;
use Laravel\Pail\ValueObjects\Origin\Console;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatalogoContableController extends Controller
{
    protected $cuentaContableService;

    public function __construct(CuentaContableService $cuentaContableService)
    {
        $this->cuentaContableService = $cuentaContableService;
    }

    public function crearCuentaContable(Request $request)
    {
        $data = $request->all();
        $result = $this->cuentaContableService->crearCuentaContable($data);
        return response()->json($result, $result['success'] ? 201 : 500);
    }

    public function obtenerCuentaContable($cuentaID)
    {
        $result = $this->cuentaContableService->obtenerCuentaContable($cuentaID);

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function obtenerCuentasContables ()
    {
        $result = $this->cuentaContableService->obtenerCuentasContables();
        return response()->json($result, $result['success'] ? 200: 404);
    }

    public function actualizarCuentaContable(Request $request)
    {
        $data = $request->all();
        $result = $this->cuentaContableService->actualizarCuentaContable($data);
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function eliminarCuentaContable (Request $request)
    {
        $cuentaID = $request->request->get('cuentaID');
        Log::info('ID de cuenta a eliminar: ' . $cuentaID);
        $result = $this->cuentaContableService->eliminarCuentaContable($cuentaID);
        return response()->json($result, $result['success'] ? 200: 404);
    }

    public function obtenerCuentasPadre(Request $request)
    {
        try {
            // Validar que se haya enviado el nivel hija
            $request->validate([
                'nivel_hija' => 'required|integer|min:2|max:7'
            ]);

            $nivelHija = $request->input('nivel_hija');

            // Ejecutar el procedimiento almacenado con el parámetro
            $cuentasPadre = DB::select('EXEC sps_ObtenerCuentasPadre @NivelHija = ?', [$nivelHija]);

            // Verificar si se encontraron cuentas padres
            if (empty($cuentasPadre)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron cuentas padres.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cuentasPadre,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuentas padres: ' . $e->getMessage(),
            ], 500);
        }
    }
}

