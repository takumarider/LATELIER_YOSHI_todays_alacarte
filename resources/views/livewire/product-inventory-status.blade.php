<div wire:poll.5s="refreshInventory" class="inline-flex items-center gap-1">
    {{-- ポーリング中のスピナー --}}
    <span
        wire:loading
        wire:target="refreshInventory"
        class="inline-block h-3 w-3 animate-spin rounded-full border-2 border-gray-300 border-t-gray-600"
    ></span>

    <span wire:loading.remove wire:target="refreshInventory">
        @if ($hasError)
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-400" title="更新に失敗しました">--</span>
        @else
            @switch($status)
                @case('in_stock')
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        在庫あり@if ($quantity > 0) ({{ $quantity }})@endif
                    </span>
                    @break

                @case('limited')
                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                        残りわずか@if ($quantity > 0) ({{ $quantity }})@endif
                    </span>
                    @break

                @default
                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                        SOLD OUT
                    </span>
            @endswitch
        @endif
    </span>
</div>
