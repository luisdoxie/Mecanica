@extends('layouts.mecanico')
@section('title', 'Nuevo Cliente')
@section('header', 'Registrar Nuevo Cliente')

@section('content')
<div class="max-w-xl">
<x-alert />

<a href="{{ route('mecanico.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← Volver al panel</a>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <p class="text-sm text-gray-500 mb-5">
        Después de registrar al cliente serás redirigido para crear la orden de trabajo.
    </p>

    <form method="POST" action="{{ route('mecanico.clientes.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('nombre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido" value="{{ old('apellido') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('apellido')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cédula de Identidad <span class="text-red-500">*</span></label>
            <input type="text" name="ci" value="{{ old('ci') }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            @error('ci')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
                Registrar Cliente →
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
