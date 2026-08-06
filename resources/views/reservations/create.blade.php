<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>取り置き予約 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-lg mx-auto px-4 py-12">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">取り置き予約</h1>
        <p class="text-sm text-gray-500 mb-8">{{ $product->name }}（{{ number_format($product->price) }}円）</p>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('reservations.store', $product) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
            @csrf

            <div>
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">お名前 <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    id="customer_name"
                    name="customer_name"
                    value="{{ old('customer_name') }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('customer_name') border-red-400 @enderror"
                    placeholder="山田 太郎"
                >
                @error('customer_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">メールアドレス <span class="text-red-500">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('email') border-red-400 @enderror"
                    placeholder="example@email.com"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">電話番号</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('phone') border-red-400 @enderror"
                    placeholder="090-1234-5678"
                >
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="visit_time" class="block text-sm font-medium text-gray-700 mb-1">来店予定時間 <span class="text-red-500">*</span></label>
                <input
                    type="time"
                    id="visit_time"
                    name="visit_time"
                    value="{{ old('visit_time') }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('visit_time') border-red-400 @enderror"
                >
                @error('visit_time')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 text-sm transition-colors"
            >
                予約を確定する
            </button>
        </form>
    </div>
</body>
</html>
