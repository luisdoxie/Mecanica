<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ReporteCobrosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $data) {}
    public function collection(): Collection { return collect($this->data); }
    public function headings(): array
    {
        return ['# Orden', 'Cliente', 'Telefono', 'Placa', 'Monto', 'Estado', 'Metodo', 'Fecha'];
    }
    public function map($row): array
    {
        return [$row->orden_id, $row->cliente, $row->telefono, $row->placa, $row->monto, $row->estado, $row->metodo_pago, $row->created_at];
    }
}
