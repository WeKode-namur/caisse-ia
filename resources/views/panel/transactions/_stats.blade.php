{{-- Partial pour les statistiques des transactions --}}
@foreach($stats as $stat)
    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                        {{ $stat['value'] }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</div>
                </div>
                <div class="w-12 h-12 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-lg flex items-center justify-center">
                    <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
                </div>
            </div>
        </div>
    </div>
@endforeach 