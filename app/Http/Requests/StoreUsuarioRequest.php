<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'fechaingreso' => ['required', 'date'],
            'cargo_usu' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'in:Activo,Inactivo'],
            'tipo' => ['required', 'in:ADMINISTRADOR,USUARIO'],
            'usuario' => ['required', 'string', 'max:50', 'unique:usuario,usuario'],
            'clave' => ['required', 'string', 'min:4', 'max:255', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombres' => 'nombre',
            'cargo_usu' => 'cargo',
            'fechaingreso' => 'fecha de ingreso',
            'clave' => 'contraseña',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge(['clave' => Hash::make($this->input('clave'))]);
    }
}
