@extends($layout)
@section('title', 'Empleados')
@section('header', 'Gestión de Empleados')

@section('content')
<x-alert />

<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-2 flex-1 max-w-sm">
        <input type="text" name="buscar" value="{{ $buscar }}"
            placeholder="Nombre, CI o cargo..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Buscar</button>
        @if($buscar)
            <a href="{{ route('gerente.empleados.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">✕</a>
        @endif
    </form>
    <a href="{{ route('gerente.empleados.create') }}"
        class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
        + Nuevo Empleado
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[560px]">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Empleado</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cargo</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Especialidades</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Ingreso</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($empleados as $empleado)
            <tr class="hover:bg-gray-50 transition {{ !$empleado->activo ? 'opacity-60' : '' }}">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $empleado->persona->nombre }} {{ $empleado->persona->apellido }}</p>
                            <p class="text-gray-400 text-xs">{{ $empleado->persona->ci }} · {{ $empleado->persona->telefono }}</p>
                        </div>
                        @if(!$empleado->activo)
                            <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-semibold">Inactivo</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $empleado->cargo }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @foreach($empleado->especialidades as $esp)
                            <span class="px-1.5 py-0.5 bg-teal-100 text-teal-700 rounded text-xs">{{ $esp->nombre }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $empleado->fecha_ingreso?->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('gerente.empleados.show', $empleado) }}" class="text-teal-600 hover:text-teal-800 text-xs font-medium">Ver</a>
                    <a href="{{ route('gerente.empleados.edit', $empleado) }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Editar</a>
                    @if($empleado->activo)
                    <form method="POST" action="{{ route('gerente.empleados.desactivar', $empleado) }}" class="inline"
                          onsubmit="return confirm('¿Desactivar a este empleado?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Desactivar</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('gerente.empleados.reactivar', $empleado) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Reactivar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                    No se encontraron empleados{{ $buscar ? " para \"{$buscar}\"" : '' }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($empleados->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $empleados->links() }}</div>
    @endif
</div>
@endsection
