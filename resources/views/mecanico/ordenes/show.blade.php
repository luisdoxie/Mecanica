@extends('layouts.mecanico')
@section('title', 'Orden #' . $orden->id)
@section('header', 'Orden #' . $orden->id)

@section('content')
<x-alert />

@php
$badgeEstado = [
    'RECIBIDO'   => 'bg-gray-100 text-gray-700',
    'DIAGNOSTICO'=> 'bg-blue-100 text-blue-700',
    'REPARACION' => 'bg-amber-100 text-amber-700',
    'LISTO'      => 'bg-green-100 text-green-700',
    'ENTREGADO'  => 'bg-purple-100 text-purple-700',
];
$estados   = \App\Models\OrdenTrabajo::ESTADOS;
$idxActual = array_search($orden->estado, $estados);
@endphp

<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('mecanico.ordenes.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $badgeEstado[$orden->estado] ?? '' }}">{{ $orden->estado }}</span>

    @php $siguiente = $orden->siguienteEstado(); @endphp
    @if($siguiente)
    <form method="POST" action="{{ route('mecanico.ordenes.cambiarEstado', $orden) }}" class="flex items-center gap-2 ml-auto" x-data="{ open: false }">
        @csrf
        <button type="button" @click="open = !open"
            class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
            → Avanzar a {{ $siguiente }}
        </button>
        <div x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
        <div x-show="open" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-xl shadow-xl border border-gray-200 p-6 w-96">
            <p class="font-semibold text-gray-800 mb-3">Nota del cambio a <span class="text-amber-600">{{ $siguiente }}</span></p>
            <textarea name="nota" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"
                placeholder="Describe el trabajo (opcional)..."></textarea>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="flex-1 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm">Confirmar</button>
                <button type="button" @click="open=false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Cancelar</button>
            </div>
        </div>
    </form>
    @endif
</div>

{{-- Progreso --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex items-center justify-between">
        @foreach($estados as $i => $e)
        <div class="flex-1 flex flex-col items-center">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2
                {{ $i <= $idxActual ? 'bg-amber-500 border-amber-500 text-white' : 'bg-white border-gray-300 text-gray-400' }}">
                {{ $i < $idxActual ? '✓' : ($i + 1) }}
            </div>
            <p class="text-xs mt-1 font-medium {{ $i <= $idxActual ? 'text-amber-700' : 'text-gray-400' }}">{{ $e }}</p>
        </div>
        @if(!$loop->last)
        <div class="flex-1 h-0.5 {{ $i < $idxActual ? 'bg-amber-500' : 'bg-gray-200' }} mx-1 mb-4"></div>
        @endif
        @endforeach
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm">
            <h3 class="font-bold text-gray-700 mb-3 text-xs uppercase tracking-wide">Vehículo</h3>
            <p class="font-bold text-gray-800">{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</p>
            <p class="text-gray-500">{{ $orden->vehiculo->anio }} · {{ $orden->vehiculo->color }}</p>
            <div class="mt-3 border-t pt-3">
                <p class="text-gray-400 text-xs">Cliente</p>
                <p class="font-medium">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</p>
                <p class="text-gray-400">{{ $orden->vehiculo->cliente->persona->telefono }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm">
            <h3 class="font-bold text-gray-700 mb-2 text-xs uppercase tracking-wide">Problema</h3>
            <p class="text-gray-600">{{ $orden->descripcion_problema }}</p>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-5">

        {{-- Repuestos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Repuestos Utilizados</h3>
            </div>
            @forelse($orden->repuestos as $rep)
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $rep->nombre }}</p>
                    <p class="text-gray-400 text-xs">{{ $rep->origen }} · x{{ $rep->cantidad }} · {{ $rep->calidad_observada }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-700">Bs. {{ number_format($rep->costo * $rep->cantidad, 2) }}</span>
                    @if($orden->estado !== 'ENTREGADO')
                    <form method="POST" action="{{ route('mecanico.ordenes.eliminarRepuesto', [$orden, $rep]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p class="px-5 py-4 text-gray-400 text-sm">Sin repuestos registrados.</p>
            @endforelse

            @if($orden->estado !== 'ENTREGADO')
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100" x-data="{ open: false }">
                <button @click="open = !open" class="text-amber-600 hover:text-amber-800 text-sm font-semibold">+ Agregar repuesto</button>
                <form x-show="open" method="POST" action="{{ route('mecanico.ordenes.agregarRepuesto', $orden) }}" class="mt-3 grid grid-cols-2 gap-3">
                    @csrf
                    <input type="text" name="nombre" required placeholder="Nombre del repuesto *"
                        class="col-span-2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <select name="origen" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Origen *</option>
                        <option value="TALLER">TALLER</option>
                        <option value="CLIENTE">CLIENTE</option>
                    </select>
                    <input type="text" name="calidad_observada" placeholder="Calidad observada"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <input type="number" name="costo" step="0.01" min="0" required placeholder="Costo unitario *"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <input type="number" name="cantidad" min="1" required placeholder="Cantidad *"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="col-span-2 py-2 bg-amber-500 hover:bg-amber-400 text-white text-sm font-semibold rounded-lg transition">
                        Agregar
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Fotos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Fotos</h3>
            </div>
            <div class="p-5 space-y-4">
                @foreach($tiposImagen as $tipo)
                <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">{{ $tipo }}</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($imagenesAgrupadas->get($tipo, collect()) as $img)
                        <div class="relative group">
                            <img src="{{ $img->cloudinary_url }}" alt="{{ $img->descripcion }}"
                                class="w-24 h-24 object-cover rounded-lg border border-gray-200 cursor-pointer"
                                onclick="window.open('{{ $img->cloudinary_url }}', '_blank')">
                            <form method="POST" action="{{ route('mecanico.ordenes.fotos.destroy', [$orden, $img]) }}"
                                  onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition">✕</button>
                            </form>
                        </div>
                        @endforeach
                        @if($imagenesAgrupadas->get($tipo, collect())->isEmpty())
                        <p class="text-gray-400 text-xs italic">Sin fotos</p>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($orden->estado !== 'ENTREGADO')
                <form method="POST" action="{{ route('mecanico.ordenes.fotos.store', $orden) }}" enctype="multipart/form-data"
                      class="border-t border-gray-100 pt-4 flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tipo</label>
                        <select name="tipo" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            @foreach($tiposImagen as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Foto</label>
                        <input type="file" name="foto" accept="image/*" required
                            class="text-sm text-gray-600 file:mr-3 file:px-3 file:py-1.5 file:bg-amber-500 file:text-white file:rounded-lg file:border-0 file:text-sm">
                    </div>
                    <input type="text" name="descripcion" placeholder="Descripción"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white text-sm font-semibold rounded-lg transition">
                        Subir foto
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Historial --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Historial de Estados</h3>
            </div>
            @forelse($orden->historialEstados as $h)
            <div class="px-5 py-3 border-b border-gray-50 last:border-0 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}</span>
                    <span class="text-gray-500">{{ $h->estado_anterior }} → <strong>{{ $h->estado_nuevo }}</strong></span>
                </div>
                @if($h->nota) <p class="text-gray-400 text-xs mt-0.5">{{ $h->nota }}</p> @endif
            </div>
            @empty
            <p class="px-5 py-4 text-gray-400 text-sm">Sin historial.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
