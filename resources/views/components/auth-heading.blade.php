@props([
    'icon' => null,
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'start',
])

<div {{ $attributes->class(['auth-heading', 'auth-heading--center' => $align === 'center']) }}>
    @if ($icon)
        <span class="auth-heading-icon" aria-hidden="true">
            <i class="bi {{ $icon }}"></i>
        </span>
    @endif

    @if ($eyebrow)
        <span class="auth-eyebrow">{{ $eyebrow }}</span>
    @endif

    <h1 class="auth-title">{{ $title }}</h1>

    @if ($description)
        <p class="auth-subtitle">{{ $description }}</p>
    @endif
</div>
