@extends('layouts.admin')
@section('title', 'Reportes')
@section('header', 'Reportes Exportables')

@section('content')
<x-alert />

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

    {{-- Ordenes por periodo --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-1">Ordenes por Periodo</h3>
        <p class="text-gray-400 text-sm mb-4">Listado de ordenes filtradas por rango de fechas.</p>
        <form method="GET" action="{{ route('admin.reportes.ordenes') }}" class="space-y-3">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs text-gray-500">Desde</label>
                    <input type="date" name="desde" value="{{ now()->startOfMonth()->toDateString() }}"
                        class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Hasta</label>
                    <input type="date" name="hasta" value="{{ now()->toDateString() }}"
                        class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button name="formato" value="pdf" class="flex-1 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold rounded-lg transition">PDF</button>
                <button name="formato" value="excel" class="flex-1 py-2 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition">Excel</button>
            </div>
        </form>
    </div>

    {{-- Financiero mensual --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-1">Reporte Financiero Mensual</h3>
        <p class="text-gray-400 text-sm mb-4">Ingresos, gastos, salarios y ganancia neta del mes.</p>
        <form method="GET" action="{{ route('admin.reportes.financiero') }}" class="space-y-3">
            <div>
                <label class="text-xs text-gray-500">Mes (YYYY-MM)</label>
                <input type="month" name="mes" value="{{ now()->format('Y-m') }}"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
            </div>
            <div class="flex gap-2">
                <button name="formato" value="pdf" class="flex-1 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold rounded-lg transition">PDF</button>
                <button name="formato" value="excel" class="flex-1 py-2 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition">Excel</button>
            </div>
        </form>
    </div>

    {{-- Cuentas por cobrar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-1">Cuentas por Cobrar</h3>
        <p class="text-gray-400 text-sm mb-4">Pagos pendientes, parciales y fiados.</p>
        <form method="GET" action="{{ route('admin.reportes.cobros') }}" class="space-y-3">
            <div class="flex gap-2 mt-8">
                <button name="formato" value="pdf" class="flex-1 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold rounded-lg transition">PDF</button>
                <button name="formato" value="excel" class="flex-1 py-2 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition">Excel</button>
            </div>
        </form>
    </div>

    {{-- Servicios mas solicitados --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-1">Servicios mas Solicitados</h3>
        <p class="text-gray-400 text-sm mb-4">Ranking de servicios por frecuencia e ingresos generados.</p>
        <form method="GET" action="{{ route('admin.reportes.servicios') }}" class="space-y-3">
            <div class="flex gap-2 mt-8">
                <button name="formato" value="pdf" class="flex-1 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold rounded-lg transition">PDF</button>
                <button name="formato" value="excel" class="flex-1 py-2 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition">Excel</button>
            </div>
        </form>
    </div>

    {{-- Clientes frecuentes --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-1">Clientes Frecuentes</h3>
        <p class="text-gray-400 text-sm mb-4">Top 20 clientes por numero de ordenes y monto gastado.</p>
        <form method="GET" action="{{ route('admin.reportes.clientes') }}" class="space-y-3">
            <div class="flex gap-2 mt-8">
                <button name="formato" value="pdf" class="flex-1 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold rounded-lg transition">PDF</button>
                <button name="formato" value="excel" class="flex-1 py-2 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold rounded-lg transition">Excel</button>
            </div>
        </form>
    </div>

</div>
@endsection
