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
                        <div class="p-6 text-gray-900">
                            <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $product->description ?? __('商品説明は準備中です。') }}
                            </p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm font-semibold text-amber-700">{{ number_format($product->price) }}円</span>
                                <livewire:product-inventory-status :product="$product" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
