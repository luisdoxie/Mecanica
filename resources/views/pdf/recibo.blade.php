<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        /* ── Banda lateral amber ── */
        .accent-bar { position: fixed; left: 0; top: 0; bottom: 0; width: 6px; background: #f59e0b; }

        /* ── Header ── */
        .header { background: #111827; padding: 28px 36px 28px 42px; }
        .header-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo-box { display: flex; align-items: center; gap: 14px; }
        .logo-icon { width: 48px; height: 48px; background: #f59e0b; border-radius: 10px; text-align: center; line-height: 48px; font-size: 16px; font-weight: bold; color: #111827; }
        .logo-text h1 { font-size: 18px; font-weight: bold; color: #ffffff; letter-spacing: 0.02em; }
        .logo-text p { font-size: 9px; color: #9ca3af; margin-top: 3px; }
        .recibo-badge { text-align: right; }
        .recibo-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; }
        .recibo-num { font-size: 22px; font-weight: bold; color: #f59e0b; margin-top: 2px; }
        .recibo-fecha { font-size: 9px; color: #9ca3af; margin-top: 4px; }

        /* ── Banda amber bajo header ── */
        .amber-stripe { height: 4px; background: #f59e0b; margin-left: 6px; }

        /* ── Sello PAGADO ── */
        .sello-wrap { padding: 12px 36px 0 42px; text-align: right; }
        .sello { display: inline-block; border: 3px solid #16a34a; color: #16a34a; font-size: 15px; font-weight: bold; padding: 4px 18px; border-radius: 6px; letter-spacing: 0.15em; opacity: 0.85; }

        /* ── Body ── */
        .body { padding: 20px 36px 20px 42px; }

        /* ── Secciones ── */
        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 9px; font-weight: bold; text-transform: uppercase;
            color: #f59e0b; letter-spacing: 0.1em;
            border-left: 3px solid #f59e0b; padding-left: 8px;
            margin-bottom: 10px;
        }

        /* ── Info grid ── */
        .info-grid { display: flex; gap: 24px; }
        .info-col { flex: 1; background: #f9fafb; border-radius: 8px; padding: 12px 14px; }
        .info-row { margin-bottom: 7px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-row .label { font-size: 8px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-row .value { font-weight: bold; color: #111827; font-size: 11px; margin-top: 1px; }

        /* ── Tablas ── */
        table { width: 100%; border-collapse: collapse; }
        table thead tr { background: #111827; }
        table th { padding: 8px 10px; text-align: left; font-size: 9px; font-weight: bold; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.05em; }
        table th.text-right { text-align: right; }
        table td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; color: #374151; }
        table tbody tr:nth-child(even) td { background: #f9fafb; }
        table tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }

        /* ── Totales ── */
        .totales-wrap { margin-top: 16px; display: flex; justify-content: flex-end; }
        .totales-box { width: 300px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .totales-row { display: flex; justify-content: space-between; padding: 8px 14px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        .totales-row:last-child { border-bottom: none; }
        .totales-row .t-label { color: #6b7280; }
        .totales-row .t-value { font-weight: bold; color: #111827; }
        .totales-total { background: #111827; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; }
        .totales-total .t-label { color: #9ca3af; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
        .totales-total .t-value { color: #f59e0b; font-size: 18px; font-weight: bold; }

        /* ── Método de pago chip ── */
        .metodo-chip { display: inline-block; background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 4px; padding: 2px 8px; font-size: 9px; font-weight: bold; }

        /* ── Firma ── */
        .firma-section { margin-top: 28px; display: flex; justify-content: flex-end; }
        .firma-box { text-align: center; width: 200px; }
        .firma-space { height: 36px; }
        .firma-line { border-top: 1px solid #374151; margin-bottom: 6px; }
        .firma-nombre { font-weight: bold; font-size: 11px; color: #111827; }
        .firma-cargo { font-size: 9px; color: #6b7280; margin-top: 2px; }

        /* ── Footer ── */
        .footer { margin-top: 28px; margin-left: 6px; padding: 12px 36px; background: #f9fafb; border-top: 3px solid #f59e0b; }
        .footer-inner { display: flex; justify-content: space-between; align-items: center; }
        .footer-msg { font-size: 9px; color: #6b7280; }
        .footer-valid { font-size: 8px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<div class="accent-bar"></div>

{{-- Header --}}
<div class="header">
    <div class="header-inner">
        <div class="logo-box">
            <div class="logo-icon">TE</div>
            <div class="logo-text">
                <h1>Taller Eusebio</h1>
                <p>Tu dirección aquí &middot; Tel: 71056485</p>
                <p>Lun&ndash;Vie 8am&ndash;6pm &nbsp;|&nbsp; Sab 8am&ndash;1pm</p>
            </div>
        </div>
        <div class="recibo-badge">
            <div class="recibo-label">Recibo de servicio</div>
            <div class="recibo-num">{{ $numRecibo }}</div>
            <div class="recibo-fecha">Emitido: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>
<div class="amber-stripe"></div>

{{-- Sello PAGADO --}}
@php $pagado = $orden->pagos->where('estado', 'PAGADO')->isNotEmpty(); @endphp
@if($pagado)
<div class="sello-wrap">
    <span class="sello">&#10003; PAGADO</span>
</div>
@endif

<div class="body">

    {{-- Cliente y vehículo --}}
    <div class="section">
        <div class="section-title">Cliente &amp; Vehículo</div>
        <div class="info-grid">
            <div class="info-col">
                <div class="info-row">
                    <div class="label">Cliente</div>
                    <div class="value">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Cédula</div>
                    <div class="value">{{ $orden->vehiculo->cliente->persona->ci }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Teléfono</div>
                    <div class="value">{{ $orden->vehiculo->cliente->persona->telefono ?? '—' }}</div>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <div class="label">Vehículo</div>
                    <div class="value">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} {{ $orden->vehiculo->anio }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Placa</div>
                    <div class="value">{{ strtoupper($orden->vehiculo->placa) }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Orden N°</div>
                    <div class="value">#{{ $orden->id }}</div>
                </div>
                <div class="info-row">
                    <div class="label">Fecha ingreso</div>
                    <div class="value">{{ \Carbon\Carbon::parse($orden->fecha_ingreso)->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Servicios --}}
    @if($orden->servicios->isNotEmpty())
    <div class="section">
        <div class="section-title">Servicios Realizados</div>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Observaciones</th>
                    <th class="text-right">Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->servicios as $svc)
                <tr>
                    <td>{{ $svc->nombre }}</td>
                    <td style="color:#9ca3af">{{ $svc->pivot->observaciones ?? '—' }}</td>
                    <td class="text-right">Bs. {{ number_format($svc->pivot->precio_aplicado, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Repuestos --}}
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
                    <td style="color:#9ca3af">{{ $rep->calidad_observada ?? '—' }}</td>
                    <td class="text-right">{{ $rep->cantidad }}</td>
                    <td class="text-right">Bs. {{ number_format($rep->costo, 2) }}</td>
                    <td class="text-right">Bs. {{ number_format($rep->costo * $rep->cantidad, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Totales --}}
    @php
        $subtotalServicios = $orden->servicios->sum(fn($s) => $s->pivot->precio_aplicado);
        $subtotalRepuestos = $orden->repuestos->sum(fn($r) => $r->costo * $r->cantidad);
        $ultimoPago = $orden->pagos->sortByDesc('created_at')->first();
    @endphp

    <div class="totales-wrap">
        <div class="totales-box">
            <div class="totales-row">
                <span class="t-label">Subtotal servicios</span>
                <span class="t-value">Bs. {{ number_format($subtotalServicios, 2) }}</span>
            </div>
            <div class="totales-row">
                <span class="t-label">Subtotal repuestos</span>
                <span class="t-value">Bs. {{ number_format($subtotalRepuestos, 2) }}</span>
            </div>
            @if($ultimoPago)
            <div class="totales-row">
                <span class="t-label">Método de pago</span>
                <span class="t-value"><span class="metodo-chip">{{ $ultimoPago->metodo_pago }}</span></span>
            </div>
            @endif
            <div class="totales-total">
                <span class="t-label">Total</span>
                <span class="t-value">Bs. {{ number_format($orden->costo_total ?? ($subtotalServicios + $subtotalRepuestos), 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Firma --}}
    @if($orden->empleado)
    <div class="firma-section">
        <div class="firma-box">
            <div class="firma-space"></div>
            <div class="firma-line"></div>
            <div class="firma-nombre">{{ $orden->empleado->persona->nombre }} {{ $orden->empleado->persona->apellido }}</div>
            <div class="firma-cargo">{{ $orden->empleado->cargo }} &mdash; Mecánico Responsable</div>
        </div>
    </div>
    @endif

</div>

{{-- Footer --}}
<div class="footer">
    <div class="footer-inner">
        <div class="footer-msg">Gracias por confiar en Taller Eusebio &mdash; 71056485</div>
        <div class="footer-valid">Documento válido como comprobante de servicio<br>{{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

</body>
</html>
