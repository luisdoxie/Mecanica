@extends($layout)
@section('title', 'Registrar Pago')
@section('header', 'Registrar Pago de Orden')

@section('content')
<div class="max-w-xl">
    <x-alert />
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.cobros.store') }}" class="space-y-5"
              x-data="{ buscar: '', ordenId: '{{ old('orden_trabajo_id', $ordenId) }}' }">
            @csrf

            {{-- Buscador de orden --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Buscar orden (placa o cliente)
                </label>
                <input type="text" x-model="buscar" placeholder="Ej: ABC-123 o García..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 mb-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Orden de Trabajo <span class="text-red-500">*</span>
                </label>
                <select name="orden_trabajo_id" x-model="ordenId" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Seleccione una orden...</option>
                    @foreach($ordenes as $o)
                    @php
                        $label = '#'.$o->id.' — '.$o->vehiculo->placa.' ('.$o->vehiculo->cliente->persona->apellido.') · Bs. '.number_format($o->costo_total ?? 0, 2).' · '.$o->estado;
                        $search = strtolower($o->vehiculo->placa.' '.$o->vehiculo->cliente->persona->nombre.' '.$o->vehiculo->cliente->persona->apellido);
                    @endphp
                    <option value="{{ $o->id }}"
                        data-search="{{ $search }}"
                        x-show="buscar === '' || '{{ $search }}'.includes(buscar.toLowerCase())"
                        {{ old('orden_trabajo_id', $ordenId) == $o->id ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('orden_trabajo_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <x-form-input label="Monto (Bs.)" name="monto" type="number" step="0.01" min="0.01" :required="true" placeholder="0.00" />

            <x-form-select label="Método de pago" name="metodo_pago" :required="true"
                :options="['EFECTIVO'=>'Efectivo','QR'=>'QR / Pago Móvil']" />

            <x-form-select label="Estado del pago" name="estado" :required="true"
                :options="['PENDIENTE'=>'Pendiente','PARCIAL'=>'Pago Parcial','PAGADO'=>'Pagado completo','FIADO'=>'Fiado']" />

            <x-form-textarea label="Observación" name="observacion" placeholder="Referencia, nota..." />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Registrar Pago
                </button>
                <a href="{{ route('gerente.cobros.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
