<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportesController extends Controller
{
    // Vistas Inertia (las páginas las creamos en el siguiente paso)
    public function pendientes(): Response
    {
        return Inertia::render('Reportes/Pendientes');
    }

    public function estadisticas(): Response
    {
        return Inertia::render('Reportes/Estadisticas');
    }

    // API: tabla de piezas pendientes por proyecto
    public function apiPendientesPorProyecto(): JsonResponse
    {
        $rows = DB::table('piezas as p')
            ->join('bloques as b', 'b.id', '=', 'p.bloque_id')
            ->join('proyectos as pr', 'pr.id', '=', 'b.proyecto_id')
            ->where('p.estado', 'pendiente')
            ->groupBy('pr.id', 'pr.nombre')
            ->orderBy('pr.nombre')
            ->get([
                'pr.nombre as proyecto',
                DB::raw('COUNT(p.id) as pendientes'),
            ]);

        return response()->json($rows);
    }

    // API: datos para Chart.js (pendientes vs fabricadas por proyecto)
    public function apiEstadisticas(): JsonResponse
    {
        $base = DB::table('piezas as p')
            ->join('bloques as b', 'b.id', '=', 'p.bloque_id')
            ->join('proyectos as pr', 'pr.id', '=', 'b.proyecto_id')
            ->groupBy('pr.id', 'pr.nombre')
            ->orderBy('pr.nombre')
            ->get([
                'pr.nombre as proyecto',
                DB::raw("SUM(CASE WHEN p.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes"),
                DB::raw("SUM(CASE WHEN p.estado = 'fabricada' THEN 1 ELSE 0 END) as fabricadas"),
            ]);

        // Formato listo para Chart.js (labels + datasets)
        $labels      = $base->pluck('proyecto');
        $pendientes  = $base->pluck('pendientes')->map(fn($v) => (int)$v);
        $fabricadas  = $base->pluck('fabricadas')->map(fn($v) => (int)$v);

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Pendientes', 'data' => $pendientes],
                ['label' => 'Fabricadas', 'data' => $fabricadas],
            ],
            'raw' => $base, // por si quieres depurar en el front
        ]);
    }
}
