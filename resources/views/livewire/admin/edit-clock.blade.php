<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Attendance</h1>
        <p class="mt-2 text-gray-600">Modify attendance record for {{ $attendance->user->name }}</p>
    </div>

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

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6 p-4 bg-gray-50 rounded-md">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Employee</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $attendance->user->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Department</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $attendance->department->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Date</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $attendance->attendance_date->format('F d, Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Current Status</div>
                    <div class="text-lg font-semibold">
                        @if($attendance->status === 'present')
                            <span class="text-green-600">Present</span>
                        @elseif($attendance->status === 'late')
                            <span class="text-yellow-600">Late</span>
                        @elseif($attendance->status === 'completed')
                            <span class="text-blue-600">Completed</span>
                        @else
                            <span class="text-gray-600">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="update">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Clock In Time <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" wire:model="clockInTime" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('clockInTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Clock Out Time
                    </label>
                    <input type="datetime-local" wire:model="clockOutTime"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('clockOutTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.attendances') }}" wire:navigate
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Update Attendance
                </button>
            </div>
        </form>
    </div>
</div>
