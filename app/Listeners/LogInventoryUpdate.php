<?php

namespace App\Listeners;

use App\Events\InventoryUpdated;
use Illuminate\Support\Facades\Log;

class LogInventoryUpdate
{
    public function handle(InventoryUpdated $event): void
    {
        Log::info('Inventory updated', [
            'product_id' => $event->inventory->product_id,
            'status' => $event->inventory->status,
            'quantity' => $event->inventory->quantity,
        ]);
    }
}
