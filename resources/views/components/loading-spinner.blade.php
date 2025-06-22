@if($overlay)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="loading-overlay">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex flex-col items-center space-y-4">
            <div class="animate-spin rounded-full {{ $size }} border-b-2 border-blue-500"></div>
            <p class="text-gray-700 dark:text-gray-300">{{ $message }}</p>
        </div>
    </div>
@else
    <div class="flex items-center justify-center py-8 space-x-3" id="table-loading">
        <div class="animate-spin rounded-full {{ $size }} border-b-2 border-blue-500"></div>
        <span class="text-gray-600 dark:text-gray-400">{{ $message }}</span>
    </div>
@endif
