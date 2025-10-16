<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\JsonResponse;

class ReportesController extends Controller
{
    // API piezas
    public function pendientesApi(): JsonResponse
    {
        $proyectos = Proyecto::with(['bloques.piezas' => function ($q) {
            $q->where('estado', 'pendiente');
        }])
            ->orderBy('nombre')
            ->get()
            ->map(function ($proyecto) {
                return [
                    'proyecto'   => $proyecto->nombre,
                    'pendientes' => $proyecto->bloques->sum(fn($b) => $b->piezas->count()),
                ];
            })
            ->values();

        return response()->json(['data' => $proyectos]);
    }

    // API: datos para Chart.js
    public function apiEstadisticas(): JsonResponse
    {
        $proyectos = Proyecto::withCount([
            'piezas as pendientes' => fn($q) => $q->where('estado', 'pendiente'),
            'piezas as fabricadas' => fn($q) => $q->where('estado', 'fabricada'),
        ])
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => [
                'labels'   => $proyectos->pluck('nombre'),
                'datasets' => [
                    [
                        'label' => 'Pendientes',
                        'data'  => $proyectos->pluck('pendientes'),
                    ],
                    [
                        'label' => 'Fabricadas',
                        'data'  => $proyectos->pluck('fabricadas'),
                    ],
                ],
            ],
        ]);
    }
}
