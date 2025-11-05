<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all departments
        $departments = Department::all();
        
        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        // Create 5 Department Heads (one per department if possible)
        $this->command->info('Creating Department Heads...');
        $deptHeads = [];
        foreach ($departments->take(5) as $index => $dept) {
            $deptHead = User::create([
                'name' => $faker->name(),
                'email' => 'depthead' . ($index + 1) . '@ecgghana.com',
                'password' => Hash::make('password'),
                'department_id' => $dept->id,
            ]);
            $deptHead->syncRoles(['Department Head']);
            $deptHeads[] = $deptHead;
            $this->command->info("Created Dept Head: {$deptHead->email}");
        }

        // Create 50 regular users
        $this->command->info('Creating regular users...');
        $users = [];
        for ($i = 1; $i <= 50; $i++) {
            $user = User::create([
                'name' => $faker->name(),
                'email' => 'user' . $i . '@ecgghana.com',
                'password' => Hash::make('password'),
                'department_id' => $departments->random()->id,
            ]);
            $user->syncRoles(['User']);
            $users[] = $user;
        }
        $this->command->info("Created 50 users");

        // Generate attendance records for the last 60 days
        $this->command->info('Generating attendance records...');
        $startDate = Carbon::now()->subDays(60);
        $endDate = Carbon::today();
        $totalRecords = 0;

        foreach ($users as $user) {
            $currentDate = $startDate->copy();
            $attendanceDays = 0;
            $maxDays = 45; // Each user attends roughly 45 out of 60 days (75% attendance)

            while ($currentDate->lte($endDate) && $attendanceDays < $maxDays) {
                // Skip weekends (Saturday = 6, Sunday = 0)
                if ($currentDate->dayOfWeek == Carbon::SATURDAY || $currentDate->dayOfWeek == Carbon::SUNDAY) {
                    $currentDate->addDay();
                    continue;
                }

                // Random chance to skip a day (to create realistic attendance patterns)
                if ($faker->boolean(20)) { // 20% chance to skip a day
                    $currentDate->addDay();
                    continue;
                }

                // Determine clock-in time
                $lateChance = $faker->numberBetween(0, 100);
                
                if ($lateChance < 70) {
                    // 70% chance: On time (between 7:30 AM and 8:30 AM)
                    $clockInHour = $faker->numberBetween(7, 8);
                    $clockInMinute = $clockInHour == 7 ? $faker->numberBetween(30, 59) : $faker->numberBetween(0, 30);
                    $isLate = false;
                } elseif ($lateChance < 90) {
                    // 20% chance: Slightly late (between 8:31 AM and 9:30 AM)
                    $clockInHour = 8;
                    $clockInMinute = $faker->numberBetween(31, 59);
                    $isLate = true;
                } else {
                    // 10% chance: Very late (after 9:30 AM)
                    $clockInHour = $faker->numberBetween(9, 11);
                    $clockInMinute = $clockInHour == 9 ? $faker->numberBetween(30, 59) : $faker->numberBetween(0, 59);
                    $isLate = true;
                }

                $clockInTime = $currentDate->copy()->setTime($clockInHour, $clockInMinute, 0);

                // Determine clock-out time and hours worked
                $workHours = $faker->numberBetween(6, 10); // Between 6 and 10 hours
                $clockOutTime = $clockInTime->copy()->addHours($workHours)->addMinutes($faker->numberBetween(0, 59));

                // Check for early departure (before 5 PM and less than 8 hours)
                $isEarlyDeparture = false;
                if ($clockOutTime->hour < 17 && $workHours < 8) {
                    $isEarlyDeparture = true;
                }

                // Determine status
                $status = 'completed';
                if ($isLate) {
                    $status = 'late';
                } elseif (!$isEarlyDeparture && $workHours >= 8) {
                    $status = 'present';
                }

                // Calculate total hours (with break time consideration)
                // Subtract 1 hour for break if worked more than 8 hours
                $totalHours = $workHours >= 8 ? $workHours - 1 : $workHours;
                $totalHours = round($totalHours, 2);

                // Create attendance record
                Attendance::create([
                    'user_id' => $user->id,
                    'department_id' => $user->department_id,
                    'clock_in_time' => $clockInTime,
                    'clock_out_time' => $clockOutTime,
                    'attendance_date' => $currentDate->toDateString(),
                    'total_hours' => $totalHours,
                    'is_late' => $isLate,
                    'is_early_departure' => $isEarlyDeparture,
                    'status' => $status,
                ]);

                $totalRecords++;
                $attendanceDays++;
                $currentDate->addDay();
            }
        }

        // Generate attendance for Department Heads (more consistent attendance)
        foreach ($deptHeads as $deptHead) {
            $currentDate = $startDate->copy();
            $attendanceDays = 0;
            $maxDays = 50; // Dept heads have better attendance

            while ($currentDate->lte($endDate) && $attendanceDays < $maxDays) {
                // Skip weekends
                if ($currentDate->dayOfWeek == Carbon::SATURDAY || $currentDate->dayOfWeek == Carbon::SUNDAY) {
                    $currentDate->addDay();
                    continue;
                }

                // Dept heads are more punctual - 90% on time
                $lateChance = $faker->numberBetween(0, 100);
                
                if ($lateChance < 90) {
                    // On time
                    $clockInHour = $faker->numberBetween(7, 8);
                    $clockInMinute = $clockInHour == 7 ? $faker->numberBetween(45, 59) : $faker->numberBetween(0, 30);
                    $isLate = false;
                } else {
                    // Slightly late
                    $clockInHour = 8;
                    $clockInMinute = $faker->numberBetween(31, 45);
                    $isLate = true;
                }

                $clockInTime = $currentDate->copy()->setTime($clockInHour, $clockInMinute, 0);

                // Dept heads work longer hours
                $workHours = $faker->numberBetween(8, 9);
                $clockOutTime = $clockInTime->copy()->addHours($workHours)->addMinutes($faker->numberBetween(0, 59));

                $isEarlyDeparture = false;
                $totalHours = round($workHours - 1, 2); // 1 hour break
                $status = $isLate ? 'late' : 'completed';

                Attendance::create([
                    'user_id' => $deptHead->id,
                    'department_id' => $deptHead->department_id,
                    'clock_in_time' => $clockInTime,
                    'clock_out_time' => $clockOutTime,
                    'attendance_date' => $currentDate->toDateString(),
                    'total_hours' => $totalHours,
                    'is_late' => $isLate,
                    'is_early_departure' => $isEarlyDeparture,
                    'status' => $status,
                ]);

                $totalRecords++;
                $attendanceDays++;
                $currentDate->addDay();
            }
        }

        $this->command->info("✅ Fake data seeding completed!");
        $this->command->info("   - Created " . count($deptHeads) . " Department Heads");
        $this->command->info("   - Created " . count($users) . " Regular Users");
        $this->command->info("   - Generated {$totalRecords} attendance records");
        $this->command->info("");
        $this->command->info("Sample login credentials:");
        $this->command->info("   Dept Head 1: depthead1@ecgghana.com / password");
        $this->command->info("   User 1: user1@ecgghana.com / password");
        $this->command->info("   User 2: user2@ecgghana.com / password");
    }
}
