<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;

class AllAttendance extends Component
{
    use WithPagination;

    public $users = [];
    public $departments = [];
    public $search = '';
    public $userId = '';
    public $departmentId = '';
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->users = User::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUserId()
    {
        $this->resetPage();
    }

    public function updatedDepartmentId()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Attendance::with(['user', 'department']);

        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            $query->whereBetween('attendance_date', [$start, $end]);
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->paginate(20);

        return view('livewire.admin.all-attendance', compact('attendances'))->layout('layouts.app');
    }
}
