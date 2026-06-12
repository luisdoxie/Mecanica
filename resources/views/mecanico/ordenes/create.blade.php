@extends('layouts.mecanico')
@section('title', 'Nueva Orden de Trabajo')
@section('header', 'Nueva Orden de Trabajo')

@section('content')
@php
    $oldVId    = old('vehiculo_id', $vehiculoId);
    $oldV      = $oldVId ? $vehiculos->firstWhere('id', $oldVId) : null;
    $oldVLabel = $oldV ? ($oldV->placa . ' — ' . $oldV->marca . ' ' . $oldV->modelo . ' (' . $oldV->cliente->persona->nombre . ' ' . $oldV->cliente->persona->apellido . ')') : '';
@endphp

<script>
    window._vehiculosMecanico = @json($vehiculos->map(fn($v) => ['id' => $v->id, 'label' => $v->placa . ' — ' . $v->marca . ' ' . $v->modelo . ' (' . $v->cliente->persona->nombre . ' ' . $v->cliente->persona->apellido . ')']));

    function buscadorVehiculoMec() {
        return {
            buscar: '', abierto: false,
            selId: '{{ $oldVId }}',
            selNombre: {{ json_encode($oldVLabel) }},
            lista: window._vehiculosMecanico || [],
            get filtrados() {
                if (!this.buscar) return this.lista;
                const b = this.buscar.toLowerCase();
                return this.lista.filter(v => v.label.toLowerCase().includes(b));
            },
            seleccionar(id, nombre) { this.selId = id; this.selNombre = nombre; this.buscar = ''; this.abierto = false; }
        };
    }
</script>

<div class="max-w-xl">
<x-alert />

{{-- Indicador de pasos (solo cuando viene del flujo de registro) --}}
@if($vehiculoId)
<div class="flex items-center gap-2 mb-6 text-sm">
    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">✓ 1. Cliente</span>
    <span class="text-gray-300">──</span>
    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">✓ 2. Vehículo</span>
    <span class="text-gray-300">──</span>
    <span class="px-3 py-1 bg-amber-500 text-white rounded-full font-semibold">3. Orden</span>
</div>
@else
<a href="{{ route('mecanico.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← Volver al panel</a>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('mecanico.ordenes.store') }}" class="space-y-5">
        @csrf

        {{-- Vehículo con buscador --}}
        @if($vehiculos->isEmpty())
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
            No hay vehículos registrados. Primero registra un <a href="{{ route('mecanico.clientes.create') }}" class="underline font-semibold">cliente y su vehículo</a>.
        </div>
        @else
        <div x-data="buscadorVehiculoMec()">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Vehículo del cliente <span class="text-red-500">*</span>
            </label>
            <input type="hidden" name="vehiculo_id" :value="selId">
            <div class="relative">
                <input type="text" x-model="buscar"
                    @focus="abierto = true" @click.outside="abierto = false"
                    :placeholder="selNombre || 'Buscar por placa, marca o cliente...'"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 pr-8">
                <button type="button" x-show="selId" @click="selId=''; selNombre=''"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">✕</button>
                <div x-show="abierto" x-transition
                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                    <template x-for="v in filtrados" :key="v.id">
                        <div @click="seleccionar(v.id, v.label)" @mousedown.prevent
                            :class="selId == v.id ? 'bg-amber-50 text-amber-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                            class="px-3 py-2 text-sm cursor-pointer">
                            <span x-text="v.label"></span>
                        </div>
                    </template>
                    <div x-show="filtrados.length === 0"
                        class="px-3 py-3 text-sm text-gray-400 text-center">Sin resultados</div>
                </div>
            </div>
            <p x-show="selId" class="mt-1 text-xs text-green-600 font-medium">✓ <span x-text="selNombre"></span></p>
            @error('vehiculo_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            @if($vehiculoId)
            <p class="mt-1 text-xs text-green-600 font-medium">✓ Vehículo recién registrado pre-seleccionado</p>
            @endif
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción del problema <span class="text-red-500">*</span>
            </label>
            <textarea name="descripcion_problema" rows="4" required
                placeholder="Describe el problema que reporta el cliente..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('descripcion_problema') }}</textarea>
            @error('descripcion_problema')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
                Registrar Orden
            </button>
            <a href="{{ route('mecanico.dashboard') }}"
                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
</div>
@endsection
