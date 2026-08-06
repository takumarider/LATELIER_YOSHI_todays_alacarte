<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlacarteProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_products_on_alacarte_page(): void
    {
        $user = User::factory()->create();

        Product::factory()->create([
            'name' => '季節のパスタ',
            'price' => 1800,
        ]);

        Product::factory()->create([
            'name' => '特製サラダ',
            'price' => 1200,
        ]);

        $response = $this->actingAs($user)->get('/alacarte');

        $response->assertOk();
        $response->assertSee('季節のパスタ');
        $response->assertSee('特製サラダ');
        $response->assertSee('1,800');
    }

    public function test_reserve_button_appears_when_in_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => '予約可能商品', 'is_active' => true]);
        \App\Models\Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'status' => 'in_stock',
        ]);

        $this->actingAs($user)->get('/alacarte')
            ->assertSee('取り置き予約する')
            ->assertSee('open-member-reservation');
    }

    public function test_reserve_button_disabled_when_sold_out(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => '売り切れ商品', 'is_active' => true]);
        \App\Models\Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
            'status' => 'sold_out',
        ]);

        $this->actingAs($user)->get('/alacarte')
            ->assertSee('予約不可（在庫なし）')
            ->assertDontSee('取り置き予約する');
    }

    public function test_storefront_displays_product_images_when_available(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Storage::disk('public')->put('products/sample.jpg', 'dummy-image-content');

        $product = Product::factory()->create([
            'name' => '画像付き商品',
            'price' => 2000,
            'image' => 'products/sample.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/alacarte');

        $response->assertOk();
        $response->assertSee('画像付き商品');
        $response->assertSee($product->image_url);
    }
}
