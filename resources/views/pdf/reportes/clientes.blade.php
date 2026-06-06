<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { background: #1f2937; color: white; padding: 15px 20px; margin-bottom: 15px; }
        h1 { font-size: 16px; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 10px; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; }
    </style>
</head>
<body>
<div class="header">
    <h1>Taller Mecanico Eusebio &mdash; Clientes Frecuentes</h1>
</div>
<table>
    <thead><tr><th>#</th><th>Cliente</th><th>CI</th><th>Telefono</th><th style="text-align:right">Ordenes</th><th style="text-align:right">Total Gastado</th></tr></thead>
    <tbody>
        @foreach($clientes as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $c->cliente }}</td>
            <td>{{ $c->ci }}</td>
            <td>{{ $c->telefono }}</td>
            <td style="text-align:right">{{ $c->total_ordenes }}</td>
            <td style="text-align:right">Bs. {{ number_format($c->total_gastado ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
