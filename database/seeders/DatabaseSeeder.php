<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed departments and roles/permissions first
        $this->call([
            DepartmentSeeder::class,
            RolePermissionSeeder::class,
        ]);

        // Get the first department for assignment
        $department = Department::first();

        if (!$department) {
            $this->command->error('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ecgghana.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'department_id' => $department->id,
            ]
        );
        $admin->syncRoles(['Admin']);

        // Create Department Head user
        $deptHead = User::firstOrCreate(
            ['email' => 'depthead@ecgghana.com'],
            [
                'name' => 'Department Head',
                'password' => Hash::make('password'),
                'department_id' => $department->id,
            ]
        );
        $deptHead->syncRoles(['Department Head']);

        // Create regular User
        $user = User::firstOrCreate(
            ['email' => 'user@ecgghana.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'department_id' => $department->id,
            ]
        );
        $user->syncRoles(['User']);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@ecgghana.com / password');
        $this->command->info('Dept Head: depthead@ecgghana.com / password');
        $this->command->info('User: user@ecgghana.com / password');

        // Uncomment the line below to seed fake data (50+ users, 2000+ attendance records)
        // $this->call(FakeDataSeeder::class);
    }
}
