<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroRequest;
use App\Http\Requests\UpdateRegistroRequest;
use App\Models\Pieza;
use App\Models\Registro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegistroController extends Controller
{
    /** Listado (paginado) */
    public function index(): Response
    {
        $registros = Registro::query()
            ->with(['pieza.bloque.proyecto', 'user'])
            ->orderByDesc('id')
            ->paginate(10)
            ->through(function ($r) {
                return [
                    'id'           => $r->id,
                    'pieza'        => [
                        'id'     => $r->pieza_id,
                        'codigo' => $r->pieza->codigo ?? null,
                        'nombre' => $r->pieza->nombre ?? null,
                    ],
                    'proyecto'     => $r->pieza?->bloque?->proyecto?->nombre,
                    'bloque'       => $r->pieza?->bloque?->nombre,
                    'peso_teorico' => (string) $r->peso_teorico,
                    'peso_real'    => (string) $r->peso_real,
                    'diferencia'   => (string) $r->diferencia,
                    'registrado_en'=> optional($r->registrado_en)->format('Y-m-d H:i:s'),
                    'usuario'      => $r->user?->name,
                ];
            });

        return Inertia::render('Registros/Index', [
            'registros' => $registros,
        ]);
    }

    /** Formulario de creación */
    public function create(): Response
    {
        return Inertia::render('Registros/Create');
    }

    /** Guardar registro nuevo y marcar pieza como fabricada */
    public function store(StoreRegistroRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // Asegurar pieza pendiente
            $pieza = Pieza::query()
                ->where('id', $data['pieza_id'])
                ->where('estado', 'pendiente')
                ->firstOrFail();

            $pesoT = (float) $pieza->peso_teorico;
            $pesoR = (float) $data['peso_real'];
            $dif   = $pesoT - $pesoR;

            Registro::create([
                'pieza_id'     => $pieza->id,
                'user_id'      => auth()->id(),
                'peso_teorico' => $pesoT,
                'peso_real'    => $pesoR,
                'diferencia'   => $dif,
            ]);

            // Marcar pieza como fabricada
            $pieza->update(['estado' => 'fabricada']);
        });

        return redirect()->route('registros.create')->with('success', 'Registro creado correctamente y pieza marcada como fabricada.');
    }

    /** Formulario de edición */
    public function edit(Registro $registro): Response
    {
        $registro->load(['pieza.bloque.proyecto']);

        return Inertia::render('Registros/Edit', [
            'registro' => [
                'id'           => $registro->id,
                'pieza'        => [
                    'id'     => $registro->pieza_id,
                    'codigo' => $registro->pieza->codigo ?? null,
                    'nombre' => $registro->pieza->nombre ?? null,
                ],
                'proyecto'     => $registro->pieza?->bloque?->proyecto?->nombre,
                'bloque'       => $registro->pieza?->bloque?->nombre,
                'peso_teorico' => (string) $registro->peso_teorico,
                'peso_real'    => (string) $registro->peso_real,
                'diferencia'   => (string) $registro->diferencia,
                'registrado_en'=> optional($registro->registrado_en)->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /** Actualizar (solo peso_real y diferencia) */
    public function update(UpdateRegistroRequest $request, Registro $registro): RedirectResponse
    {
        $data    = $request->validated();
        $pesoT   = (float) $registro->peso_teorico;
        $pesoR   = (float) $data['peso_real'];
        $dif     = $pesoT - $pesoR;

        $registro->update([
            'peso_real'  => $pesoR,
            'diferencia' => $dif,
        ]);

        return redirect()->route('registros.index')->with('success', 'Registro actualizado correctamente.');
    }

    /** Eliminar y, si la pieza queda sin registros, volverla a pendiente */
    public function destroy(Registro $registro): RedirectResponse
    {
        DB::transaction(function () use ($registro) {
            $piezaId = $registro->pieza_id;

            $registro->delete();

            $remaining = Registro::where('pieza_id', $piezaId)->count();
            if ($remaining === 0) {
                Pieza::where('id', $piezaId)->update(['estado' => 'pendiente']);
            }
        });

        return redirect()->route('registros.index')->with('success', 'Registro eliminado. La pieza se marcó pendiente si no tiene más registros.');
    }
}