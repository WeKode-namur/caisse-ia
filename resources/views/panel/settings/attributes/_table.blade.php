<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
    <tr>
        <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Valeur
        </th>
        <th class="hidden lg:table-cell px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Utilisation
        </th>
        <th class="px-2 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Actions
        </th>
    </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700" id="sortable-values">
    @forelse($values as $value)
        <tr data-id="{{ $value->id }}" class="{{ !$value->actif ? 'bg-gray-50 opacity-75' : '' }} items-center">
            <td class="px-3 lg:px-6 py-3 lg:py-4 flex items-center gap-2">
                <i class="fa-solid fa-grip-vertical cursor-move text-gray-400 mr-2"></i>
                <div class="flex items-center">
                    @if($attribute->type === 'color')
                        <span
                            class="inline-block w-5 h-5 relative rounded-full border dark:border-gray-700 mr-2 align-middle text-center"
                            style="background: {{ $value->second_value ?? '' }}">
                            @if(!$value->second_value)
                                <span
                                    class="absolute top-1/2 left-1/2 p-0 -translate-y-1/2 -translate-x-1/2 text-xs text-slate-500">
                                    <i class="fas fa-droplet"></i>
                                </span>
                            @endif
                        </span>
                    @endif
                    @if(!$value->actif)
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">
                            <i class="fa-solid fa-eye-slash w-3 h-3 mr-1"></i>
                            Inactif
                        </span>
                    @endif
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value->value }}</div>
                </div>
            </td>
            <td class="hidden lg:table-cell px-3 lg:px-6 py-3 lg:py-4">
                <div class="flex flex-col space-y-1">
                    <div class="flex items-center">
                        <i class="fa-solid fa-boxes w-4 h-4 mr-3 text-blue-500"></i>
                        <span class="text-sm font-medium dark:text-white">{{ $value->articles_count ?? 0 }}</span>
                        <span class="text-sm text-gray-500 ml-1">articles</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-tags w-4 h-4 mr-3 text-green-500"></i>
                        <span class="text-sm font-medium dark:text-white">{{ $value->variants_count ?? 0 }}</span>
                        <span class="text-sm text-gray-500 ml-1">variants</span>
                    </div>
                </div>
            </td>
            <td class="px-2 lg:px-6 py-3 lg:py-4 text-sm font-medium">
                <div class="flex items-center justify-end space-x-1 lg:space-x-3">
                    <button
                        onclick="editValue({{ $value->id }}, '{{ addslashes($value->value) }}', '{{ addslashes($value->second_value) }}', {{ $value->order }})"
                        class="group relative p-1 lg:p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                        title="Modifier cette valeur">
                        <i class="fa-solid fa-pen w-4 h-4 lg:w-5 lg:h-5"></i>
                    </button>

                    @if($value->actif)
                        <!-- Désactiver -->
                        <button type="button"
                                onclick="showDeactivateValueModal({{ $value->id }}, '{{ addslashes($value->value) }}', {{ $value->articles_count ?? 0 }}, {{ $value->variants_count ?? 0 }})"
                                class="group relative p-1 lg:p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                title="Désactiver cette valeur">
                            <i class="fa-solid fa-eye-slash w-4 h-4 lg:w-5 lg:h-5"></i>
                        </button>
                    @else
                        <!-- Réactiver -->
                        <form action="{{ route('settings.attributes.values.activate', [$attribute, $value]) }}"
                              method="POST"
                              class="inline"
                              data-ajax>
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="group relative p-1 lg:p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                    title="Réactiver cette valeur">
                                <i class="fa-solid fa-eye w-4 h-4 lg:w-5 lg:h-5"></i>
                            </button>
                        </form>
                    @endif
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
