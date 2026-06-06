<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { background: #1f2937; color: white; padding: 15px 20px; margin-bottom: 20px; }
        h1 { font-size: 16px; margin: 0; }
        .kpi { display: flex; gap: 15px; margin-bottom: 20px; }
        .kpi-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center; }
        .kpi-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
        .kpi-val { font-size: 18px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 7px 10px; text-align: left; font-size: 10px; }
        td { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Taller Mecanico Eusebio &mdash; Reporte Financiero {{ $mes }}</h1>
</div>

<div class="kpi">
    <div class="kpi-box">
        <div class="kpi-label">Ingresos</div>
        <div class="kpi-val" style="color:#16a34a">Bs. {{ number_format($ingresos, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-label">Gastos</div>
        <div class="kpi-val" style="color:#dc2626">Bs. {{ number_format($gastos, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-label">Salarios</div>
        <div class="kpi-val" style="color:#d97706">Bs. {{ number_format($salarios, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-label">Ganancia Neta</div>
        <div class="kpi-val" style="color:{{ $ganancia >= 0 ? '#16a34a' : '#dc2626' }}">Bs. {{ number_format($ganancia, 2) }}</div>
    </div>
</div>

<table>
    <thead><tr><th>Categoria de Gasto</th><th style="text-align:right">Total</th></tr></thead>
    <tbody>
        @foreach($detalleGastos as $g)
        <tr><td>{{ $g->categoria }}</td><td style="text-align:right">Bs. {{ number_format($g->total, 2) }}</td></tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
