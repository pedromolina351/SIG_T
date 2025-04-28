<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{

    public function listarClientes(Request $request)
    {
        try {
            $clienteID = $request->query('clienteID');

            $clientes = DB::select('EXEC dbo.sps_ObtenerCliente @clienteID = :clienteID', [
                'clienteID' => $clienteID ?? null
            ]);

            if (empty($clientes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron clientes.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Clientes obtenidos correctamente.',
                'data' => $clientes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los clientes: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCustomers()
    {
        try {
            $resultados = DB::select('EXEC sps_ObtenerCliente');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los clientes: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function crearCliente(Request $request)
    {
        try {
            // ✅ Validación de los datos
            $validated = $request->validate([
                'nroIdentificacion' => 'required|string|max:20',
                'tipoID' => 'required|integer',
                'nombre' => 'required|string|max:100',
                'apellido1' => 'required|string|max:50',
                'apellido2' => 'nullable|string|max:50',
                'apellido3' => 'nullable|string|max:50',
                'tipoPersona' => 'required|string|max:1',
                'fechaNacimiento' => 'nullable|date',
                'fechaConstitucion' => 'nullable|date',
                'sexo' => 'required|string|max:1',
                'direccion' => 'nullable|string|max:150',
                'telefono1' => 'nullable|string|max:50',
                'telefono2' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:50',
                'usuarioRegistro' => 'required|integer|max:30',
                'agremiadoID' => 'nullable|integer',
                'tipoClienteID' => 'required|integer',
                'departamentoID' => 'required|integer',
                'municipioID' => 'required|integer',
                'estadoID' => 'nullable|integer',
                'nombre_comercial' => 'nullable|string|max:255',
                'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            ]);

            $fotoPerfilUrl = null;
            if ($request->hasFile('foto_perfil')) {
                $file = $request->file('foto_perfil');
                $filename = 'cliente_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('images/perfiles', $filename, 'public');
                $fotoPerfilUrl = url('/cqfh/storage/images/perfiles/' . $filename);
            }


            $params = [
                $validated['nroIdentificacion'],
                (int) $validated['tipoID'],
                $validated['nombre'],
                $validated['apellido1'],
                trim($validated['apellido2'] ?? null),
                trim($validated['apellido3'] ?? null),
                $validated['tipoPersona'],
                !empty($validated['fechaNacimiento']) ? date('Y-m-d H:i:s', strtotime($validated['fechaNacimiento'])) : null,
                !empty($validated['fechaConstitucion']) ? date('Y-m-d H:i:s', strtotime($validated['fechaConstitucion'])) : null,
                $validated['sexo'],
                $validated['direccion'] ?? null,
                $validated['telefono1'] ?? null,
                $validated['telefono2'] ?? null,
                $validated['email'] ?? null,
                $validated['usuarioRegistro'],
                isset($validated['agremiadoID']) ? (int) $validated['agremiadoID'] : null,
                (int) $validated['tipoClienteID'],
                (int) $validated['departamentoID'],
                (int) $validated['municipioID'],
                isset($validated['estadoID']) ? (int) $validated['estadoID'] : 1,
                $validated['nombre_comercial'] ?? null,
                $fotoPerfilUrl,
            ];

            // ✅ Ejecutar el procedimiento almacenado con logs

            $result = DB::select('EXEC dbo.spi_CrearCliente ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'imgen' => $fotoPerfilUrl,
                //'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la base de datos al crear el cliente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarTipoCliente(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipoClienteID' => 'required|integer',
                'nombre_tipo' => 'required|string|max:50',
                'estadoID' => 'nullable|integer',
            ]);

            $tipoClienteExist = DB::table('TiposCliente')->where('tipoClienteID', $validated['tipoClienteID'])->exists();

            if (!$tipoClienteExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'El tipo de cliente no existe.',
                ], 404);
            }

            DB::statement('EXEC [dbo].[spu_ActualizarTipoCliente]
                @tipoClienteID = :tipoClienteID,
                @nombre_tipo = :nombre_tipo,
                @estadoID = :estadoID', [
                'tipoClienteID' => $validated['tipoClienteID'],
                'nombre_tipo' => $validated['nombre_tipo'],
                'estadoID' => $validated['estadoID']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de cliente actualizado correctamente.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de cliente: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function actualizarCliente(UpdateClienteRequest $request, $clienteID)
    {
        $validated = $request->validated();

        try {

            // ✅ Subir imagen si existe
            $fotoPerfilUrl = null;
            if ($request->hasFile('foto_perfil')) {
                $file = $request->file('foto_perfil');
                $filename = 'cliente_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('images/perfiles', $filename, 'public');
                $fotoPerfilUrl = url('/cqfh/storage/images/perfiles/' . $filename);
            }

            // ✅ Verificar si el cliente existe
            $clienteExist = DB::table('Clientes')->where('clienteID', $clienteID)->exists();

            if (!$clienteExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no existe.',
                    'clienteID' => $validated['nombre'],
                ], 404);
            }

            // ✅ Ejecutar SP para actualizar cliente
            DB::statement('EXEC [dbo].[spu_ActualizarCliente]
            @clienteID = :clienteID,
            @codigo_cliente = :codigo_cliente,
            @personaID = :personaID,
            @agremiadoID = :agremiadoID,
            @tipoClienteID = :tipoClienteID,
            @departamentoID = :departamentoID,
            @municipioID = :municipioID,
            @estadoID = :estadoID,
            @nombre_comercial = :nombre_comercial,
            @nroIdentificacion = :nroIdentificacion,
            @nombre = :nombre,
            @apellido1 = :apellido1,
            @apellido2 = :apellido2,
            @apellido3 = :apellido3,
            @sexo = :sexo,
            @direccion = :direccion,
            @telefono1 = :telefono1,
            @email = :email,
            @foto_perfil = :foto_perfil', [
                'clienteID' => $clienteID,
                'codigo_cliente' => $validated['codigo_cliente'] ?? null,
                'personaID' => $validated['personaID'] ?? null,
                'agremiadoID' => $validated['agremiadoID'] ?? null,
                'tipoClienteID' => $validated['tipoClienteID'] ?? null,
                'departamentoID' => $validated['departamentoID'] ?? null,
                'municipioID' => $validated['municipioID'] ?? null,
                'estadoID' => $validated['estadoID'] ?? null,
                'nombre_comercial' => $validated['nombre_comercial'] ?? null,
                'nroIdentificacion' => $validated['nroIdentificacion'] ?? null,
                'nombre' => $validated['nombre'] ?? null,
                'apellido1' => $validated['apellido1'] ?? null,
                'apellido2' => $validated['apellido2'] ?? null,
                'apellido3' => $validated['apellido3'] ?? null,
                'sexo' => $validated['sexo'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'telefono1' => $validated['telefono1'] ?? null,
                'email' => $validated['email'] ?? null,
                'foto_perfil' => $fotoPerfilUrl ?? null,
            ]);

            // ✅ Respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado correctamente.',
                'data' => [
                    'clienteID' => $clienteID,
                    'foto_perfil' => $fotoPerfilUrl,
                    'nombre' => $validated['nombre'] ?? null,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getCustomerById($clienteID)
    {
        try {
            $resultados = DB::select('EXEC sps_ObtenerCliente ?', [$clienteID]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '
                ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCustomer($clienteID)
    {
        try {
            $clienteExiste = DB::table('Clientes')->where('clienteID', $clienteID)->exists();

            if (!$clienteExiste) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: El cliente no existe.',
                ], 404);
            }
            DB::statement('EXEC dbo.spd_EliminarCliente @clienteID = :clienteID', [
                'clienteID' => $clienteID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente desactivado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar el cliente: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCustomerTypes()
    {
        try {
            $resultados = DB::select('EXEC [sps_ObtenerTiposCliente]');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de cliente: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarTipoCliente($tipoClienteID)
    {
        try {
            $tipoClienteExist = DB::table('TiposCliente')->where('tipoClienteID', $tipoClienteID)->exists();

            if (!$tipoClienteExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'El tipo de cliente no existe.',
                ], 404);
            }

            DB::statement('EXEC dbo.[spd_EliminarTipoCliente] @tipoClienteID = :tipoClienteID', [
                'tipoClienteID' => $tipoClienteID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de cliente eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de cliente: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerDepartamentos()
    {
        try {
            $resultados = DB::select('EXEC [sps_ObtenerDepartamentos]');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los departamentos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerMunicipios($departamentoID)
    {
        try {
            $resultados = DB::select('EXEC [sps_ObtenerMunicipios] ?', [$departamentoID]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los municipios: ' . $e->getMessage(),
            ], 500);
        }
    }
}
