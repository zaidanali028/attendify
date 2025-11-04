<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

class ClockOut extends Component
{
    public $canClockOut = false;
    public $todayAttendance;

    public function mount()
    {
        $this->todayAttendance = Auth::user()->todayAttendance();
        $this->canClockOut = $this->todayAttendance && $this->todayAttendance->clock_in_time && !$this->todayAttendance->clock_out_time;
    }

    public function clockOut()
    {
        if (!$this->canClockOut) {
            session()->flash('error', 'You must clock in before clocking out.');
            return;
        }

        try {
            $service = app(AttendanceService::class);
            $service->clockOut(Auth::user());
            session()->flash('success', 'Clocked out successfully!');
            $this->canClockOut = false;
            $this->todayAttendance->refresh();
            $this->dispatch('attendance-updated');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.clock-out')->layout('layouts.app');
    }
}
