<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroRequest;
use App\Http\Requests\UpdateRegistroRequest;
use App\Models\Pieza;
use App\Models\Proyecto;
use App\Models\Registro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegistroController extends Controller
{
    /** Listado paginado */
    public function index(): Response
    {
        $registros = Registro::with(['pieza.bloque.proyecto', 'user'])
            ->orderBy('registrado_en', 'desc') // campo real en la tabla
            ->paginate(10)
            ->through(function (Registro $r) {
                return [
                    'id'            => $r->id,
                    'proyecto'      => $r->pieza->bloque->proyecto->nombre,
                    'bloque'        => $r->pieza->bloque->nombre,
                    'pieza'         => [
                        'id'     => $r->pieza->id,
                        'codigo' => $r->pieza->codigo,
                        'nombre' => $r->pieza->nombre,
                    ],
                    // usar valores persistidos en el registro
                    'peso_teorico'  => number_format((float) $r->peso_teorico, 3, '.', ''),
                    'peso_real'     => number_format((float) $r->peso_real, 3, '.', ''),
                    'diferencia'    => number_format((float) $r->diferencia, 3, '.', ''),
                    'usuario'       => $r->user?->name ?? $r->user?->email,
                    'registrado_en' => optional($r->registrado_en)->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Registros/Index', [
            'registros' => $registros,
        ]);
    }

    /** Formulario de creación */
    public function create(): Response
    {
        return Inertia::render('Registros/Create', [
            'proyectos' => Proyecto::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Crear registro
     * Regla: al crear un registro, la pieza pasa a 'fabricada'
     */
    public function store(StoreRegistroRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // asegurar que la pieza está pendiente
            $pieza = Pieza::query()
                ->whereKey($data['pieza_id'])
                ->where('estado', 'pendiente')
                ->firstOrFail();

            $pesoTeorico = (float) $pieza->peso_teorico;
            $pesoReal    = (float) $data['peso_real'];
            $diferencia  = $pesoTeorico - $pesoReal;

            Registro::create([
                'pieza_id'     => $pieza->id,
                'user_id'      => auth()->id(),
                'peso_teorico' => $pesoTeorico,
                'peso_real'    => $pesoReal,
                'diferencia'   => $diferencia,
                // 'registrado_en' se llena con useCurrent() desde la BD
            ]);

            // marcar pieza como fabricada
            $pieza->update(['estado' => 'fabricada']);
        });

        return redirect()
            ->route('registros.create')
            ->with('success', 'Registro creado correctamente y pieza marcada como fabricada.');
    }

    /** Redirigir show al índice */
    public function show(Registro $registro): RedirectResponse
    {
        return redirect()->route('registros.index');
    }

    /** Formulario de edición */
    public function edit(Registro $registro): Response
    {
        return Inertia::render('Registros/Edit', [
            'registro' => $registro->load('pieza.bloque.proyecto'),
        ]);
    }

    /**
     * Actualizar registro
     * Regla: recalcula diferencia; NO cambia estado de la pieza
     */
    public function update(UpdateRegistroRequest $request, Registro $registro): RedirectResponse
    {
        $data       = $request->validated();
        $pesoTeorico = (float) $registro->peso_teorico; // usar el valor persistido en el registro
        $pesoReal    = (float) $data['peso_real'];
        $diferencia  = $pesoTeorico - $pesoReal;

        $registro->update([
            'peso_real'  => $pesoReal,
            'diferencia' => $diferencia,
        ]);

        return redirect()
            ->route('registros.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Eliminar registro
     * Regla: si la pieza queda sin registros, vuelve a 'pendiente'
     */
    public function destroy(Registro $registro): RedirectResponse
    {
        DB::transaction(function () use ($registro) {
            $piezaId = $registro->pieza_id;

            $registro->delete();

            $remaining = Registro::where('pieza_id', $piezaId)->count();
            if ($remaining === 0) {
                Pieza::whereKey($piezaId)->update(['estado' => 'pendiente']);
            }
        });

        return redirect()
            ->route('registros.index')
            ->with('success', 'Registro eliminado. La pieza se marcó pendiente si no tiene más registros.');
        }
}