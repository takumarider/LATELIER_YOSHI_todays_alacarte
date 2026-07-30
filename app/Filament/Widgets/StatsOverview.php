<?php

namespace App\Filament\Widgets;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('公開商品数', Product::query()->where('is_active', true)->count())
                ->description('現在公開中の商品')
                ->color('success'),
            Stat::make('予約件数', Reservation::query()->count())
                ->description('登録済みの予約')
                ->color('info'),
            Stat::make('売り切れ商品数', Inventory::query()->where('status', 'sold_out')->count())
                ->description('在庫切れの商品')
                ->color('warning'),
        ];
    }
}
