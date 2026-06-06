@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('header', 'Panel de Administracion')

@section('content')

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Usuarios Activos</p>
        <p class="text-3xl font-black text-purple-700">{{ $usuariosActivos }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Ordenes del Mes</p>
        <p class="text-3xl font-black text-gray-800">{{ $ordenesMes }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Ingresos del Mes</p>
        <p class="text-2xl font-black text-green-600">Bs. {{ number_format($ingresosMes, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-400 uppercase mb-1">Bitacora Hoy</p>
        <p class="text-3xl font-black text-amber-600">{{ $bitacoraHoy }}</p>
    </div>
</div>

{{-- Graficos --}}
<div class="grid lg:grid-cols-3 gap-6 mb-6">

    {{-- Dona: Ordenes por estado --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Ordenes por Estado</h3>
        <canvas id="chartEstados" height="220"></canvas>
    </div>

    {{-- Barras: Ingresos vs Gastos --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Ingresos vs Gastos (6 meses)</h3>
        <canvas id="chartFinanciero" height="120"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">

    {{-- Barras horizontal: Servicios --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Servicios mas Solicitados</h3>
        <canvas id="chartServicios" height="220"></canvas>
    </div>

    {{-- Ultimas acciones bitacora --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Ultimas Acciones</h3>
            <a href="{{ route('admin.bitacora.index') }}" class="text-purple-600 hover:text-purple-800 text-xs">Ver todo</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($ultimasAcciones as $a)
            <div class="px-5 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $a->accion }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $a->usuario?->name ?? 'Sistema' }} &middot; {{ $a->modulo }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
// Grafico 1: Dona estados
const estadosData = @json($ordenesPorEstado);
new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(estadosData),
        datasets: [{
            data: Object.values(estadosData),
            backgroundColor: ['#6b7280','#3b82f6','#f59e0b','#22c55e','#a855f7'],
            borderWidth: 0,
        }]
    },
    options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
});

// Grafico 2: Barras ingresos vs gastos
const meses = @json($meses->values());
const ingresos = meses.map(m => Number(@json($ingresosPorMes)[m] ?? 0));
const gastos   = meses.map(m => Number(@json($gastosPorMes)[m] ?? 0));
new Chart(document.getElementById('chartFinanciero'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [
            { label: 'Ingresos', data: ingresos, backgroundColor: '#22c55e' },
            { label: 'Gastos',   data: gastos,   backgroundColor: '#ef4444' },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
});

// Grafico 3: Barras horizontal servicios
const svcData = @json($serviciosTop);
new Chart(document.getElementById('chartServicios'), {
    type: 'bar',
    data: {
        labels: svcData.map(s => s.nombre),
        datasets: [{ label: 'Solicitudes', data: svcData.map(s => s.total), backgroundColor: '#a855f7' }]
    },
    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});
</script>
@endsection
