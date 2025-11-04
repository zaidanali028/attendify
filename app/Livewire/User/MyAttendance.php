<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyAttendance extends Component
{
    public $startDate;
    public $endDate;
    public $attendances = [];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadAttendances();
    }

    public function updatedStartDate()
    {
        $this->loadAttendances();
    }

    public function updatedEndDate()
    {
        $this->loadAttendances();
    }

    public function loadAttendances()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $this->attendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('attendance_date', [$start, $end])
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.user.my-attendance')->layout('layouts.app');
    }
}
