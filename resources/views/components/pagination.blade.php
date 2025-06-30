@props(['currentPage', 'lastPage'])
@if ($lastPage > 1)
    <nav class="flex justify-center space-x-1 mt-6" aria-label="Pagination">
        {{-- Page précédente --}}
        <button class="pagination-btn px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-blue-100"
                data-page="{{ max(1, $currentPage - 1) }}" @if($currentPage == 1) disabled @endif>
            &lt;
        </button>

        {{-- Pages (max 5 visibles) --}}
        @php
            $pages = [];
            if ($lastPage <= 5) {
                for ($i = 1; $i <= $lastPage; $i++) $pages[] = $i;
            } else {
                if ($currentPage <= 3) {
                    // Début
                    $pages = [1, 2, 3, 4, '...', $lastPage];
                } elseif ($currentPage >= $lastPage - 2) {
                    // Fin
                    $pages = [1, '...', $lastPage-3, $lastPage-2, $lastPage-1, $lastPage];
                } else {
                    // Milieu
                    $pages = [1, '...', $currentPage-1, $currentPage, $currentPage+1, '...', $lastPage];
                }
            }
        @endphp
        @foreach ($pages as $page)
            @if ($page === '...')
                <span class="px-2 py-1 text-gray-400">...</span>
            @else
                <button
                    class="pagination-btn px-3 py-1 rounded {{ $page == $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-100' }}"
                    data-page="{{ $page }}"
                    @if($page == $currentPage) disabled @endif
                >
                    {{ $page }}
                </button>
            @endif
        @endforeach

        {{-- Page suivante --}}
        <button class="pagination-btn px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-blue-100"
                data-page="{{ min($lastPage, $currentPage + 1) }}" @if($currentPage == $lastPage) disabled @endif>
            &gt;
        </button>
    </nav>
@endif 