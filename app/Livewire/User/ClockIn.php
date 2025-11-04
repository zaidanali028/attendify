<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

class ClockIn extends Component
{
    public $canClockIn = true;

    public function mount()
    {
        $todayAttendance = Auth::user()->todayAttendance();
        $this->canClockIn = !($todayAttendance && $todayAttendance->clock_in_time);
    }

    public function clockIn()
    {
        if (!$this->canClockIn) {
            session()->flash('error', 'You have already clocked in today.');
            return;
        }

        $user = Auth::user();
        
        if (!$user->department_id) {
            session()->flash('error', 'You must be assigned to a department before clocking in. Please contact an administrator.');
            return;
        }

        try {
            $service = app(AttendanceService::class);
            $service->clockIn($user);
            session()->flash('success', 'Clocked in successfully!');
            $this->canClockIn = false;
            $this->dispatch('attendance-updated');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.clock-in')->layout('layouts.app');
    }
}
