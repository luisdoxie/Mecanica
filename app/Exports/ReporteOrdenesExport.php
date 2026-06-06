<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ReporteOrdenesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $data) {}

    public function collection(): Collection { return collect($this->data); }

    public function headings(): array
    {
        return ['# Orden', 'Placa', 'Vehiculo', 'Cliente', 'Mecanico', 'Estado', 'Costo Total', 'Fecha Ingreso'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->placa,
            "{$row->marca} {$row->modelo}",
            $row->cliente,
            $row->mecanico,
            $row->estado,
            $row->costo_total,
            $row->fecha_ingreso,
        ];
    }
}
