@extends('layouts.gerente')
@section('title', 'Editar Cliente')
@section('header', 'Editar Cliente')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.clientes.update', $cliente) }}" class="space-y-5">
            @csrf @method('PUT')

            <h3 class="font-bold text-gray-700 border-b pb-2">Datos Personales</h3>

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Nombre" name="nombre" :required="true" :value="$cliente->persona->nombre" />
                <x-form-input label="Apellido" name="apellido" :required="true" :value="$cliente->persona->apellido" />
            </div>

            <x-form-input label="Cédula de Identidad" name="ci" :required="true" :value="$cliente->persona->ci" />
            <x-form-input label="Teléfono" name="telefono" :value="$cliente->persona->telefono" />
            <x-form-input label="Correo Electrónico" name="email" type="email" :value="$cliente->persona->email" />
            <x-form-textarea label="Dirección" name="direccion" :value="$cliente->persona->direccion" />

            <div class="border-t pt-4 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="puede_login" value="1"
                        {{ old('puede_login', $cliente->puede_login) ? 'checked' : '' }}
                        class="w-4 h-4 accent-teal-600">
                    <span class="text-sm text-gray-700">Permitir acceso web</span>
                </label>
                <p class="text-xs text-gray-500 ml-7">PIN actual: <code class="bg-gray-100 px-1.5 rounded font-mono text-amber-700">{{ $cliente->pin_acceso }}</code></p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Actualizar
                </button>
                <a href="{{ route('gerente.clientes.show', $cliente) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
