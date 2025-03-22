<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ClienteController extends Controller
{
    /**
     * Listar todos los clientes activos (no eliminados).
     */
    public function index()
    {
        return Cliente::all(); // Laravel automáticamente ignora los soft-deleted
    }

    /**
     * Registrar un nuevo cliente.
     */
    public function store(StoreClienteRequest $request)
    {
        Log::info('inicia store');
        try {
        $cliente = Cliente::create($request->validated());
        Log::info('Nuevo cliente registrado', [
            'cliente' => $cliente->toArray(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json($cliente, 201); // 201 Created

        } catch (\Exception $e) {
            Log::error('Error al registrar cliente', [
                'mensaje' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => 'Ocurrió un error al registrar el cliente.'
            ], 500);
         }
    }

    /**
     * Mostrar un cliente específico.
     */
    public function show(Cliente $cliente)
    {
        return response()->json($cliente);
    }

    /**
     * Actualizar un cliente existente.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return response()->json($cliente);
    }

    /**
     * Eliminar un cliente (borrado lógico).
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json(null, 204); // 204 No Content
    }
}
