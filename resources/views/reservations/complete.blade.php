<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>予約完了 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4 py-12 text-center">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mx-auto mb-4">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-xl font-bold text-gray-800 mb-2">予約が完了しました</h1>
            <p class="text-sm text-gray-500 mb-6">
                {{ $reservation->customer_name }} 様、ご予約ありがとうございます。<br>
                確認メールを <span class="font-medium text-gray-700">{{ $reservation->email }}</span> へ送りますので、ご確認ください。
            </p>

            <div class="text-left bg-gray-50 rounded-lg px-4 py-3 text-sm text-gray-600 space-y-1 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-400">予約番号</span>
                    <span class="font-mono font-semibold">#{{ $reservation->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">ステータス</span>
                    <span class="text-amber-700 font-medium">確認待ち</span>
                </div>
            </div>

            <a href="{{ url('/') }}" class="inline-block text-sm text-amber-600 hover:underline">
                トップページへ戻る
            </a>
        </div>
    </div>
</body>
</html>
