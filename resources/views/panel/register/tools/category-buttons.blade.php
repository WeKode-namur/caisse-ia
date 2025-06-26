
<button onclick="clearFilters()" class="bg-gray-500 dark:bg-gray-600 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">
    <i class="fas fa-refresh"></i>
</button>
@foreach($categories as $category)
    <button onclick="filterByCategory({{ $category->id }})"
            class="bg-blue-500 dark:bg-blue-800 text-white text-sm px-3 py-1.5 flex items-center justify-center rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 transition duration-300 ease-in-out">
        {{ Str::limit($category->name, 10) }}
    </button>
@endforeach
