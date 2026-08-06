<div>
    @if($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50" wire:click="close()"></div>
        <div class="relative z-10 w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            @if($success)
                <div class="py-4 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-gray-800">予約完了！</h3>
                    <p class="mb-5 text-sm text-gray-500">「{{ $productName }}」の取り置きが完了しました。</p>
                    <button wire:click="close()" class="w-full rounded-lg bg-amber-600 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                        閉じる
                    </button>
                </div>
            @else
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">取り置き予約</h3>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $productName }}</p>
                    </div>
                    <button wire:click="close()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @if($errorMessage)
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        {{ $errorMessage }}
                    </div>
                @endif

                <div class="mb-5 space-y-1.5 rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span class="text-gray-400">お名前</span>
                        <span class="font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">メール</span>
                        <span class="font-medium text-xs">{{ auth()->user()->email }}</span>
                    </div>
                    @if($saleDate)
                        <div class="flex justify-between">
                            <span class="text-gray-400">予約日</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($saleDate)->format('n月j日') }}</span>
                        </div>
                    @endif
                </div>

                <div class="mb-5">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        来店予定時間 <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="time"
                        wire:model="visitTime"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                    >
                    @error('visitTime')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    wire:click="reserve()"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-wait"
                    class="w-full rounded-lg bg-amber-600 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors"
                >
                    <span wire:loading.remove wire:target="reserve">予約を確定する</span>
                    <span wire:loading wire:target="reserve">処理中...</span>
                </button>
            @endif
        </div>
    </div>
    @endif
</div>
