<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Operations', 'description' => 'Delivery logistics, fleet management, and day-to-day operations.', 'is_active' => true],
            ['name' => 'Customer Care', 'description' => 'Resolving customer inquiries, support issues, and order inquiries.', 'is_active' => true],
            ['name' => 'Marketing', 'description' => 'Growth, brand awareness, partnership management, and social media.', 'is_active' => true],
            ['name' => 'Finance', 'description' => 'Accounting, billing, payments, and financial planning.', 'is_active' => true],
            ['name' => 'Technology', 'description' => 'Software engineering, product design, infrastructure, and IT support.', 'is_active' => true],
            ['name' => 'Executive', 'description' => 'Leadership, strategy, and business management.', 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
