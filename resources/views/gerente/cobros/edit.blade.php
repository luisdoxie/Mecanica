@extends('layouts.gerente')
@section('title', 'Actualizar Pago')
@section('header', 'Actualizar Pago')

@section('content')
<div class="max-w-xl">
    <x-alert />
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 text-sm text-amber-800">
        <strong>Orden #{{ $cobro->orden->id }}</strong> ·
        {{ $cobro->orden->vehiculo->placa }} —
        {{ $cobro->orden->vehiculo->cliente->persona->nombre }} {{ $cobro->orden->vehiculo->cliente->persona->apellido }} ·
        Total orden: <strong>Bs. {{ number_format($cobro->orden->costo_total ?? 0, 2) }}</strong>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.cobros.update', $cobro) }}" class="space-y-5">
            @csrf @method('PUT')

            <x-form-input label="Monto (Bs.)" name="monto" type="number" step="0.01" min="0.01" :required="true" :value="$cobro->monto" />

            <x-form-select label="Método de pago" name="metodo_pago" :required="true" :selected="$cobro->metodo_pago"
                :options="['EFECTIVO'=>'Efectivo','QR'=>'QR / Pago Móvil']" />

            <x-form-select label="Estado del pago" name="estado" :required="true" :selected="$cobro->estado"
                :options="['PENDIENTE'=>'Pendiente','PARCIAL'=>'Pago Parcial','PAGADO'=>'Pagado completo','FIADO'=>'Fiado']" />

            <x-form-textarea label="Observación" name="observacion" :value="$cobro->observacion" />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Actualizar
                </button>
                <a href="{{ route('gerente.cobros.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
