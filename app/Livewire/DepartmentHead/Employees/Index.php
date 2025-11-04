<?php

namespace App\Livewire\DepartmentHead\Employees;

use Livewire\Component;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public $employees = [];
    public $search = '';
    
    // Modal states
    public $showCreateModal = false;
    
    // Form properties
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        $user = Auth::user();
        
        if (!$user->department_id) {
            $this->employees = collect([]);
            return;
        }

        $query = User::with('roles')
            ->where('department_id', $user->department_id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'User');
            });
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        $this->employees = $query->orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->loadEmployees();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->department_id) {
            session()->flash('error', 'You must be assigned to a department to add employees.');
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $newUser = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $user->department_id,
        ]);

        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $newUser->syncRoles([$userRole]);
        }

        session()->flash('message', 'Employee added successfully.');
        $this->closeCreateModal();
        $this->loadEmployees();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation();
    }

    public function render()
    {
        $department = Auth::user()->department;
        return view('livewire.department-head.employees.index', compact('department'))->layout('layouts.app');
    }
}
