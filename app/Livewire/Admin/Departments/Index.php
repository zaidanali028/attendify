<?php

namespace App\Livewire\Admin\Departments;

use Livewire\Component;
use App\Models\Department;

class Index extends Component
{
    public $departments = [];
    public $search = '';
    
    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    // Form properties
    public $editingId = null;
    public $name = '';
    public $description = '';
    public $status = true;
    public $deletingId = null;

    public function mount()
    {
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        $query = Department::query();
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }
        
        $this->departments = $query->orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->loadDepartments();
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
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        Department::create([
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Department created successfully.');
        $this->closeCreateModal();
        $this->loadDepartments();
    }

    public function openEditModal($id)
    {
        $department = Department::findOrFail($id);
        $this->editingId = $department->id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->status = $department->status;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $this->editingId,
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $department = Department::findOrFail($this->editingId);
        $department->update([
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Department updated successfully.');
        $this->closeEditModal();
        $this->loadDepartments();
    }

    public function openDeleteModal($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function delete()
    {
        if ($this->deletingId) {
            $department = Department::findOrFail($this->deletingId);
            $department->delete();
            session()->flash('message', 'Department deleted successfully.');
            $this->closeDeleteModal();
            $this->loadDepartments();
        }
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->status = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.departments.index')->layout('layouts.app');
    }
}
