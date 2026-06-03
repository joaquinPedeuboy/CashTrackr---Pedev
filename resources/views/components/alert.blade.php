@props(['type' => 'success', 'message' => ''])

@php
    
    $colors = [
        'error' => 'bg-red-100 text-red-700 border-red-700',
        'success' => 'bg-green-100 text-green-700 border-green-700'
    ];

    $class = $colors[$type] ?? $colors['success'];
@endphp

@if ($message)
    <p class="my-10 text-center border-l-8 py-3 font-bold uppercase {{ $class }}">
        {{ $message }}
    </p>
@endif