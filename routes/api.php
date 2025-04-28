<?php

use App\Http\Controllers\Api\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InstitucionController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\AgremiadoController;
use App\Http\Controllers\Api\CentroCostoController;
use App\Http\Controllers\Api\CatalogoContableController;
use App\Http\Controllers\Api\ConductoPagoController;
use App\Http\Controllers\Api\MonedaController;
use App\Http\Controllers\Api\MovimientosContablesController;
use App\Http\Controllers\Api\PeriodoContableController;
use App\Http\Controllers\Api\CuentasPorCobrarPagarController;

Route::prefix('personas')->group(function () {
    Route::get('/obtener-persona/{personaID}', [PersonaController::class, 'obtenerPersona']);
    Route::post('/crear-persona', [PersonaController::class, 'crearPersona']);
});

Route::prefix('customer')->group(callback: function (): void {
    Route::post('/create-customer', [ClienteController::class, 'crearCliente']);
    Route::get('/get-customers', [ClienteController::class, 'getCustomers']);
    Route::get('/get-customer/{clienteID}', [ClienteController::class, 'getCustomerById']);
    Route::post('/create-customer-type', [ClienteController::class, 'crearTipoCliente']);
    Route::post('/update-customer/{clienteID}', [ClienteController::class, 'actualizarCliente']);
    Route::delete('/delete-customer/{clienteID}', [ClienteController::class, 'deleteCustomer']);
    Route::get('/get-customer-types', [ClienteController::class, 'getCustomerTypes']);
    Route::delete('/delete-customer-type/{tipoClienteID}', [ClienteController::class, 'eliminarTipoCliente']);
    Route::put('/update-customer-type', [ClienteController::class, 'actualizarTipoCliente']);
    Route::get('/get-departments', [ClienteController::class, 'obtenerDepartamentos']);
    Route::get('/get-municipios/{departamentoID}', [ClienteController::class, 'obtenerMunicipios']);
});

Route::prefix('affiliate')->group(callback: function ():  void {
    Route::get('/get-affiliates-license-number', [AgremiadoController::class, 'getAffiliatesLicenseNumber']);
});

Route::prefix('centro-costo')->group(callback: function (): void {
    Route::post('/crear-centro-costo', [CentroCostoController::class, 'crearCentroCosto']);
    Route::get('/obtener-centro-costo', [CentroCostoController::class, 'obtenerCentroCosto']);
    Route::delete('/eliminar-centro-costo/{centroCostoID}', [CentroCostoController::class, 'eliminarCentroCosto']);
    Route::put('/actualizar-centro-costo', [CentroCostoController::class, 'actualizarCentroCosto']);
});


Route::prefix('cuenta-contable')->group(callback: function (): void {
    Route::post('/crear', [CatalogoContableController::class, 'crearCuentaContable']);
    Route::get('/obtener-cuentas', [CatalogoContableController::class, 'obtenerCuentasContables']);
    Route::get('/obtener-cuentas/{cuentaID}', [CatalogoContableController::class, 'obtenerCuentaContable']);
    Route::post('/obtener-cuentas-padres', [CatalogoContableController::class, 'obtenerCuentasPadre']);
    Route::get('/obtener', [CatalogoContableController::class, 'obtenerCuentaContable']);
    Route::patch('/actualizar', [CatalogoContableController::class, 'actualizarCuentaContable']);
    Route::delete('/eliminar', [CatalogoContableController::class,'eliminarCuentaContable']);
});

Route::prefix('moneda')->group(function () {
    Route::post('/crear-moneda', [MonedaController::class, 'crearMoneda']);
    Route::get('/obtener-moneda', [MonedaController::class, 'obtenerMoneda']);
    Route::put('/actualizar-moneda', [MonedaController::class, 'actualizarMoneda']);
    Route::delete('/eliminar-moneda/{monedaID}', [MonedaController::class, 'eliminarMoneda']);
});

Route::prefix('conducto')->group(function () {
    Route::post('/crear-conducto-pago', [ConductoPagoController::class, 'crearConductoPago']);
    Route::get('/obtener-conducto-pago', [ConductoPagoController::class, 'obtenerConductos']);
    Route::put('/actualizar-conducto-pago', [ConductoPagoController::class, 'actualizarConductoPago']);
    Route::delete('/eliminar-conducto-pago/{conductoID}', [ConductoPagoController::class, 'eliminarConductoPago']);
});


Route::prefix('periodo-contable')->group(function () {
    Route::post('/crear-periodo', [PeriodoContableController::class, 'crearPeriodoContable']);
    Route::get('/obtener-periodos', [PeriodoContableController::class, 'obtenerPeriodosContables']);
    Route::get('/obtener-periodo/{periodoID}', [PeriodoContableController::class, 'obtenerPeriodoContablePorID']);
    Route::put('/actualizar-periodo', [PeriodoContableController::class, 'actualizarPeriodoContable']);
    Route::put('/cerrar-periodo', [PeriodoContableController::class, 'cerrarPeriodoContable']);
    Route::put('/abrir-periodo', [PeriodoContableController::class, 'abrirPeriodoContable']);
    Route::delete('/eliminar-periodo/{periodoID}', [PeriodoContableController::class, 'eliminarPeriodoContable']);
});

Route::prefix('movimiento-contable')->group(function () {
    Route::post('/crear-movimiento', [MovimientosContablesController::class, 'crearMovimientoContable']);
    Route::get('/obtener-asientos', [MovimientosContablesController::class, 'obtenerAsientosContables']);
    Route::post('/crear-asiento', [MovimientosContablesController::class, 'crearAsientoContable']);
    Route::put('/anular-asiento', [MovimientosContablesController::class, 'anularAsientoContable']);
    Route::put('/reactivar-asiento', [MovimientosContablesController::class, 'reactivarAsientoContable']);
    Route::post('/crear-tipo-transaccion', [MovimientosContablesController::class, 'crearTipoTransaccion']);
    Route::get('/obtener-tipos-transaccion', [MovimientosContablesController::class, 'obtenerTiposTransaccion']);
    Route::get('/obtener-asiento/{asientoID}', [MovimientosContablesController::class, 'obtenerAsientoContablePorID']);
    Route::get('/obtener-movimientos', [MovimientosContablesController::class, 'obtenerMovimientosContables']);
    Route::put('/actualizar-movimiento', [MovimientosContablesController::class, 'actualizarMovimientoContable']);
    Route::delete('/eliminar-movimiento/{movimientoID}', [MovimientosContablesController::class, 'eliminarMovimientoContable']);
    Route::put('/actualizar-tipo-transaccion', [MovimientosContablesController::class, 'actualizarTipoTransaccion']);
    Route::delete('/eliminar-tipo-transaccion/{tipoTransaccionID}', [MovimientosContablesController::class, 'eliminarTipoTransaccion']);
});

Route::prefix('cuentas-cobrar-pagar')->group(function () {
    Route::get('/obtener-cuentas-por-cobrar', [CuentasPorCobrarPagarController::class, 'obtenerCuentasPorCobrar']);
    Route::post('/crear-cuenta-por-cobrar', [CuentasPorCobrarPagarController::class, 'crearCuentaPorCobrar']);
    Route::get('/obtener-cuentas-por-pagar', [CuentasPorCobrarPagarController::class, 'obtenerCuentasPorPagar']);
    Route::get('/obtener-contrapartes-tipo/{tipo_contraparte}', [CuentasPorCobrarPagarController::class, 'obtenerContrapartesPorTipo']);

});
