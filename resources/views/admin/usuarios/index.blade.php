@extends('layouts.admin')
@section('title', 'Usuarios')
@section('header', 'Gestion de Usuarios')

@section('content')
<x-alert />

@php $badgeRol = ['SUPER_ADMIN'=>'bg-purple-100 text-purple-700','GERENTE'=>'bg-teal-100 text-teal-700','MECANICO'=>'bg-amber-100 text-amber-700','CLIENTE'=>'bg-gray-100 text-gray-600']; @endphp

{{-- Filtros --}}
<form method="GET" class="flex gap-3 items-end mb-5 flex-wrap">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Rol</label>
        <select name="rol" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">Todos los roles</option>
            @foreach($roles as $r)
            <option value="{{ $r }}" {{ ($filtros['rol'] ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Estado</label>
        <select name="activo" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">Todos</option>
            <option value="1" {{ ($filtros['activo'] ?? '') === '1' ? 'selected' : '' }}>Activos</option>
            <option value="0" {{ ($filtros['activo'] ?? '') === '0' ? 'selected' : '' }}>Inactivos</option>
        </select>
    </div>
    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Filtrar</button>
    <a href="{{ route('admin.usuarios.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Usuario</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Rol</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Estado</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" x-data="{}">
            @forelse($usuarios as $u)
            <tr class="hover:bg-gray-50 transition" x-data="{ showPass: false, showRol: false }">
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $u->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $u->email }}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeRol[$u->rol] ?? 'bg-gray-100 text-gray-600' }}">{{ $u->rol }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $u->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $u->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">

                        {{-- Cambiar Rol --}}
                        <div x-show="showRol" class="flex items-center gap-1">
                            <form method="POST" action="{{ route('admin.usuarios.rol', $u) }}" class="flex gap-1">
                                @csrf @method('PATCH')
                                <select name="rol" class="px-2 py-1 border border-gray-300 rounded text-xs bg-white">
                                    @foreach($roles as $r)
                                    <option value="{{ $r }}" {{ $u->rol === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-2 py-1 bg-purple-600 text-white rounded text-xs">OK</button>
                            </form>
                        </div>
                        <button @click="showRol = !showRol" class="text-purple-600 hover:text-purple-800 text-xs font-medium">Rol</button>

                        {{-- Toggle activo --}}
                        <form method="POST" action="{{ route('admin.usuarios.toggle', $u) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs font-medium {{ $u->activo ? 'text-amber-600 hover:text-amber-800' : 'text-green-600 hover:text-green-800' }}">
                                {{ $u->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>

                        {{-- Reset password --}}
                        <div x-show="showPass" class="flex items-center gap-1">
                            <form method="POST" action="{{ route('admin.usuarios.password', $u) }}" class="flex gap-1">
                                @csrf @method('PATCH')
                                <input type="password" name="password" placeholder="Nueva clave" minlength="6"
                                    class="px-2 py-1 border border-gray-300 rounded text-xs w-28">
                                <button type="submit" class="px-2 py-1 bg-amber-500 text-white rounded text-xs">OK</button>
                            </form>
                        </div>
                        <button @click="showPass = !showPass" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Password</button>

                        {{-- Eliminar --}}
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.usuarios.destroy', $u) }}"
                              onsubmit="return confirm('Eliminar usuario {{ $u->name }} y su persona? Esta accion es irreversible.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-gray-400">No se encontraron usuarios.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($usuarios->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $usuarios->links() }}</div>
    @endif
</div>
@endsection
