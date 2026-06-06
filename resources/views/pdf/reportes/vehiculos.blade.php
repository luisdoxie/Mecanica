<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }

        .header { background: #0f766e; color: white; padding: 14px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 9px; opacity: 0.85; margin-top: 2px; }

        .periodo { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 14px; margin: 0 20px 16px 20px; font-size: 11px; color: #166534; }

        .orden-card { margin: 0 20px 20px 20px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; page-break-inside: avoid; }
        .orden-header { background: #f8fafc; padding: 8px 12px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .orden-num { font-size: 12px; font-weight: bold; color: #0f766e; }
        .estado-badge { padding: 2px 8px; border-radius: 20px; font-size: 8px; font-weight: bold; }
        .estado-RECIBIDO   { background: #f1f5f9; color: #64748b; }
        .estado-DIAGNOSTICO{ background: #dbeafe; color: #1d4ed8; }
        .estado-REPARACION { background: #fef3c7; color: #b45309; }
        .estado-LISTO      { background: #dcfce7; color: #166534; }
        .estado-ENTREGADO  { background: #f3e8ff; color: #7e22ce; }

        .orden-body { padding: 10px 12px; }
        .info-grid { display: flex; gap: 12px; margin-bottom: 8px; }
        .info-col { flex: 1; }
        .label { font-size: 8px; color: #94a3b8; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .value { font-size: 10px; color: #1e293b; }

        .section-title { font-size: 9px; font-weight: bold; color: #475569; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; margin: 8px 0 5px 0; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th { background: #f8fafc; padding: 4px 6px; text-align: left; font-weight: bold; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background: #f0fdf4; color: #166534; }

        .fotos-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
        .foto-item { text-align: center; }
        .foto-item img { width: 120px; height: 90px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; }
        .foto-label { font-size: 7px; color: #94a3b8; margin-top: 2px; }

        .sin-ordenes { padding: 30px; text-align: center; color: #94a3b8; }
        .footer { text-align: center; font-size: 8px; color: #94a3b8; padding: 10px; margin-top: 10px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="header">
    <h1>Reporte de Vehículos — Taller Mecánico Eusebio</h1>
    <p>Generado el {{ now()->format('d/m/Y H:i') }} · Sistema de Gestión</p>
</div>

<div class="periodo">
    Período: <strong>{{ $desde->format('d/m/Y') }}</strong> al <strong>{{ $hasta->format('d/m/Y') }}</strong>
    &nbsp;·&nbsp; Total de órdenes: <strong>{{ $ordenes->count() }}</strong>
    &nbsp;·&nbsp; Ingresos del período: <strong>Bs. {{ number_format($ordenes->sum('costo_total'), 2) }}</strong>
</div>

@forelse($ordenes as $orden)
<div class="orden-card">
    <div class="orden-header">
        <span class="orden-num">Orden #{{ $orden->id }}</span>
        <span class="estado-badge estado-{{ $orden->estado }}">{{ $orden->estado }}</span>
    </div>
    <div class="orden-body">
        {{-- Info principal --}}
        <div class="info-grid">
            <div class="info-col">
                <div class="label">Vehículo</div>
                <div class="value"><strong>{{ $orden->vehiculo->placa }}</strong> — {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->año }})</div>
            </div>
            <div class="info-col">
                <div class="label">Cliente</div>
                <div class="value">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</div>
                <div style="font-size:8px;color:#94a3b8">{{ $orden->vehiculo->cliente->persona->telefono }}</div>
            </div>
            <div class="info-col">
                <div class="label">Mecánico</div>
                <div class="value">{{ $orden->empleado?->persona->nombre }} {{ $orden->empleado?->persona->apellido ?? 'Sin asignar' }}</div>
            </div>
            <div class="info-col">
                <div class="label">Fechas</div>
                <div class="value">Ingreso: {{ $orden->fecha_ingreso?->format('d/m/Y') }}</div>
                @if($orden->fecha_entrega)
                <div style="font-size:8px;color:#94a3b8">Entrega: {{ $orden->fecha_entrega->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>

        {{-- Problema --}}
        @if($orden->descripcion_problema)
        <div style="margin-bottom:6px">
            <div class="label">Descripción del problema</div>
            <div class="value">{{ $orden->descripcion_problema }}</div>
        </div>
        @endif

        {{-- Servicios --}}
        @if($orden->servicios->isNotEmpty())
        <div class="section-title">Servicios realizados</div>
        <table>
            <thead><tr><th>Servicio</th><th class="text-right">Precio aplicado</th></tr></thead>
            <tbody>
                @foreach($orden->servicios as $srv)
                <tr>
                    <td>{{ $srv->nombre }}</td>
                    <td class="text-right">Bs. {{ number_format($srv->pivot->precio_aplicado, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Repuestos --}}
        @if($orden->repuestos->isNotEmpty())
        <div class="section-title">Repuestos utilizados</div>
        <table>
            <thead><tr><th>Repuesto</th><th class="text-right">Cant.</th><th class="text-right">Costo unit.</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody>
                @foreach($orden->repuestos as $rep)
                <tr>
                    <td>{{ $rep->descripcion }}</td>
                    <td class="text-right">{{ $rep->cantidad }}</td>
                    <td class="text-right">Bs. {{ number_format($rep->costo, 2) }}</td>
                    <td class="text-right">Bs. {{ number_format($rep->costo * $rep->cantidad, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Total --}}
        @if($orden->costo_total)
        <table style="margin-top:4px">
            <tr class="total-row">
                <td colspan="{{ ($orden->repuestos->isNotEmpty() ? 3 : 1) }}">TOTAL ORDEN</td>
                <td class="text-right">Bs. {{ number_format($orden->costo_total, 2) }}</td>
            </tr>
        </table>
        @endif

        {{-- Fotos --}}
        @if(!empty($fotosPorOrden[$orden->id]))
        <div class="section-title">Fotos ({{ count($fotosPorOrden[$orden->id]) }})</div>
        <div class="fotos-grid">
            @foreach($fotosPorOrden[$orden->id] as $foto)
            <div class="foto-item">
                <img src="{{ $foto['data'] }}" alt="{{ $foto['tipo'] }}">
                <div class="foto-label">{{ $foto['tipo'] }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@empty
<div class="sin-ordenes">No hay órdenes en el período seleccionado.</div>
@endforelse

<div class="footer">
    Taller Mecánico Eusebio &mdash; Reporte generado automáticamente &mdash; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
