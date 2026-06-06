<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion &mdash; Taller Mecanico Eusebio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500 rounded-full mb-4">
                <svg class="w-9 h-9 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Taller Mecanico Eusebio</h1>
            <p class="text-gray-400 mt-1">Ingresa a tu cuenta</p>
        </div>

        <div class="bg-gray-800 rounded-2xl shadow-2xl p-8">
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-900/50 border border-red-700 rounded-lg text-red-300 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Correo electronico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400
                               focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                        placeholder="correo@ejemplo.com">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Contrasena</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400
                               focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                        placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                </div>

                <div class="flex items-center mb-6">
                    <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 accent-amber-500">
                        Recordarme
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition duration-200 text-base tracking-wide">
                    Iniciar sesion
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-amber-400 text-sm transition">
                &larr; Volver a la pagina principal
            </a>
        </div>
    </div>

</body>
</html>
