<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dbedc.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'is_active' => true,
                'provider' => 'email',
                'email_verified_at' => now(),
            ],
        );
    }
}
