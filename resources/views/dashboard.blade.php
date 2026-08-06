<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('アラカルト') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-gray-600">{{ __('本日のおすすめ商品をこちらでご案内します。') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                {{ __('画像なし') }}
                            </div>
                        @endif
                        <div class="p-6 text-gray-900">
                            <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $product->description ?? __('商品説明は準備中です。') }}
                            </p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm font-semibold text-amber-700">{{ number_format($product->price) }}円</span>
                                <livewire:product-inventory-status :product="$product" />
                            </div>
                            <div class="mt-4">
                                @if ($product->inventory && $product->inventory->status !== 'sold_out' && $product->inventory->quantity > 0)
                                    <button
                                        onclick="Livewire.dispatch('open-member-reservation', {productId: {{ $product->id }}, productName: {{ Js::from($product->name) }}, saleDate: null})"
                                        class="block w-full text-center rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold py-2 transition-colors"
                                    >
                                        取り置き予約する
                                    </button>
                                @else
                                    <span class="block w-full text-center rounded-lg bg-gray-200 text-gray-400 text-sm font-semibold py-2 cursor-not-allowed">
                                        予約不可（在庫なし）
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($futureProducts->isNotEmpty())
                <div class="mt-10">
                    <h2 class="text-lg font-bold text-gray-700 mb-1">近日入荷予定</h2>
                    <p class="text-sm text-gray-500 mb-4">事前にご予約いただけます。</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($futureProducts as $product)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover opacity-80">
                                @else
                                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                        {{ __('画像なし') }}
                                    </div>
                                @endif
                                <div class="p-6 text-gray-900">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                            {{ \Carbon\Carbon::parse($product->sale_date)->format('n月j日') }}
                                        </span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $product->description ?? __('商品説明は準備中です。') }}
                                    </p>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-sm font-semibold text-amber-700">{{ number_format($product->price) }}円</span>
                                    </div>
                                    <div class="mt-4">
                                        <button
                                            onclick="Livewire.dispatch('open-member-reservation', {productId: {{ $product->id }}, productName: {{ Js::from($product->name) }}, saleDate: {{ Js::from($product->sale_date?->toDateString()) }}})"
                                            class="block w-full text-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 transition-colors"
                                        >
                                            事前予約する
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <livewire:member-reservation-modal />
</x-app-layout>
