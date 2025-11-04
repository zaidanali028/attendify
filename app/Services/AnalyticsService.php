<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Department;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get analytics for a specific user
     */
    public function getUserAnalytics(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $totalHours = $attendances->sum('total_hours') ?? 0;
        $lateArrivals = $attendances->where('is_late', true)->count();
        $earlyDepartures = $attendances->where('is_early_departure', true)->count();

        // Calculate attendance percentage
        $workingDays = $this->getWorkingDays($startDate, $endDate);
        $attendedDays = $attendances->count();
        $attendancePercentage = $workingDays > 0 ? ($attendedDays / $workingDays) * 100 : 0;

        // Calculate overtime (assuming 8 hours per day is standard)
        $overtimeHours = $attendances->filter(function ($attendance) {
            return $attendance->total_hours && $attendance->total_hours > 8;
        })->sum(function ($attendance) {
            return $attendance->total_hours - 8;
        });

        // Calculate average break time (assuming 1 hour break per day, subtract from total if > 8 hours)
        $avgBreakTime = $attendances->filter(function ($attendance) {
            return $attendance->total_hours && $attendance->total_hours > 8;
        })->avg(function ($attendance) {
            // If worked more than 8 hours, assume 1 hour break, otherwise calculate break
            if ($attendance->total_hours > 8) {
                return 1.0; // 1 hour break
            }
            return max(0, 8 - $attendance->total_hours); // Break time
        }) ?? 0;

        return [
            'total_hours' => round($totalHours, 2),
            'late_arrivals' => $lateArrivals,
            'early_departures' => $earlyDepartures,
            'attendance_percentage' => round($attendancePercentage, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'avg_break_time' => round($avgBreakTime, 2),
        ];
    }

    /**
     * Get analytics for admin (all users)
     */
    public function getAdminAnalytics(Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->with(['user', 'department'])
            ->get();

        $totalUsers = User::count();
        $activeUsers = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');

        $totalHoursWorked = $attendances->sum('total_hours') ?? 0;
        $totalLateArrivals = $attendances->where('is_late', true)->count();

        // Department statistics
        $departmentStats = [];
        $departments = Department::with('users')->get();

        foreach ($departments as $department) {
            $deptAttendances = $attendances->where('department_id', $department->id);
            $deptUsers = $department->users->count();
            
            if ($deptUsers > 0) {
                $avgHours = $deptAttendances->avg('total_hours') ?? 0;
                $avgLate = $deptAttendances->where('is_late', true)->count() / max(1, $deptAttendances->count());
                
                // Calculate average attendance percentage for department
                $deptUserIds = $department->users->pluck('id');
                $workingDays = $this->getWorkingDays($startDate, $endDate);
                $totalExpectedDays = $deptUserIds->count() * $workingDays;
                $totalAttendedDays = $deptAttendances->count();
                $avgAttendancePercentage = $totalExpectedDays > 0 ? ($totalAttendedDays / $totalExpectedDays) * 100 : 0;

                $departmentStats[] = [
                    'name' => $department->name,
                    'total_employees' => $deptUsers,
                    'avg_hours_worked' => round($avgHours, 2),
                    'avg_late_arrivals' => round($avgLate, 2),
                    'avg_early_departures' => round($deptAttendances->where('is_early_departure', true)->count() / max(1, $deptAttendances->count()), 2),
                    'avg_attendance_percentage' => round($avgAttendancePercentage, 2),
                ];
            }
        }

        // Employee rankings by hours worked
        $employeeRankings = [];
        $userHours = $attendances->groupBy('user_id')->map(function ($userAttendances) {
            return $userAttendances->sum('total_hours') ?? 0;
        })->sortDesc()->take(10);

        foreach ($userHours as $userId => $hours) {
            $user = User::with('department')->find($userId);
            if ($user) {
                $employeeRankings[] = [
                    'name' => $user->name,
                    'department' => $user->department->name ?? 'N/A',
                    'total_hours' => round($hours, 2),
                ];
            }
        }

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_hours_worked' => round($totalHoursWorked, 2),
            'total_late_arrivals' => $totalLateArrivals,
            'department_stats' => $departmentStats,
            'employee_rankings' => $employeeRankings,
        ];
    }

    /**
     * Calculate working days between two dates (excluding weekends)
     */
    private function getWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Count Monday to Friday as working days
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }
} 