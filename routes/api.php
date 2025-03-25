<?php
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\GastoController;

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('categorias', CategoriaController::class);
//Ruta para consultar las ventas por id de cliente
Route::get('ventas/cliente/{id}', [VentaController::class, 'ventasPorCliente']);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('pagos', PagoController::class);
Route::apiResource('gastos', GastoController::class);






