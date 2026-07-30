<?php

namespace App\Filament\Pages;

use App\Models\Reservation;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class ReservationOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.reservation-overview';

    protected static ?string $navigationLabel = '予約一覧';

    protected static ?int $navigationSort = 2;

    public function getReservations(): Collection
    {
        return Reservation::query()
            ->latest('reservation_date')
            ->limit(10)
            ->get();
    }
}
