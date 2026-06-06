@extends('layouts.mecanico')
@section('title', 'Registrar Vehículo')
@section('header', 'Registrar Vehículo')

@section('content')
<div class="max-w-xl">
<x-alert />

{{-- Indicador de pasos --}}
<div class="flex items-center gap-2 mb-6 text-sm">
    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">✓ 1. Cliente</span>
    <span class="text-gray-300">──</span>
    <span class="px-3 py-1 bg-amber-500 text-white rounded-full font-semibold">2. Vehículo</span>
    <span class="text-gray-300">──</span>
    <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full">3. Orden</span>
</div>

{{-- Banner del cliente recién creado --}}
@if($cliente)
<div class="mb-4 p-4 bg-green-50 border-2 border-green-400 rounded-xl flex items-center gap-3">
    <span class="text-2xl">✅</span>
    <div>
        <p class="font-bold text-green-800 text-sm">Cliente registrado exitosamente</p>
        <p class="text-green-700 text-sm">
            <strong>{{ $cliente->persona->nombre }} {{ $cliente->persona->apellido }}</strong>
            · CI: {{ $cliente->persona->ci }}
        </p>
        <p class="text-green-600 text-xs mt-0.5">Ya está seleccionado abajo ↓</p>
    </div>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('mecanico.vehiculos.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Cliente <span class="text-red-500">*</span>
                <span class="text-gray-400 font-normal text-xs">({{ $todos->count() }} registrados)</span>
            </label>
            <select name="cliente_id" required
                class="w-full px-3 py-2 border {{ $cliente ? 'border-green-400 bg-green-50' : 'border-gray-300' }} rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">-- Seleccione el cliente --</option>
                @foreach($todos as $c)
                <option value="{{ $c->id }}"
                    {{ (old('cliente_id', $clienteId) == $c->id) ? 'selected' : '' }}>
                    @if($clienteId && $c->id == $clienteId)[NUEVO] @endif{{ $c->persona?->apellido ?? '' }}, {{ $c->persona?->nombre ?? '' }}{{ $c->persona?->ci ? ' — CI: '.$c->persona->ci : '' }}
                </option>
                @endforeach
            </select>
            @error('cliente_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Placa <span class="text-red-500">*</span></label>
            <input type="text" name="placa" value="{{ old('placa') }}" required
                placeholder="ABC-1234"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-amber-500">
            @error('placa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                <input type="text" name="marca" value="{{ old('marca') }}" required
                    placeholder="Toyota, Honda..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('marca')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                <input type="text" name="modelo" value="{{ old('modelo') }}" required
                    placeholder="Corolla, Civic..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('modelo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año <span class="text-red-500">*</span></label>
                <input type="number" name="anio" value="{{ old('anio', date('Y')) }}" required
                    min="1900" max="{{ date('Y') + 1 }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('anio')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <input type="text" name="color" value="{{ old('color') }}"
                    placeholder="Rojo, Negro..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Km actuales</label>
                <input type="number" name="km_actual" value="{{ old('km_actual', 0) }}" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
                Registrar Vehículo →
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
