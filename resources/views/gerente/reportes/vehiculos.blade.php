@extends($layout)
@section('title', 'Reporte de Vehículos')
@section('header', 'Reporte Semanal de Vehículos')

@section('content')
<x-alert />

<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-sm text-gray-500 mb-5">
            Genera un PDF con todas las órdenes del período seleccionado, incluyendo servicios, repuestos y fotos.
        </p>
        <form method="POST" action="{{ route('gerente.reportes.vehiculos.generar') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Desde <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="desde" required value="{{ now()->startOfWeek()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    @error('desde')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Hasta <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="hasta" required value="{{ now()->endOfWeek()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    @error('hasta')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico (opcional)</label>
                <select name="empleado_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Todos los mecánicos</option>
                    @foreach($empleados as $e)
                    <option value="{{ $e->id }}">{{ $e->persona->nombre }} {{ $e->persona->apellido }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-700">
                El reporte incluye hasta <strong>6 fotos por orden</strong>. Si hay muchas órdenes o fotos, la generación puede tardar unos segundos.
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                Generar PDF
            </button>
        </form>
    </div>
</div>
@endsection
