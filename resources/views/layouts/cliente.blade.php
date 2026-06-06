<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Área') &mdash; Taller Eusebio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); }
        .card-glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }
        .amber-glow { box-shadow: 0 0 30px rgba(245, 158, 11, 0.15); }
        .pulse-ring { animation: pulse-ring 2s ease-out infinite; }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(245,158,11,0.4); }
            70%  { box-shadow: 0 0 0 12px rgba(245,158,11,0); }
            100% { box-shadow: 0 0 0 0 rgba(245,158,11,0); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">

    {{-- Navbar --}}
    <nav class="border-b border-white/10 sticky top-0 z-50" style="background:rgba(15,23,42,0.85);backdrop-filter:blur(20px)">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center amber-glow">
                    <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-sm leading-none">Taller Eusebio</p>
                    <p class="text-amber-400 text-xs">Panel del cliente</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-500/20 border border-amber-500/30 rounded-full flex items-center justify-center text-amber-400 font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="text-slate-300 text-sm">{{ Auth::user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-1.5 text-xs font-semibold text-slate-300 hover:text-white border border-white/20 hover:border-red-500/50 hover:bg-red-500/10 rounded-lg transition-all duration-200">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

    <footer class="mt-12 py-6 text-center border-t border-white/10">
        <p class="text-slate-500 text-xs">&copy; {{ date('Y') }} Taller Mecánico Eusebio &mdash; Todos los derechos reservados</p>
    </footer>

</body>
</html>
