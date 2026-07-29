<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_mypage(): void
    {
        $response = $this->get('/mypage');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_mypage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertOk();
        $response->assertSee('マイページ');
    }

    public function test_user_role_is_cast_to_enum(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $this->assertInstanceOf(UserRole::class, $user->role);
        $this->assertSame(UserRole::ADMIN, $user->role);
    }
}
