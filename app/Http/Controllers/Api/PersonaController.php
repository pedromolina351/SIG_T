<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePersonaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function obtenerDepartamentos()
    {
        $departamentos = DB::table('t_glo_departamentos')->get();
        return response()->json($departamentos);
    }
}
