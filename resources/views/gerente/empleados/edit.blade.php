@extends($layout)
@section('title', 'Editar Empleado')
@section('header', 'Editar Empleado')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.empleados.update', $empleado) }}" class="space-y-5">
            @csrf @method('PUT')

            <h3 class="font-bold text-gray-700 border-b pb-2">Datos Personales</h3>

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Nombre" name="nombre" :required="true" :value="$empleado->persona->nombre" />
                <x-form-input label="Apellido" name="apellido" :required="true" :value="$empleado->persona->apellido" />
            </div>

            <x-form-input label="Cédula de Identidad" name="ci" :required="true" :value="$empleado->persona->ci" />
            <x-form-input label="Teléfono" name="telefono" :value="$empleado->persona->telefono" />
            <x-form-input label="Correo Electrónico" name="email" type="email" :required="true" :value="$empleado->persona->email" />
            <x-form-textarea label="Dirección" name="direccion" :value="$empleado->persona->direccion" />

            <h3 class="font-bold text-gray-700 border-b pb-2 pt-2">Datos Laborales</h3>

            <x-form-input label="Cargo" name="cargo" :required="true" :value="$empleado->cargo" />

            <x-form-input label="Fecha de Ingreso" name="fecha_ingreso" type="date" :required="true"
                :value="$empleado->fecha_ingreso?->format('Y-m-d')" />

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Especialidades</label>
                @php $seleccionadas = old('especialidades', $empleado->especialidades->pluck('id')->toArray()); @endphp
                <div class="grid grid-cols-2 gap-2">
                    @foreach($especialidades as $esp)
                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="especialidades[]" value="{{ $esp->id }}"
                            {{ in_array($esp->id, $seleccionadas) ? 'checked' : '' }}
                            class="w-4 h-4 accent-teal-600">
                        <span class="text-sm text-gray-700">{{ $esp->nombre }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Actualizar
                </button>
                <a href="{{ route('gerente.empleados.show', $empleado) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Zona peligrosa: eliminar permanentemente --}}
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mt-4" x-data="{ confirmar: false }">
        <h3 class="font-bold text-red-700 mb-1">Zona de peligro</h3>
        <p class="text-sm text-red-600 mb-4">
            Eliminar al empleado <strong>borra permanentemente</strong> su registro, acceso web y datos personales.
            Esta acción <strong>no se puede deshacer</strong>. Si solo quiere suspenderlo temporalmente, use
            <strong>Desactivar</strong> desde la lista de empleados.
        </p>

        <template x-if="!confirmar">
            <button @click="confirmar = true"
                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg text-sm transition">
                Eliminar empleado permanentemente
            </button>
        </template>

        <template x-if="confirmar">
            <div class="bg-red-100 border border-red-300 rounded-xl p-4 space-y-3">
                <p class="text-sm font-bold text-red-800">
                    ¿Confirma que desea eliminar a <span class="underline">{{ $empleado->persona->nombre }} {{ $empleado->persona->apellido }}</span>?
                </p>
                <p class="text-xs text-red-600">No podrá recuperar esta información.</p>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('gerente.empleados.destroy', $empleado) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-5 py-2 bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg text-sm transition">
                            Sí, eliminar definitivamente
                        </button>
                    </form>
                    <button @click="confirmar = false"
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg text-sm transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
