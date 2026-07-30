<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSetupSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        $product = Product::firstOrCreate(
            ['name' => '季節のパスタ'],
            [
                'price' => 1800,
                'description' => 'トマトとバジルの香りが楽しめる一皿です。',
                'is_active' => true,
            ]
        );

        Inventory::firstOrCreate(
            ['product_id' => $product->id],
            [
                'quantity' => 10,
                'status' => 'in_stock',
            ]
        );

        BusinessHour::firstOrCreate(
            ['day_of_week' => 'monday'],
            [
                'open_time' => '10:00',
                'close_time' => '20:00',
                'is_closed' => false,
                'note' => '通常営業',
            ]
        );

        Reservation::firstOrCreate(
            ['email' => 'guest@example.com', 'reservation_date' => '2026-07-30'],
            [
                'customer_name' => '山田太郎',
                'phone' => '090-0000-0000',
                'guest_count' => 2,
                'status' => 'pending',
                'note' => 'テスト予約',
            ]
        );
    }
}
