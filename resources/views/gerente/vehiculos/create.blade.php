@extends($layout)
@section('title', 'Nuevo Vehículo')
@section('header', 'Registrar Nuevo Vehículo')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.vehiculos.store') }}" class="space-y-5">
            @csrf

            <x-form-select
                label="Cliente propietario"
                name="cliente_id"
                :required="true"
                :selected="$clienteId ?? old('cliente_id')"
                :options="$clientes->mapWithKeys(fn($c) => [$c->id => $c->persona->apellido . ', ' . $c->persona->nombre . ' — ' . $c->persona->ci])->toArray()"
                placeholder="Seleccione un cliente..."
            />

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Marca" name="marca" :required="true" placeholder="Toyota" />
                <x-form-input label="Modelo" name="modelo" :required="true" placeholder="Corolla" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <x-form-input label="Año" name="anio" type="number" :required="true" placeholder="{{ date('Y') }}" />
                <x-form-input label="Color" name="color" placeholder="Blanco" />
                <x-form-input label="Kilometraje" name="km_actual" type="number" placeholder="0" />
            </div>

            <x-form-input label="Placa" name="placa" :required="true" placeholder="ABC-1234"
                class="uppercase" style="text-transform:uppercase" />

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Guardar Vehículo
                </button>
                <a href="{{ route('gerente.vehiculos.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
