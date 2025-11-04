<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Your attendance analytics and statistics</p>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-6 bg-white shadow rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" wire:model.live="startDate"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" wire:model.live="endDate"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <!-- Today's Attendance Status -->
    @if($todayAttendance)
        <div class="mb-6 bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Today's Attendance</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Clock In</div>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $todayAttendance->clock_in_time ? $todayAttendance->clock_in_time->format('h:i A') : 'N/A' }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Clock Out</div>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $todayAttendance->clock_out_time ? $todayAttendance->clock_out_time->format('h:i A') : 'Not clocked out yet' }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="text-lg font-semibold">
                        @if($todayAttendance->status === 'present')
                            <span class="text-green-600">Present</span>
                        @elseif($todayAttendance->status === 'late')
                            <span class="text-yellow-600">Late</span>
                        @else
                            <span class="text-gray-600">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Hours -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Hours</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ number_format($analytics['total_hours'] ?? 0, 2) }} hrs
                    </div>
                </div>
            </div>
        </div>
        <!-- Late Arrivals -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Late Arrivals</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $analytics['late_arrivals'] ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <!-- Early Departures -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Early Departures</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $analytics['early_departures'] ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <!-- Attendance Percentage -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Attendance Rate</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ number_format($analytics['attendance_percentage'] ?? 0, 1) }}%
                    </div>
                </div>
            </div>
        </div>
        <!-- Overtime Hours -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Overtime Hours</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ number_format($analytics['overtime_hours'] ?? 0, 2) }} hrs
                    </div>
                </div>
            </div>
        </div>
        <!-- Break Time -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Avg Break Time</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ number_format($analytics['avg_break_time'] ?? 0, 2) }} hrs
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
