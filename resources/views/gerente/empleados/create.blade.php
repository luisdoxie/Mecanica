@extends($layout)
@section('title', 'Nuevo Empleado')
@section('header', 'Registrar Nuevo Empleado')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.empleados.store') }}" class="space-y-5">
            @csrf

            <h3 class="font-bold text-gray-700 border-b pb-2">Datos Personales</h3>

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Nombre" name="nombre" :required="true" placeholder="Juan" />
                <x-form-input label="Apellido" name="apellido" :required="true" placeholder="García" />
            </div>

            <x-form-input label="Cédula de Identidad" name="ci" :required="true" placeholder="V-12345678" />
            <x-form-input label="Teléfono" name="telefono" placeholder="+58 412-555-0000" />
            <x-form-input label="Correo Electrónico" name="email" type="email" :required="true"
                placeholder="correo@ejemplo.com" />
            <x-form-textarea label="Dirección" name="direccion" />

            <h3 class="font-bold text-gray-700 border-b pb-2 pt-2">Datos Laborales</h3>

            <x-form-input label="Cargo" name="cargo" :required="true" placeholder="Mecánico, Electricista..." />

            <x-form-input label="Fecha de Ingreso" name="fecha_ingreso" type="date" :required="true"
                :value="date('Y-m-d')" />

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Especialidades</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($especialidades as $esp)
                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="especialidades[]" value="{{ $esp->id }}"
                            {{ in_array($esp->id, old('especialidades', [])) ? 'checked' : '' }}
                            class="w-4 h-4 accent-teal-600">
                        <span class="text-sm text-gray-700">{{ $esp->nombre }}</span>
                    </label>
                    @endforeach
                </div>
                @error('especialidades')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
                💡 Se creará automáticamente un acceso web con rol <strong>MECANICO</strong>.<br>
                Contraseña inicial: <strong>su número de cédula</strong>.
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Guardar Empleado
                </button>
                <a href="{{ route('gerente.empleados.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
