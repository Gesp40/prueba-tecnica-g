<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Bloque;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelectsController extends Controller
{
    public function proyectos(): JsonResponse
    {
        // Solo activos, ordenados
        $data = Proyecto::activos()->orderBy('nombre')->get(['id', 'nombre']);
        return response()->json($data);
    }

    public function bloques(Proyecto $proyecto): JsonResponse
    {
        $data = $proyecto->bloques()->orderBy('nombre')->get(['id', 'nombre']);
        return response()->json($data);
    }

    public function piezas(Bloque $bloque, Request $request): JsonResponse
    {
        // Por defecto, solo pendientes; se puede pasar ?estado=fabricada|pendiente|*
        $estado = $request->query('estado', 'pendiente');

        $query = $bloque->piezas()->orderBy('codigo')->select('id', 'codigo', 'nombre', 'peso_teorico', 'estado');

        if ($estado !== '*') {
            $query->where('estado', $estado);
        }

        return response()->json($query->get());
    }
}
