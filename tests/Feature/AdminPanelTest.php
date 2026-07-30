<?php

namespace Tests\Feature;

use App\Models\BusinessHour;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_panel_pages_are_registered(): void
    {
        $this->get('/admin/login')->assertOk();
        $this->get('/admin/products')->assertStatus(302);
        $this->get('/admin/inventories')->assertStatus(302);
        $this->get('/admin/business-hours')->assertStatus(302);
        $this->get('/admin/reservations')->assertStatus(302);
    }

    public function test_admin_users_can_manage_admin_only_models_and_non_admin_users_cannot(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $models = [
            Product::class,
            Inventory::class,
            BusinessHour::class,
            Reservation::class,
        ];

        foreach ($models as $model) {
            $this->assertTrue(Gate::forUser($admin)->allows('viewAny', $model));
            $this->assertTrue(Gate::forUser($admin)->allows('create', $model));
            $this->assertFalse(Gate::forUser($user)->allows('viewAny', $model));
            $this->assertFalse(Gate::forUser($user)->allows('create', $model));
        }
    }
}
