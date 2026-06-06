<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mecanico') &mdash; Taller Eusebio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <aside class="w-64 bg-amber-900 min-h-screen flex flex-col fixed left-0 top-0 bottom-0 z-40">
        <div class="p-6 border-b border-amber-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-400 rounded-lg flex items-center justify-center">
                    <span class="text-gray-900 font-bold text-xs">TE</span>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">Taller Eusebio</p>
                    <p class="text-amber-300 text-xs">Mecanico</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('mecanico.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('mecanico.dashboard') ? 'bg-amber-700 text-white' : 'text-amber-100 hover:bg-amber-800' }} transition text-sm font-medium">
                🏠 Mi Panel
            </a>
            <a href="{{ route('mecanico.ordenes.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('mecanico.ordenes.*') && !request()->routeIs('mecanico.ordenes.create') ? 'bg-amber-700 text-white' : 'text-amber-100 hover:bg-amber-800' }} transition text-sm">
                🔧 Mis Órdenes
            </a>
            <a href="{{ route('mecanico.ordenes.create') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('mecanico.ordenes.create') ? 'bg-amber-700 text-white' : 'text-amber-100 hover:bg-amber-800' }} transition text-sm">
                ➕ Nueva Orden
            </a>
            <a href="{{ route('mecanico.clientes.create') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('mecanico.clientes.*') ? 'bg-amber-700 text-white' : 'text-amber-100 hover:bg-amber-800' }} transition text-sm">
                👤 Nuevo Cliente
            </a>
            <a href="{{ route('mecanico.cobros.create') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('mecanico.cobros.*') ? 'bg-amber-700 text-white' : 'text-amber-100 hover:bg-amber-800' }} transition text-sm">
                💰 Registrar Cobro
            </a>
            <a href="{{ route('mecanico.vehiculos.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('mecanico.vehiculos.*') ? 'bg-amber-700 text-white' : 'text-amber-100 hover:bg-amber-800' }} transition text-sm">
                🚗 Vehículos en Taller
            </a>
        </nav>
        <div class="p-4 border-t border-amber-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-amber-300 hover:bg-amber-800 hover:text-white transition text-sm">
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    <div class="ml-64 flex-1 flex flex-col min-h-screen">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800">@yield('header', 'Mi Panel')</h1>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-amber-600 font-semibold">{{ Auth::user()->rol }}</p>
                </div>
                <div class="w-9 h-9 bg-amber-500 rounded-full flex items-center justify-center text-gray-900 font-bold text-sm">
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
