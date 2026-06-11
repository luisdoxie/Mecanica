@extends($layout)
@section('title', 'Perfil de Empleado')
@section('header', 'Perfil de Empleado')

@section('content')
<x-alert />

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('gerente.empleados.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
    <div class="ml-auto flex items-center gap-2">
        @if(!$empleado->activo)
            <span class="px-3 py-1.5 bg-red-100 text-red-600 rounded-lg text-sm font-semibold">● Inactivo</span>
        @endif
        <a href="{{ route('gerente.empleados.edit', $empleado) }}"
            class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white font-semibold rounded-lg text-sm transition">
            Editar
        </a>
        @if($empleado->activo)
        <form method="POST" action="{{ route('gerente.empleados.desactivar', $empleado) }}"
              onsubmit="return confirm('¿Confirma desactivar a este empleado?')">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-lg text-sm transition">
                Desactivar
            </button>
        </form>
        @else
        <form method="POST" action="{{ route('gerente.empleados.reactivar', $empleado) }}">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-semibold rounded-lg text-sm transition">
                Reactivar
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-5">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-amber-700">{{ strtoupper(substr($empleado->persona->nombre, 0, 1)) }}</span>
                </div>
                <h2 class="font-bold text-gray-800 text-lg">{{ $empleado->persona->nombre }} {{ $empleado->persona->apellido }}</h2>
                <p class="text-teal-600 font-semibold text-sm">{{ $empleado->cargo }}</p>
            </div>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">CI</dt><dd class="font-medium">{{ $empleado->persona->ci }}</dd></div>
                <div><dt class="text-gray-400">Teléfono</dt><dd class="font-medium">{{ $empleado->persona->telefono ?? '—' }}</dd></div>
                <div><dt class="text-gray-400">Correo</dt><dd class="font-medium text-xs break-all">{{ $empleado->persona->email ?? '—' }}</dd></div>
                <div><dt class="text-gray-400">Salario</dt><dd class="font-medium">Bs. {{ number_format($empleado->salario, 2) }}</dd></div>
                <div><dt class="text-gray-400">Fecha ingreso</dt><dd class="font-medium">{{ $empleado->fecha_ingreso?->format('d/m/Y') }}</dd></div>
                <div>
                    <dt class="text-gray-400 mb-1">Especialidades</dt>
                    <dd class="flex flex-wrap gap-1">
                        @foreach($empleado->especialidades as $esp)
                            <span class="px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full text-xs">{{ $esp->nombre }}</span>
                        @endforeach
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Últimas Órdenes Asignadas</h3>
            </div>
            @forelse($empleado->ordenesTrabajo as $orden)
            <div class="px-5 py-4 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">Orden #{{ $orden->numero_orden ?? $orden->id }}</p>
                        <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($orden->created_at)->format('d/m/Y') }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        {{ str_replace('_', ' ', $orden->estado ?? '') }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-gray-400 text-sm">No tiene órdenes asignadas.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
