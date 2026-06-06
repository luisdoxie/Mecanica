@extends('layouts.mecanico')
@section('title', 'Vehículos en Taller')
@section('header', 'Vehículos en Taller')

@section('content')
<x-alert />

<form method="GET" class="flex gap-2 mb-6 max-w-sm">
    <input type="text" name="buscar" value="{{ $buscar ?? '' }}"
        placeholder="Placa, marca, cliente..."
        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Buscar</button>
    @if($buscar ?? false)
        <a href="{{ route('mecanico.vehiculos.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">✕</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Vehículo</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Placa</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cliente</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Km</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($vehiculos as $v)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $v->marca }} {{ $v->modelo }}</p>
                    <p class="text-xs text-gray-400">{{ $v->anio }} · {{ $v->color }}</p>
                </td>
                <td class="px-4 py-3 font-mono font-bold text-gray-700">{{ $v->placa }}</td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $v->cliente->persona->nombre }} {{ $v->cliente->persona->apellido }}
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ number_format($v->km_actual ?? 0) }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('mecanico.ordenes.create') }}?vehiculo_id={{ $v->id }}"
                        class="text-amber-600 hover:text-amber-800 text-xs font-medium">Nueva orden →</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-gray-400">No se encontraron vehículos.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($vehiculos->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $vehiculos->links() }}</div>
    @endif
</div>
@endsection
