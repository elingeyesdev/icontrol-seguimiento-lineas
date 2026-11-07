@props([
    'name',
    'label',
    'type' => 'text',
    'icon' => null,
    'placeholder' => null,
    'autocomplete' => null,
    'required' => false,
    'autofocus' => false,
    'value' => null,
    'hint' => null,
])

@php
    $hasError = $errors->has($name);
    $errorId = $name.'-error';
    $hintId = $hint ? $name.'-hint' : null;
    $describedBy = trim(implode(' ', array_filter([$hasError ? $errorId : null, $hintId])));
@endphp

<div {{ $attributes->class('auth-field') }}>
    <label for="{{ $name }}" class="auth-label">
        {{ $label }}@if ($required)<span class="auth-required" aria-hidden="true">*</span>@endif
    </label>

    <div @class(['auth-control', 'auth-control--error' => $hasError])>
        @if ($icon)
            <span class="auth-control-icon" aria-hidden="true">
                <i class="bi {{ $icon }}"></i>
            </span>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except('class')->merge(['class' => 'auth-input']) }}
        >
    </div>

    @if ($hint)
        <p class="auth-hint" id="{{ $hintId }}">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="auth-error" id="{{ $errorId }}">
            <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
