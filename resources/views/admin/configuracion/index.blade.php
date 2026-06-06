@extends('layouts.admin')
@section('title', 'Configuracion')
@section('header', 'Configuracion del Taller')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.configuracion.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <x-form-input label="Nombre del taller" name="nombre" :required="true"
                :value="$config['nombre'] ?? 'Taller Mecanico Eusebio'" />

            <x-form-textarea label="Direccion" name="direccion" :required="true"
                :value="$config['direccion'] ?? ''" placeholder="Av. Principal, Local 4B..." />

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Telefono" name="telefono" :required="true"
                    :value="$config['telefono'] ?? ''" placeholder="+58 412-555-0100" />
                <x-form-input label="Correo" name="email" type="email"
                    :value="$config['email'] ?? ''" placeholder="taller@ejemplo.com" />
            </div>

            <x-form-input label="Horario" name="horario" :required="true"
                :value="$config['horario'] ?? ''" placeholder="Lun-Vie 8am-6pm | Sab 8am-1pm" />

            <x-form-input label="Slogan" name="slogan"
                :value="$config['slogan'] ?? ''" placeholder="Tu auto en las mejores manos" />

            {{-- QR de pago --}}
            <div class="border-t border-gray-100 pt-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Imagen QR para cobros</label>
                @php $qrUrl = $config['qr_imagen_url'] ?? null; @endphp
                @if($qrUrl)
                <div class="mb-3 flex items-center gap-4">
                    <img src="{{ $qrUrl }}" alt="QR actual" class="w-24 h-24 object-cover rounded-xl border border-gray-200">
                    <p class="text-xs text-gray-500">QR configurado actualmente. Sube uno nuevo para reemplazarlo.</p>
                </div>
                @endif
                <input type="file" name="qr_imagen" accept="image/*"
                    class="text-sm text-gray-600 file:mr-3 file:px-4 file:py-2 file:bg-purple-600 file:text-white file:rounded-lg file:border-0 file:text-sm file:cursor-pointer">
                <p class="mt-1 text-xs text-gray-400">Sube la imagen QR de tu banco. Se mostrará al mecánico cuando cobra por QR.</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-800">
                Estos datos se muestran en la portada publica y en los recibos PDF generados.
            </div>

            <button type="submit"
                class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg text-sm transition">
                Guardar Configuracion
            </button>
        </form>
    </div>
</div>
@endsection
