@props(['name' => 'modal',
    'title' => '',
    'subtitle' => '',
    'size' => 'md', // xs, sm, md, lg, xl, 2xl, full
    'closable' => true,
    'footer' => true,
    'backdrop' => true, // Cliquer sur backdrop pour fermer
    'showHeader' => true,
    'showFooter' => true,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
    'icon' => '', // Icône à afficher dans le header
    'iconColor' => 'blue', // Couleur de l`icône
])

<div x-data="{ open: false }"
     x-on:open-modal-{{ $name }}.window="open = true"
     x-on:close-modal-{{ $name }}.window="open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-75"
         @if($backdrop) @click="open = false" @endif>
    </div>

    <!-- Modal -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="flex items-center justify-center min-h-screen px-4 py-6 sm:p-0">

        <div @click.stop
             class="relative w-full max-w-{{ $size ?? '' }} bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all">

            @if($showHeader)
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 {{ $headerClass }}">
                    <div class="flex items-center space-x-3">
                        @if($icon)
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-{{ $iconColor }}-100 dark:bg-{{ $iconColor }}-900">
                                    <i class="fas fa-{{ $icon }} text-{{$iconColor}}-600 dark:text-{{$iconColor}}-400 text-lg"></i>
                                </div>
                            </div>
                        @endif
                        <div>
                            @if($title)
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
                            @endif
                            @if($subtitle)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>

                    @if($closable)
                        <button @click="open = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    @endif
                </div>
            @endif


                <!-- Body -->
            <div class="px-6 py-4 {{ $bodyClass }}">
                {{ $slot }}
            </div>

            @if($showFooter && ($footer || isset($actions)))
                <!-- Footer -->
                <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg {{ $footerClass }}">
                    @if(isset($actions))
                        {{ $actions }}
                    @elseif($footer)
                        <!-- Footer par défaut -->
                        <button @click="open = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                            Annuler
                        </button>
                        <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                            Confirmer
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fonctions helpers pour ouvrir/fermer les modals
    window.openModal = function(name) {
        window.dispatchEvent(new CustomEvent('open-modal-' + name));
    }

    window.closeModal = function(name) {
        window.dispatchEvent(new CustomEvent('close-modal-' + name));
    }
</script>
@endpush
