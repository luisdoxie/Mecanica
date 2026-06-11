@extends('layouts.cliente')
@section('title', 'Mi Vehículo')

@section('content')

@php
$estados = \App\Models\OrdenTrabajo::ESTADOS;

$estadoConfig = [
    'RECIBIDO'    => ['color' => 'from-slate-600 to-slate-700',  'badge' => 'bg-slate-500/20 text-slate-300 border-slate-500/30',  'dot' => 'bg-slate-400',  'icon' => '📥', 'label' => 'Vehículo recibido',        'msg' => 'Hemos recibido tu vehículo. Pronto iniciamos el diagnóstico.'],
    'DIAGNOSTICO' => ['color' => 'from-blue-600 to-blue-800',    'badge' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',      'dot' => 'bg-blue-400',   'icon' => '🔍', 'label' => 'En diagnóstico',          'msg' => 'Nuestro mecánico está evaluando tu vehículo detalladamente.'],
    'REPARACION'  => ['color' => 'from-amber-500 to-orange-600', 'badge' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',   'dot' => 'bg-amber-400',  'icon' => '🔧', 'label' => 'En reparación',           'msg' => 'Tu vehículo está siendo reparado. ¡Ya casi está!'],
    'LISTO'       => ['color' => 'from-green-500 to-emerald-600','badge' => 'bg-green-500/20 text-green-300 border-green-500/30',   'dot' => 'bg-green-400',  'icon' => '✅', 'label' => '¡Listo para retirar!',   'msg' => '¡Tu vehículo está listo! Puedes pasar a retirarlo cuando quieras.'],
    'ENTREGADO'   => ['color' => 'from-purple-600 to-violet-700','badge' => 'bg-purple-500/20 text-purple-300 border-purple-500/30','dot' => 'bg-purple-400', 'icon' => '🎉', 'label' => 'Vehículo entregado',      'msg' => '¡Gracias por confiar en Taller Eusebio!'],
];

$nombreCliente = explode(' ', Auth::user()->name)[0];
@endphp

{{-- Hero bienvenida --}}
<div class="mb-8">
    <p class="text-slate-400 text-sm mb-1">Bienvenido de vuelta 👋</p>
    <h1 class="text-3xl sm:text-4xl font-black text-white">Hola, <span class="text-amber-400">{{ $nombreCliente }}</span></h1>
    <p class="text-slate-400 mt-1">Aquí puedes seguir el estado de tu vehículo en tiempo real.</p>
</div>

@if($ordenActiva)
@php
    $cfg      = $estadoConfig[$ordenActiva->estado] ?? $estadoConfig['RECIBIDO'];
    $idxActual = array_search($ordenActiva->estado, $estados);
@endphp

{{-- Tarjeta estado principal --}}
<div class="relative rounded-3xl overflow-hidden mb-6 amber-glow">
    <div class="absolute inset-0 bg-gradient-to-br {{ $cfg['color'] }} opacity-90"></div>
    <div class="absolute inset-0" style="background:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2280%22 cy=%2220%22 r=%2240%22 fill=%22white%22 fill-opacity=%220.04%22/><circle cx=%2210%22 cy=%2280%22 r=%2230%22 fill=%22white%22 fill-opacity=%220.03%22/></svg>')"></div>
    <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6">
        <div class="pulse-ring w-20 h-20 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
            <span class="text-5xl">{{ $cfg['icon'] }}</span>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $cfg['badge'] }}">
                    Orden #{{ $ordenActiva->id }}
                </span>
                <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                <span class="text-white/60 text-xs">{{ $ordenActiva->fecha_ingreso?->format('d/m/Y') }}</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white mb-1">{{ $cfg['label'] }}</h2>
            <p class="text-white/70 text-sm">{{ $cfg['msg'] }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-white/50 text-xs mb-1">Costo total</p>
            <p class="text-3xl font-black text-white">Bs. {{ number_format($ordenActiva->costo_total ?? 0, 2) }}</p>
        </div>
    </div>
</div>

{{-- Progreso --}}
<div class="card-glass rounded-2xl p-6 mb-6">
    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-5">Progreso del servicio</p>
    <div class="flex items-center">
        @foreach($estados as $i => $e)
        @php $done = $i < $idxActual; $current = $i === $idxActual; @endphp
        <div class="flex-1 flex flex-col items-center gap-2">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all border-2
                @if($done) bg-amber-500 border-amber-500 text-slate-900
                @elseif($current) bg-amber-500/20 border-amber-500 text-amber-400 pulse-ring
                @else bg-white/5 border-white/10 text-slate-600 @endif">
                @if($done)
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @else
                    {{ $i + 1 }}
                @endif
            </div>
            <p class="text-xs text-center font-medium leading-tight
                {{ $done || $current ? 'text-amber-400' : 'text-slate-600' }}">
                {{ $e }}
            </p>
        </div>
        @if(!$loop->last)
        <div class="flex-1 h-0.5 mx-1 mb-5 rounded-full {{ $done ? 'bg-amber-500' : 'bg-white/10' }}"></div>
        @endif
        @endforeach
    </div>
</div>

{{-- Vehículo y Mecánico --}}
<div class="grid sm:grid-cols-2 gap-4 mb-6">

    {{-- Vehículo --}}
    <div class="card-glass rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                </svg>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Mi Vehículo</p>
        </div>
        <p class="text-3xl font-black text-white tracking-wider mb-1">{{ $ordenActiva->vehiculo->placa }}</p>
        <p class="text-amber-400 font-semibold">{{ $ordenActiva->vehiculo->marca }} {{ $ordenActiva->vehiculo->modelo }}</p>
        <div class="flex gap-3 mt-3">
            <span class="px-2.5 py-1 bg-white/5 rounded-lg text-slate-400 text-xs font-medium">{{ $ordenActiva->vehiculo->anio }}</span>
            @if($ordenActiva->vehiculo->color)
            <span class="px-2.5 py-1 bg-white/5 rounded-lg text-slate-400 text-xs font-medium">{{ $ordenActiva->vehiculo->color }}</span>
            @endif
            @if($ordenActiva->vehiculo->km_actual)
            <span class="px-2.5 py-1 bg-white/5 rounded-lg text-slate-400 text-xs font-medium">{{ number_format($ordenActiva->vehiculo->km_actual) }} km</span>
            @endif
        </div>
    </div>

    {{-- Mecánico --}}
    <div class="card-glass rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Mecánico Asignado</p>
        </div>
        @if($ordenActiva->empleado)
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-slate-900 font-black text-xl flex-shrink-0">
                {{ strtoupper(substr($ordenActiva->empleado->persona->nombre, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-white text-base">{{ $ordenActiva->empleado->persona->nombre }} {{ $ordenActiva->empleado->persona->apellido }}</p>
                <p class="text-slate-400 text-xs mt-0.5">{{ $ordenActiva->empleado->cargo }}</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    <span class="text-green-400 text-xs font-medium">Disponible</span>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-4 mt-2">
            <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center border-2 border-dashed border-white/10">
                <span class="text-2xl">⏳</span>
            </div>
            <div>
                <p class="text-white font-semibold">Pendiente</p>
                <p class="text-slate-500 text-xs mt-0.5">Se asignará pronto</p>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Detalle del servicio --}}
<div class="card-glass rounded-2xl p-6 mb-6">
    <div class="flex items-center gap-2 mb-5">
        <div class="w-8 h-8 bg-violet-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Detalle del Servicio</p>
    </div>
    <div class="grid sm:grid-cols-2 gap-5">
        <div class="bg-white/5 rounded-xl p-4">
            <p class="text-slate-500 text-xs mb-2 font-medium">Problema reportado</p>
            <p class="text-white text-sm leading-relaxed">{{ $ordenActiva->descripcion_problema }}</p>
        </div>
        @if($ordenActiva->diagnostico)
        <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
            <p class="text-blue-400 text-xs mb-2 font-medium">Diagnóstico del mecánico</p>
            <p class="text-white text-sm leading-relaxed">{{ $ordenActiva->diagnostico }}</p>
        </div>
        @endif
        <div class="bg-white/5 rounded-xl p-4">
            <p class="text-slate-500 text-xs mb-2 font-medium">Fecha de ingreso</p>
            <p class="text-white font-semibold">{{ $ordenActiva->fecha_ingreso?->format('d/m/Y') }}</p>
            <p class="text-slate-400 text-xs">{{ $ordenActiva->fecha_ingreso?->format('H:i') }} hrs</p>
        </div>
        @if($ordenActiva->fecha_entrega)
        <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
            <p class="text-green-400 text-xs mb-2 font-medium">Fecha de entrega</p>
            <p class="text-white font-semibold">{{ \Carbon\Carbon::parse($ordenActiva->fecha_entrega)->format('d/m/Y') }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Servicios realizados --}}
@if($ordenActiva->servicios->isNotEmpty())
<div class="card-glass rounded-2xl p-6 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Servicios Realizados</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach($ordenActiva->servicios as $svc)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-300 rounded-xl text-sm font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
            {{ $svc->nombre }}
        </span>
        @endforeach
    </div>
</div>
@endif

{{-- Línea de tiempo de estados --}}
@if($ordenActiva->historialEstados->isNotEmpty())
<div class="card-glass rounded-2xl p-6 mb-6">
    <div class="flex items-center gap-2 mb-5">
        <div class="w-8 h-8 bg-teal-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Historial de Estados</p>
    </div>
    <div class="relative pl-6">
        <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-white/10 rounded-full"></div>
        @foreach($ordenActiva->historialEstados as $h)
        <div class="relative mb-4 last:mb-0">
            <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-amber-500 border-2 border-slate-800 flex-shrink-0"></div>
            <div class="bg-white/5 rounded-xl px-4 py-3">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <p class="text-white font-semibold text-sm">
                        {{ $h->estado_anterior }} <span class="text-amber-400">→</span> {{ $h->estado_nuevo }}
                    </p>
                    <span class="text-slate-500 text-xs">{{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                @if($h->nota)
                <p class="text-slate-400 text-xs mt-1">{{ $h->nota }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Fotos --}}
@php $fotos = $ordenActiva->imagenes->whereIn('tipo', ['DAÑO', 'ENTREGA']); @endphp
@if($fotos->isNotEmpty())
<div class="card-glass rounded-2xl p-6 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 bg-pink-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Fotos de tu Vehículo</p>
    </div>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
        @foreach($fotos as $foto)
        <a href="{{ $foto->cloudinary_url }}" target="_blank" rel="noopener noreferrer" class="group relative aspect-square rounded-xl overflow-hidden">
            <img src="{{ $foto->cloudinary_url }}"
                class="w-full h-full object-cover transition-all duration-300 group-hover:scale-110">
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Entregado --}}
@if($ordenActiva->estado === 'ENTREGADO')
<div class="relative rounded-3xl overflow-hidden mb-6">
    <div class="absolute inset-0 bg-gradient-to-br from-green-600 to-emerald-800"></div>
    <div class="relative p-8 text-center">
        <p class="text-6xl mb-4">🎉</p>
        <h3 class="text-white font-black text-2xl mb-2">¡Vehículo entregado!</h3>
        <p class="text-green-200 mb-6">Gracias por confiar en Taller Eusebio. ¡Hasta la próxima!</p>
        @php $recibo = \App\Models\Recibo::where('orden_trabajo_id', $ordenActiva->id)->first(); @endphp
        @if($recibo)
        <a href="{{ route('gerente.recibos.descargar', $recibo) }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white text-green-800 font-bold rounded-2xl text-sm hover:bg-green-50 transition shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Descargar Recibo PDF
        </a>
        @endif
    </div>
</div>
@endif

@else
{{-- Sin orden activa --}}
<div class="card-glass rounded-3xl p-12 text-center mb-6">
    <div class="w-24 h-24 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-amber-400/50" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
        </svg>
    </div>
    <h3 class="text-white font-bold text-xl mb-2">Sin órdenes activas</h3>
    <p class="text-slate-400 text-sm max-w-xs mx-auto">Cuando traigas tu vehículo al taller podrás seguir su progreso aquí en tiempo real.</p>
</div>
@endif

{{-- Historial --}}
@php $entregados = $vehiculos->flatMap->ordenesTrabajo->where('estado', 'ENTREGADO')->sortByDesc('created_at'); @endphp
@if($entregados->isNotEmpty())
<div class="card-glass rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-white/10 flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-white font-bold text-sm">Historial de Servicios</h3>
        <span class="ml-auto px-2 py-0.5 bg-white/10 rounded-full text-slate-400 text-xs">{{ $entregados->count() }}</span>
    </div>
    @foreach($entregados as $orden)
    <div class="px-6 py-4 border-b border-white/5 last:border-0 flex items-center justify-between hover:bg-white/5 transition">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-purple-400 text-xs font-bold">#{{ $orden->id }}</span>
            </div>
            <div>
                <p class="text-white font-semibold text-sm">{{ $orden->vehiculo->placa ?? 'N/A' }} — {{ $orden->vehiculo->marca ?? '' }} {{ $orden->vehiculo->modelo ?? '' }}</p>
                <p class="text-slate-500 text-xs">{{ \Carbon\Carbon::parse($orden->fecha_entrega)->format('d/m/Y') }}</p>
            </div>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-white font-bold">Bs. {{ number_format($orden->costo_total ?? 0, 2) }}</p>
            <span class="inline-block px-2 py-0.5 bg-purple-500/20 text-purple-400 rounded-full text-xs font-semibold mt-1">Completado</span>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection

