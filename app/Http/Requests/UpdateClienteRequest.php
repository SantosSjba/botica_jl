<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:255'],
            'id_tipo_docu' => ['required', 'integer', 'exists:tipo_documento,idtipo_docu'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'nrodoc' => ['required', 'string', 'max:30'],
            'tipo' => ['required', 'in:cliente,laboratorio'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombres' => 'razón social',
            'id_tipo_docu' => 'tipo de documento',
            'nrodoc' => 'número de documento',
            'tipo' => 'tipo (cliente/laboratorio)',
        ];
    }
}
