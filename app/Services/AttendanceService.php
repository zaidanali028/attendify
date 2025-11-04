<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Clock in a user
     */
    public function clockIn(User $user): Attendance
    {
        // Check if user has a department assigned
        if (!$user->department_id) {
            throw new \Exception('You must be assigned to a department before you can clock in.');
        }

        // Check if user has already clocked in today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($todayAttendance) {
            throw new \Exception('You have already clocked in today.');
        }

        $now = Carbon::now();
        $expectedTime = $now->copy()->setTime(8, 30, 0); // 8:30 AM
        $isLate = $now->gt($expectedTime);

        DB::beginTransaction();
        try {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'clock_in_time' => $now,
                'attendance_date' => $now->toDateString(),
                'is_late' => $isLate,
                'status' => $isLate ? 'late' : 'present',
            ]);

            // Log the activity
            ActivityLog::create([
                'user_id' => $user->id,
                'subject_type' => Attendance::class,
                'subject_id' => $attendance->id,
                'description' => 'Clocked in',
                'properties' => [
                    'clock_in_time' => $now->toDateTimeString(),
                    'is_late' => $isLate,
                ],
            ]);

            DB::commit();
            return $attendance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Clock out a user
     */
    public function clockOut(User $user): Attendance
    {
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$todayAttendance) {
            throw new \Exception('You need to clock in first before you can clock out.');
        }

        if ($todayAttendance->clock_out_time) {
            throw new \Exception('You have already clocked out today.');
        }

        $now = Carbon::now();
        $expectedTime = $now->copy()->setTime(17, 0, 0); // 5:00 PM
        $isEarlyDeparture = $now->lt($expectedTime);

        // Calculate total hours
        $totalHours = $todayAttendance->clock_in_time->diffInHours($now, true);
        
        // Check for early departure (if leaving before 5 PM)
        $isEarlyDeparture = $isEarlyDeparture && $totalHours < 8;

        DB::beginTransaction();
        try {
            $todayAttendance->update([
                'clock_out_time' => $now,
                'total_hours' => round($totalHours, 2),
                'is_early_departure' => $isEarlyDeparture,
                'status' => $todayAttendance->is_late || $isEarlyDeparture ? 'completed' : 'completed',
            ]);

            // Log the activity
            ActivityLog::create([
                'user_id' => $user->id,
                'subject_type' => Attendance::class,
                'subject_id' => $todayAttendance->id,
                'description' => 'Clocked out',
                'properties' => [
                    'clock_out_time' => $now->toDateTimeString(),
                    'total_hours' => $totalHours,
                    'is_early_departure' => $isEarlyDeparture,
                ],
            ]);

            DB::commit();
            return $todayAttendance->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an attendance record (admin only)
     */
    public function updateAttendance(Attendance $attendance, array $data, User $user): Attendance
    {
        DB::beginTransaction();
        try {
            $oldData = [
                'clock_in_time' => $attendance->clock_in_time?->toDateTimeString(),
                'clock_out_time' => $attendance->clock_out_time?->toDateTimeString(),
                'total_hours' => $attendance->total_hours,
                'is_late' => $attendance->is_late,
                'is_early_departure' => $attendance->is_early_departure,
                'status' => $attendance->status,
            ];

            $clockInTime = Carbon::parse($data['clock_in_time']);
            $clockOutTime = $data['clock_out_time'] ? Carbon::parse($data['clock_out_time']) : null;

            // Validate clock out is after clock in
            if ($clockOutTime && $clockOutTime->lte($clockInTime)) {
                throw new \Exception('Clock out time must be after clock in time.');
            }

            // Calculate if late (8:30 AM is expected time)
            $expectedTime = $clockInTime->copy()->setTime(8, 30, 0);
            $isLate = $clockInTime->gt($expectedTime);

            // Calculate total hours if both times are present
            $totalHours = null;
            $isEarlyDeparture = false;
            if ($clockOutTime) {
                $totalHours = round($clockInTime->diffInHours($clockOutTime, true), 2);
                
                // Check for early departure (if leaving before 5 PM and worked less than 8 hours)
                $expectedDeparture = $clockOutTime->copy()->setTime(17, 0, 0);
                $isEarlyDeparture = $clockOutTime->lt($expectedDeparture) && $totalHours < 8;
            }

            // Determine status
            $status = 'present';
            if ($isLate) {
                $status = 'late';
            } elseif ($clockOutTime) {
                $status = 'completed';
            }

            $attendance->update([
                'clock_in_time' => $clockInTime,
                'clock_out_time' => $clockOutTime,
                'total_hours' => $totalHours,
                'is_late' => $isLate,
                'is_early_departure' => $isEarlyDeparture,
                'status' => $status,
            ]);

            // Log the activity
            ActivityLog::create([
                'user_id' => $user->id,
                'subject_type' => Attendance::class,
                'subject_id' => $attendance->id,
                'description' => 'Attendance record updated',
                'properties' => [
                    'old_data' => $oldData,
                    'new_data' => [
                        'clock_in_time' => $clockInTime->toDateTimeString(),
                        'clock_out_time' => $clockOutTime?->toDateTimeString(),
                        'total_hours' => $totalHours,
                        'is_late' => $isLate,
                        'is_early_departure' => $isEarlyDeparture,
                        'status' => $status,
                    ],
                    'updated_by' => $user->name . ' (' . $user->email . ')',
                ],
            ]);

            DB::commit();
            return $attendance->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 