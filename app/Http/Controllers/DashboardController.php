<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Gasto;


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
}
