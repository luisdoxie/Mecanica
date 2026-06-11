@extends('layouts.gerente')
@section('title', 'Orden #' . $orden->id)
@section('header', 'Orden de Trabajo #' . $orden->id)

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
$estados = \App\Models\OrdenTrabajo::ESTADOS;
$idxActual = array_search($orden->estado, $estados);
@endphp

{{-- Header con estado y acciones --}}
<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('gerente.ordenes.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>

    {{-- Botón generar recibo --}}
    @if(in_array($orden->estado, ['LISTO','ENTREGADO']))
    @php $reciboExistente = \App\Models\Recibo::where('orden_trabajo_id', $orden->id)->first(); @endphp
    @if($reciboExistente)
    <a href="{{ route('gerente.recibos.descargar', $reciboExistente) }}"
        class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
        📄 Descargar Recibo
    </a>
    @else
    <form method="POST" action="{{ route('gerente.recibos.generar', $orden) }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
            📄 Generar Recibo PDF
        </button>
    </form>
    @endif
    @endif

    {{-- Botón registrar cobro --}}
    @if(in_array($orden->estado, ['LISTO','ENTREGADO']))
    <a href="{{ route('gerente.cobros.create', ['orden_id' => $orden->id]) }}"
        class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-lg text-sm transition">
        💰 Registrar Cobro
    </a>
    @endif
    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $badgeEstado[$orden->estado] ?? '' }}">
        {{ $orden->estado }}
    </span>
    @if($orden->siguienteEstado())
    <form method="POST" action="{{ route('gerente.ordenes.cambiarEstado', $orden) }}" class="flex items-center gap-2 ml-auto" x-data="{ open: false }">
        @csrf
        <button type="button" @click="open = !open"
            class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
            → Avanzar a {{ $orden->siguienteEstado() }}
        </button>
        <div x-show="open" x-transition class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
        <div x-show="open" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-xl shadow-xl border border-gray-200 p-5 w-80">
            <p class="font-semibold text-gray-800 mb-3">Nota de cambio de estado</p>
            <textarea name="nota" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"
                placeholder="Describe el trabajo (opcional)..."></textarea>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="flex-1 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Confirmar
                </button>
                <button type="button" @click="open=false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancelar
                </button>
            </div>
        </div>
    </form>
    @endif
</div>

