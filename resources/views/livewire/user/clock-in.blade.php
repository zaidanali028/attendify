<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Clock In</h1>
        <p class="mt-2 text-gray-600">Record your clock-in time for today</p>
    </div>

    @if(session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if(!auth()->user()->department_id)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">No Department Assigned</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>You must be assigned to a department before you can clock in. Please contact an administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($canClockIn)
        <div class="bg-white shadow rounded-lg p-6">
            <div class="mb-6">
                <div class="text-sm text-gray-500">Current Time</div>
                <div class="text-2xl font-semibold text-gray-900" x-data="{ time: new Date().toLocaleTimeString() }" x-init="setInterval(() => time = new Date().toLocaleTimeString(), 1000)" x-text="time"></div>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded-md">
                <div class="text-sm text-gray-500">Department</div>
                <div class="text-lg font-semibold text-gray-900">{{ auth()->user()->department->name }}</div>
            </div>

            <button wire:click="clockIn"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Clock In
            </button>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Already Clocked In</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>You have already clocked in today. You can clock out when you're ready to leave.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
