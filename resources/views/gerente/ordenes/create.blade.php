@extends('layouts.gerente')
@section('title', 'Nueva Orden')
@section('header', 'Nueva Orden de Trabajo')

@section('content')
<script>
    const _vehiculosOrden  = @json($vehiculos->map(fn($v) => ['id' => $v->id, 'label' => $v->placa . ' — ' . $v->marca . ' ' . $v->modelo . ' (' . $v->cliente->persona->apellido . ', ' . $v->cliente->persona->nombre . ')']));
    const _empleadosOrden  = @json($empleados->map(fn($e) => ['id' => $e->id, 'label' => $e->persona->apellido . ', ' . $e->persona->nombre . ' — ' . $e->cargo]));

    function buscadorVehiculo() {
        return {
            buscar: '', abierto: false,
            selId: '{{ old('vehiculo_id', '') }}',
            selNombre: '{{ addslashes(old('vehiculo_id') ? (($vehiculos->firstWhere('id', old('vehiculo_id'))?->placa ?? '') . ' — ' . ($vehiculos->firstWhere('id', old('vehiculo_id'))?->marca ?? '') . ' ' . ($vehiculos->firstWhere('id', old('vehiculo_id'))?->modelo ?? '')) : '') }}',
            lista: _vehiculosOrden,
            get filtrados() {
                if (!this.buscar) return this.lista;
                const b = this.buscar.toLowerCase();
                return this.lista.filter(v => v.label.toLowerCase().includes(b));
            },
            seleccionar(id, nombre) { this.selId = id; this.selNombre = nombre; this.buscar = ''; this.abierto = false; }
        };
    }

    function buscadorEmpleado() {
        return {
            buscar: '', abierto: false,
            selId: '{{ old('empleado_id', '') }}',
            selNombre: '{{ addslashes(old('empleado_id') ? (($empleados->firstWhere('id', old('empleado_id'))?->persona->apellido ?? '') . ', ' . ($empleados->firstWhere('id', old('empleado_id'))?->persona->nombre ?? '')) : '') }}',
            lista: _empleadosOrden,
            get filtrados() {
                if (!this.buscar) return this.lista;
                const b = this.buscar.toLowerCase();
                return this.lista.filter(e => e.label.toLowerCase().includes(b));
            },
            seleccionar(id, nombre) { this.selId = id; this.selNombre = nombre; this.buscar = ''; this.abierto = false; },
            limpiar() { this.selId = ''; this.selNombre = ''; this.buscar = ''; }
        };
    }
</script>

<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.ordenes.store') }}" class="space-y-5">
            @csrf

            {{-- Vehículo --}}
            <div x-data="buscadorVehiculo()">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Vehículo <span class="text-red-500">*</span>
                </label>
                <input type="hidden" name="vehiculo_id" :value="selId">
                <div class="relative">
                    <input type="text" x-model="buscar"
                        @focus="abierto = true" @click.outside="abierto = false"
                        :placeholder="selNombre || 'Buscar por placa, marca o cliente...'"
                        class="w-full px-3 py-2 border {{ $errors->has('vehiculo_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 pr-8">
                    <button type="button" x-show="selId" @click="selId=''; selNombre=''"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">✕</button>
                    <div x-show="abierto" x-transition
                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                        <template x-for="v in filtrados" :key="v.id">
                            <div @click="seleccionar(v.id, v.label)" @mousedown.prevent
                                :class="selId == v.id ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="px-3 py-2 text-sm cursor-pointer">
                                <span x-text="v.label"></span>
                            </div>
                        </template>
                        <div x-show="filtrados.length === 0"
                            class="px-3 py-3 text-sm text-gray-400 text-center">Sin resultados</div>
                    </div>
                </div>
                <p x-show="selId" class="mt-1 text-xs text-teal-600 font-medium">✓ <span x-text="selNombre"></span></p>
                @error('vehiculo_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Mecánico asignado --}}
            <div x-data="buscadorEmpleado()">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico asignado</label>
                <input type="hidden" name="empleado_id" :value="selId">
                <div class="relative">
                    <input type="text" x-model="buscar"
                        @focus="abierto = true" @click.outside="abierto = false"
                        :placeholder="selNombre || 'Sin asignar — buscar por nombre o cargo...'"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 pr-8">
                    <button type="button" x-show="selId" @click="limpiar()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">✕</button>
                    <div x-show="abierto" x-transition
                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                        <div @click="limpiar(); abierto = false" @mousedown.prevent
                            class="px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                            Sin asignar por ahora
                        </div>
                        <template x-for="e in filtrados" :key="e.id">
                            <div @click="seleccionar(e.id, e.label)" @mousedown.prevent
                                :class="selId == e.id ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="px-3 py-2 text-sm cursor-pointer">
                                <span x-text="e.label"></span>
                            </div>
                        </template>
                        <div x-show="filtrados.length === 0"
                            class="px-3 py-3 text-sm text-gray-400 text-center">Sin resultados</div>
                    </div>
                </div>
                <p x-show="selId" class="mt-1 text-xs text-teal-600 font-medium">✓ <span x-text="selNombre"></span></p>
            </div>

            <x-form-textarea
                label="Descripción del problema"
                name="descripcion_problema"
                :required="true"
                :rows="4"
                placeholder="Describa el problema o motivo de ingreso del vehículo..."
            />

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Crear Orden
                </button>
                <a href="{{ route('gerente.ordenes.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
