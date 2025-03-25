<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GastoController extends Controller
{
    public function index()
    {
        Log::info('Listando gastos');
        $gastos = Gasto::with(['usuario', 'categoria'])->get();
        return response()->json($gastos);
    }

    public function store(Request $request)
    {
        Log::info('Intentando crear gasto', $request->all());

        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'categoria_id' => 'required|exists:categorias,id',
            'observacion' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'fecha_hora' => 'required|date',
        ]);

        $gasto = Gasto::create($request->all());
        Log::info('Gasto creado', $gasto->toArray());

        return response()->json($gasto, 201);
    }

    public function show($id)
    {
        Log::info("Consultando gasto ID: $id");
        $gasto = Gasto::with(['usuario', 'categoria'])->find($id);

        if (!$gasto) {
            Log::warning("Gasto no encontrado ID: $id");
            return response()->json(['message' => 'Gasto no encontrado'], 404);
        }

        return response()->json($gasto);
    }

    public function update(Request $request, $id)
    {
        Log::info("Intentando actualizar gasto ID: $id", $request->all());

        $gasto = Gasto::find($id);
        if (!$gasto) {
            Log::warning("Gasto no encontrado para actualizar ID: $id");
            return response()->json(['message' => 'Gasto no encontrado'], 404);
        }

        $request->validate([
            'usuario_id' => 'sometimes|required|exists:usuarios,id',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'observacion' => 'nullable|string',
            'valor' => 'sometimes|required|numeric|min:0',
            'fecha_hora' => 'sometimes|required|date',
        ]);

        $gasto->update($request->all());
        Log::info("Gasto actualizado ID: $id", $gasto->toArray());

        return response()->json($gasto);
    }

    public function destroy($id)
    {
        Log::info("Intentando eliminar gasto ID: $id");

        $gasto = Gasto::find($id);
        if (!$gasto) {
            Log::warning("Gasto no encontrado para eliminar ID: $id");
            return response()->json(['message' => 'Gasto no encontrado'], 404);
        }

        $gasto->delete();
        Log::info("Gasto eliminado ID: $id");

        return response()->json(['message' => 'Gasto eliminado correctamente']);
    }
}
