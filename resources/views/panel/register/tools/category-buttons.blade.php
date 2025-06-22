
@foreach($categories as $category)
    <button onclick="filterByCategory({{ $category->id }})"
            class="bg-blue-500 text-white text-sm px-3 py-1.5 flex items-center justify-center rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 transition duration-300 ease-in-out">
        {{ Str::limit($category->name, 10) }}
    </button>
@endforeach
