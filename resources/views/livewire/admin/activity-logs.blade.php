<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
        <p class="mt-2 text-gray-600">View all attendance-related activities and modifications</p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Description</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search activities..." 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select wire:model.live="userId" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject Type</label>
                <select wire:model.live="subjectType" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">
                    <option value="">All Types</option>
                    @foreach($subjectTypes as $type)
                        <option value="{{ $type }}">{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Properties</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activityLogs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="font-medium text-gray-900">{{ $log->user->name ?? 'N/A' }}</div>
                                <div class="text-gray-500 text-xs">{{ $log->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->subject)
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                @else
                                    <span class="text-gray-400">Deleted</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->properties && count($log->properties) > 0)
                                    <div class="max-w-xs">
                                        <details class="cursor-pointer">
                                            <summary class="text-blue-600 hover:text-blue-800">View Details</summary>
                                            <div class="mt-2 p-2 bg-gray-50 rounded text-xs">
                                                @if(isset($log->properties['old_data']) && isset($log->properties['new_data']))
                                                    <div class="mb-2">
                                                        <strong>Old Data:</strong>
                                                        <pre class="mt-1 text-red-600">{{ json_encode($log->properties['old_data'], JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                    <div>
                                                        <strong>New Data:</strong>
                                                        <pre class="mt-1 text-green-600">{{ json_encode($log->properties['new_data'], JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                    @if(isset($log->properties['updated_by']))
                                                        <div class="mt-2 text-gray-600">
                                                            <strong>Updated By:</strong> {{ $log->properties['updated_by'] }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <pre>{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                                @endif
                                            </div>
                                        </details>
                                    </div>
                                @else
                                    <span class="text-gray-400">No properties</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($activityLogs, 'links'))
            <div class="px-6 py-4">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </div>
</div>
