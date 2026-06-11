@extends('layouts.gerente')
@section('title', 'Perfil de Cliente')
@section('header', 'Perfil de Cliente')

@section('content')
<x-alert />

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gerente.clientes.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
    <a href="{{ route('gerente.clientes.edit', $cliente) }}"
        class="ml-auto px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
        Editar
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Datos personales -->
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-teal-700">{{ strtoupper(substr($cliente->persona->nombre, 0, 1)) }}</span>
                </div>
                <h2 class="font-bold text-gray-800 text-lg">{{ $cliente->persona->nombre }} {{ $cliente->persona->apellido }}</h2>
                <p class="text-gray-400 text-sm">CI: {{ $cliente->persona->ci }}</p>
            </div>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-400">Teléfono</dt>
                    <dd class="font-medium text-gray-700">{{ $cliente->persona->telefono ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Correo</dt>
                    <dd class="font-medium text-gray-700">{{ $cliente->persona->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Dirección</dt>
                    <dd class="font-medium text-gray-700">{{ $cliente->persona->direccion ?? '—' }}</dd>
                </div>
                <div class="border-t pt-3">
                    <dt class="text-gray-400">PIN de acceso</dt>
                    <dd class="text-xs text-gray-400 italic">Se entrega al cliente al momento de activar el acceso web.</dd>
                </div>
                <div>
                    <dt class="text-gray-400 mb-1">Acceso web</dt>
                    <dd>
                        <form method="POST" action="{{ route('gerente.clientes.toggle-login', $cliente) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-3 py-1 rounded-full text-xs font-semibold border transition
                                    {{ $cliente->puede_login ? 'bg-green-100 text-green-700 border-green-300' : 'bg-gray-100 text-gray-500 border-gray-300' }}">
                                {{ $cliente->puede_login ? '✓ Activo — Click para desactivar' : '✗ Inactivo — Click para activar' }}
                            </button>
                        </form>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Vehículos -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Vehículos ({{ $cliente->vehiculos->count() }})</h3>
                <a href="{{ route('gerente.vehiculos.create', ['cliente_id' => $cliente->id]) }}"
                    class="px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold rounded-lg transition">
                    + Agregar vehículo
                </a>
            </div>
            @forelse($cliente->vehiculos as $vehiculo)
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
                <div>
                    <p class="font-semibold text-gray-800">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->anio }}</p>
                    <p class="text-sm text-gray-400">
                        Placa: <span class="font-mono font-bold text-gray-600">{{ $vehiculo->placa }}</span>
                        · Color: {{ $vehiculo->color ?? '—' }}
                        · {{ number_format($vehiculo->km_actual ?? 0) }} km
                    </p>
                </div>
                <a href="{{ route('gerente.vehiculos.show', $vehiculo) }}" class="text-teal-600 hover:text-teal-800 text-sm">Ver historial →</a>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">Este cliente no tiene vehículos registrados.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
