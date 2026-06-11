@extends($layout)
@section('title', 'Nuevo Cliente')
@section('header', 'Registrar Nuevo Cliente')

@section('content')
<div class="max-w-2xl">
    <x-alert />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('gerente.clientes.store') }}" class="space-y-5">
            @csrf

            <h3 class="font-bold text-gray-700 border-b pb-2">Datos Personales</h3>

            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Nombre" name="nombre" :required="true" placeholder="Juan" />
                <x-form-input label="Apellido" name="apellido" :required="true" placeholder="Pérez" />
            </div>

            <x-form-input label="Cédula de Identidad" name="ci" :required="true" placeholder="V-12345678" />
            <x-form-input label="Teléfono" name="telefono" placeholder="+58 412-555-0000" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Correo Electrónico
                    <span id="email-required-star" class="text-red-500 hidden"> *</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    placeholder="correo@ejemplo.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-red-400 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p id="email-hint" class="text-xs text-amber-600 mt-1 hidden">Requerido para activar el acceso web.</p>
            </div>

            <x-form-textarea label="Dirección" name="direccion" placeholder="Av. Principal, Casa 5..." />

            <div class="border-t pt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="puede_login" id="puede_login" value="1" {{ old('puede_login') ? 'checked' : '' }}
                        class="w-4 h-4 accent-teal-600">
                    <span class="text-sm text-gray-700">Permitir acceso web al cliente</span>
                </label>
                <p class="text-xs text-gray-400 mt-1 ml-7">Se generará un PIN de 6 dígitos. El cliente usa su correo + PIN para entrar.</p>
            </div>

            <script>
                const cb = document.getElementById('puede_login');
                const hint = document.getElementById('email-hint');
                const star = document.getElementById('email-required-star');
                function toggle() {
                    hint.classList.toggle('hidden', !cb.checked);
                    star.classList.toggle('hidden', !cb.checked);
                }
                cb.addEventListener('change', toggle);
                toggle();
            </script>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-lg text-sm transition">
                    Guardar Cliente
                </button>
                <a href="{{ route('gerente.clientes.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
