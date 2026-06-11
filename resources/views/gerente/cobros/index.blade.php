@extends($layout)
@section('title', 'Cobros')
@section('header', 'Cobros y Pagos')

@section('content')
<x-alert />

@php
$badgePago = [
    'PENDIENTE'  => 'bg-yellow-100 text-yellow-700',
    'PARCIAL'    => 'bg-blue-100 text-blue-700',
    'PAGADO'     => 'bg-green-100 text-green-700',
    'FIADO'      => 'bg-red-100 text-red-700',
];
@endphp

{{-- Cuentas por cobrar --}}
@if($porCobrar->isNotEmpty())
<div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">
    <h3 class="font-bold text-red-700 mb-3 flex items-center gap-2">
        ⚠️ Cuentas por Cobrar ({{ $porCobrar->count() }})
    </h3>
    <div class="space-y-2">
        @foreach($porCobrar as $p)
        <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-red-100">
            <div class="text-sm">
                <span class="font-semibold text-gray-800">Orden #{{ $p->orden->id }}</span>
                <span class="text-gray-400 ml-2">{{ $p->orden->vehiculo->cliente->persona->nombre }} {{ $p->orden->vehiculo->cliente->persona->apellido }}</span>
                <span class="text-gray-400 ml-2">·</span>
                <span class="text-gray-500 ml-2">{{ \Carbon\Carbon::parse($p->created_at)->diffForHumans() }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-bold text-gray-800">Bs. {{ number_format($p->monto, 2) }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgePago[$p->estado] }}">{{ $p->estado }}</span>
                <a href="{{ route('gerente.cobros.edit', $p) }}" class="text-teal-600 hover:text-teal-800 text-xs font-medium">Actualizar</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Header con filtros y búsqueda --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-4">
    <div class="w-full sm:w-auto">
        <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
        <select name="estado" onchange="this.form.submit()"
            class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            <option value="">Todos</option>
            @foreach(['PENDIENTE','PARCIAL','PAGADO','FIADO'] as $e)
            <option value="{{ $e }}" {{ ($filtros['estado'] ?? '') === $e ? 'selected' : '' }}>{{ $e }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-full sm:w-auto">
        <label class="block text-xs font-medium text-gray-500 mb-1">Buscar cliente / placa</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Nombre o placa..."
            class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
    </div>
    <div class="flex gap-2 flex-wrap w-full sm:w-auto">
        <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Filtrar</button>
        <a href="{{ route('gerente.cobros.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
        <a href="{{ route('gerente.cobros.create') }}"
            class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
            + Registrar Pago
        </a>
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[580px]">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Orden</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cliente</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Método</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Estado</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Monto</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Fecha</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($pagos as $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono text-gray-500 text-xs">#{{ $p->orden->id }}</td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800 text-xs">{{ $p->orden->vehiculo->cliente->persona->nombre }} {{ $p->orden->vehiculo->cliente->persona->apellido }}</p>
                    <p class="text-gray-400 text-xs">{{ $p->orden->vehiculo->placa }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ ['EFECTIVO'=>'Efectivo','QR'=>'QR / Pago Móvil'][$p->metodo_pago] ?? $p->metodo_pago }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgePago[$p->estado] }}">{{ $p->estado }}</span>
                </td>
                <td class="px-4 py-3 text-right font-bold text-gray-800">Bs. {{ number_format($p->monto, 2) }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-right">
                    @if($p->estado !== 'PAGADO')
                    <a href="{{ route('gerente.cobros.edit', $p) }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Editar</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Sin pagos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($pagos->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $pagos->links() }}</div>
    @endif
</div>
@endsection
