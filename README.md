# Attendify - Attendance Management System

An attendance management system for ECG Ghana built with Laravel 12, Livewire 3, and Tailwind CSS, containerized with Docker and Docker Compose.

## Table of Contents

-   [Features](#features)
    -   [User Features](#user-features)
    -   [Admin Features](#admin-features)
    -   [Department Head Features](#department-head-features)
-   [Technology Stack](#technology-stack)
-   [Prerequisites](#prerequisites)
-   [Installation](#installation)
-   [Accessing the Application](#accessing-the-application)
-   [Default Credentials](#default-credentials)
-   [Database Seeding](#database-seeding)
    -   [Initial Seeders](#initial-seeders)
    -   [Fake Data Seeder](#fake-data-seeder)
-   [Docker Services](#docker-services)
-   [Project Structure](#project-structure)
-   [Roles and Permissions](#roles-and-permissions)
    -   [User](#user)
    -   [Admin](#admin)
    -   [Department Head](#department-head)
-   [Key Features](#key-features)
    -   [Attendance Tracking](#attendance-tracking)
    -   [Activity Logging](#activity-logging)
    -   [Analytics](#analytics)
-   [Metrics and Calculations](#metrics-and-calculations)
    -   [Late Arrival Detection](#late-arrival-detection)
    -   [Total Hours Worked](#total-hours-worked)
    -   [Early Departure Detection](#early-departure-detection)
    -   [Attendance Percentage](#attendance-percentage)
    -   [Overtime Hours](#overtime-hours)
    -   [Average Break Time](#average-break-time)
    -   [Department Statistics (Admin Dashboard)](#department-statistics-admin-dashboard)
    -   [Employee Rankings](#employee-rankings)
    -   [Working Days Calculation](#working-days-calculation)
    -   [Standard Work Hours](#standard-work-hours)
-   [Configuration](#configuration)
-   [Development](#development)
    -   [Running Commands](#running-commands)
    -   [Frontend Development](#frontend-development)
    -   [Database Access](#database-access)
-   [Troubleshooting](#troubleshooting)
    -   [Permission Issues](#permission-issues)
    -   [Clear Cache](#clear-cache)
-   [License](#license)
-   [Support](#support)

## Features

### User Features

-   **Authentication**: Secure login system using Laravel Sanctum
-   **Clock In/Out**: Simple one-click clock-in and clock-out (once per day)
-   **My Attendance**: View personal attendance records with date range filtering
-   **Dashboard**: Comprehensive analytics dashboard showing:
    -   Total hours worked
    -   Late arrivals count
    -   Early departures count
    -   Attendance percentage
    -   Overtime hours tracking
    -   Average break time

### Admin Features

-   **All Attendances**: View and manage all employee attendance records
-   **Edit Attendance**: Modify any attendance record (clock-in/out times)
-   **User Management**: Full CRUD operations for creating and managing:
    -   Admin users
    -   Department Head users
    -   Regular users
-   **Department Management**: Full CRUD operations for departments with status (active/inactive)
-   **Analytics Dashboard**: Comprehensive dashboard with:
    -   Total and active employee counts
    -   Department performance statistics
    -   Employee rankings by hours worked
    -   Exportable reports

### Department Head Features

-   **Employee Management**: Add and manage employees within their assigned department
-   **Personal Dashboard**: View their own attendance analytics

## Technology Stack

-   **Backend**: Laravel 12
-   **Frontend**: Livewire 3, Tailwind CSS, Alpine.js
-   **Database**: MySQL 8.0
-   **Authentication**: Laravel Sanctum
-   **Authorization**: Spatie Laravel Permission (Role-Based Access Control)
-   **Containerization**: Docker & Docker Compose
-   **Build Tool**: Vite

## Prerequisites

-   Docker and Docker Compose installed
-   Git

## Installation

1. Clone the repository:

```bash
git clone https://github.com/zaidanali028/attendify.git
cd attendify
```

2. Build and start the Docker containers:

```bash
docker-compose up -d --build
```

3. Install PHP dependencies:

```bash
docker exec -it attendify_php composer install
```

4. Copy the environment file:

```bash
docker exec -it attendify_php cp .env.example .env
```

5. Generate application key:

```bash
docker exec -it attendify_php php artisan key:generate
```

6. Run migrations and seeders:

```bash
docker exec -it attendify_php php artisan migrate --seed
```

7. Set up storage link:

```bash
docker exec -it attendify_php php artisan storage:link
```

8. Build frontend assets:

```bash
docker exec -it attendify_php npm install
docker exec -it attendify_php npm run build
```

## Accessing the Application

-   **Application URL**: http://localhost:8888
-   **MySQL Port**: 3316

## Default Credentials

After running the seeders, you can login with:

### Admin Account

-   Email: `admin@ecgghana.com`
-   Password: `password`

### Department Head Account

-   Email: `depthead@ecgghana.com`
-   Password: `password`

### Regular User Account

-   Email: `user@ecgghana.com`
-   Password: `password`

## Database Seeding

The project includes seeders for setting up the database with initial data and optionally, comprehensive fake data for testing.

### Initial Seeders

The default seeders (`DatabaseSeeder`, `RolePermissionSeeder`, `DepartmentSeeder`) create:

-   **5 Sample Departments**: Human Resources, Finance, IT, Operations, Customer Service
-   **3 Default Roles**: User, Admin, Department Head
-   **All Required Permissions**: Clock management, dashboard access, user/department management
-   **3 Default Users**: One Admin, one Department Head, and one Regular User (see credentials above)

Run the initial seeders:

```bash
docker exec -it attendify_php php artisan db:seed
```

### Fake Data Seeder

For testing and demonstration purposes, a comprehensive fake data seeder (`FakeDataSeeder`) is available that generates:

-   **5 Department Heads** (one per department)
-   **50 Regular Users** (distributed across departments)
-   **2,000+ Attendance Records** over the last 60 days with realistic patterns:
    -   **70% On-time arrivals** (7:30 AM - 8:30 AM)
    -   **20% Slightly late** (8:31 AM - 9:30 AM)
    -   **10% Very late** (after 9:30 AM)
    -   **Early departures** (before 5 PM with < 8 hours)
    -   **Overtime scenarios** (> 8 hours worked)
    -   **Random absences** (~20% chance per weekday)
    -   **Weekend skipping** (realistic work patterns)

**Run the fake data seeder:**

```bash
docker exec -it attendify_php php artisan db:seed --class=FakeDataSeeder
```

**Note**: The fake data seeder requires that departments and roles/permissions already exist (run initial seeders first).

**Sample Credentials After Fake Data Seeding:**

-   Department Head 1: `depthead1@ecgghana.com` / `password`
-   Department Head 2: `depthead2@ecgghana.com` / `password`
-   User 1: `user1@ecgghana.com` / `password`
-   User 2: `user2@ecgghana.com` / `password`
-   ... and so on (up to user50@ecgghana.com)

**To run everything together:**

```bash
# Step 1: Run initial seeders
docker exec -it attendify_php php artisan db:seed

# Step 2: Run fake data seeder
docker exec -it attendify_php php artisan db:seed --class=FakeDataSeeder
```

**Or**, edit `database/seeders/DatabaseSeeder.php` and uncomment the line:

```php
$this->call(FakeDataSeeder::class);
```

Then run:

```bash
docker exec -it attendify_php php artisan db:seed
```

## Docker Services

-   **Nginx**: Web server (Port 8888)
-   **PHP-FPM**: PHP 8.2 with required extensions
-   **MySQL**: Database server (Port 3316)

## Project Structure

```
Attendify/
├── app/
│   ├── Livewire/          # Livewire components
│   │   ├── Admin/         # Admin components
│   │   ├── User/          # User components
│   │   ├── DepartmentHead/# Department Head components
│   │   └── Auth/          # Authentication components
│   ├── Models/            # Eloquent models
│   ├── Policies/          # Authorization policies
│   └── Services/          # Business logic services
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── views/             # Blade templates
│   └── css/               # Tailwind CSS
├── docker/
│   └── nginx/             # Nginx configuration
├── Dockerfile             # PHP-FPM container definition
└── docker-compose.yml     # Docker services configuration
```

## Roles and Permissions

The system uses three roles with different permissions:

### User

-   Clock in/out
-   View own attendance records
-   View own dashboard

### Admin

-   All User permissions
-   View all attendance records
-   Edit any attendance record
-   Manage users (create/edit admins, dept heads, users)
-   Manage departments (CRUD operations)
-   View comprehensive analytics dashboard
-   Export reports

### Department Head

-   View own dashboard
-   Manage employees in their assigned department
-   Add new employees to their department

## Key Features

### Attendance Tracking

-   Automatic late arrival detection (after 8:30 AM)
-   Early departure detection (before 5:00 PM with less than 8 hours)
-   Total hours calculation
-   One attendance record per user per day

### Activity Logging

-   All attendance entries are logged
-   Attendance modifications are tracked with audit trails
-   Includes who made changes and when

### Analytics

-   User-level analytics for personal insights
-   Admin-level analytics for organization-wide insights
-   Department performance comparisons
-   Employee rankings and statistics

## Metrics and Calculations

This section explains how the system calculates various metrics and attendance indicators.

### Late Arrival Detection

**Expected Clock-In Time Definition:**

The expected clock-in time is currently **hardcoded** in the following files:

-   `app/Services/AttendanceService.php`:
    -   Line 37: `setTime(8, 30, 0)` in `clockIn()` method
    -   Line 158: `setTime(8, 30, 0)` in `updateAttendance()` method
-   `app/Models/Attendance.php`:
    -   Line 55: `setTime(8, 30, 0)` in `checkLateArrival()` method

**To change the expected clock-in time**, modify the hour and minute values in these locations:

```php
$expectedTime = $now->copy()->setTime(8, 30, 0); // Change 8, 30 to desired time
```

**Calculation Method:**

-   **Expected Clock-In Time**: 8:30 AM (configurable in code)
-   **Detection Logic**: An employee is marked as late if their clock-in time is **after** 8:30 AM
-   **Implementation**: `clock_in_time > 08:30:00` on the same day
-   **Status Update**: Attendance status is set to `'late'` instead of `'present'` if late

**Example:**

-   Clock-in at 8:25 AM → **Not Late** (status: `present`)
-   Clock-in at 8:30 AM → **Not Late** (status: `present`)
-   Clock-in at 8:31 AM → **Late** (status: `late`)

### Total Hours Worked

**Calculation Method:**

-   **Formula**: `total_hours = clock_out_time - clock_in_time` (calculated in hours)
-   **Precision**: Rounded to 2 decimal places
-   **Method**: Uses Carbon's `diffInHours()` method with `true` parameter for precise calculation
-   **Unit**: Hours (e.g., 8.50 hours = 8 hours 30 minutes)

**Example:**

-   Clock-in: 08:00 AM, Clock-out: 05:00 PM → **9.00 hours**
-   Clock-in: 09:15 AM, Clock-out: 06:30 PM → **9.25 hours**
-   Clock-in: 08:30 AM, Clock-out: 12:45 PM → **4.25 hours**

**Note**: Total hours are only calculated when both clock-in and clock-out times are recorded.

### Early Departure Detection

**Expected Clock-Out Time Definition:**

The expected clock-out time is currently **hardcoded** in the following files:

-   `app/Services/AttendanceService.php`:
    -   Line 94: `setTime(17, 0, 0)` in `clockOut()` method
    -   Line 168: `setTime(17, 0, 0)` in `updateAttendance()` method
-   `app/Models/Attendance.php`:
    -   Line 64: `setTime(17, 0, 0)` in `checkEarlyDeparture()` method

**To change the expected clock-out time**, modify the hour and minute values in these locations:

```php
$expectedTime = $now->copy()->setTime(17, 0, 0); // Change 17, 0 to desired time (24-hour format)
```

**Calculation Method:**

-   **Expected Clock-Out Time**: 5:00 PM (17:00) - configurable in code
-   **Condition 1**: Employee clocks out **before** 5:00 PM (17:00)
-   **Condition 2**: Total hours worked is **less than** 8 hours
-   **Both conditions must be true** for an early departure flag

**Implementation:**

```php
$isEarlyDeparture = ($clock_out_time < 17:00) && ($total_hours < 8.00)
```

**Example:**

-   Clock-out at 4:30 PM with 7.5 hours → **Early Departure** ✓
-   Clock-out at 4:30 PM with 8.5 hours → **Not Early Departure** (worked > 8 hours)
-   Clock-out at 5:30 PM with 7.0 hours → **Not Early Departure** (left after 5 PM)
-   Clock-out at 5:30 PM with 9.0 hours → **Not Early Departure**

### Attendance Percentage

**Calculation Method:**

-   **Working Days Definition**: Monday through Friday (weekends excluded)
-   **Formula**: `(Days Attended / Total Working Days) × 100`
-   **Days Attended**: Count of unique days with attendance records in the date range
-   **Total Working Days**: Count of weekdays (Mon-Fri) in the selected date range

**Example:**

-   Date Range: Nov 1-7, 2024 (7 days = 5 working days: Mon-Fri)
-   Days Attended: 4
-   **Attendance Percentage**: (4 / 5) × 100 = **80%**

### Overtime Hours

**Calculation Method:**

-   **Standard Work Day**: 8 hours
-   **Overtime Definition**: Hours worked **in excess** of 8 hours per day
-   **Formula per Day**: `overtime = max(0, total_hours - 8)`
-   **Total Overtime**: Sum of overtime hours across all days in the date range

**Example:**

-   Day 1: 8.5 hours → Overtime: 0.5 hours
-   Day 2: 7.0 hours → Overtime: 0 hours
-   Day 3: 9.25 hours → Overtime: 1.25 hours
-   **Total Overtime**: 0.5 + 0 + 1.25 = **1.75 hours**

### Average Break Time

**Calculation Method:**

-   **For days with > 8 hours worked**: Assumes 1 hour break time
-   **For days with ≤ 8 hours worked**: Break time = `8 - total_hours`
    -   This represents the difference between standard work day and actual hours worked
-   **Average**: Mean of all break times calculated across the date range

**Example:**

-   Day 1: 9 hours worked → Break: 1.0 hour (assumed)
-   Day 2: 7.5 hours worked → Break: 0.5 hours (8 - 7.5)
-   Day 3: 8 hours worked → Break: 0 hours (8 - 8)
-   **Average Break Time**: (1.0 + 0.5 + 0) / 3 = **0.5 hours**

**Note**: Break time calculation assumes a standard 8-hour work day and may not reflect actual break durations taken.

### Department Statistics (Admin Dashboard)

**Metrics Calculated:**

1. **Average Hours Worked**: Mean of total hours across all attendance records in the department
2. **Average Late Arrivals**: Percentage of attendance records marked as late
3. **Average Early Departures**: Percentage of attendance records marked as early departure
4. **Average Attendance Percentage**:
    - `(Total Attended Days / Total Expected Days) × 100`
    - Expected Days = (Number of Employees) × (Working Days in Date Range)

### Employee Rankings

**Calculation:**

-   Employees are ranked by **total hours worked** in the selected date range
-   Rankings are sorted in descending order (highest hours first)
-   Top 10 employees are displayed on the admin dashboard

### Working Days Calculation

**Method:**

-   **Includes**: Monday through Friday
-   **Excludes**: Saturday and Sunday
-   **Implementation**: Iterates through each day in the date range and counts only weekdays using Carbon's `isWeekday()` method

**Example:**

-   Date Range: Nov 1-10, 2024
-   Total Days: 10
-   Working Days: 8 (excluding 2 weekends)

### Standard Work Hours

The standard work day duration is currently **hardcoded** in the analytics calculations:

-   **Standard Work Day**: 8 hours
-   Used for calculating:
    -   Overtime hours (hours worked > 8 hours)
    -   Break time estimations
    -   Early departure detection (when combined with early clock-out)

**Location in Code:**

-   `app/Services/AnalyticsService.php`: Lines 31-33 (overtime calculation)
-   `app/Services/AttendanceService.php`: Line 99 (early departure check)

**To change the standard work hours**, update the numeric value `8` in these calculations.

## Configuration

Currently, the expected clock-in time, expected clock-out time, and standard work hours are **hardcoded** in the source code. To make these configurable:

1.  Add configuration values to `.env` file:

    ```env
    EXPECTED_CLOCK_IN_HOUR=8
    EXPECTED_CLOCK_IN_MINUTE=30
    EXPECTED_CLOCK_OUT_HOUR=17
    EXPECTED_CLOCK_OUT_MINUTE=0
    STANDARD_WORK_HOURS=8
    ```

2.  Create a config file (`config/attendance.php`) to read these values

3.  Update the service classes to use `config('attendance.expected_clock_in_hour')` instead of hardcoded values

This would allow administrators to adjust these settings without modifying the source code.

## Development

### Running Commands

All artisan commands should be run inside the PHP container:

```bash
docker exec -it attendify_php php artisan [command]
```

### Frontend Development

Watch for changes during development:

```bash
docker exec -it attendify_php npm run dev
```

### Database Access

Access MySQL:

```bash
docker exec -it attendify_mysql mysql -u attendify -pattendify_password attendify
```

## Troubleshooting

### Permission Issues

If you encounter permission issues with storage or cache:

```bash
docker exec -it attendify_php chown -R www-data:www-data storage bootstrap/cache
docker exec -it attendify_php chmod -R 775 storage bootstrap/cache
```

### Clear Cache

```bash
docker exec -it attendify_php php artisan optimize:clear
docker exec -it attendify_php php artisan permission:cache-reset
```

## License

This project is proprietary software developed for ECG Ghana.

## Support

For issues or questions, please contact the development team.
