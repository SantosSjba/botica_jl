<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresentacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'presentacion' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'presentacion' => 'presentación',
        ];
    }
}
