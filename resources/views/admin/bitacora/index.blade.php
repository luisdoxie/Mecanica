@extends('layouts.admin')
@section('title', 'Bitacora')
@section('header', 'Bitacora del Sistema')

@section('content')
<x-alert />

{{-- Filtros --}}
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Usuario</label>
        <input type="text" name="usuario" value="{{ $filtros['usuario'] ?? '' }}" placeholder="Nombre..."
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 w-36">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Acción</label>
        <input type="text" name="accion" value="{{ $filtros['accion'] ?? '' }}" placeholder="inicio de sesión..."
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 w-44">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Módulo</label>
        <select name="modulo"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">Todos</option>
            @foreach(['Auth','ClienteController','EmpleadoController','OrdenTrabajoController','CobrosController','ImagenOrden'] as $m)
            <option value="{{ $m }}" {{ ($filtros['modulo'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Desde</label>
        <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Hasta</label>
        <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>
    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Filtrar</button>
    <a href="{{ route('admin.bitacora.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
    <a href="{{ route('admin.bitacora.exportar', $filtros) }}"
        class="ml-auto px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-lg text-sm transition">
        Exportar Excel
    </a>
</form>

@php
$badgeAccion = function(string $accion): string {
    $a = strtolower($accion);
    if (str_contains($a, 'inicio de sesión'))  return 'bg-green-100 text-green-700';
    if (str_contains($a, 'cierre de sesión'))  return 'bg-red-100 text-red-700';
    if (str_contains($a, 'registrado') || str_contains($a, 'creado') || str_contains($a, 'activado'))
        return 'bg-blue-100 text-blue-700';
    if (str_contains($a, 'actualizado') || str_contains($a, 'modificado') || str_contains($a, 'cambiado'))
        return 'bg-amber-100 text-amber-700';
    if (str_contains($a, 'eliminado') || str_contains($a, 'desactivado'))
        return 'bg-red-100 text-red-700';
    return 'bg-gray-100 text-gray-600';
};
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Fecha y hora</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Usuario / Rol</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Acción</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Módulo</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">IP</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Dispositivo</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Datos</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($registros as $r)
            <tr class="hover:bg-gray-50 transition" x-data="{ open: false }">
                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i:s') }}
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800 text-xs">{{ $r->usuario?->name ?? 'Sistema' }}</p>
                    <span class="text-xs text-gray-400">{{ $r->rol }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeAccion($r->accion) }}">
                        {{ $r->accion }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $r->modulo }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs font-mono">{{ $r->ip }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs max-w-[180px] truncate" title="{{ $r->dispositivo }}">
                    {{ $r->dispositivo ? \Str::limit($r->dispositivo, 40) : '—' }}
                </td>
                <td class="px-4 py-3 text-center">
                    @if($r->datos_anteriores || $r->datos_nuevos)
                    <button @click="open = !open" class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                        <span x-text="open ? 'Cerrar' : 'Ver'"></span>
                    </button>
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @if($r->datos_anteriores || $r->datos_nuevos)
            <tr x-show="open" class="bg-purple-50/50">
                <td colspan="7" class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4">
                        @if($r->datos_anteriores)
                        <div>
                            <p class="text-xs font-bold text-red-600 mb-1">Antes</p>
                            <pre class="text-xs bg-white border border-red-100 rounded p-2 overflow-auto max-h-40">{{ json_encode($r->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                        @if($r->datos_nuevos)
                        <div>
                            <p class="text-xs font-bold text-green-600 mb-1">Después</p>
                            <pre class="text-xs bg-white border border-green-100 rounded p-2 overflow-auto max-h-40">{{ json_encode($r->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @endif
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Sin registros en la bitácora.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($registros->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $registros->links() }}</div>
    @endif
</div>
@endsection
