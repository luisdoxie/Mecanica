<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        .header { background: #1f2937; color: white; padding: 15px 20px; margin-bottom: 15px; }
        h1 { font-size: 16px; margin: 0; }
        .sub { font-size: 10px; color: #9ca3af; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f3f4f6; padding: 7px 8px; text-align: left; font-size: 9px; border-bottom: 2px solid #e5e7eb; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; font-size: 9px; }
        .total { font-weight: bold; background: #f9fafb; }
        .badge { padding: 2px 6px; border-radius: 20px; font-size: 8px; font-weight: bold; }
    </style>
</head>
<body>
<div class="header">
    <h1>Taller Mecanico Eusebio &mdash; Reporte de Ordenes</h1>
    <div class="sub">Periodo: {{ $desde }} al {{ $hasta }} &middot; Generado: {{ now()->format('d/m/Y H:i') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th><th>Placa</th><th>Vehiculo</th><th>Cliente</th><th>Mecanico</th><th>Estado</th><th style="text-align:right">Costo</th><th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($ordenes as $o)
        <tr>
            <td>{{ $o->id }}</td>
            <td style="font-family:monospace;font-weight:bold">{{ $o->placa }}</td>
            <td>{{ $o->marca }} {{ $o->modelo }}</td>
            <td>{{ $o->cliente }}</td>
            <td>{{ $o->mecanico }}</td>
            <td>{{ $o->estado }}</td>
            <td style="text-align:right">Bs. {{ number_format($o->costo_total ?? 0, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($o->fecha_ingreso)->format('d/m/Y') }}</td>
        </tr>
        @php $total += $o->costo_total ?? 0; @endphp
        @endforeach
        <tr class="total">
            <td colspan="6" style="text-align:right;font-weight:bold">TOTAL ({{ $ordenes->count() }} ordenes)</td>
            <td style="text-align:right">Bs. {{ number_format($total, 2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
</body>
</html>
