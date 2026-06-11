@extends($layout)
@section('title', 'Editar Vehículo')
@section('header', 'Editar Vehículo')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.vehiculos.update', $vehiculo) }}" class="space-y-5">
            @csrf @method('PUT')

            <x-form-select
                label="Cliente propietario"
                name="cliente_id"
                :required="true"
                :selected="$vehiculo->cliente_id"
                :options="$clientes->mapWithKeys(fn($c) => [$c->id => $c->persona->apellido . ', ' . $c->persona->nombre . ' — ' . $c->persona->ci])->toArray()"
            />

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Marca" name="marca" :required="true" :value="$vehiculo->marca" />
                <x-form-input label="Modelo" name="modelo" :required="true" :value="$vehiculo->modelo" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <x-form-input label="Año" name="anio" type="number" :required="true" :value="$vehiculo->anio" />
                <x-form-input label="Color" name="color" :value="$vehiculo->color" />
                <x-form-input label="Kilometraje" name="km_actual" type="number" :value="$vehiculo->km_actual" />
            </div>

            <x-form-input label="Placa" name="placa" :required="true" :value="$vehiculo->placa" class="uppercase" />

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Actualizar
                </button>
                <a href="{{ route('gerente.vehiculos.show', $vehiculo) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
