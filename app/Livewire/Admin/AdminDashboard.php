<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\AnalyticsService;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public $startDate;
    public $endDate;
    public $analytics = [];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
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
        $this->analytics = $service->getAdminAnalytics($start, $end);
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard')->layout('layouts.app');
    }
}
