<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- En-tête -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <h1 class="font-bold lg:text-2xl text-xl">Inventaire</h1>
                    <a href="{{ route('inventory.create.index') }}" class="bg-green-500 text-white rounded py-1 px-3 hover:opacity-75 hover:scale-105">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>

                <!-- Filtres -->
                <div class="p-4">
                    <form id="filters-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        Filtre
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
{{--                @foreach($stats as $stat)--}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
{{--                                <div class="text-2xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">--}}
                                        {{--{{ $stat['value'] }}--}}

                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        Value

                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
{{--                                        {{ $stat['label'] }}--}}
                                        Label
                                    </div>
                                </div>
{{--                                <div class="w-12 h-12 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-lg flex items-center justify-center">--}}
{{--                                    <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>--}}
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cubes text-blue-600 dark:text-blue-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
{{--                @endforeach--}}
            </div>

            <!-- Tableau des articles -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    <!-- Container pour le tableau -->
                    <div id="clients-container">
                        <!-- Le tableau sera chargé ici via AJAX -->
                        <x-loading-spinner message="Chargement des articles..." />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Chargement du tableau
        </script>
    @endpush
</x-app-layout>
