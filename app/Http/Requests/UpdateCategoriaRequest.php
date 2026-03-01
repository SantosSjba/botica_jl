<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'forma_farmaceutica' => ['required', 'string', 'max:255'],
            'ff_simplificada' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'forma_farmaceutica' => 'forma farmacéutica',
            'ff_simplificada' => 'F.F. simplificada',
        ];
    }
}
