@php
    $count = count($versions);
@endphp
@if($count > 0)
    <x-modal name="changelog" size="7xl" id="changelog-modal" :footer="false">
        <x-slot name="title">
            Nouveautés ({{ $versions[0] }} → {{ $versions[$count-1] }})
        </x-slot>
        <div class="prose dark:prose-invert max-w-none overflow-y-auto overflow-x-hidden flex flex-col gap-6 p-6" style="max-height:75vh">
            @foreach($versions as $v)
                <div class="rounded border shadow hover:shadow-lg duration-500 p-3 z-30">
                    {!! $contents[$v] !!}
                    <div class="text-end text-xs">{{$v}}</div>
                </div>
            @endforeach
        </div>
        <x-slot:actions>
            <div class="flex justify-end w-full">
                <x-button id="changelog-close">J'ai lu et compris</x-button>
            </div>
        </x-slot>
    </x-modal>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            window.openModal('changelog');
            const close = document.getElementById('changelog-close');
            const versions = @json($versions);
            if(close) close.onclick = function() {
                fetch('{{ route('user.seen-version') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ version: versions[versions.length-1] })
                }).then(() => window.location.reload());
            };
        });
    </script>
@endif
