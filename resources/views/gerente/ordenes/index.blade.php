@extends('layouts.gerente')
@section('title', 'Órdenes de Trabajo')
@section('header', 'Órdenes de Trabajo')

@section('content')
<x-alert />

{{-- Filtros --}}
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
            <select name="estado" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                <option value="">Todos</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" {{ ($filtros['estado'] ?? '') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Fecha ingreso</label>
            <input type="date" name="fecha" value="{{ $filtros['fecha'] ?? '' }}"
                class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Placa</label>
            <input type="text" name="placa" value="{{ $filtros['placa'] ?? '' }}" placeholder="ABC-1234"
                class="w-full sm:w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 uppercase">
        </div>
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Cliente</label>
            <input type="text" name="cliente" value="{{ $filtros['cliente'] ?? '' }}" placeholder="Nombre..."
                class="w-full sm:w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="flex gap-2 flex-wrap w-full sm:w-auto">
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Filtrar</button>
            <a href="{{ route('gerente.ordenes.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
            <a href="{{ route('gerente.ordenes.create') }}"
                class="sm:ml-auto px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                + Nueva Orden
            </a>
        </div>
    </div>
</form>

@php
$badgeEstado = [
    'RECIBIDO'   => 'bg-gray-100 text-gray-600',
    'DIAGNOSTICO'=> 'bg-blue-100 text-blue-700',
    'REPARACION' => 'bg-amber-100 text-amber-700',
    'LISTO'      => 'bg-green-100 text-green-700',
    'ENTREGADO'  => 'bg-purple-100 text-purple-700',
];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600"># Orden</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Vehículo / Cliente</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Mecánico</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Estado</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Ingreso</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Total</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($ordenes as $orden)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono font-bold text-gray-500">#{{ $orden->id }}</td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</p>
                    <p class="text-gray-400 text-xs">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">
                    {{ $orden->empleado?->persona->nombre }} {{ $orden->empleado?->persona->apellido ?? 'Sin asignar' }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeEstado[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $orden->estado }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $orden->fecha_ingreso?->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-700">
                    {{ $orden->costo_total ? 'Bs. ' . number_format($orden->costo_total, 2) : '—' }}
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('gerente.ordenes.show', $orden) }}" class="text-teal-600 hover:text-teal-800 text-xs font-medium">Ver</a>
                    <form method="POST" action="{{ route('gerente.ordenes.destroy', $orden) }}" class="inline"
                          onsubmit="return confirm('¿Eliminar esta orden?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-gray-400">No se encontraron órdenes con los filtros aplicados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($ordenes->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $ordenes->links() }}</div>
    @endif
</div>
@endsection
