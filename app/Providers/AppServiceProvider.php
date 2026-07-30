<?php

namespace App\Providers;

use App\Models\BusinessHour;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use App\Policies\BusinessHourPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReservationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Inventory::class, InventoryPolicy::class);
        Gate::policy(BusinessHour::class, BusinessHourPolicy::class);
        Gate::policy(Reservation::class, ReservationPolicy::class);
    }
}
