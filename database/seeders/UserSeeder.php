<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'full_name' => 'Charles Valtron',
                'email' => 'charlesvaltron@gmail.com',
                'password' => Hash::make('Manuu@20'),
                'role' => 'admin',
                'department' => 'Executive',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
