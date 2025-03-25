<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Gasto;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Producto;


class DashboardController extends Controller
{
    public function kpi(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        // Consulta base para ventas
        $ventasQuery = Venta::query();
        if ($from) {
            $ventasQuery->where('fecha_venta', '>=', $from);
        }
        if ($to) {
            $ventasQuery->where('fecha_venta', '<=', $to);
        }

        $ventas = $ventasQuery->get();

        // Totales por tipo de pago
        $totalContado = (float) $ventas->where('tipo_pago', 'contado')->sum('total');
        $totalCredito = (float) $ventas->where('tipo_pago', 'credito')->sum('total');
        $totalVentas = $totalContado + $totalCredito;


        // Consulta base para gastos
        $gastosQuery = Gasto::query();
        if ($from) {
            $gastosQuery->where('fecha_hora', '>=', $from);
        }
        if ($to) {
            $gastosQuery->where('fecha_hora', '<=', $to);
        }

        $totalGastos = $gastosQuery->sum('valor');

        // Ganancia neta
        $gananciaNeta = $totalVentas - $totalGastos;

        return response()->json([
            'total_ventas'   => (float) $totalVentas,
            'total_gastos'   => (float) $totalGastos,
            'ganancia_neta'  => (float) ($totalVentas - $totalGastos),
            'contado'        => (float) $totalContado,
            'credito'        => (float) $totalCredito
        ]);
    }

    public function gananciasDiarias(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        // --- Ventas agrupadas por fecha ---
        $ventasQuery = Venta::selectRaw('DATE(fecha_venta) as fecha, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha');

        if ($from) $ventasQuery->where('fecha_venta', '>=', $from);
        if ($to)   $ventasQuery->where('fecha_venta', '<=', $to);

        $ventas = $ventasQuery->get()->keyBy('fecha');

        // --- Gastos agrupados por fecha ---
        $gastosQuery = Gasto::selectRaw('DATE(fecha_hora) as fecha, SUM(valor) as total')
            ->groupBy('fecha')
            ->orderBy('fecha');

        if ($from) $gastosQuery->where('fecha_hora', '>=', $from);
        if ($to)   $gastosQuery->where('fecha_hora', '<=', $to);

        $gastos = $gastosQuery->get()->keyBy('fecha');

        // --- Unir fechas únicas ---
        $fechas = collect($ventas->keys())->merge($gastos->keys())->unique()->sort();

        // --- Construir respuesta final ---
        $resultado = $fechas->map(function ($fecha) use ($ventas, $gastos) {
            $venta = (float) ($ventas[$fecha]->total ?? 0);
            $gasto = (float) ($gastos[$fecha]->total ?? 0);
            return [
                'fecha' => $fecha,
                'ventas' => $venta,
                'gastos' => $gasto,
                'ganancia' => $venta - $gasto
            ];
        })->values();

        return response()->json($resultado);
    }

    public function gastosPorCategoria(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Gasto::select('categorias.nombre as categoria', DB::raw('SUM(gastos.valor) as total'))
            ->join('categorias', 'gastos.categoria_id', '=', 'categorias.id')
            ->groupBy('categorias.nombre')
            ->orderByDesc('total');

        if ($from) $query->where('gastos.fecha_hora', '>=', $from);
        if ($to)   $query->where('gastos.fecha_hora', '<=', $to);

        $gastos = $query->get();

        return response()->json($gastos);
    }

    public function productosRentables(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $query = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_venta.venta_id', '=', 'ventas.id')
            ->select(
                'productos.nombre',
                DB::raw('SUM((detalle_venta.precio_venta - productos.precio_compra) * detalle_venta.cantidad) as ganancia_total')
            )
            ->groupBy('productos.nombre')
            ->orderByDesc('ganancia_total')
            ->limit(10);

        if ($from) {
            $query->where('ventas.fecha_venta', '>=', $from);
        }

        if ($to) {
            $query->where('ventas.fecha_venta', '<=', $to);
        }

        $productos = $query->get();

        return response()->json($productos);
    }

    public function gananciasPorTipo(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $query = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_venta.venta_id', '=', 'ventas.id')
            ->select(
                'ventas.tipo_pago',
                DB::raw('SUM((detalle_venta.precio_venta - productos.precio_compra) * detalle_venta.cantidad) as ganancia')
            )
            ->groupBy('ventas.tipo_pago');

        if ($from) {
            $query->where('ventas.fecha_venta', '>=', $from);
        }

        if ($to) {
            $query->where('ventas.fecha_venta', '<=', $to);
        }

        $resultados = $query->get();
        return response()->json($resultados);
    }
}
