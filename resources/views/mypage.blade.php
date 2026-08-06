<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('マイページ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- プロフィール --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">アカウント情報</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400">お名前</dt>
                        <dd class="font-medium text-gray-800">{{ auth()->user()->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">メールアドレス</dt>
                        <dd class="font-medium text-gray-800">{{ auth()->user()->email }}</dd>
                    </div>
                </dl>
            </div>

            {{-- 予約履歴 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">予約履歴</h3>

                @if($reservations->isEmpty())
                    <p class="text-sm text-gray-400">まだ予約はありません。</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b text-xs text-gray-400 uppercase">
                                    <th class="pb-2 pr-4">商品</th>
                                    <th class="pb-2 pr-4">予約日</th>
                                    <th class="pb-2 pr-4">ステータス</th>
                                    <th class="pb-2">予約日時</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($reservations as $reservation)
                                    <tr>
                                        <td class="py-3 pr-4 font-medium text-gray-800">
                                            {{ $reservation->product?->name ?? $reservation->note ?? '—' }}
                                        </td>
                                        <td class="py-3 pr-4 text-gray-600">
                                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('n月j日') }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            @php
                                                $badge = match($reservation->status) {
                                                    'confirmed' => ['text' => '確認済み', 'class' => 'bg-green-100 text-green-700'],
                                                    'cancelled' => ['text' => 'キャンセル', 'class' => 'bg-red-100 text-red-700'],
                                                    default     => ['text' => '確認待ち',  'class' => 'bg-yellow-100 text-yellow-700'],
                                                };
                                            @endphp
                                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $badge['class'] }}">
                                                {{ $badge['text'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-gray-400 text-xs">
                                            {{ $reservation->created_at->format('Y/m/d H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="text-center">
                <a href="{{ route('alacarte') }}" class="text-sm text-amber-600 hover:underline">← アラカルトへ戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>
