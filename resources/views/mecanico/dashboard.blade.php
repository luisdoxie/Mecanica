@extends('layouts.mecanico')

@section('title', 'Mi Panel')
@section('header', 'Mi Panel de Trabajo')

@section('content')
<x-alert />

{{-- KPIs --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Mis órdenes activas</p>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-xl">🔧</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ $ordenesActivas }}</p>
        <p class="text-xs text-gray-400 mt-1">en proceso</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Completadas hoy</p>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-xl">✅</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ $completadasHoy }}</p>
        <p class="text-xs text-gray-400 mt-1">marcadas como LISTO</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Vehículos esta semana</p>
            <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-xl">🚗</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ $vehiculosSemana }}</p>
        <p class="text-xs text-gray-400 mt-1">atendidos esta semana</p>
    </div>
</div>

{{-- Acciones rápidas --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
    <a href="{{ route('mecanico.clientes.create') }}"
        class="flex flex-col items-center gap-2 p-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl transition text-center">
        <span class="text-2xl">👤</span>
        <span class="text-xs font-semibold">Nuevo Cliente</span>
    </a>
    <a href="{{ route('mecanico.ordenes.create') }}"
        class="flex flex-col items-center gap-2 p-4 bg-amber-500 hover:bg-amber-400 text-white rounded-2xl transition text-center">
        <span class="text-2xl">➕</span>
        <span class="text-xs font-semibold">Nueva Orden</span>
    </a>
    <a href="{{ route('mecanico.cobros.create') }}"
        class="flex flex-col items-center gap-2 p-4 bg-green-600 hover:bg-green-500 text-white rounded-2xl transition text-center">
        <span class="text-2xl">💰</span>
        <span class="text-xs font-semibold">Registrar Cobro</span>
    </a>
    <a href="{{ route('mecanico.ordenes.index') }}"
        class="flex flex-col items-center gap-2 p-4 bg-gray-700 hover:bg-gray-600 text-white rounded-2xl transition text-center">
        <span class="text-2xl">📋</span>
        <span class="text-xs font-semibold">Mis Órdenes</span>
    </a>
</div>

<div class="grid lg:grid-cols-2 gap-6">

    {{-- Mis órdenes activas --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Mis Órdenes Activas</h3>
            <a href="{{ route('mecanico.ordenes.index') }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Ver todas →</a>
        </div>
        @php
        $badge = [
            'RECIBIDO'   => 'bg-gray-100 text-gray-600',
            'DIAGNOSTICO'=> 'bg-blue-100 text-blue-700',
            'REPARACION' => 'bg-amber-100 text-amber-700',
            'LISTO'      => 'bg-green-100 text-green-700',
        ];
        @endphp
        @if(!$empleado)
        <div class="px-5 py-6 text-center">
            <p class="text-amber-600 font-medium text-sm mb-1">⚠️ Sin perfil de empleado vinculado</p>
            <p class="text-gray-400 text-xs">Tu cuenta no está vinculada a un empleado del sistema. Pídele al gerente que te registre como empleado.</p>
        </div>
        @else
        @forelse($ordenesRecientes as $orden)
        <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex items-center justify-between">
            <div>
                <p class="font-semibold text-gray-800 text-sm">
                    #{{ $orden->id }} — {{ $orden->vehiculo->placa }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ·
                    {{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge[$orden->estado] ?? '' }}">
                    {{ $orden->estado }}
                </span>
                <a href="{{ route('mecanico.ordenes.show', $orden) }}"
                   class="text-amber-600 hover:text-amber-800 text-xs font-medium">Ver →</a>
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-gray-400 text-sm">
            No tienes órdenes activas asignadas.<br>
            <span class="text-xs">Toma una orden disponible o crea una nueva.</span>
        </div>
        @endforelse
        @endif
    </div>

    {{-- Órdenes sin asignar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Órdenes Disponibles</h3>
            <p class="text-xs text-gray-400 mt-0.5">Puedes asignarte a cualquiera de estas</p>
        </div>
        @forelse($ordenesSinAsignar as $orden)
        <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex items-center justify-between">
            <div>
                <p class="font-semibold text-gray-800 text-sm">
                    #{{ $orden->id }} — {{ $orden->vehiculo->placa }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ·
                    {{ $orden->fecha_ingreso?->format('d/m/Y H:i') }}
                </p>
            </div>
            <form method="POST" action="{{ route('mecanico.ordenes.asignar', $orden) }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white text-xs font-semibold rounded-lg transition">
                    Asignarme
                </button>
            </form>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-gray-400 text-sm">
            No hay órdenes sin asignar en este momento.
        </div>
        @endforelse
    </div>

</div>

{{-- Alerta: órdenes sin foto de recepción --}}
@if($ordenesSinFotoRecepcion->isNotEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mt-6">
    <div class="flex items-start gap-3">
        <span class="text-xl">⚠️</span>
        <div class="flex-1">
            <p class="font-bold text-amber-800 text-sm mb-1">Órdenes sin foto de recepción</p>
            <p class="text-amber-700 text-xs mb-3">Estas órdenes no tienen foto de RECEPCIÓN registrada:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($ordenesSinFotoRecepcion as $o)
                <a href="{{ route('mecanico.ordenes.show', $o) }}"
                    class="px-3 py-1 bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-lg text-xs font-semibold transition">
                    #{{ $o->id }} — {{ $o->vehiculo->placa ?? '?' }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Últimas fotos subidas --}}
@if($ultimasFotos->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Últimas Fotos Subidas</h3>
    </div>
    <div class="p-4 grid grid-cols-3 sm:grid-cols-6 gap-3">
        @foreach($ultimasFotos as $foto)
        <a href="{{ $foto->cloudinary_url }}" target="_blank"
            class="group relative aspect-square rounded-xl overflow-hidden border border-gray-100">
            <img src="{{ $foto->cloudinary_url }}" alt="{{ $foto->tipo }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            <div class="absolute bottom-0 inset-x-0 bg-black/50 text-white text-xs text-center py-0.5 opacity-0 group-hover:opacity-100 transition">
                {{ $foto->tipo }}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Cambio rápido de estado desde dashboard --}}
@if($ordenesRecientes->isNotEmpty() && $empleado)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Avance Rápido de Estado</h3>
        <p class="text-xs text-gray-400 mt-0.5">Cambia el estado sin entrar al detalle</p>
    </div>
    @php
    $progBadge = ['RECIBIDO'=>'bg-gray-100 text-gray-600','DIAGNOSTICO'=>'bg-blue-100 text-blue-700','REPARACION'=>'bg-amber-100 text-amber-700','LISTO'=>'bg-green-100 text-green-700'];
    @endphp
    @foreach($ordenesRecientes as $orden)
    @php $sig = $orden->siguienteEstado(); @endphp
    @if($sig)
    <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex items-center justify-between gap-3"
         x-data="{ open: false }">
        <div>
            <p class="font-semibold text-gray-800 text-sm">#{{ $orden->id }} — {{ $orden->vehiculo->placa }}</p>
            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $progBadge[$orden->estado] ?? '' }}">{{ $orden->estado }}</span>
        </div>
        <div class="relative">
            <button @click="open = true" type="button"
                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white text-xs font-semibold rounded-lg transition whitespace-nowrap">
                → {{ $sig }}
            </button>
            <div x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
            <div x-show="open" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-xl shadow-xl border border-gray-200 p-5 w-80">
                <p class="font-semibold text-gray-800 mb-3 text-sm">Avanzar #{{ $orden->id }} a <span class="text-amber-600">{{ $sig }}</span></p>
                <form method="POST" action="{{ route('mecanico.ordenes.cambiarEstado', $orden) }}">
                    @csrf
                    <textarea name="nota" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none mb-3"
                        placeholder="Nota opcional..."></textarea>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm">Confirmar</button>
                        <button type="button" @click="open=false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endif

@endsection
