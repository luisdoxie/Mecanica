<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { background: #1f2937; color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo-area h1 { font-size: 20px; font-weight: bold; }
        .logo-area p { font-size: 10px; color: #9ca3af; margin-top: 2px; }
        .recibo-info { text-align: right; }
        .recibo-info .num { font-size: 16px; font-weight: bold; color: #f59e0b; }
        .recibo-info .fecha { font-size: 10px; color: #9ca3af; margin-top: 3px; }
        .body { padding: 24px 30px; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 10px; letter-spacing: 0.05em; }
        .info-grid { display: flex; gap: 30px; }
        .info-col { flex: 1; }
        .info-row { margin-bottom: 6px; }
        .info-row .label { font-size: 9px; color: #9ca3af; text-transform: uppercase; }
        .info-row .value { font-weight: bold; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        table thead tr { background: #f9fafb; }
        table th { padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; color: #374151; border-bottom: 2px solid #e5e7eb; }
        table td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        table tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .totales { margin-top: 12px; }
        .totales table { width: 280px; margin-left: auto; }
        .totales td { padding: 5px 10px; font-size: 11px; }
        .totales .total-row td { font-size: 13px; font-weight: bold; background: #1f2937; color: white; padding: 8px 10px; }
        .metodo { display: inline-block; background: #f3f4f6; border-radius: 4px; padding: 2px 8px; font-size: 10px; font-weight: bold; }
        .firma-section { margin-top: 30px; display: flex; justify-content: flex-end; }
        .firma-box { text-align: center; width: 200px; }
        .firma-line { border-top: 1px solid #374151; margin-bottom: 6px; }
        .firma-nombre { font-weight: bold; font-size: 11px; }
        .firma-cargo { font-size: 10px; color: #6b7280; }
        .footer { margin-top: 30px; padding: 12px 30px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-area">
        <h1>Taller Mecanico Eusebio</h1>
        <p>Av. Principal, Local 4B &middot; Tel: +58 412-555-0100</p>
        <p>Lun&ndash;Vie 8am&ndash;6pm | Sab 8am&ndash;1pm</p>
    </div>
    <div class="recibo-info">
        <div class="num">{{ $numRecibo }}</div>
        <div class="fecha">Emitido: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="body">

    <div class="section">
        <div class="section-title">Datos del Cliente y Vehiculo</div>
        <div class="info-grid">
            <div class="info-col">
                <div class="info-row">
                    <div class="label">Cliente</div>
                    <div class="value">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Cedula</div>
                    <div class="value">{{ $orden->vehiculo->cliente->persona->ci }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Telefono</div>
                    <div class="value">{{ $orden->vehiculo->cliente->persona->telefono ?? '---' }}</div>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <div class="label">Vehiculo</div>
                    <div class="value">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} {{ $orden->vehiculo->anio }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Placa</div>
                    <div class="value">{{ $orden->vehiculo->placa }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Orden N.</div>
                    <div class="value">#{{ $orden->id }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Fecha ingreso</div>
                    <div class="value">{{ \Carbon\Carbon::parse($orden->fecha_ingreso)->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($orden->servicios->isNotEmpty())
    <div class="section">
        <div class="section-title">Servicios Realizados</div>
        <table>
            <thead>
                <tr>
                    <th>Descripcion</th>
                    <th>Observaciones</th>
                    <th class="text-right">Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->servicios as $svc)
                <tr>
                    <td>{{ $svc->nombre }}</td>
                    <td style="color:#6b7280">{{ $svc->pivot->observaciones ?? '---' }}</td>
                    <td class="text-right">Bs. {{ number_format($svc->pivot->precio_aplicado, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($orden->repuestos->isNotEmpty())
    <div class="section">
        <div class="section-title">Repuestos Utilizados</div>
        <table>
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Origen</th>
                    <th>Calidad</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">C/U</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->repuestos as $rep)
                <tr>
                    <td>{{ $rep->nombre }}</td>
                    <td>{{ $rep->origen }}</td>
                    <td style="color:#6b7280">{{ $rep->calidad_observada ?? '---' }}</td>
                    <td class="text-right">{{ $rep->cantidad }}</td>
                    <td class="text-right">Bs. {{ number_format($rep->costo, 2) }}</td>
                    <td class="text-right">Bs. {{ number_format($rep->costo * $rep->cantidad, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @php
        $subtotalServicios = $orden->servicios->sum(fn($s) => $s->pivot->precio_aplicado);
        $subtotalRepuestos = $orden->repuestos->sum(fn($r) => $r->costo * $r->cantidad);
        $ultimoPago = $orden->pagos->sortByDesc('created_at')->first();
    @endphp

    <div class="totales">
        <table>
            <tr>
                <td>Subtotal servicios</td>
                <td class="text-right">Bs. {{ number_format($subtotalServicios, 2) }}</td>
            </tr>
            <tr>
                <td>Subtotal repuestos</td>
                <td class="text-right">Bs. {{ number_format($subtotalRepuestos, 2) }}</td>
            </tr>
            @if($ultimoPago)
            <tr>
                <td>Metodo de pago</td>
                <td class="text-right"><span class="metodo">{{ $ultimoPago->metodo_pago }}</span></td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-right">Bs. {{ number_format($orden->costo_total ?? ($subtotalServicios + $subtotalRepuestos), 2) }}</td>
            </tr>
        </table>
    </div>

    @if($orden->empleado)
    <div class="firma-section">
        <div class="firma-box">
            <div style="height: 40px;"></div>
            <div class="firma-line"></div>
            <div class="firma-nombre">{{ $orden->empleado->persona->nombre }} {{ $orden->empleado->persona->apellido }}</div>
            <div class="firma-cargo">{{ $orden->empleado->cargo }} &mdash; Mecanico Responsable</div>
        </div>
    </div>
    @endif

</div>

<div class="footer">
    Gracias por confiar en Taller Mecanico Eusebio &middot; Este documento es valido como comprobante de servicio
</div>

</body>
</html>
