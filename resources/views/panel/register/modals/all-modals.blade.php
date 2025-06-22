
@if(config('app.register_customer_management', false))
    @include('panel.register.modals.client-modal')
@endif

@include('panel.register.modals.discount-modal')
@include('panel.register.modals.items-unknown-modal')
@include('panel.register.modals.gift-card-modal')
@include('panel.register.modals.return-modal')
@include('panel.register.modals.note-modal')
