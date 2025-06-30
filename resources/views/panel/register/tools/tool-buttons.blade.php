
<button id="btn-clients-list-modal" class="disabled:opacity-50 disabled:cursor-not-allowed bg-blue-500 dark:bg-blue-800 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">
    <i class="fas fa-user-plus"></i>
</button>

<button id="btn-discount-modal"
        class="disabled:opacity-50 disabled:cursor-not-allowed bg-blue-500 dark:bg-blue-800 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">
    <i class="fas fa-percent"></i>
</button>

<button id="btn-items-unknown-modal" class="disabled:opacity-50 disabled:cursor-not-allowed bg-blue-500 dark:bg-blue-800 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">
    <div class="w-6 h-6 border border-dotted rounded-full flex items-center justify-center">
        <i class="fas fa-question"></i>
    </div>
</button>

<button @click="openGiftCardModal()" disabled class="disabled:opacity-50 disabled:cursor-not-allowed bg-blue-500 dark:bg-blue-800 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">
    <i class="fas fa-gift"></i>
</button>

<button @click="openReturnModal()" disabled class="disabled:opacity-50 disabled:cursor-not-allowed bg-blue-500 dark:bg-blue-800 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">
    <i class="fas fa-right-left"></i>
</button>

{{--<button @click="openNoteModal()" disabled class="disabled:opacity-50 disabled:cursor-not-allowed bg-blue-500 text-white text-sm px-3 py-1.5 flex items-center justify-center w-12 h-8 rounded shadow hover:shadow-lg hover:scale-105 hover:bg-opacity-75 hover:dark:bg-opacity-50 transition duration-300 ease-in-out">--}}
{{--    <i class="fas fa-comment"></i>--}}
{{--</button>--}}



<!-- Script corrigé -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('registerTools', () => ({
            @if(config('app.register_customer_management', false))
            showClientModal: false,
            @endif
            showDiscountModal: false,
            showUnknownItemModal: false,
            showGiftCardModal: false,
            showReturnModal: false,
            // showNoteModal: false,

            @if(config('app.register_customer_management', false))
                openClientModal() {
                    this.closeAllModals();
                    this.showClientModal = true;
                },
            @endif

            openDiscountModal() {
                this.closeAllModals();
                this.showDiscountModal = true;
            },

            openUnknownItemModal() {
                this.closeAllModals();
                this.showUnknownItemModal = true;
            },

            openGiftCardModal() {
                this.closeAllModals();
                this.showGiftCardModal = true;
            },

            openReturnModal() {
                this.closeAllModals();
                this.showReturnModal = true;
            },
            //
            // openNoteModal() {
            //     this.closeAllModals();
            //     this.showNoteModal = true;
            // },

            closeAllModals() {
                @if(config('app.register_customer_management', false))
                    this.showClientModal = false;
                @endif
                    this.showDiscountModal = false;
                this.showUnknownItemModal = false;
                this.showGiftCardModal = false;
                this.showReturnModal = false;
                // this.showNoteModal = false;
            }
        }));
    });
</script>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            @if(config('app.register_customer_management', false))
                document.getElementById('btn-clients-list-modal').addEventListener('click', function () {
                    window.openModal('clients-list-modal');
                });
            @endif
            document.getElementById('btn-discount-modal').addEventListener('click', function () {
                window.openModal('discount-modal');
            });

            document.getElementById('btn-items-unknown-modal').addEventListener('click', function () {
                window.openModal('items-unknown-modal');
            });
        });
    </script>
@endpush
