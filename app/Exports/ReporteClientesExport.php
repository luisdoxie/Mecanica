<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ReporteClientesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $data) {}
    public function collection(): Collection { return collect($this->data); }
    public function headings(): array { return ['Cliente', 'CI', 'Telefono', 'Total Ordenes', 'Total Gastado (Bs.)']; }
    public function map($row): array { return [$row->cliente, $row->ci, $row->telefono, $row->total_ordenes, $row->total_gastado]; }
}
