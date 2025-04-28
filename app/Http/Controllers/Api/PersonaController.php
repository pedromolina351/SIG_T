<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePersonaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function obtenerPersona($personaID){
        try {
    
            // Ejecutar el procedimiento almacenado para obtener la lista
            $personas = DB::select('EXEC [dbo].[sps_ObtenerRegistroPersona] @personaID = :personaID', [
                'personaID' => $personaID
            ]);
    
            // Verificar si hay resultados
            if (empty($personas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una persona con el Id proporcionado.',
                    'data' => []
                ], 404);
            }
    
            // Retornar respuesta exitosa con los datos obtenidos
            return response()->json([
                'success' => true,
                'message' => 'Registro obtenido correctamente.',
                'data' => $personas
            ], 200);
    
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la persona: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function crearPersona(CreatePersonaRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado para insertar la persona
            DB::statement('EXEC dbo.spi_InsertarRegistroPersona 
                @nombre = :nombre,
                @apellido1 = :apellido1,
                @apellido2 = :apellido2,
                @apellido3 = :apellido3,
                @tipoPersona = :tipoPersona,
                @tipoID = :tipoID,
                @nroIdentificacion = :nroIdentificacion,
                @fechaNacimiento = :fechaNacimiento,
                @fechaConstitucion = :fechaConstitucion,
                @sexo = :sexo,
                @direccion = :direccion,
                @telefono1 = :telefono1,
                @telefono2 = :telefono2,
                @estadoID = :estadoID,
                @email = :email,
                @usuarioRegistro = :usuarioRegistro', [
                'nombre' => $validated['nombre'],
                'apellido1' => $validated['apellido1'],
                'apellido2' => $validated['apellido2'] ?? null,
                'apellido3' => $validated['apellido3'] ?? null,
                'tipoPersona' => $validated['tipoPersona'],
                'tipoID' => $validated['tipoID'],
                'nroIdentificacion' => $validated['nroIdentificacion'],
                'fechaNacimiento' => $validated['fechaNacimiento'],
                'fechaConstitucion' => $validated['fechaConstitucion'],
                'sexo' => $validated['sexo'],
                'direccion' => $validated['direccion'] ?? null,
                'telefono1' => $validated['telefono1'] ?? null,
                'telefono2' => $validated['telefono2'] ?? null,
                'estadoID' => $validated['estadoID'] ?? null,
                'email' => $validated['email'] ?? null,
                'usuarioRegistro' => $validated['usuarioRegistro']
            ]);
    
            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Persona registrada correctamente.',
            ], 201);
    
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
                'message' => 'Error al registrar la persona: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
