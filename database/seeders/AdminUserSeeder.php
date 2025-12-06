<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LoginSecurity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@legalassistant.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        LoginSecurity::create([
            'user_id' => $admin->id,
            'login_notifications' => true,
            'two_factor_required' => false,
            'failed_login_attempts' => 0,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@legalassistant.com');
        $this->command->info('Password: admin123');
    }
}