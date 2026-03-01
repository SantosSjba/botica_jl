<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSintomaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sintoma' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sintoma' => 'síntoma',
        ];
    }
}
