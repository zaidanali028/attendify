<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Handles employee relations, recruitment, and staff management',
                'status' => true,
            ],
            [
                'name' => 'Finance',
                'description' => 'Manages financial operations and accounting',
                'status' => true,
            ],
            [
                'name' => 'IT',
                'description' => 'Information Technology and Technical Support',
                'status' => true,
            ],
            [
                'name' => 'Operations',
                'description' => 'Daily operational activities and management',
                'status' => true,
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Client relations and customer support',
                'status' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}
