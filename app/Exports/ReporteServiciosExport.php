<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ReporteServiciosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $data) {}
    public function collection(): Collection { return collect($this->data); }
    public function headings(): array { return ['Servicio', 'Veces Solicitado', 'Total Generado (Bs.)']; }
    public function map($row): array { return [$row->nombre, $row->veces, $row->total_generado]; }
}
