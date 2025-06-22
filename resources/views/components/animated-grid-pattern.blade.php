{{-- resources/views/components/animated-grid-pattern.blade.php --}}
@props([
    'width' => 40,
    'height' => 40,
    'x' => -1,
    'y' => -1,
    'strokeDasharray' => 0,
    'numSquares' => 50,
    'maxOpacity' => 0.5,
    'duration' => 4,
    'repeatDelay' => 0.5,
    'className' => '',
    'idRandom' => Str::random(8)
])

<div {{ $attributes->merge(['class' => "absolute inset-0 h-full w-full overflow-hidden {$className}"]) }}>
    <svg
        class="magic-animated-grid-{{ $idRandom }}"
        width="100%"
        height="100%"
        xmlns="http://www.w3.org/2000/svg"
    >
        <defs>
            <pattern
                id="animated-grid-pattern-{{ $idRandom }}"
                width="{{ $width }}"
                height="{{ $height }}"
                patternUnits="userSpaceOnUse"
                x="{{ $x }}"
                y="{{ $y }}"
            >
                <path
                    d="M {{ $width }} 0 L 0 0 0 {{ $height }}"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1"
                    @if($strokeDasharray) stroke-dasharray="{{ $strokeDasharray }}" @endif
                />
            </pattern>
        </defs>

        <rect width="100%" height="100%" fill="url(#animated-grid-pattern-{{ $idRandom }})" />

        @php
            $squares = collect(range(0, $numSquares - 1))->map(function($i) {
                return [
                    'id' => $i,
                    'x' => rand(0, 50) * 40, // Distribute across viewport
                    'y' => rand(0, 50) * 40,
                ];
            });
        @endphp

        @foreach($squares as $square)
            <rect
                x="{{ $square['x'] }}"
                y="{{ $square['y'] }}"
                width="{{ $width }}"
                height="{{ $height }}"
                fill="currentColor"
                class="animated-square-{{ $idRandom }}"
                style="
                    animation-delay: {{ $square['id'] * $repeatDelay }}s;
                    animation-duration: {{ $duration }}s;
                "
            />
        @endforeach
    </svg>

    <style>
        .magic-animated-grid-{{ $idRandom }} {
            color: rgba(0, 0, 0, 0.15);
            opacity: {{ $maxOpacity }};
            mask-image: radial-gradient(ellipse 70% 80% at 50% 0%, #000 60%, transparent 110%);
            -webkit-mask-image: radial-gradient(ellipse 70% 80% at 50% 0%, #000 60%, transparent 110%);
        }

        .dark .magic-animated-grid-{{ $idRandom }} {
            color: rgba(255, 255, 255, 0.4);
            opacity: {{ $maxOpacity * 0.6 }};
        }

        .animated-square-{{ $idRandom }} {
            opacity: 0;
            animation: fadeInOut{{ $idRandom }} {{ $duration }}s ease-in-out infinite;
        }

        @keyframes fadeInOut{{ $idRandom }} {
            0%, 20% {
                opacity: 0;
            }
            40% {
                opacity: {{ $maxOpacity }};
            }
            80%, 100% {
                opacity: 0;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .magic-animated-grid-{{ $idRandom }} {
                opacity: {{ $maxOpacity * 0.7 }};
            }
        }
    </style>
</div>
