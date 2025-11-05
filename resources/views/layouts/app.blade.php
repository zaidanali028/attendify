<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Attendify') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    @auth
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <div class="flex items-center space-x-3 mb-2">
                  
                </div>
                <h1 class="text-2xl font-bold">Attendify</h1>
                <p class="text-gray-400 text-sm">ECG Ghana</p>
            </div>
            <nav class="mt-8">
                @if(auth()->user()->hasRole('Admin'))
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.attendances') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.attendances') || request()->routeIs('admin.attendances.edit') ? 'bg-gray-700' : '' }}">
                        All Attendances
                    </a>
                    <a href="{{ route('admin.departments.index') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.departments.*') ? 'bg-gray-700' : '' }}">
                        Departments
                    </a>
                    <a href="{{ route('admin.users.index') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">
                        User Management
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.activity-logs') ? 'bg-gray-700' : '' }}">
                        Activity Logs
                    </a>
                @elseif(auth()->user()->hasRole('Department Head'))
                    <a href="{{ route('dept-head.employees.index') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('dept-head.*') ? 'bg-gray-700' : '' }}">
                        Employee Management
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('user.dashboard') ? 'bg-gray-700' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('user.clock-in') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('user.clock-in') ? 'bg-gray-700' : '' }}">
                        Clock In
                    </a>
                    <a href="{{ route('user.clock-out') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('user.clock-out') ? 'bg-gray-700' : '' }}">
                        Clock Out
                    </a>
                    <a href="{{ route('user.my-attendance') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('user.my-attendance') ? 'bg-gray-700' : '' }}">
                        My Attendance
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-700">
                        Logout
                    </button>
                </form>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div>
                {{ $slot }}
            </div>
        </main>
    </div>
    @else
    <div class="min-h-screen flex items-center justify-center">
        {{ $slot }}
    </div>
    @endauth
    @livewireScripts
</body>
</html> 