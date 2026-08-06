<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlacarteInventoryStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_alacarte_page_shows_inventory_status(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => '季節のパスタ',
            'price' => 1800,
        ]);

        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'status' => 'in_stock',
        ]);

        $response = $this->actingAs($user)->get('/alacarte');

        $response->assertOk();
        $response->assertSee('季節のパスタ');
        $response->assertSee('在庫あり');
    }

    public function test_alacarte_page_shows_sold_out_state(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => '売り切れ商品',
            'price' => 1500,
        ]);

        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
            'status' => 'sold_out',
        ]);

        $response = $this->actingAs($user)->get('/alacarte');

        $response->assertOk();
        $response->assertSee('売り切れ商品');
        $response->assertSee('SOLD OUT');
    }

    public function test_alacarte_page_shows_limited_state(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => '残り少ない商品',
            'price' => 1200,
        ]);

        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'status' => 'limited',
        ]);

        $response = $this->actingAs($user)->get('/alacarte');

        $response->assertOk();
        $response->assertSee('残り少ない商品');
        $response->assertSee('残りわずか');
    }
}
