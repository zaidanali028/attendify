# Attendify - Attendance Management System

An attendance management system for ECG Ghana built with Laravel 12, Livewire 3, and Tailwind CSS, containerized with Docker and Docker Compose.

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
