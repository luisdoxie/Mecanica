@extends($layout)
@section('title', 'Nuevo Gasto')
@section('header', 'Registrar Gasto')

@section('content')
<div class="max-w-xl">
    <x-alert />
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.gastos.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <x-form-select label="Categoría" name="categoria_id" :required="true"
                :options="$categorias->mapWithKeys(fn($c) => [$c->id => $c->nombre . ' (' . $c->tipo . ')'])->toArray()" />

            <x-form-input label="Descripción" name="descripcion" :required="true" placeholder="Descripción del gasto..." />

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Monto (Bs.)" name="monto" type="number" step="0.01" min="0.01" :required="true" placeholder="0.00" />
                <x-form-input label="Fecha" name="fecha" type="date" :required="true" :value="date('Y-m-d')" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden vinculada (opcional)</label>
                <select name="orden_trabajo_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Sin orden vinculada</option>
                    @foreach($ordenes as $o)
                    <option value="{{ $o->id }}">
                        #{{ $o->id }} — {{ $o->vehiculo->placa }} · {{ $o->estado }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto comprobante (opcional)</label>
                <input type="file" name="comprobante" accept="image/*"
                    class="text-sm text-gray-600 file:mr-3 file:px-3 file:py-1.5 file:bg-teal-600 file:text-white file:rounded-lg file:border-0 file:text-sm file:cursor-pointer">
                @error('comprobante')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Guardar Gasto
                </button>
                <a href="{{ route('gerente.gastos.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
