<x-filament-panels::page>
    <div class="space-y-4">
        @foreach ($this->getReservations() as $reservation)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $reservation->customer_name }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $reservation->reservation_date->format('Y年m月d日') }} · {{ $reservation->guest_count }}名
                        </div>
                    </div>
                    <div class="rounded-full px-3 py-1 text-sm font-medium
                        @if($reservation->status === 'confirmed') bg-green-100 text-green-700
                        @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-600 @endif">
                        @switch($reservation->status)
                            @case('confirmed') 確定 @break
                            @case('pending') 未確認 @break
                            @default キャンセル @break
                        @endswitch
                    </div>
                </div>
                @if($reservation->note)
                    <div class="mt-3 text-sm text-gray-500">
                        {{ $reservation->note }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
