<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'clock_in_time',
        'clock_out_time',
        'department_id',
        'total_hours',
        'is_late',
        'is_early_departure',
        'status',
        'attendance_date',
    ];

    protected $casts = [
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'attendance_date' => 'date',
        'is_late' => 'boolean',
        'is_early_departure' => 'boolean',
        'total_hours' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function calculateTotalHours(): ?float
    {
        if (!$this->clock_in_time || !$this->clock_out_time) {
            return null;
        }
        return round($this->clock_in_time->diffInHours($this->clock_out_time, true), 2);
    }

    public function checkLateArrival(): bool
    {
        if (!$this->clock_in_time) {
            return false;
        }
        $expectedTime = $this->clock_in_time->copy()->setTime(8, 30, 0);
        return $this->clock_in_time->gt($expectedTime);
    }

    public function checkEarlyDeparture(): bool
    {
        if (!$this->clock_out_time) {
            return false;
        }
        $expectedTime = $this->clock_out_time->copy()->setTime(17, 0, 0);
        return $this->clock_out_time->lt($expectedTime);
    }

    public function scopeDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
