@extends('layouts.gerente')
@section('title', 'Dashboard')
@section('header', 'Panel de Gerencia')

@section('content')
<x-alert />

@php
$mesLabel = \Carbon\Carbon::createFromFormat('Y-m', $mes)->translatedFormat('F Y');
@endphp

{{-- Resumen financiero --}}
<div class="mb-2">
    <p class="text-sm text-gray-500 font-medium">Resumen financiero — <strong>{{ $mesLabel }}</strong></p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Ingresos</p>
        <p class="text-2xl font-black text-green-600">Bs. {{ number_format($ingresos, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Pagos PAGADO del mes</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Gastos</p>
        <p class="text-2xl font-black text-red-500">Bs. {{ number_format($gastos, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Gastos operativos del mes</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Salarios</p>
        <p class="text-2xl font-black text-amber-600">Bs. {{ number_format($salarios, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Planilla del mes</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 {{ $ganancia >= 0 ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Ganancia Neta</p>
        <p class="text-2xl font-black {{ $ganancia >= 0 ? 'text-green-700' : 'text-red-700' }}">
            Bs. {{ number_format(abs($ganancia), 2) }}
        </p>
        <p class="text-xs {{ $ganancia >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
            {{ $ganancia >= 0 ? '▲ Superávit' : '▼ Déficit' }}
        </p>
    </div>
</div>

{{-- Indicadores operativos --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <p class="text-3xl font-black text-gray-800">{{ $totalOrdenes }}</p>
        <p class="text-sm text-gray-500 mt-1">Órdenes este mes</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <p class="text-3xl font-black text-amber-600">{{ $ordenesPendientes }}</p>
        <p class="text-sm text-gray-500 mt-1">Órdenes activas</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <p class="text-3xl font-black text-green-600">{{ $autosListosHoy }}</p>
        <p class="text-sm text-gray-500 mt-1">Autos listos hoy</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <p class="text-2xl font-black text-red-500">Bs. {{ number_format($porCobrar, 2) }}</p>
        <p class="text-sm text-gray-500 mt-1">Por cobrar (fiado)</p>
    </div>
</div>

{{-- Órdenes activas con barra de progreso --}}
@if($ordenesActivasList->isNotEmpty())
@php
$progreso = ['RECIBIDO'=>20,'DIAGNOSTICO'=>40,'REPARACION'=>60,'LISTO'=>80,'ENTREGADO'=>100];
$badgeColor = ['RECIBIDO'=>'bg-gray-100 text-gray-600','DIAGNOSTICO'=>'bg-blue-100 text-blue-700','REPARACION'=>'bg-amber-100 text-amber-700','LISTO'=>'bg-green-100 text-green-700','ENTREGADO'=>'bg-purple-100 text-purple-700'];
$barColor   = ['RECIBIDO'=>'bg-gray-400','DIAGNOSTICO'=>'bg-blue-500','REPARACION'=>'bg-amber-500','LISTO'=>'bg-green-500','ENTREGADO'=>'bg-purple-500'];
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Órdenes en Proceso</h3>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($ordenesActivasList as $o)
        @php $pct = $progreso[$o->estado] ?? 0; @endphp
        <div class="px-5 py-3">
            <div class="flex items-center justify-between mb-1.5">
                <div>
                    <span class="font-semibold text-gray-800 text-sm">#{{ $o->id }} — {{ $o->vehiculo->placa }}</span>
                    <span class="text-gray-400 text-xs ml-2">{{ $o->vehiculo->cliente->persona->apellido }}, {{ $o->vehiculo->cliente->persona->nombre }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor[$o->estado] ?? '' }}">{{ $o->estado }}</span>
                    <a href="{{ route('gerente.ordenes.show', $o) }}" class="text-teal-600 hover:text-teal-800 text-xs">Ver →</a>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $barColor[$o->estado] ?? 'bg-gray-400' }}" style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-right text-xs text-gray-400 mt-0.5">{{ $pct }}%</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Top 5 trabajos más costosos --}}
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Top 5 Trabajos del Mes</h3>
        </div>
        @forelse($topOrdenes as $i => $orden)
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs font-bold flex items-center justify-center">{{ $i+1 }}</span>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }}</p>
                    <p class="text-gray-400 text-xs">{{ $orden->vehiculo->cliente->persona->apellido }}, {{ $orden->vehiculo->cliente->persona->nombre }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-bold text-gray-800 text-sm">Bs. {{ number_format($orden->costo_total ?? 0, 2) }}</p>
                <a href="{{ route('gerente.ordenes.show', $orden) }}" class="text-teal-600 hover:text-teal-800 text-xs">Ver orden →</a>
            </div>
        </div>
        @empty
        <p class="px-5 py-8 text-center text-gray-400 text-sm">Sin órdenes este mes.</p>
        @endforelse
    </div>

    {{-- Accesos rápidos --}}
    <div class="space-y-3">
        <h3 class="font-bold text-gray-700 text-sm px-1">Accesos Rápidos</h3>
        @php
        $accesos = [
            ['label'=>'Nueva Orden', 'icon'=>'📋', 'route'=>'gerente.ordenes.create'],
            ['label'=>'Nuevo Cliente', 'icon'=>'👤', 'route'=>'gerente.clientes.create'],
            ['label'=>'Registrar Cobro', 'icon'=>'💰', 'route'=>'gerente.cobros.create'],
            ['label'=>'Registrar Gasto', 'icon'=>'💸', 'route'=>'gerente.gastos.create'],
            ['label'=>'Planilla', 'icon'=>'👷', 'route'=>'gerente.planilla.index'],
            ['label'=>'Recibos PDF', 'icon'=>'📄', 'route'=>'gerente.recibos.index'],
            ['label'=>'Reporte Semanal', 'icon'=>'📊', 'route'=>'gerente.reportes.vehiculos'],
        ];
        @endphp
        <div class="grid grid-cols-2 gap-3">
            @foreach($accesos as $a)
            <a href="{{ route($a['route']) }}"
                class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3 hover:border-teal-200 hover:shadow-sm transition group">
                <span class="text-2xl">{{ $a['icon'] }}</span>
                <span class="font-semibold text-gray-700 text-sm group-hover:text-teal-700 transition">{{ $a['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Gráfico 7 días + Cuentas por cobrar --}}
<div class="grid lg:grid-cols-2 gap-6 mt-6">

    {{-- Gráfico ingresos últimos 7 días --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-4">Ingresos — Últimos 7 días</h3>
        <canvas id="chartIngresos" height="120"></canvas>
    </div>

    {{-- Cuentas por cobrar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Cuentas por Cobrar</h3>
            <p class="text-xs text-gray-400 mt-0.5">Top 5 más antiguas (fiado/parcial)</p>
        </div>
        @forelse($cuentasPorCobrar as $pago)
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
            <div>
                <p class="font-semibold text-gray-800 text-sm">
                    {{ $pago->orden->vehiculo->cliente->persona->apellido ?? '—' }},
                    {{ $pago->orden->vehiculo->cliente->persona->nombre ?? '' }}
                </p>
                <p class="text-gray-400 text-xs">
                    Orden #{{ $pago->orden_trabajo_id }} ·
                    {{ $pago->created_at ? \Carbon\Carbon::parse($pago->created_at)->format('d/m/Y') : '—' }}
                </p>
            </div>
            <div class="text-right">
                <p class="font-bold text-red-600 text-sm">Bs. {{ number_format($pago->monto, 2) }}</p>
                <span class="text-xs px-1.5 py-0.5 rounded bg-red-50 text-red-600 font-medium">{{ $pago->estado }}</span>
            </div>
        </div>
        @empty
        <p class="px-5 py-6 text-center text-gray-400 text-sm">Sin cuentas pendientes.</p>
        @endforelse
    </div>
</div>

{{-- Actividad reciente --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Actividad Reciente del Taller</h3>
    </div>
    @forelse($actividadReciente as $log)
    <div class="flex items-start gap-3 px-5 py-3 border-b border-gray-50 last:border-0">
        <div class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0 mt-0.5">
            <span class="text-teal-600 text-xs font-bold">{{ strtoupper(substr($log->rol ?? 'S', 0, 1)) }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-gray-800 text-sm font-medium truncate">{{ $log->accion }}</p>
            <p class="text-gray-400 text-xs">{{ $log->modulo }} · {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</p>
        </div>
        <span class="text-xs text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
    </div>
    @empty
    <p class="px-5 py-6 text-center text-gray-400 text-sm">Sin actividad registrada.</p>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartIngresos').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($ingresosUltimos7->pluck('fecha')),
            datasets: [{
                label: 'Ingresos (Bs.)',
                data: @json($ingresosUltimos7->pluck('total')),
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13,148,136,0.08)',
                borderWidth: 2,
                pointBackgroundColor: '#0d9488',
                pointRadius: 4,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
