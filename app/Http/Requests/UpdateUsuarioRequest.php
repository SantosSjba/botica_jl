<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $idusu = $usuario instanceof \App\Models\Usuario ? $usuario->idusu : $usuario;

        return [
            'nombres' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'fechaingreso' => ['required', 'date'],
            'cargo_usu' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'in:Activo,Inactivo'],
            'tipo' => ['required', 'in:ADMINISTRADOR,USUARIO'],
            'usuario' => ['required', 'string', 'max:50', Rule::unique('usuario', 'usuario')->ignore($idusu, 'idusu')],
            'clave' => ['nullable', 'string', 'min:4', 'max:255', 'confirmed'],
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
        $clave = $this->input('clave');
        if ($clave && strlen($clave) > 0) {
            $this->merge(['clave' => \Illuminate\Support\Facades\Hash::make($clave)]);
        } else {
            $this->replace($this->except('clave'));
        }
    }
}
