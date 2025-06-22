<!--
    #listing-items      - Affiche le panier
-->
<div class="border-r dark:border-gray-700 lg:w-1/3 flex flex-col z-0">
    <div class="border-b dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-0.5 flex gap-6 text-gray-400 dark:text-gray-500 text-xs">
        <div class="">Nom de l'article</div>
        <div class="flex flex-1 gap-6">
            <div class="w-full flex gap-6 justify-end">
                <p>Prix U.</p>
                <div class="w-24 text-center">Nombre</div>
                <p>Prix T.</p>
            </div>
            <div class="w-8 text-end"></div>
        </div>
    </div>
    <div id="listing-items" class="h-full">
        <div class="border-b dark:border-gray-700 hover:scale-105 duration-300 bg-white dark:bg-gray-800 hover:border hover:shadow-lg p-3 flex gap-6 group">
            <div class="">
                <div class="flex items-center gap-3">
                    <p>Chemise d'Hawaï</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">- XS</p>
                </div>
                <div class="flex items-center gap-3 text-gray-500 text-xs">
                    <i class="fas fa-barcode"></i>
                    <p>012354523566</p>
                </div>
            </div>
            <div class="flex flex-1 gap-6 items-center">
                <div class="w-full flex gap-6 items-center justify-end">
                    <p>12 €</p>
                    <div class="flex w-24">
                        <button type="button" class="border-y border-l dark:border-gray-700 py-1 px-2 rounded-l group-hover:bg-red-50 dark:group-hover:bg-green-950 group-hover:hover:bg-red-400 dark:group-hover:hover:bg-green-600 duration-300">-</button>
                        <div class="border dark:border-gray-700 py-1 px-2 text-center w-12">2</div>
                        <button type="button" class="border-y border-r dark:border-gray-700 py-1 px-2 rounded-r group-hover:bg-green-50 dark:group-hover:bg-red-950 group-hover:hover:bg-green-400 dark:group-hover:hover:bg-red-600 duration-300">+</button>
                    </div>
                    <p>24 €</p>
                </div>
                <button class="bg-red-300 dark:bg-red-800/50 group-hover:bg-red-500 text-white rounded w-8 h-8 text-sm text-center hover:bg-red-700 duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="border-y border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 duration-300">
        <div class="p-4 flex gap-6 items-center">
            <div class="flex-1">
                <table>
                    <tbody class="*:*:px-2 text-gray-500">
                        <tr>
                            <td class="text-end">Articles</td>
                            <td class="font-semibold">2</td>
                        </tr>
                        <tr>
                            <td class="text-end">Prix TVAC</td>
                            <td class="font-semibold">24 €</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-right">
                <button class="bg-green-500 text-white rounded px-4 py-2 hover:bg-green-600 duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-cash-register mr-3"></i>
                    Payer
                </button>
            </div>
        </div>
    </div>
</div>