{{-- Progreso de estados --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex items-center justify-between">
        @foreach($estados as $i => $e)
        <div class="flex-1 flex flex-col items-center">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2
                {{ $i <= $idxActual ? 'bg-teal-600 border-teal-600 text-white' : 'bg-white border-gray-300 text-gray-400' }}">
                {{ $i < $idxActual ? '✓' : ($i + 1) }}
            </div>
            <p class="text-xs mt-1 font-medium {{ $i <= $idxActual ? 'text-teal-700' : 'text-gray-400' }}">{{ $e }}</p>
        </div>
        @if(!$loop->last)
        <div class="flex-1 h-0.5 {{ $i < $idxActual ? 'bg-teal-600' : 'bg-gray-200' }} mx-1 mb-4"></div>
        @endif
        @endforeach
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Columna izquierda: Info general --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Vehículo</h3>
            <p class="font-bold text-lg text-gray-800">{{ $orden->vehiculo->placa }}</p>
            <p class="text-gray-600">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} {{ $orden->vehiculo->anio }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ $orden->vehiculo->color }}</p>
            <div class="border-t mt-3 pt-3">
                <p class="text-xs text-gray-400">Cliente</p>
                <p class="font-semibold text-gray-700">{{ $orden->vehiculo->cliente->persona->nombre }} {{ $orden->vehiculo->cliente->persona->apellido }}</p>
                <p class="text-sm text-gray-400">{{ $orden->vehiculo->cliente->persona->telefono }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wide">Mecánico Asignado</h3>
            @if($orden->empleado)
            <p class="font-semibold text-gray-700">{{ $orden->empleado->persona->nombre }} {{ $orden->empleado->persona->apellido }}</p>
            <p class="text-sm text-gray-400">{{ $orden->empleado->cargo }}</p>
            @else
            <p class="text-gray-400 text-sm">Sin asignar</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wide">Fechas</h3>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-gray-400">Ingreso</dt><dd class="font-medium">{{ $orden->fecha_ingreso?->format('d/m/Y H:i') }}</dd></div>
                <div><dt class="text-gray-400">Entrega</dt><dd class="font-medium">{{ $orden->fecha_entrega?->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="border-t pt-2"><dt class="text-gray-400">Costo total</dt>
                    <dd class="font-bold text-lg text-gray-800">Bs. {{ number_format($orden->costo_total ?? 0, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Columna central y derecha --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Descripción y Diagnóstico --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-2 text-sm uppercase tracking-wide">Descripción del Problema</h3>
            <p class="text-gray-600 text-sm">{{ $orden->descripcion_problema }}</p>
            @if($orden->diagnostico)
            <div class="mt-4 border-t pt-3">
                <h4 class="font-semibold text-gray-700 mb-1 text-sm">Diagnóstico</h4>
                <p class="text-gray-600 text-sm">{{ $orden->diagnostico }}</p>
            </div>
            @endif
        </div>

        {{-- Servicios --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Servicios</h3>
            </div>
            @forelse($orden->servicios as $svc)
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $svc->nombre }}</p>
                    @if($svc->pivot->observaciones)
                    <p class="text-gray-400 text-xs">{{ $svc->pivot->observaciones }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-semibold text-gray-700 text-sm">Bs. {{ number_format($svc->pivot->precio_aplicado, 2) }}</span>
                    <form method="POST" action="{{ route('gerente.ordenes.quitarServicio', $orden) }}">
                        @csrf @method('DELETE')
                        <input type="hidden" name="servicio_id" value="{{ $svc->id }}">
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="px-5 py-4 text-gray-400 text-sm">Sin servicios agregados.</p>
            @endforelse

            {{-- Agregar servicio --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100" x-data="{ open: false }">
                <button @click="open = !open" class="text-teal-600 hover:text-teal-800 text-sm font-semibold">+ Agregar servicio</button>
                <form x-show="open" method="POST" action="{{ route('gerente.ordenes.agregarServicio', $orden) }}" class="mt-3 space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <select name="servicio_id" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">Seleccionar servicio...</option>
                            @foreach($serviciosDisponibles as $s)
                                <option value="{{ $s->id }}" data-precio="{{ $s->precio_base }}">
                                    {{ $s->nombre }} (Bs. {{ number_format($s->precio_base, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="precio_aplicado" step="0.01" min="0" required placeholder="Precio aplicado"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <input type="text" name="observaciones" placeholder="Observaciones (opcional)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold rounded-lg transition">
                        Agregar
                    </button>
                </form>
            </div>
        </div>

        {{-- Repuestos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Repuestos Utilizados</h3>
            </div>
            @forelse($orden->repuestos as $rep)
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $rep->nombre }}</p>
                    <p class="text-gray-400 text-xs">Origen: {{ $rep->origen }} · Cant: {{ $rep->cantidad }} · {{ $rep->calidad_observada }}</p>
                </div>
                <span class="font-semibold text-gray-700 text-sm">Bs. {{ number_format($rep->costo * $rep->cantidad, 2) }}</span>
            </div>
            @empty
            <p class="px-5 py-4 text-gray-400 text-sm">Sin repuestos registrados.</p>
            @endforelse
        </div>

        {{-- Fotos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Fotos</h3>
            </div>
            <div class="p-5 space-y-5">
                @foreach($tiposImagen as $tipo)
                <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">{{ $tipo }}</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($imagenesAgrupadas->get($tipo, collect()) as $img)
                        <div class="relative group">
                            <img src="{{ $img->cloudinary_url }}" alt="{{ $img->descripcion }}"
                                class="w-24 h-24 object-cover rounded-lg border border-gray-200 cursor-pointer"
                                data-url="{{ $img->cloudinary_url }}" onclick="window.open(this.dataset.url, '_blank', 'noopener,noreferrer')">
                            <form method="POST" action="{{ route('gerente.ordenes.fotos.destroy', [$orden, $img]) }}"
                                  onsubmit="return confirm('¿Eliminar foto?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition">✕</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                {{-- Upload form --}}
                <form method="POST" action="{{ route('gerente.ordenes.fotos.store', $orden) }}" enctype="multipart/form-data"
                      class="border-t border-gray-100 pt-4 flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tipo</label>
                        <select name="tipo" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            @foreach($tiposImagen as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Foto</label>
                        <input type="file" name="foto" accept="image/*" required
                            class="text-sm text-gray-600 file:mr-3 file:px-3 file:py-1.5 file:bg-teal-600 file:text-white file:rounded-lg file:border-0 file:text-sm file:cursor-pointer">
                    </div>
                    <input type="text" name="descripcion" placeholder="Descripción (opcional)"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold rounded-lg transition">
                        Subir foto
                    </button>
                </form>
            </div>
        </div>

        {{-- Historial de estados --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Historial de Estados</h3>
            </div>
            @forelse($orden->historialEstados as $h)
            <div class="px-5 py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}</span>
                    <span class="text-gray-400">·</span>
                    <span class="font-medium text-gray-600">{{ $h->estado_anterior }}</span>
                    <span class="text-gray-400">→</span>
                    <span class="font-bold text-gray-800">{{ $h->estado_nuevo }}</span>
                    @if($h->empleado)
                    <span class="text-gray-400">·</span>
                    <span class="text-gray-500 text-xs">{{ $h->empleado->persona->nombre }} {{ $h->empleado->persona->apellido }}</span>
                    @endif
                </div>
                @if($h->nota)
                <p class="text-gray-500 text-xs mt-1 ml-0">{{ $h->nota }}</p>
                @endif
            </div>
            @empty
            <p class="px-5 py-4 text-gray-400 text-sm">Sin cambios de estado registrados.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

