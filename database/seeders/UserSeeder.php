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
            [
                'full_name' => 'Munchify HR Manager',
                'email' => 'hr@munchify.com',
                'password' => Hash::make('password'),
                'role' => 'hr_manager',
                'department' => 'Executive',
                'is_active' => true,
            ],
            [
                'full_name' => 'Tech Hiring Manager',
                'email' => 'tech.hiring@munchify.com',
                'password' => Hash::make('password'),
                'role' => 'hiring_manager',
                'department' => 'Technology',
                'is_active' => true,
            ],
            [
                'full_name' => 'Ops Hiring Manager',
                'email' => 'ops.hiring@munchify.com',
                'password' => Hash::make('password'),
                'role' => 'hiring_manager',
                'department' => 'Operations',
                'is_active' => true,
            ],
            [
                'full_name' => 'Senior Developer Interviewer',
                'email' => 'interviewer1@munchify.com',
                'password' => Hash::make('password'),
                'role' => 'interviewer',
                'department' => 'Technology',
                'is_active' => true,
            ],
            [
                'full_name' => 'Ops Supervisor Interviewer',
                'email' => 'interviewer2@munchify.com',
                'password' => Hash::make('password'),
                'role' => 'interviewer',
                'department' => 'Operations',
                'is_active' => true,
            ],
            [
                'full_name' => 'General Viewer',
                'email' => 'viewer@munchify.com',
                'password' => Hash::make('password'),
                'role' => 'viewer',
                'department' => 'Customer Care',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
