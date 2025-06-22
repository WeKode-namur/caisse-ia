{{-- resources/views/components/wave-dots-pattern.blade.php --}}
@props([
    'opacity' => '0.6',
    'darkOpacity' => '0.4',
    'duration' => '8s',
    'dotSize' => '2px',
    'spacing' => '40px',
    'idRandom' => Str::random(5)
])

<div {{ $attributes->merge(['class' => 'absolute inset-0 overflow-hidden pointer-events-none']) }}>
    <div
        class="wave-dots-pattern-{{ $idRandom }} absolute inset-0"
        style="
            --dot-size: {{ $dotSize }};
            --dot-spacing: {{ $spacing }};
            --wave-duration: {{ $duration }};
            --wave-opacity: {{ $opacity }};
            --wave-dark-opacity: {{ $darkOpacity }};
        "
    ></div>

    <style>
        .wave-dots-pattern-{{ $idRandom }} {
            background-image: radial-gradient(circle, rgba(0, 0, 0, 0.2) var(--dot-size), transparent var(--dot-size));
            background-size: var(--dot-spacing) var(--dot-spacing);
            opacity: var(--wave-opacity);
            animation: wave-effect-{{ $idRandom }} var(--wave-duration) ease-in-out infinite;
        }

        .dark .wave-dots-pattern-{{ $idRandom }} {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.2) var(--dot-size), transparent var(--dot-size));
            opacity: var(--wave-dark-opacity);
        }

        @keyframes wave-effect-{{ $idRandom }} {
            0%, 100% {
                transform: translateY(0) scale(1);
                filter: blur(0px);
            }
            25% {
                transform: translateY(-10px) scale(1.1);
                filter: blur(0.5px);
            }
            50% {
                transform: translateY(0) scale(1.2);
                filter: blur(1px);
            }
            75% {
                transform: translateY(10px) scale(1.1);
                filter: blur(0.5px);
            }
        }

        .wave-dots-pattern-{{ $idRandom }}::before,
        .wave-dots-pattern-{{ $idRandom }}::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(0, 0, 0, 0.1) var(--dot-size), transparent var(--dot-size));
            background-size: var(--dot-spacing) var(--dot-spacing);
            animation: wave-effect-{{ $idRandom }} var(--wave-duration) ease-in-out infinite;
        }

        .wave-dots-pattern-{{ $idRandom }}::before {
            animation-delay: -2s;
            transform: translateX(20px);
        }

        .wave-dots-pattern-{{ $idRandom }}::after {
            animation-delay: -4s;
            transform: translateX(-20px);
        }

        .dark .wave-dots-pattern-{{ $idRandom }}::before,
        .dark .wave-dots-pattern-{{ $idRandom }}::after {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) var(--dot-size), transparent var(--dot-size));
        }
    </style>
</div>
