<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Comprehensive attendance analytics and statistics</p>
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

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Employees</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $analytics['total_users'] ?? 0 }}</div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Active Employees</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $analytics['active_users'] ?? 0 }}</div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Hours Worked</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($analytics['total_hours_worked'] ?? 0, 2) }} hrs</div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Late Arrivals</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $analytics['total_late_arrivals'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="mb-6 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Department Performance</h2>
        @if(!empty($analytics['department_stats']))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Attendance %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($analytics['department_stats'] as $deptStat)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $deptStat['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $deptStat['total_employees'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($deptStat['avg_hours_worked'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($deptStat['avg_attendance_percentage'], 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">No department statistics available for the selected period.</p>
        @endif
    </div>

    <!-- Employee Rankings -->
    <div class="mb-6 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Employees (Hours Worked)</h2>
        @if(!empty($analytics['employee_rankings']))
            <ul class="divide-y divide-gray-200">
                @foreach($analytics['employee_rankings'] as $employee)
                    <li class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-900">{{ $employee['name'] }} ({{ $employee['department'] }})</span>
                        <span class="text-sm text-gray-600">{{ number_format($employee['total_hours'], 2) }} hrs</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500">No employee rankings available for the selected period.</p>
        @endif
    </div>
</div>
