<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gerente') &mdash; Taller Eusebio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <aside class="w-64 bg-teal-900 min-h-screen flex flex-col fixed left-0 top-0 bottom-0 z-40">
        <div class="p-6 border-b border-teal-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                    <span class="text-gray-900 font-bold text-xs">TE</span>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">Taller Eusebio</p>
                    <p class="text-teal-300 text-xs">Gerencia</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('gerente.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm font-medium">
                Dashboard
            </a>
            <a href="{{ route('gerente.ordenes.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Ordenes de Trabajo
            </a>
            <a href="{{ route('gerente.clientes.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Clientes
            </a>
            <a href="{{ route('gerente.vehiculos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Vehiculos
            </a>
            <a href="{{ route('gerente.empleados.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Empleados
            </a>
            <a href="{{ route('gerente.cobros.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Cobros
            </a>
            <a href="{{ route('gerente.gastos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Gastos
            </a>
            <a href="{{ route('gerente.planilla.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Planilla
            </a>
            <a href="{{ route('gerente.recibos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Recibos PDF
            </a>
        </nav>
        <div class="p-4 border-t border-teal-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-300 hover:bg-teal-800 hover:text-white transition text-sm">
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    <div class="ml-64 flex-1 flex flex-col min-h-screen">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-teal-600 font-semibold">{{ Auth::user()->rol }}</p>
                </div>
                <div class="w-9 h-9 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>
