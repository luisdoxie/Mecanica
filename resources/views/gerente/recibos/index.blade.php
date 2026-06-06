@extends('layouts.gerente')
@section('title', 'Recibos')
@section('header', 'Recibos PDF')

@section('content')
<x-alert />

<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 text-sm text-amber-800">
    💡 Para generar un recibo, ve al detalle de una orden en estado <strong>LISTO</strong> o <strong>ENTREGADO</strong>
    y haz clic en <strong>"Generar Recibo PDF"</strong>.
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">N° Recibo</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Orden</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cliente / Vehículo</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Total</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Emitido</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($recibos as $r)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono font-bold text-amber-700">{{ $r->numero_recibo }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">#{{ $r->orden->id }}</td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800 text-sm">{{ $r->orden->vehiculo->cliente->persona->nombre }} {{ $r->orden->vehiculo->cliente->persona->apellido }}</p>
                    <p class="text-gray-400 text-xs">{{ $r->orden->vehiculo->placa }} — {{ $r->orden->vehiculo->marca }} {{ $r->orden->vehiculo->modelo }}</p>
                </td>
                <td class="px-4 py-3 text-right font-bold text-gray-800">Bs. {{ number_format($r->total, 2) }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $r->emitido_en?->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('gerente.recibos.descargar', $r) }}"
                        class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-white text-xs font-semibold rounded-lg transition">
                        📄 Descargar PDF
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Sin recibos generados aún.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($recibos->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $recibos->links() }}</div>
    @endif
</div>
@endsection
