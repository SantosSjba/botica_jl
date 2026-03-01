<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero' => ['required', 'string', 'max:100'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'numero' => 'número de lote',
            'fecha_vencimiento' => 'fecha de vencimiento',
        ];
    }
}
