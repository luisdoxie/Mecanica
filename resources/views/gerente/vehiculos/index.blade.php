@extends('layouts.gerente')
@section('title', 'Vehículos')
@section('header', 'Gestión de Vehículos')

@section('content')
<x-alert />

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex gap-2 flex-1 min-w-0">
        <input type="text" name="buscar" value="{{ $buscar }}"
            placeholder="Placa, marca, modelo o cliente..."
            class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition shrink-0">Buscar</button>
        @if($buscar)
            <a href="{{ route('gerente.vehiculos.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm shrink-0">✕</a>
        @endif
    </form>
    <a href="{{ route('gerente.vehiculos.create') }}"
        class="shrink-0 px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
        + Nuevo Vehículo
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[480px]">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Placa</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Vehículo</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cliente</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Km</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($vehiculos as $vehiculo)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded">{{ $vehiculo->placa }}</span>
                </td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                    <p class="text-gray-400 text-xs">{{ $vehiculo->anio }} · {{ $vehiculo->color }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $vehiculo->cliente->persona->nombre }} {{ $vehiculo->cliente->persona->apellido }}
                </td>
                <td class="px-4 py-3 text-gray-600">{{ number_format($vehiculo->km_actual ?? 0) }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('gerente.vehiculos.show', $vehiculo) }}" class="text-teal-600 hover:text-teal-800 text-xs font-medium">Ver</a>
                    <a href="{{ route('gerente.vehiculos.edit', $vehiculo) }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Editar</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                    No se encontraron vehículos{{ $buscar ? " para \"{$buscar}\"" : '' }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($vehiculos->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $vehiculos->links() }}</div>
    @endif
</div>
@endsection
