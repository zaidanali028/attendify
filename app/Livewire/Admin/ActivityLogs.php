<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $userId = '';
    public $subjectType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'userId' => ['except' => ''],
        'subjectType' => ['except' => ''],
    ];



    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUserId()
    {
        $this->resetPage();
    }

    public function updatedSubjectType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::with(['user', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filter by search (description)
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Filter by user
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        // Filter by subject type
        if ($this->subjectType) {
            $query->where('subject_type', $this->subjectType);
        }

        $activityLogs = $query->paginate(20);

        // Get unique users for filter dropdown
        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.admin.activity-logs', [
            'activityLogs' => $activityLogs,
            'users' => $users,
            'subjectTypes' => ActivityLog::distinct()->pluck('subject_type')->filter()->sort(),
        ])->layout('layouts.app');
    }
}
