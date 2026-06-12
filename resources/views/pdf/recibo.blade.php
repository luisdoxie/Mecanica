<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; background: #fff; }

/* ─── Header ─── */
.header-table { width: 100%; border-collapse: collapse; background: #111827; }
.header-logo-cell { padding: 22px 20px 22px 24px; vertical-align: middle; width: 60%; }
.header-num-cell  { padding: 22px 24px 22px 10px; vertical-align: middle; text-align: right; }

.logo-icon { display: inline-block; background: #f59e0b; width: 44px; height: 44px; border-radius: 8px; text-align: center; vertical-align: middle; font-size: 15px; font-weight: bold; color: #111827; line-height: 44px; }
.logo-name { display: inline-block; vertical-align: middle; padding-left: 10px; }
.logo-name h1 { font-size: 17px; font-weight: bold; color: #ffffff; }
.logo-name p  { font-size: 8px; color: #9ca3af; margin-top: 3px; }

.rec-label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; }
.rec-num   { font-size: 20px; font-weight: bold; color: #f59e0b; margin-top: 2px; }
.rec-fecha { font-size: 8px; color: #9ca3af; margin-top: 4px; }

/* amber stripe */
.amber-stripe { height: 4px; background: #f59e0b; }

/* ─── Sello PAGADO ─── */
.sello-wrap { text-align: right; padding: 10px 24px 0 24px; }
.sello { display: inline; border: 3px solid #16a34a; color: #16a34a; font-size: 13px; font-weight: bold; padding: 3px 14px; }

/* ─── Cuerpo ─── */
.body { padding: 16px 24px; }

/* sección título */
.sec-title { font-size: 8px; font-weight: bold; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.08em; border-left: 3px solid #f59e0b; padding-left: 7px; margin-bottom: 8px; margin-top: 16px; }

/* info cards */
.info-table { width: 100%; border-collapse: collapse; }
.info-card { background: #f9fafb; padding: 10px 12px; vertical-align: top; width: 50%; }
.info-label { font-size: 7.5px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
.info-value { font-size: 10px; font-weight: bold; color: #111827; margin-top: 1px; margin-bottom: 6px; }

/* ─── Tablas de servicios / repuestos ─── */
.data-table { width: 100%; border-collapse: collapse; }
.data-table thead tr { background: #111827; }
.data-table th { padding: 7px 9px; font-size: 8px; font-weight: bold; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.04em; text-align: left; }
.data-table th.r { text-align: right; }
.data-table td { padding: 7px 9px; font-size: 9px; color: #374151; border-bottom: 1px solid #f3f4f6; }
.data-table td.r { text-align: right; }
.data-table tr.even td { background: #f9fafb; }
.data-table tr:last-child td { border-bottom: none; }

/* ─── Totales ─── */
.totales-outer { width: 100%; border-collapse: collapse; margin-top: 14px; }
.totales-outer td.spacer { width: 55%; }
.totales-box-cell { width: 45%; vertical-align: top; }
.totales-box { border: 1px solid #e5e7eb; }
.totales-row { width: 100%; border-collapse: collapse; border-bottom: 1px solid #f3f4f6; }
.totales-row td { padding: 7px 12px; font-size: 9.5px; }
.totales-row td.tl { color: #6b7280; }
.totales-row td.tv { font-weight: bold; color: #111827; text-align: right; }
.totales-total-row { background: #111827; }
.totales-total-row td { padding: 10px 12px; }
.totales-total-row td.tl { color: #9ca3af; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
.totales-total-row td.tv { color: #f59e0b; font-size: 16px; font-weight: bold; text-align: right; }
.metodo-chip { background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; padding: 1px 7px; font-size: 8px; font-weight: bold; }

/* ─── Firma ─── */
.firma-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
.firma-cell { width: 65%; }
.firma-right { width: 35%; text-align: center; }
.firma-space { height: 32px; }
.firma-line { border-top: 1px solid #374151; padding-top: 5px; }
.firma-nombre { font-weight: bold; font-size: 10px; color: #111827; }
.firma-cargo  { font-size: 8px; color: #6b7280; margin-top: 2px; }

/* ─── Footer ─── */
.footer { margin-top: 24px; background: #f9fafb; border-top: 3px solid #f59e0b; }
.footer-table { width: 100%; border-collapse: collapse; }
.footer-table td { padding: 10px 24px; font-size: 8px; }
.footer-left  { color: #6b7280; }
.footer-right { text-align: right; color: #9ca3af; font-size: 7.5px; }
</style>
</head>
<body>

{{-- ── Header ── --}}
<table class="header-table">
  <tr>
    <td class="header-logo-cell">
      <span class="logo-icon">TE</span>
      <span class="logo-name">
        <h1>Taller Eusebio</h1>
        <p>Tu direcci&oacute;n aqu&iacute; &middot; Tel: 71056485</p>
        <p>Lun&ndash;Vie 8am&ndash;6pm &nbsp;|&nbsp; Sab 8am&ndash;1pm</p>
      </span>
    </td>
    <td class="header-num-cell">
      <div class="rec-label">Recibo de servicio</div>
      <div class="rec-num">{{ $numRecibo }}</div>
      <div class="rec-fecha">Emitido: {{ now()->format('d/m/Y H:i') }}</div>
    </td>
  </tr>
</table>
<div class="amber-stripe"></div>

{{-- ── Sello PAGADO ── --}}
@php $pagado = $orden->pagos->where('estado', 'PAGADO')->isNotEmpty(); @endphp
@if($pagado)
<div class="sello-wrap"><span class="sello">&#10003; PAGADO</span></div>
@endif

<div class="body">

  {{-- ── Cliente & Vehículo ── --}}
  <div class="sec-title">Cliente &amp; Veh&iacute;culo</div>
  <table class="info-table">
    <tr>
      <td class="info-card" style="padding-right:8px;">
        <div class="info-label">Cliente</div>
        <div class="info-value">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</div>
        <div class="info-label">C&eacute;dula</div>
        <div class="info-value">{{ $orden->vehiculo->cliente->persona->ci }}</div>
        <div class="info-label">Tel&eacute;fono</div>
        <div class="info-value">{{ $orden->vehiculo->cliente->persona->telefono ?? '—' }}</div>
      </td>
      <td class="info-card" style="padding-left:8px;">
        <div class="info-label">Veh&iacute;culo</div>
        <div class="info-value">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} {{ $orden->vehiculo->anio }}</div>
        <div class="info-label">Placa</div>
        <div class="info-value">{{ strtoupper($orden->vehiculo->placa) }}</div>
        <div class="info-label">Orden N&deg; &nbsp;|&nbsp; Ingreso</div>
        <div class="info-value">#{{ $orden->id }} &nbsp;&mdash;&nbsp; {{ \Carbon\Carbon::parse($orden->fecha_ingreso)->format('d/m/Y') }}</div>
      </td>
    </tr>
  </table>

  {{-- ── Servicios ── --}}
  @if($orden->servicios->isNotEmpty())
  <div class="sec-title">Servicios Realizados</div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:50%">Descripci&oacute;n</th>
        <th>Observaciones</th>
        <th class="r" style="width:18%">Precio</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orden->servicios as $i => $svc)
      <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
        <td>{{ $svc->nombre }}</td>
        <td style="color:#9ca3af">{{ $svc->pivot->observaciones ?? '—' }}</td>
        <td class="r">Bs. {{ number_format($svc->pivot->precio_aplicado, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- ── Repuestos ── --}}
  @if($orden->repuestos->isNotEmpty())
  <div class="sec-title">Repuestos Utilizados</div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:30%">Repuesto</th>
        <th>Origen</th>
        <th>Calidad</th>
        <th class="r" style="width:8%">Cant.</th>
        <th class="r" style="width:14%">C/U</th>
        <th class="r" style="width:14%">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orden->repuestos as $i => $rep)
      <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
        <td>{{ $rep->nombre }}</td>
        <td>{{ $rep->origen }}</td>
        <td style="color:#9ca3af">{{ $rep->calidad_observada ?? '—' }}</td>
        <td class="r">{{ $rep->cantidad }}</td>
        <td class="r">Bs. {{ number_format($rep->costo, 2) }}</td>
        <td class="r">Bs. {{ number_format($rep->costo * $rep->cantidad, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- ── Totales ── --}}
  @php
    $subtotalServicios = $orden->servicios->sum(fn($s) => $s->pivot->precio_aplicado);
    $subtotalRepuestos = $orden->repuestos->sum(fn($r) => $r->costo * $r->cantidad);
    $ultimoPago = $orden->pagos->sortByDesc('created_at')->first();
    $total = $orden->costo_total ?? ($subtotalServicios + $subtotalRepuestos);
  @endphp

  <table class="totales-outer">
    <tr>
      <td class="spacer"></td>
      <td class="totales-box-cell">
        <div class="totales-box">
          <table style="width:100%;border-collapse:collapse;">
            <tr class="totales-row">
              <td class="tl">Subtotal servicios</td>
              <td class="tv">Bs. {{ number_format($subtotalServicios, 2) }}</td>
            </tr>
            <tr class="totales-row">
              <td class="tl">Subtotal repuestos</td>
              <td class="tv">Bs. {{ number_format($subtotalRepuestos, 2) }}</td>
            </tr>
            @if($ultimoPago)
            <tr class="totales-row">
              <td class="tl">M&eacute;todo de pago</td>
              <td class="tv"><span class="metodo-chip">{{ $ultimoPago->metodo_pago }}</span></td>
            </tr>
            @endif
            <tr class="totales-total-row">
              <td class="tl">Total</td>
              <td class="tv">Bs. {{ number_format($total, 2) }}</td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>

  {{-- ── Firma ── --}}
  @if($orden->empleado)
  <table class="firma-table">
    <tr>
      <td class="firma-cell"></td>
      <td class="firma-right">
        <div class="firma-space"></div>
        <div class="firma-line">
          <div class="firma-nombre">{{ $orden->empleado->persona->nombre }} {{ $orden->empleado->persona->apellido }}</div>
          <div class="firma-cargo">{{ $orden->empleado->cargo }} &mdash; Mec&aacute;nico Responsable</div>
        </div>
      </td>
    </tr>
  </table>
  @endif

</div>

{{-- ── Footer ── --}}
<div class="footer">
  <table class="footer-table">
    <tr>
      <td class="footer-left">Gracias por confiar en Taller Eusebio &mdash; 71056485</td>
      <td class="footer-right">Documento v&aacute;lido como comprobante de servicio<br>{{ now()->format('d/m/Y H:i') }}</td>
    </tr>
  </table>
</div>

</body>
</html>
