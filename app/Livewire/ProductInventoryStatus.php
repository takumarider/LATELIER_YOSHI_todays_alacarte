<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductInventoryStatus extends Component
{
    public Product $product;
    public string $status = 'sold_out';
    public int $quantity = 0;

    protected $listeners = [
        'echo:inventory,inventory.updated' => 'refreshInventory',
        'inventory-updated' => 'refreshInventory',
    ];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->refreshInventory();
    }

    public function refreshInventory(): void
    {
        $inventory = $this->product->inventory()->first();

        if (! $inventory) {
            $this->status = 'sold_out';
            $this->quantity = 0;
            return;
        }

        $this->quantity = (int) $inventory->quantity;
        $this->status = $inventory->status;
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.product-inventory-status');
    }
}
