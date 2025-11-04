<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        if (auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('Department Head')) {
            return redirect()->route('dept-head.employees.index');
        }
        return redirect()->route('user.dashboard');
    });

    // User routes
    Route::middleware(['permission:dashboard.view-own'])->group(function () {
        Route::get('/dashboard', \App\Livewire\User\UserDashboard::class)->name('user.dashboard');
        Route::get('/clock-in', \App\Livewire\User\ClockIn::class)->name('user.clock-in');
        Route::get('/clock-out', \App\Livewire\User\ClockOut::class)->name('user.clock-out');
        Route::get('/my-attendance', \App\Livewire\User\MyAttendance::class)->name('user.my-attendance');
    });

    // Admin routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\AdminDashboard::class)->name('dashboard');
        Route::get('/attendances', \App\Livewire\Admin\AllAttendance::class)->name('attendances');
        Route::get('/attendances/{id}/edit', \App\Livewire\Admin\EditClock::class)->name('attendances.edit');
        Route::get('/departments', \App\Livewire\Admin\Departments\Index::class)->name('departments.index');
        Route::get('/users', \App\Livewire\Admin\Users\Index::class)->name('users.index');
    });

    // Department Head routes
    Route::middleware(['role:Department Head'])->prefix('dept-head')->name('dept-head.')->group(function () {
        Route::get('/employees', \App\Livewire\DepartmentHead\Employees\Index::class)->name('employees.index');
    });
});
