<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['nullable', 'string', 'max:100'],
            'idlote' => ['required', 'integer', 'exists:lote,idlote'],
            'descripcion' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:Generico,No Generico,No Aplica'],
            'stock' => ['required', 'integer', 'min:0'],
            'stockminimo' => ['required', 'integer', 'min:0'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'ventasujeta' => ['required', 'string', 'in:Con receta medica,sin receta medica,No aplica'],
            'fecha_registro' => ['required', 'date'],
            'reg_sanitario' => ['nullable', 'string', 'max:100'],
            'idcategoria' => ['required', 'integer', 'exists:categoria,idcategoria'],
            'idpresentacion' => ['required', 'integer', 'exists:presentacion,idpresentacion'],
            'idcliente' => ['nullable', 'integer', 'exists:cliente,idcliente'],
            'idsintoma' => ['required', 'integer', 'exists:sintoma,idsintoma'],
            'idtipoaf' => ['required', 'integer', 'exists:tipo_afectacion,idtipoa'],
            'estado' => ['required', 'in:0,1'],
            'tipo_precio' => ['required', 'in:01,02'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'idlote' => 'lote',
            'idcategoria' => 'forma farmacéutica',
            'idpresentacion' => 'presentación',
            'idcliente' => 'laboratorio',
            'idsintoma' => 'síntoma',
            'idtipoaf' => 'tipo afectación',
        ];
    }
}
