<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-100">
    <tr>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Valeur
        </th>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Utilisation
        </th>
        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
        </th>
    </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
    @forelse($inactiveValues as $value)
        <tr class="opacity-75 items-center">
            <td class="px-3 py-2 flex items-center">
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
                <div class="text-sm text-gray-900">{{ $value->value }}</div>
            </td>
            <td class="px-3 py-2">
                <div class="flex flex-col space-y-1">
                    <div class="flex items-center">
                        <i class="fa-solid fa-boxes w-3 h-3 mr-3 text-blue-500"></i>
                        <span class="text-xs font-medium">{{ $value->articles_count ?? 0 }}</span>
                        <span class="text-xs text-gray-500 ml-1">articles</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-tags w-3 h-3 mr-3 text-green-500"></i>
                        <span class="text-xs font-medium">{{ $value->variants_count ?? 0 }}</span>
                        <span class="text-xs text-gray-500 ml-1">variants</span>
                    </div>
                </div>
            </td>
            <td class="px-3 py-2 text-right">
                <form action="{{ route('settings.attributes.values.activate', [$attribute, $value]) }}"
                      method="POST"
                      class="inline"
                      data-ajax>
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="text-green-600 hover:text-green-900 text-sm font-medium"
                            title="Réactiver cette valeur">
                        <i class="fa-solid fa-eye w-4 h-4"></i>
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">
                Aucune valeur archivée
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
