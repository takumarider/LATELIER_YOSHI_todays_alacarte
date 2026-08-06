<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductInventoryStatus extends Component
{
    public Product $product;
    public string $status = 'sold_out';
    public int $quantity = 0;
    public bool $hasError = false;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->refreshInventory();
    }

    #[On('inventory-updated')]
    public function refreshInventory(): void
    {
        try {
            $inventory = $this->product->inventory()->first();

            if (! $inventory) {
                $this->status = 'sold_out';
                $this->quantity = 0;
                $this->hasError = false;
                return;
            }

            $this->quantity = (int) $inventory->quantity;
            $this->status = $inventory->status;
            $this->hasError = false;
        } catch (\Throwable) {
            $this->hasError = true;
        }
    }

    public function render()
    {
        return view('livewire.product-inventory-status');
    }
}
