<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public $users = [];
    public $departments = [];
    public $roles = [];
    public $search = '';
    
    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    
    // Form properties
    public $editingId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $department_id = '';
    public $role = '';
    public $currentPassword = '';

    public function mount()
    {
        $this->loadUsers();
        $this->departments = Department::where('status', 1)->orderBy('name')->get();
        $this->roles = Role::whereIn('name', ['Admin', 'Department Head', 'User'])->get();
    }

    public function loadUsers()
    {
        $query = User::with(['department', 'roles']);
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }
        
        $this->users = $query->orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->loadUsers();
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
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:Admin,Department Head,User',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id ?: null,
        ]);

        $user->syncRoles([$this->role]);

        session()->flash('message', 'User created successfully.');
        $this->closeCreateModal();
        $this->loadUsers();
    }

    public function openEditModal($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department_id = $user->department_id;
        $this->role = $user->roles->first()->name ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function update()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingId,
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:Admin,Department Head,User',
        ];

        if ($this->password) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules);

        $user = User::findOrFail($this->editingId);
        
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id ?: null,
        ];

        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user->update($updateData);
        $user->syncRoles([$this->role]);

        session()->flash('message', 'User updated successfully.');
        $this->closeEditModal();
        $this->loadUsers();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->department_id = '';
        $this->role = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.users.index')->layout('layouts.app');
    }
}
