<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Employee Management</h1>
                <p class="mt-2 text-gray-600">
                    @if($department)
                        Manage employees in {{ $department->name }} department
                    @else
                        Manage employees
                    @endif
                </p>
            </div>
            @if($department)
                <button wire:click="openCreateModal" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Add Employee
                </button>
            @endif
        </div>
    </div>

    @if(!$department)
        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">No Department Assigned</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>You must be assigned to a department to manage employees. Please contact an administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($department)
        <!-- Search -->
        <div class="mb-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search employees..." 
                   class="w-full max-w-md border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Employees Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($employees as $employee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $employee->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee->department->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No employees found in your department.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        @if($showCreateModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeCreateModal">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add Employee</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form wire:submit.prevent="create">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" wire:model="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" wire:model="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-600">This employee will be automatically assigned to <strong>{{ $department->name }}</strong> department.</p>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add Employee</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
