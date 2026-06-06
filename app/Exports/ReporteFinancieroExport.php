<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReporteFinancieroExport implements FromArray, WithHeadings
{
    public function __construct(private array $data) {}

    public function headings(): array
    {
        return ['Concepto', 'Monto (Bs.)'];
    }

    public function array(): array
    {
        $rows = [
            ['Ingresos (pagos cobrados)', $this->data['ingresos']],
            ['Gastos operativos',         $this->data['gastos']],
            ['Salarios',                  $this->data['salarios']],
            ['GANANCIA NETA',             $this->data['ganancia']],
            ['', ''],
            ['--- Detalle Gastos por Categoria ---', ''],
        ];
        foreach ($this->data['detalleGastos'] as $g) {
            $rows[] = [$g->categoria, $g->total];
        }
        return $rows;
    }
}
