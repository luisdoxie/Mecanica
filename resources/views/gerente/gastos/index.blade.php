@extends($layout)
@section('title', 'Gastos')
@section('header', 'Registro de Gastos')

@section('content')
<x-alert />

{{-- Total del mes --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 mb-6 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-500">Total gastos de <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $mesActual)->translatedFormat('F Y') }}</strong></p>
        <p class="text-3xl font-black text-red-600">Bs. {{ number_format($totalMes, 2) }}</p>
    </div>
    <a href="{{ route('gerente.gastos.create') }}"
        class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
        + Registrar Gasto
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Categoría</label>
        <select name="categoria_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            <option value="">Todas</option>
            @foreach($categorias as $cat)
            <option value="{{ $cat->id }}" {{ ($filtros['categoria_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Desde</label>
        <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Hasta</label>
        <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
    </div>
    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-600 transition">Filtrar</button>
    <a href="{{ route('gerente.gastos.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Fecha</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Categoría</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Descripción</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Orden</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Monto</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Comprobante</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($gastos as $g)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $g->fecha?->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-medium">{{ $g->categoria?->nombre }}</span>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $g->descripcion }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $g->orden_trabajo_id ? '#'.$g->orden_trabajo_id : '—' }}</td>
                <td class="px-4 py-3 text-right font-bold text-red-600">Bs. {{ number_format($g->monto, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    @if($g->comprobante_url)
                    <a href="{{ $g->comprobante_url }}" target="_blank" rel="noopener noreferrer" class="text-teal-600 hover:text-teal-800 text-xs">Ver 📎</a>
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Sin gastos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($gastos->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $gastos->links() }}</div>
    @endif
</div>
@endsection

