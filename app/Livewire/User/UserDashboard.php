<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboard extends Component
{
    public $startDate;
    public $endDate;
    public $analytics = [];
    public $todayAttendance;

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->todayAttendance = Auth::user()->todayAttendance();
        $this->loadAnalytics();
    }

    public function updatedStartDate()
    {
        $this->loadAnalytics();
    }

    public function updatedEndDate()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $service = app(AnalyticsService::class);
        $this->analytics = $service->getUserAnalytics(Auth::user(), $start, $end);
    }

    public function render()
    {
        return view('livewire.user.user-dashboard')->layout('layouts.app');
    }
}
