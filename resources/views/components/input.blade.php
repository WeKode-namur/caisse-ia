@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-gray-400 focus:ring-gray-400 rounded-md shadow-sm py-1.5 px-2']) !!}>
