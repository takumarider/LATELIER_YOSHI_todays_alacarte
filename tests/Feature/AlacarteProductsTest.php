<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
