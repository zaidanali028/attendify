<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

class EditClock extends Component
{
    public $attendanceId;
    public $attendance;
    public $clockInTime;
    public $clockOutTime;

    public function mount($id)
    {
        $this->attendanceId = $id;
        $this->attendance = Attendance::findOrFail($id);
        $this->clockInTime = $this->attendance->clock_in_time ? $this->attendance->clock_in_time->format('Y-m-d\TH:i') : '';
        $this->clockOutTime = $this->attendance->clock_out_time ? $this->attendance->clock_out_time->format('Y-m-d\TH:i') : '';
    }

    public function update()
    {
        $this->validate([
            'clockInTime' => 'required|date',
            'clockOutTime' => 'nullable|date|after:clockInTime',
        ]);

        try {
            $service = app(AttendanceService::class);
            $data = [
                'clock_in_time' => $this->clockInTime,
                'clock_out_time' => $this->clockOutTime ?: null,
            ];
            
            $service->updateAttendance($this->attendance, $data, Auth::user());
            session()->flash('message', 'Attendance record updated successfully.');
            return redirect()->route('admin.attendances');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.edit-clock')->layout('layouts.app');
    }
}
