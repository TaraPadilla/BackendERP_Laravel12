<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProveedorController extends Controller
{
    // Obtener lista de todos los proveedores
    public function index(): JsonResponse
    {
        $proveedores = Proveedor::all();
        return response()->json($proveedores);
    }

    // Registrar un nuevo proveedor
    public function store(StoreProveedorRequest $request): JsonResponse
    {
        $proveedor = Proveedor::create($request->validated());
        return response()->json($proveedor, 201); // 201: Creado
    }

    // Mostrar un proveedor por ID
    public function show($id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);
        return response()->json($proveedor);
    }

    // Actualizar un proveedor existente
    public function update(UpdateProveedorRequest $request, Proveedor $proveedore)
    {
        $proveedore->update($request->validated());
        return response()->json($proveedore);
    }

    // Eliminar un proveedor (soft delete)
    public function destroy($id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();
        return response()->json(null, 204); // 204: Sin contenido
    }
}
