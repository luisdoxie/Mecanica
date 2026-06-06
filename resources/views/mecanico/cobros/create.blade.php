@extends('layouts.mecanico')
@section('title', 'Registrar Cobro')
@section('header', 'Registrar Cobro')

@section('content')
<div class="max-w-xl" x-data="{
    metodo: '',
    tipo: 'TOTAL',
    costoTotal: 0,
    monto: '',
    buscar: '',
    seleccionarOrden(sel) {
        this.costoTotal = parseFloat(sel.options[sel.selectedIndex].dataset.costo || 0);
        this.monto = this.costoTotal.toFixed(2);
    },
    cambiarTipo(val) {
        this.tipo = val;
        this.monto = val === 'TOTAL' ? this.costoTotal.toFixed(2) : '';
    }
}">
<x-alert />

<a href="{{ route('mecanico.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-4 inline-block">← Volver al panel</a>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('mecanico.cobros.store') }}" class="space-y-5">
        @csrf

        {{-- Buscador --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar orden (placa o cliente)</label>
            <input type="text" x-model="buscar" placeholder="Ej: ABC-123 o García..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        {{-- Orden --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Orden de Trabajo <span class="text-red-500">*</span>
                @if($ordenes->isEmpty())
                    <span class="text-red-500 font-normal text-xs ml-2">(No tienes órdenes pendientes de cobro)</span>
                @endif
            </label>
            <select name="orden_trabajo_id" required x-on:change="seleccionarOrden($event.target)"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Seleccione una orden...</option>
                @foreach($ordenes as $o)
                @php
                    $st = strtolower($o->vehiculo->placa.' '.$o->vehiculo->cliente->persona->nombre.' '.$o->vehiculo->cliente->persona->apellido);
                @endphp
                <option value="{{ $o->id }}"
                    data-costo="{{ $o->costo_total ?? 0 }}"
                    x-show="buscar === '' || '{{ $st }}'.includes(buscar.toLowerCase())"
                    {{ old('orden_trabajo_id', $ordenId) == $o->id ? 'selected' : '' }}>
                    #{{ $o->id }} — {{ $o->vehiculo->placa }}
                    ({{ $o->vehiculo->cliente->persona->apellido }}) ·
                    Bs. {{ number_format($o->costo_total ?? 0, 2) }} · {{ $o->estado }}
                </option>
                @endforeach
            </select>
            @error('orden_trabajo_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Método de pago --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Método de pago <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                    :class="metodo === 'EFECTIVO' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="metodo_pago" value="EFECTIVO" x-model="metodo" class="hidden">
                    <div class="text-center w-full">
                        <p class="text-2xl mb-1">💵</p>
                        <p class="font-semibold text-gray-800 text-sm">Efectivo</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                    :class="metodo === 'QR' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="metodo_pago" value="QR" x-model="metodo" class="hidden">
                    <div class="text-center w-full">
                        <p class="text-2xl mb-1">📱</p>
                        <p class="font-semibold text-gray-800 text-sm">QR / Pago Móvil</p>
                    </div>
                </label>
            </div>
            @error('metodo_pago')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Tipo de cobro (solo EFECTIVO) --}}
        <div x-show="metodo === 'EFECTIVO'" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de cobro</label>
            <div class="flex gap-3">
                <label class="flex-1 flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                    :class="tipo === 'TOTAL' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                    <input type="radio" name="tipo_cobro" value="TOTAL"
                        x-model="tipo" @change="cambiarTipo('TOTAL')" class="accent-green-500">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">Pago total</p>
                        <p class="text-xs text-gray-400">Cobro completo</p>
                    </div>
                </label>
                <label class="flex-1 flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                    :class="tipo === 'PARCIAL' ? 'border-amber-500 bg-amber-50' : 'border-gray-200'">
                    <input type="radio" name="tipo_cobro" value="PARCIAL"
                        x-model="tipo" @change="cambiarTipo('PARCIAL')" class="accent-amber-500">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">Pago parcial</p>
                        <p class="text-xs text-gray-400">Adelanto del cliente</p>
                    </div>
                </label>
            </div>
            {{-- Saldo pendiente cuando es parcial --}}
            <div x-show="tipo === 'PARCIAL' && monto > 0 && costoTotal > 0" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm">
                <p class="text-amber-800 font-medium">
                    Saldo pendiente: Bs. <span x-text="Math.max(0, costoTotal - parseFloat(monto || 0)).toFixed(2)"></span>
                </p>
            </div>
        </div>

        {{-- QR imagen --}}
        <div x-show="metodo === 'QR'" x-transition>
            @if($qrUrl)
            <div class="text-center p-5 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-sm font-medium text-gray-600 mb-3">Mostrar al cliente para el pago:</p>
                <img src="{{ $qrUrl }}" alt="QR de pago" class="mx-auto max-w-[200px] rounded-xl border border-gray-300 shadow-sm">
            </div>
            @else
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-center">
                <p class="text-amber-700 text-sm">El código QR aún no ha sido configurado por el administrador.</p>
            </div>
            @endif
        </div>

        {{-- ÚNICO campo de monto -- siempre presente en el DOM --}}
        <div x-show="metodo !== ''" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Monto (Bs.) <span class="text-red-500">*</span>
                <span x-show="metodo === 'EFECTIVO' && tipo === 'TOTAL'" class="text-xs text-gray-400 font-normal ml-1">— total de la orden</span>
                <span x-show="metodo === 'EFECTIVO' && tipo === 'PARCIAL'" class="text-xs text-amber-600 font-normal ml-1">— ingrese el adelanto</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Bs.</span>
                <input type="number" name="monto" step="0.01" min="0.01"
                    x-model="monto"
                    placeholder="0.00"
                    required
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            @error('monto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Observación --}}
        <div x-show="metodo !== ''" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
            <textarea name="observacion" rows="2" placeholder="Nota o referencia..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('observacion') }}</textarea>
        </div>

        <div x-show="metodo !== ''" x-transition class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
                Confirmar Cobro
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
