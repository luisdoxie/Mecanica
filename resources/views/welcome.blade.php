<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Mecánico Eusebio — Tu auto en las mejores manos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hero-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); }
    </style>
</head>
<body class="bg-gray-950 text-white">

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-950/90 backdrop-blur-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-lg text-white">Taller Eusebio</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="#servicios" class="text-gray-400 hover:text-amber-400 text-sm transition hidden sm:block">Servicios</a>
                <a href="#contacto" class="text-gray-400 hover:text-amber-400 text-sm transition hidden sm:block">Contacto</a>
                <a href="{{ route('login') }}"
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-semibold text-sm rounded-lg transition">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-bg min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 rounded-full px-4 py-1.5 mb-6">
                    <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                    <span class="text-amber-400 text-sm font-medium">Servicio disponible hoy</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Taller Mecánico<br>
                    <span class="text-amber-400">Eusebio</span>
                </h1>
                <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                    Tu auto en las <span class="text-amber-400 font-semibold">mejores manos</span>.<br>
                    Servicio profesional, diagnóstico preciso y transparencia total.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#consulta"
                        class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-xl text-center transition duration-200">
                        🔍 Ver estado de mi vehículo
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-6 py-3 bg-gray-800 hover:bg-gray-700 border border-gray-600 text-white font-semibold rounded-xl text-center transition duration-200">
                        Iniciar sesión
                    </a>
                </div>
            </div>
            <div class="hidden lg:grid grid-cols-2 gap-4">
                <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-black text-amber-400 mb-2">+500</div>
                    <div class="text-gray-400 text-sm">Vehículos atendidos</div>
                </div>
                <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-black text-amber-400 mb-2">15+</div>
                    <div class="text-gray-400 text-sm">Años de experiencia</div>
                </div>
                <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-black text-amber-400 mb-2">⏱️</div>
                    <div class="text-gray-400 text-sm">Entrega a tiempo</div>
                </div>
                <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-black text-amber-400 mb-2">98%</div>
                    <div class="text-gray-400 text-sm">Clientes satisfechos</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONSULTA RÁPIDA -->
    <section id="consulta" class="bg-gray-900 py-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-white mb-3">Consulta el estado de tu vehículo</h2>
                <p class="text-gray-400">Ingresa la placa y obtén información en tiempo real sin necesidad de iniciar sesión.</p>
            </div>
            <form method="POST" action="{{ route('estado.consultar') }}"
                class="bg-gray-800 border border-gray-700 rounded-2xl p-8 shadow-xl">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="placa" value="{{ $placa ?? '' }}"
                        placeholder="Ej: ABC-1234"
                        class="flex-1 px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white placeholder-gray-400
                               focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition uppercase"
                        style="text-transform:uppercase" maxlength="10">
                    <button type="submit"
                        class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-xl transition whitespace-nowrap">
                        Consultar estado
                    </button>
                </div>
            </form>

            @if(isset($resultado))
                <div class="mt-6" id="resultado">
                    @if($resultado['encontrado'])
                        @php
                            $estadoColors = [
                                'RECIBIDO'   => 'bg-gray-500/20 border-gray-500 text-gray-400',
                                'DIAGNOSTICO'=> 'bg-blue-500/20 border-blue-500 text-blue-400',
                                'REPARACION' => 'bg-amber-500/20 border-amber-500 text-amber-400',
                                'LISTO'      => 'bg-green-500/20 border-green-500 text-green-400',
                                'ENTREGADO'  => 'bg-purple-500/20 border-purple-500 text-purple-400',
                            ];
                            $estado = $resultado['orden']->estado ?? 'RECIBIDO';
                            $colorClass = $estadoColors[$estado] ?? 'bg-amber-500/20 border-amber-500 text-amber-400';
                        @endphp
                        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <h3 class="text-lg font-bold text-white">
                                    {{ $resultado['vehiculo']->marca ?? '' }} {{ $resultado['vehiculo']->modelo ?? '' }}
                                    <span class="text-gray-400 text-sm font-normal ml-2">{{ strtoupper($resultado['vehiculo']->placa ?? '') }}</span>
                                </h3>
                                <span class="px-3 py-1 rounded-full border text-sm font-semibold {{ $colorClass }}">
                                    {{ $estado }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Orden N°</p>
                                    <p class="text-white font-medium">#{{ $resultado['orden']->id }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Mecánico asignado</p>
                                    <p class="text-white font-medium">
                                        @if($resultado['mecanico'])
                                            {{ $resultado['mecanico']->nombre }} {{ $resultado['mecanico']->apellido }}
                                        @else
                                            Sin asignar
                                        @endif
                                    </p>
                                </div>
                                @if(isset($resultado['servicios']) && $resultado['servicios']->isNotEmpty())
                                <div class="col-span-2">
                                    <p class="text-gray-500 mb-1">Servicios en curso</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($resultado['servicios'] as $svc)
                                        <span class="px-2 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded text-xs">{{ $svc->nombre }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @if(isset($resultado['fotos']) && $resultado['fotos']->isNotEmpty())
                                <div class="col-span-2">
                                    <p class="text-gray-500 mb-2">Fotos</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($resultado['fotos'] as $foto)
                                        <a href="{{ $foto->cloudinary_url }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $foto->cloudinary_url }}" class="w-20 h-20 object-cover rounded-lg border border-gray-600 hover:opacity-90 transition">
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @if(isset($resultado['orden']->fecha_entrega) && $resultado['orden']->fecha_entrega)
                                <div>
                                    <p class="text-gray-500">Fecha entrega</p>
                                    <p class="text-white font-medium">
                                        {{ \Carbon\Carbon::parse($resultado['orden']->fecha_entrega)->format('d/m/Y') }}
                                    </p>
                                </div>
                                @endif
                                @if(isset($resultado['orden']->diagnostico) && $resultado['orden']->diagnostico)
                                <div class="col-span-2">
                                    <p class="text-gray-500">Diagnóstico</p>
                                    <p class="text-white">{{ $resultado['orden']->diagnostico }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 text-center">
                            <p class="text-gray-400">No se encontró ningún vehículo con la placa
                                <span class="text-amber-400 font-bold">{{ strtoupper($resultado['placa']) }}</span>.
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="bg-gray-950 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-white mb-3">Nuestros Servicios</h2>
                <p class="text-gray-400 max-w-xl mx-auto">Contamos con los equipos y especialistas para mantener tu vehículo en óptimas condiciones.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $servicios = [
                    ['icon' => '🔧', 'name' => 'Mantenimiento Preventivo',  'desc' => 'Cambio de aceite, filtros y revisión completa según kilometraje.'],
                    ['icon' => '🔩', 'name' => 'Mecánica General',           'desc' => 'Reparación de motor, transmisión, frenos y suspensión.'],
                    ['icon' => '🚗', 'name' => 'Suspensión y Dirección',      'desc' => 'Revisión de amortiguadores, rótulas, terminales y barra estabilizadora.'],
                    ['icon' => '⛽', 'name' => 'Sistema de Combustible',     'desc' => 'Limpieza de inyectores, bomba de combustible y filtros.'],
                    ['icon' => '🔋', 'name' => 'Sistema Eléctrico',          'desc' => 'Diagnóstico y reparación de fallas eléctricas y batería.'],
                    ['icon' => '🛢️', 'name' => 'Cambio de Fluidos',          'desc' => 'Aceite de caja, dirección hidráulica, líquido de frenos y refrigerante.'],
                ];
                @endphp
                @foreach($servicios as $svc)
                <div class="bg-gray-900 border border-gray-800 hover:border-amber-500/50 rounded-2xl p-6 transition duration-300 group">
                    <div class="text-4xl mb-4">{{ $svc['icon'] }}</div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-amber-400 transition">{{ $svc['name'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $svc['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" class="bg-gray-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-white mb-3">Contáctanos</h2>
                <p class="text-gray-400">Estamos listos para atenderte</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-3xl mb-3">📞</div>
                    <h4 class="text-white font-semibold mb-1">Teléfono</h4>
                    <p class="text-amber-400 font-medium">71056485</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-3xl mb-3">📍</div>
                    <h4 class="text-white font-semibold mb-1">Dirección</h4>
                    <p class="text-gray-400 text-sm">Av. Principal, Local 4B<br>Centro Comercial El Taller</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 text-center">
                    <div class="text-3xl mb-3">🕐</div>
                    <h4 class="text-white font-semibold mb-1">Horario</h4>
                    <p class="text-gray-400 text-sm">Lun – Vie: 8am – 6pm<br>Sáb: 8am – 1pm</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-950 border-t border-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500 text-sm">© {{ date('Y') }} Taller Mecánico Eusebio. Todos los derechos reservados.</p>
        </div>
    </footer>

@if(isset($resultado))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('resultado');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>
@endif

</body>
</html>

