<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class ProductoController extends Controller
{
    public function index()
    {
        Log::info('inicia index producto');
        return Producto::with(['categoria', 'proveedor'])->get();
    }

    public function store(StoreProductoRequest $request)
    {
        $start = microtime(true);
        Log::info('inicia producto');
        $producto = Producto::create($request->validated());
        Log::info('Nuevo producto registrado', [
            'cliente' => $producto->toArray(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        $end = microtime(true);
        Log::info('Tiempo de ejecución store: ' . round($end - $start, 4) . ' segundos');
        return response()->json($producto, 201);

    }

    public function show($id)
    {
        $producto = Producto::with(['categoria', 'proveedor'])->findOrFail($id);
        return response()->json($producto);
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());
        return response()->json($producto);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return response()->json(null, 204); // 204: Sin contenido
    }
}
