<?php
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('productos', ProductoController::class);
