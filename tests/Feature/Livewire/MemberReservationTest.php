<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MemberReservationModal;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberReservationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['is_active' => true]);
        Inventory::factory()->create([
            'product_id' => $this->product->id,
            'quantity'   => 5,
            'status'     => 'in_stock',
        ]);
    }

    public function test_modal_opens_on_event(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->assertSet('show', true)
            ->assertSet('productId', $this->product->id)
            ->assertSet('productName', $this->product->name);
    }

    public function test_member_can_reserve_product(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->set('visitTime', '12:00')
            ->call('reserve')
            ->assertSet('success', true)
            ->assertSet('errorMessage', '');

        $this->assertDatabaseHas('reservations', [
            'user_id'       => $this->user->id,
            'product_id'    => $this->product->id,
            'customer_name' => $this->user->name,
            'email'         => $this->user->email,
            'status'        => 'pending',
        ]);
    }

    public function test_reserve_decrements_inventory(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->set('visitTime', '18:30')
            ->call('reserve');

        $this->assertDatabaseHas('inventories', [
            'product_id' => $this->product->id,
            'quantity'   => 4,
        ]);
    }

    public function test_sold_out_shows_error(): void
    {
        $this->actingAs($this->user);
        Inventory::where('product_id', $this->product->id)
            ->update(['quantity' => 0, 'status' => 'sold_out']);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->set('visitTime', '12:00')
            ->call('reserve')
            ->assertSet('success', false)
            ->assertSet('errorMessage', 'この商品は在庫がなく、予約できません。');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_visit_time_is_required(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->set('visitTime', '')
            ->call('reserve')
            ->assertHasErrors(['visitTime' => 'required']);
    }

    public function test_visit_time_format_validated(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->set('visitTime', '99:99')
            ->call('reserve')
            ->assertHasErrors(['visitTime']);
    }

    public function test_future_product_reserves_without_inventory_check(): void
    {
        $this->actingAs($this->user);
        $futureDate   = now()->addDay()->toDateString();
        $futureProduct = Product::factory()->create(['is_active' => true, 'sale_date' => $futureDate]);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $futureProduct->id, productName: $futureProduct->name, saleDate: $futureDate)
            ->set('visitTime', '12:00')
            ->call('reserve')
            ->assertSet('success', true);

        $this->assertDatabaseHas('reservations', [
            'product_id'       => $futureProduct->id,
            'reservation_date' => $futureDate,
        ]);
    }

    public function test_close_hides_modal(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MemberReservationModal::class)
            ->dispatch('open-member-reservation', productId: $this->product->id, productName: $this->product->name)
            ->assertSet('show', true)
            ->call('close')
            ->assertSet('show', false);
    }
}
