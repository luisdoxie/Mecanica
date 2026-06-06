<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BitacoraExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filtros) {}

    public function collection()
    {
        $query = ActivityLog::with('usuario')->orderByDesc('created_at');

        if ($u = $this->filtros['usuario'] ?? null) {
            $query->whereHas('usuario', fn($q) => $q->where('name', 'ilike', "%{$u}%"));
        }
        if ($a = $this->filtros['accion'] ?? null) {
            $query->where('accion', 'ilike', "%{$a}%");
        }
        if ($m = $this->filtros['modulo'] ?? null) {
            $query->where('modulo', 'ilike', "%{$m}%");
        }
        if ($d = $this->filtros['desde'] ?? null) {
            $query->where('created_at', '>=', $d . ' 00:00:00');
        }
        if ($h = $this->filtros['hasta'] ?? null) {
            $query->where('created_at', '<=', $h . ' 23:59:59');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Fecha', 'Usuario', 'Rol', 'Accion', 'Modulo', 'Registro ID', 'IP', 'Dispositivo'];
    }

    public function map($row): array
    {
        return [
            $row->created_at?->format('d/m/Y H:i:s'),
            $row->usuario?->name ?? 'Sistema',
            $row->rol,
            $row->accion,
            $row->modulo,
            $row->registro_id,
            $row->ip,
            $row->dispositivo,
        ];
    }
}
