<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with([
            'cliente:id,nombre_completo',
            'usuario:id,nombre',
            'detalles.producto:id,nombre'])->orderBy('id', 'desc')->get();
        return response()->json($ventas);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Recibiendo datos para registrar venta', $request->all());

            $data = $request->validate([
                'fecha_venta' => 'required|date',
                'tipo_pago' => 'required|in:contado,credito',
                'cliente_id' => 'required|exists:clientes,id',
                'total' => 'required|numeric',
                'abono_inicial' => 'nullable|numeric',
                'cuotas' => 'nullable|integer',
                'saldo_pendiente' => 'nullable|numeric',
                'usuario_id' => 'required|exists:usuarios,id',
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio_venta' => 'required|numeric|min:0',
                'productos.*.subtotal' => 'required|numeric|min:0',
            ]);

            // Crear la venta sin factura_id
            $venta = Venta::create($data);
            Log::info('Venta creada', ['id' => $venta->id]);

            // Actualizar factura_id basado en ID
            $venta->update([
                'factura_id' => 'FAC-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT)
            ]);

            // Crear los detalles de venta
            foreach ($data['productos'] as $item) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_venta' => $item['precio_venta'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            Log::info('Detalles de venta registrados');

            // Crear abono inicial si aplica
            if ($data['tipo_pago'] === 'credito' && !empty($data['abono_inicial'])) {
                Pago::create([
                    'venta_id' => $venta->id,
                    'monto' => $data['abono_inicial'],
                    'fecha' => $data['fecha_venta'],
                ]);
                Log::info('Primer abono registrado');
            }

            DB::commit();
            Log::info('Venta registrada correctamente');

            return response()->json($venta->load('detalles', 'pagos'), 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al registrar venta', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al registrar la venta'], 500);
        }
    }


    public function show($id)
    {
        $venta = Venta::with(['cliente:id,nombre_completo', 'usuario:id,nombre', 'detalles', 'pagos'])->findOrFail($id);
        return response()->json($venta);
    }

    public function update(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);

        $data = $request->validate([
            'fecha_venta' => 'required|date',
            'tipo_pago' => 'required|string',
            'cliente_id' => 'required|exists:clientes,id',
            'factura_id' => 'nullable|string',
            'total' => 'required|numeric',
            'abono_inicial' => 'nullable|numeric',
            'cuotas' => 'nullable|integer',
            'saldo_pendiente' => 'nullable|numeric',
            'usuario_id' => 'required|exists:usuarios,id',
        ]);

        $venta->update($data);
        return response()->json($venta);
    }

    public function destroy($id)
    {
        $venta = Venta::findOrFail($id);
        // Eliminar detalles y pagos relacionados
        $venta->detalles()->delete();
        // Eliminar la venta (soft delete)
        $venta->delete();

        return response()->json(['message' => 'Venta anulada correctamente']);
    }

    public function ventasPorCliente($id)
    {
        $ventas = Venta::with('cliente')
            ->where('cliente_id', $id)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($ventas);
    }


}
