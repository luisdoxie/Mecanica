<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="text-8xl mb-6">🔒</div>
        <h1 class="text-4xl font-black text-white mb-3">Acceso Denegado</h1>
        <p class="text-gray-400 mb-8">No tienes permiso para acceder a esta sección.</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ url()->previous() }}"
               class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl text-sm transition">
                ← Volver
            </a>
            <a href="{{ route('home') }}"
               class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-semibold rounded-xl text-sm transition">
                Ir al inicio
            </a>
        </div>
    </div>
</body>
</html>
