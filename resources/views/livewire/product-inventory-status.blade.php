<div wire:poll.5s="refreshInventory" wire:loading.class="opacity-70" class="inline-flex items-center">
    @switch($status)
        @case('in_stock')
            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                在庫あり
                @if ($quantity > 0)
                    ({{ $quantity }})
                @endif
            </span>
            @break

        @case('limited')
            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                残りわずか
                @if ($quantity > 0)
                    ({{ $quantity }})
                @endif
            </span>
            @break

        @default
            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                SOLD OUT
            </span>
    @endswitch
</div>
