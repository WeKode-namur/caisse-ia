<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
    <tr>
        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Valeur
        </th>
        <th class="hidden md:table-cell px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Description
        </th>
        <th class="px-2 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Actions
        </th>
    </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700" id="sortable-values">
    @forelse($values as $value)
        <tr data-id="{{ $value->id }}">
            <td class="px-3 lg:px-6 py-3 lg:py-4 flex items-center gap-2">
                <i class="fa-solid fa-grip-vertical cursor-move text-gray-400 mr-2"></i>
                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value->value }}</div>
            </td>
            <td class="hidden md:table-cell px-3 lg:px-6 py-3 lg:py-4">
                <div
                    class="text-sm text-gray-900 dark:text-gray-200">{{ $value->description ?: 'Aucune description' }}</div>
            </td>
            <td class="px-2 lg:px-6 py-3 lg:py-4 text-sm font-medium">
                <div class="flex items-center justify-end space-x-1 lg:space-x-3">
                    <button
                        onclick="editValue({{ $value->id }}, '{{ addslashes($value->value) }}', '{{ addslashes($value->description) }}', {{ $value->order }})"
                        class="group relative p-1 lg:p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                        title="Modifier cette valeur">
                        <i class="fa-solid fa-pen w-4 h-4 lg:w-5 lg:h-5"></i>
                    </button>
                    <form action="{{ route('settings.attributes.values.destroy', [$attribute, $value]) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette valeur ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="group relative p-1 lg:p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                title="Supprimer cette valeur">
                            <i class="fa-solid fa-trash w-4 h-4 lg:w-5 lg:h-5"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune valeur définie pour cet attribut.</td>
        </tr>
    @endforelse
    </tbody>
</table>
