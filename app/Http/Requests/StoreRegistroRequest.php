<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

class StoreRegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'proyecto_id' => ['nullable', 'integer', 'exists:proyectos,id'],
            'bloque_id'   => ['nullable', 'integer', 'exists:bloques,id'],
            'pieza_id'    => ['required', 'integer', 'exists:piezas,id'],
            'peso_real'   => ['required', 'numeric', 'min:0', 'max:999999.999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'proyecto_id' => $this->filled('proyecto_id') ? (int) $this->input('proyecto_id') : null,
            'bloque_id'   => $this->filled('bloque_id')   ? (int) $this->input('bloque_id')   : null,
            'pieza_id'    => $this->filled('pieza_id')    ? (int) $this->input('pieza_id')    : null,
            'peso_real'   => $this->filled('peso_real')   ? (float) $this->input('peso_real') : null,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $piezaId    = $this->input('pieza_id');
            $bloqueId   = $this->input('bloque_id');
            $proyectoId = $this->input('proyecto_id');

            if (!$piezaId) return;

            $pieza = DB::table('piezas')
                ->select('id', 'bloque_id', 'estado', 'peso_teorico')
                ->where('id', $piezaId)
                ->first();

            if (!$pieza) return;

            if ($pieza->estado !== 'pendiente') {
                $v->errors()->add('pieza_id', 'La pieza seleccionada no está en estado pendiente.');
            }

            if ($bloqueId && (int)$bloqueId !== (int)$pieza->bloque_id) {
                $v->errors()->add('bloque_id', 'La pieza no pertenece al bloque seleccionado.');
            }

            if ($proyectoId) {
                $ok = DB::table('bloques')
                    ->where('id', $pieza->bloque_id)
                    ->where('proyecto_id', (int)$proyectoId)
                    ->exists();
                if (!$ok) {
                    $v->errors()->add('proyecto_id', 'La pieza no pertenece al proyecto seleccionado.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'pieza_id.required'  => 'Selecciona una pieza.',
            'pieza_id.exists'    => 'La pieza seleccionada no existe.',
            'peso_real.required' => 'Ingresa el peso real.',
            'peso_real.numeric'  => 'El peso real debe ser numérico.',
        ];
    }
}
