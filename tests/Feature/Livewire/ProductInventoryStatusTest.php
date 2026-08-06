<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProductInventoryStatus;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductInventoryStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_in_stock_status(): void
    {
        $product = Product::factory()->create();
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'status' => 'in_stock',
        ]);

        Livewire::test(ProductInventoryStatus::class, ['product' => $product])
            ->assertSee('在庫あり')
            ->assertSee('(5)');
    }

    public function test_shows_limited_status(): void
    {
        $product = Product::factory()->create();
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'status' => 'limited',
        ]);

        Livewire::test(ProductInventoryStatus::class, ['product' => $product])
            ->assertSee('残りわずか')
            ->assertSee('(2)');
    }

    public function test_shows_sold_out_status(): void
    {
        $product = Product::factory()->create();
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
            'status' => 'sold_out',
        ]);

        Livewire::test(ProductInventoryStatus::class, ['product' => $product])
            ->assertSee('SOLD OUT');
    }

    public function test_defaults_to_sold_out_when_no_inventory(): void
    {
        $product = Product::factory()->create();

        Livewire::test(ProductInventoryStatus::class, ['product' => $product])
            ->assertSee('SOLD OUT');
    }

    public function test_refreshes_status_on_inventory_updated_event(): void
    {
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'status' => 'in_stock',
        ]);

        $component = Livewire::test(ProductInventoryStatus::class, ['product' => $product])
            ->assertSee('在庫あり');

        $inventory->update(['quantity' => 0, 'status' => 'sold_out']);

        $component->dispatch('inventory-updated')
            ->assertSee('SOLD OUT');
    }

    public function test_status_reflects_latest_inventory_on_refresh(): void
    {
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'status' => 'in_stock',
        ]);

        $component = Livewire::test(ProductInventoryStatus::class, ['product' => $product])
            ->assertSee('在庫あり');

        $inventory->update(['quantity' => 2, 'status' => 'limited']);

        $component->call('refreshInventory')
            ->assertSee('残りわずか')
            ->assertSee('(2)');
    }
}
