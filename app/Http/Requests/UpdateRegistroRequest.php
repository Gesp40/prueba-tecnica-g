<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'peso_real' => ['required', 'numeric', 'min:0', 'max:999999.999'],
        ];
    }

    public function messages(): array
    {
        return [
            'peso_real.required' => 'Ingresa el peso real.',
            'peso_real.numeric'  => 'El peso real debe ser numérico.',
        ];
    }
}
