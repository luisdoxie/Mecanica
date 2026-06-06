@extends('layouts.gerente')
@section('title', 'Clientes')
@section('header', 'Gestión de Clientes')

@section('content')
<x-alert />

<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-2 flex-1 max-w-sm">
        <input type="text" name="buscar" value="{{ $buscar }}"
            placeholder="Buscar por nombre, CI o teléfono..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Buscar</button>
        @if($buscar)
            <a href="{{ route('gerente.clientes.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">✕</a>
        @endif
    </form>
    <a href="{{ route('gerente.clientes.create') }}"
        class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
        + Nuevo Cliente
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cliente</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">CI</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Teléfono</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Pin acceso</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Puede login</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($clientes as $cliente)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $cliente->persona->nombre }} {{ $cliente->persona->apellido }}</p>
                    <p class="text-gray-400 text-xs">{{ $cliente->persona->email }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $cliente->persona->ci }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $cliente->persona->telefono ?? '—' }}</td>
                <td class="px-4 py-3">
                    <code class="bg-gray-100 px-2 py-0.5 rounded text-xs font-mono text-amber-700">{{ $cliente->pin_acceso }}</code>
                </td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('gerente.clientes.toggle-login', $cliente) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-2 py-0.5 rounded-full text-xs font-semibold border transition
                                {{ $cliente->puede_login ? 'bg-green-100 text-green-700 border-green-300 hover:bg-green-200' : 'bg-gray-100 text-gray-500 border-gray-300 hover:bg-gray-200' }}">
                            {{ $cliente->puede_login ? 'Activo' : 'Inactivo' }}
                        </button>
                    </form>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('gerente.clientes.show', $cliente) }}" class="text-teal-600 hover:text-teal-800 text-xs font-medium">Ver</a>
                    <a href="{{ route('gerente.clientes.edit', $cliente) }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Editar</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                    No se encontraron clientes{{ $buscar ? " para \"{$buscar}\"" : '' }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($clientes->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $clientes->links() }}
    </div>
    @endif
</div>
@endsection
