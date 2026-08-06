<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_form_is_accessible_for_active_product(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 5, 'status' => 'in_stock']);

        $this->get(route('reservations.create', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('取り置き予約');
    }

    public function test_reservation_form_returns_404_for_inactive_product(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->get(route('reservations.create', $product))
            ->assertNotFound();
    }

    public function test_guest_can_submit_reservation(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 5, 'status' => 'in_stock']);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '山田 太郎',
            'email' => 'yamada@example.com',
            'visit_time' => '12:00',
        ])->assertRedirectContains('/reserve/complete/');

        $this->assertDatabaseHas('reservations', [
            'customer_name' => '山田 太郎',
            'email' => 'yamada@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_inventory_is_decremented_after_reservation(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $inventory = Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'status' => 'in_stock',
        ]);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '鈴木 花子',
            'email' => 'suzuki@example.com',
            'visit_time' => '14:30',
        ]);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 4,
        ]);
    }

    public function test_inventory_becomes_sold_out_when_last_item_reserved(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'status' => 'in_stock',
        ]);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '田中 次郎',
            'email' => 'tanaka@example.com',
            'visit_time' => '11:00',
        ]);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 0,
            'status' => 'sold_out',
        ]);
    }

    public function test_reservation_fails_when_sold_out(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
            'status' => 'sold_out',
        ]);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '佐藤 三郎',
            'email' => 'sato@example.com',
            'visit_time' => '13:00',
        ])->assertSessionHasErrors(['product']);

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_reservation_fails_when_no_inventory_record(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '高橋 四郎',
            'email' => 'takahashi@example.com',
            'visit_time' => '15:00',
        ])->assertSessionHasErrors(['product']);
    }

    public function test_validation_requires_name_and_email_and_time(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->post(route('reservations.store', $product), [])
            ->assertSessionHasErrors(['customer_name', 'email', 'visit_time']);
    }

    public function test_validation_rejects_invalid_email(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '山田',
            'email' => 'not-an-email',
            'visit_time' => '12:00',
        ])->assertSessionHasErrors(['email']);
    }

    public function test_validation_rejects_invalid_phone_format(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->post(route('reservations.store', $product), [
            'customer_name' => '山田',
            'email' => 'test@example.com',
            'phone' => '09012345678',
            'visit_time' => '12:00',
        ])->assertSessionHasErrors(['phone']);
    }

    public function test_complete_page_shows_reservation_details(): void
    {
        $reservation = Reservation::factory()->create([
            'customer_name' => '山田 太郎',
            'email' => 'yamada@example.com',
            'status' => 'pending',
        ]);

        $this->get(route('reservations.complete', $reservation))
            ->assertOk()
            ->assertSee('山田 太郎')
            ->assertSee('yamada@example.com')
            ->assertSee('予約が完了しました');
    }
}
