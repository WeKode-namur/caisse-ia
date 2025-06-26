<!--
    #listing-items      - Affiche le panier
-->
<div class="border-r dark:border-gray-700 lg:w-1/3 flex flex-col hover:z-10">
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
        <!-- Les articles du panier seront injectés ici par register.js -->
    </div>
    <div class="border-y border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 duration-300">
        <div class="p-4 flex gap-6 items-center">
            <div class="flex-1 cart-totals">
                <!-- Totaux du panier générés dynamiquement -->
            </div>
            <div class="text-right">
                <button id="pay-button" class="bg-green-500 dark:bg-green-700 text-white rounded px-4 py-2 hover:bg-green-600 duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-cash-register mr-3"></i>
                    Payer
                </button>
            </div>
        </div>
    </div>
</div>
