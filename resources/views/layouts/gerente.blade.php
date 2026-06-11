<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gerente') &mdash; Taller Eusebio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Overlay móvil --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-teal-900 flex flex-col
                  transform transition-transform duration-300
                  -translate-x-full md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="p-6 border-b border-teal-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                    <span class="text-gray-900 font-bold text-xs">TE</span>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">Taller Eusebio</p>
                    <p class="text-teal-300 text-xs">Gerencia</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-teal-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('gerente.dashboard') }}" @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm font-medium">
                Dashboard
            </a>
            <a href="{{ route('gerente.ordenes.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Ordenes de Trabajo
            </a>
            <a href="{{ route('gerente.clientes.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Clientes
            </a>
            <a href="{{ route('gerente.vehiculos.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Vehiculos
            </a>
            <a href="{{ route('gerente.empleados.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Empleados
            </a>
            <a href="{{ route('gerente.cobros.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Cobros
            </a>
            <a href="{{ route('gerente.gastos.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Gastos
            </a>
            <a href="{{ route('gerente.planilla.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
                Planilla
            </a>
            <a href="{{ route('gerente.recibos.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-teal-100 hover:bg-teal-800 transition text-sm">
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

    {{-- Contenido principal --}}
    <div class="md:ml-64 flex flex-col min-h-screen">
        <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg md:text-xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2 md:gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-teal-600 font-semibold">{{ Auth::user()->rol }}</p>
                </div>
                <div class="w-9 h-9 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>
        <main class="flex-1 p-4 md:p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>
