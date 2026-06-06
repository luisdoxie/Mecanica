@props(['label', 'name', 'options' => [], 'selected' => null, 'required' => false, 'placeholder' => 'Seleccione...'])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white ' . ($errors->has($name) ? 'border-red-400 bg-red-50' : 'border-gray-300')]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $val => $label)
            <option value="{{ $val }}" {{ old($name, $selected) == $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
