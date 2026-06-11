@extends($layout)
@section('title', 'Planilla')
@section('header', 'Comisiones Semanales')

@section('content')
<x-alert />

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Formulario de pago --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-1">Registrar Comisión</h3>
            <p class="text-xs text-gray-400 mb-4">El período se asigna automáticamente según la fecha de pago.</p>
            <form method="POST" action="{{ route('gerente.planilla.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Empleado <span class="text-red-500">*</span>
                    </label>
                    <select name="empleado_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Seleccione...</option>
                        @foreach($empleados as $e)
                        <option value="{{ $e->id }}" {{ old('empleado_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->persona->apellido }}, {{ $e->persona->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('empleado_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de pago <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_pago" required value="{{ old('fecha_pago', date('Y-m-d')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    @error('fecha_pago')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Monto (Bs.) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="monto" step="0.01" min="0.01" required
                        value="{{ old('monto') }}" placeholder="0.00"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    @error('monto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
                    <textarea name="observacion" rows="2" placeholder="Vehículos reparados, nota..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none">{{ old('observacion') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Registrar Pago
                </button>
            </form>
        </div>

        <div class="bg-teal-50 border border-teal-200 rounded-2xl p-5 text-center">
            <p class="text-xs text-teal-600 font-medium mb-1">{{ $semanaLabel }}</p>
            <p class="text-xs text-teal-500 mb-2">Total pagado esta semana</p>
            <p class="text-3xl font-black text-teal-700">Bs. {{ number_format($totalSemana, 2) }}</p>
        </div>
    </div>

    {{-- Lista de empleados --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Historial filtrado --}}
        @if($historial)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Historial de pagos</h3>
                <a href="{{ route('gerente.planilla.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">✕ Cerrar</a>
            </div>
            @forelse($historial as $p)
            @php
                $fp       = \Carbon\Carbon::parse($p->fecha_pago);
                $etiqueta = 'Semana ' . $fp->weekOfYear . ' — ' . $fp->format('d/m/Y');
            @endphp
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $etiqueta }}</p>
                    <p class="text-gray-400 text-xs">
                        {{ $p->observacion ?: 'Sin observación' }}
                    </p>
                </div>
                <span class="font-bold text-teal-700 text-base">Bs. {{ number_format($p->monto, 2) }}</span>
            </div>
            @empty
            <p class="px-5 py-6 text-center text-gray-400 text-sm">Sin pagos registrados.</p>
            @endforelse
        </div>
        @endif

        {{-- Empleados activos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Empleados Activos</h3>
            </div>
            @foreach($empleados as $e)
            @php $ultimoPago = $e->pagos->first(); @endphp
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $e->persona->nombre }} {{ $e->persona->apellido }}</p>
                    <p class="text-gray-400 text-xs">{{ $e->cargo }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($ultimoPago)
                    @php
                        $fu = \Carbon\Carbon::parse($ultimoPago->fecha_pago);
                        $labelU = 'Sem. ' . $fu->weekOfYear . '/' . $fu->year;
                    @endphp
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Último: {{ $labelU }}</p>
                        <p class="font-semibold text-gray-700 text-sm">Bs. {{ number_format($ultimoPago->monto, 2) }}</p>
                    </div>
                    @else
                    <span class="text-gray-400 text-xs">Sin pagos aún</span>
                    @endif
                    <a href="{{ route('gerente.planilla.index', ['empleado_id' => $e->id]) }}"
                        class="text-teal-600 hover:text-teal-800 text-xs font-medium whitespace-nowrap">Ver historial</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
