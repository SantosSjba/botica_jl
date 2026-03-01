<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Exportación a XLSX con las mismas columnas del sistema antiguo (producto/datatable + exportOptions 0-6).
 * Columnas: Cod., Descripcion, Presentacion, Stock, P.Venta, Estado, Tipo
 */
class ProductosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected Collection $productos,
        protected string $simboloMoneda = 'S/'
    ) {}

    public function collection(): Collection
    {
        return $this->productos;
    }

    public function headings(): array
    {
        return ['Cod.', 'Descripcion', 'Presentacion', 'Stock', 'P.Venta', 'Estado', 'Tipo'];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->codigo ?? '',
            $row->descripcion ?? '',
            $row->presentacion_nombre ?? '',
            (int) ($row->stock ?? 0),
            (float) ($row->precio_venta ?? 0),
            $row->estado == '1' ? 'Activo' : 'Inactivo',
            $row->tipo ?? '',
        ];
    }
}
