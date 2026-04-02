<?php

namespace Database\Seeders;

use App\Models\RewardItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Admin
        User::firstOrCreate(
            ['email' => 'admin@aquahub.pro'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'balance_point' => 999999,
            ]
        );

        // Tạo User demo
        User::firstOrCreate(
            ['email' => 'demo@aquahub.pro'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('demo123456'),
                'role' => 'user',
                'balance_point' => 1000,
            ]
        );

        // Tạo Reward Items mẫu
        $rewards = [
            [
                'name' => 'Netflix Premium 1 tháng',
                'description' => 'Tài khoản Netflix gói Premium 4K, xem được 4 màn hình cùng lúc.',
                'point_price' => 500,
                'image' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Canva Pro 1 tháng',
                'description' => 'Gói Canva Pro đầy đủ tính năng, không giới hạn template premium.',
                'point_price' => 300,
                'image' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Spotify Premium 1 tháng',
                'description' => 'Nghe nhạc không quảng cáo, tải nhạc offline với chất lượng cao nhất.',
                'point_price' => 200,
                'image' => null,
                'status' => 'active',
            ],
            [
                'name' => 'ChatGPT Plus 1 tháng',
                'description' => 'Truy cập GPT-4, không giới hạn, nhanh nhất và thông minh nhất.',
                'point_price' => 800,
                'image' => null,
                'status' => 'active',
            ],
            [
                'name' => 'YouTube Premium 1 tháng',
                'description' => 'Xem YouTube không quảng cáo, tải video offline, YouTube Music.',
                'point_price' => 250,
                'image' => null,
                'status' => 'active',
            ],
        ];

        foreach ($rewards as $reward) {
            RewardItem::firstOrCreate(['name' => $reward['name']], $reward);
        }

        $this->command->info('✅ Seeder hoàn tất!');
        $this->command->info('Admin: admin@aquahub.pro / admin123456');
        $this->command->info('Demo: demo@aquahub.pro / demo123456');
    }
}
