<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
