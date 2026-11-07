@props(['compact' => false])

<div {{ $attributes->class(['auth-brand', 'auth-brand--compact' => $compact]) }}>
    <span class="auth-brand-mark" aria-hidden="true">
        <i class="bi bi-bus-front"></i>
    </span>
    <span class="auth-brand-text">
        <span class="auth-brand-name">Línea 61</span>
        <span class="auth-brand-tagline">Control y monitoreo de flota</span>
    </span>
</div>
