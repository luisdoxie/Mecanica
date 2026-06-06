@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="mb-4 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
    <span class="text-lg leading-none">✅</span>
    <p class="flex-1">{{ session('success') }}</p>
    <button @click="show = false" class="text-green-600 hover:text-green-800 font-bold leading-none">×</button>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     class="mb-4 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
    <span class="text-lg leading-none">❌</span>
    <p class="flex-1">{{ session('error') }}</p>
    <button @click="show = false" class="text-red-600 hover:text-red-800 font-bold leading-none">×</button>
</div>
@endif

@if ($errors->any())
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
    <p class="font-semibold mb-1">Por favor corrige los siguientes errores:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
