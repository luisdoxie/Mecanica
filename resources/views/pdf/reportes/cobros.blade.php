<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { background: #1f2937; color: white; padding: 15px 20px; margin-bottom: 15px; }
        h1 { font-size: 16px; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 7px 8px; text-align: left; font-size: 9px; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; }
    </style>
</head>
<body>
<div class="header">
    <h1>Taller Mecanico Eusebio &mdash; Cuentas por Cobrar</h1>
</div>
<table>
    <thead><tr><th>#</th><th>Cliente</th><th>Telefono</th><th>Placa</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr></thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($cobros as $c)
        <tr>
            <td>#{{ $c->orden_id }}</td>
            <td>{{ $c->cliente }}</td>
            <td>{{ $c->telefono }}</td>
            <td style="font-family:monospace;font-weight:bold">{{ $c->placa }}</td>
            <td>Bs. {{ number_format($c->monto, 2) }}</td>
            <td>{{ $c->estado }}</td>
            <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y') }}</td>
        </tr>
        @php $total += $c->monto; @endphp
        @endforeach
        <tr style="font-weight:bold;background:#f9fafb">
            <td colspan="4" style="text-align:right">TOTAL</td>
            <td>Bs. {{ number_format($total, 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
</body>
</html>
