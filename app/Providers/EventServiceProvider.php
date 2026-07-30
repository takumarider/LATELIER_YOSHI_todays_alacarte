<?php

namespace App\Providers;

use App\Events\InventoryUpdated;
use App\Listeners\LogInventoryUpdate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        InventoryUpdated::class => [
            LogInventoryUpdate::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
