<?php
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('proveedores', ProveedorController::class);
