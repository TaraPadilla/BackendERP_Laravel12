<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index()
    {
        return Pago::with('venta.cliente:id,nombre_completo')
        ->whereNull('deleted_at')
        ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $pago = Pago::create($data);

            $venta = Venta::find($data['venta_id']);
            $venta->aplicarAbono($data['monto']);

            DB::commit();

            return response()->json($pago, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $pago = Pago::where('id', $id)->whereNull('deleted_at')->first();

        if (!$pago) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }

        return response()->json($pago);
    }

    public function update(Request $request, $id)
    {
        $pago = Pago::where('id', $id)->whereNull('deleted_at')->first();

        if (!$pago) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }

        $data = $request->validate([
            'venta_id' => 'sometimes|exists:ventas,id',
            'monto' => 'sometimes|numeric|min:0.01',
            'fecha' => 'sometimes|date',
        ]);

        $pago->update($data);

        return response()->json($pago);
    }

    public function destroy($id)
    {
        $pago = Pago::find($id);

        if (!$pago || $pago->deleted_at) {
            return response()->json(['error' => 'Pago no encontrado'], 404);
        }

        $pago->delete();

        return response()->json(['mensaje' => 'Pago eliminado']);
    }
}
