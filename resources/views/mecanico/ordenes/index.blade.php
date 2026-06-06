@extends('layouts.mecanico')
@section('title', 'Mis Órdenes')
@section('header', 'Mis Órdenes de Trabajo')

@section('content')
<x-alert />

@php
$badgeEstado = [
    'RECIBIDO'   => 'bg-gray-100 text-gray-600',
    'DIAGNOSTICO'=> 'bg-blue-100 text-blue-700',
    'REPARACION' => 'bg-amber-100 text-amber-700',
    'LISTO'      => 'bg-green-100 text-green-700',
    'ENTREGADO'  => 'bg-purple-100 text-purple-700',
];
@endphp

<form method="GET" class="flex flex-wrap gap-3 items-end mb-6">
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
        <select name="estado" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
            <option value="">Todos</option>
            @foreach($estados as $e)
                <option value="{{ $e }}" {{ ($filtros['estado'] ?? '') === $e ? 'selected' : '' }}>{{ $e }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Placa</label>
        <input type="text" name="placa" value="{{ $filtros['placa'] ?? '' }}" placeholder="ABC-1234"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 uppercase w-32">
    </div>
    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Filtrar</button>
    <a href="{{ route('mecanico.ordenes.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600"># Orden</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Vehículo / Cliente</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Estado</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Ingreso</th>
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
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeEstado[$orden->estado] ?? '' }}">
                        {{ $orden->estado }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $orden->fecha_ingreso?->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('mecanico.ordenes.show', $orden) }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Ver detalle →</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-gray-400">No hay órdenes asignadas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($ordenes->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $ordenes->links() }}</div>
    @endif
</div>
@endsection
