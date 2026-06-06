@extends('layouts.gerente')
@section('title', 'Vehículo')
@section('header', 'Historial del Vehículo')

@section('content')
<x-alert />

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gerente.vehiculos.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
    <a href="{{ route('gerente.vehiculos.edit', $vehiculo) }}"
        class="ml-auto px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
        Editar
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="text-center mb-4">
                <div class="text-5xl mb-2">🚗</div>
                <h2 class="font-bold text-gray-800 text-xl">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h2>
                <code class="text-amber-600 font-mono font-bold text-lg">{{ $vehiculo->placa }}</code>
            </div>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">Año</dt><dd class="font-medium text-gray-700">{{ $vehiculo->anio }}</dd></div>
                <div><dt class="text-gray-400">Color</dt><dd class="font-medium text-gray-700">{{ $vehiculo->color ?? '—' }}</dd></div>
                <div><dt class="text-gray-400">Kilometraje</dt><dd class="font-medium text-gray-700">{{ number_format($vehiculo->km_actual ?? 0) }} km</dd></div>
                <div class="border-t pt-3">
                    <dt class="text-gray-400">Propietario</dt>
                    <dd class="font-medium text-gray-700">
                        <a href="{{ route('gerente.clientes.show', $vehiculo->cliente) }}" class="text-teal-600 hover:underline">
                            {{ $vehiculo->cliente->persona->nombre }} {{ $vehiculo->cliente->persona->apellido }}
                        </a>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Historial de Órdenes ({{ $vehiculo->ordenesTrabajo->count() }})</h3>
            </div>
            @forelse($vehiculo->ordenesTrabajo as $orden)
            <div class="px-5 py-4 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">Orden #{{ $orden->numero_orden ?? $orden->id }}</p>
                        <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($orden->created_at)->format('d/m/Y') }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $orden->estado === 'ENTREGADO' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700' }}">
                        {{ str_replace('_', ' ', $orden->estado ?? '') }}
                    </span>
                </div>
                @if($orden->diagnostico)
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($orden->diagnostico, 100) }}</p>
                @endif
            </div>
            @empty
            <div class="px-5 py-10 text-center text-gray-400 text-sm">Este vehículo no tiene órdenes de trabajo.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
