@props(['type' => 'success'])

@php
    $icons = [
        'success' => 'check-circle',
        'info' => 'info-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'exclamation-octagon',
    ];
@endphp

<div {{ $attributes->class(['auth-alert', 'auth-alert--'.$type]) }} role="{{ $type === 'danger' ? 'alert' : 'status' }}">
    <i class="bi bi-{{ $icons[$type] ?? $icons['info'] }}" aria-hidden="true"></i>
    <div>{{ $slot }}</div>
</div>
