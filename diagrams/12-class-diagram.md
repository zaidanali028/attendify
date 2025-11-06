# Class Diagram

## System Classes and Relationships

```mermaid
classDiagram
    class User {
        +bigint id
        +string name
        +string email
        +string password
        +bigint department_id
        +timestamp email_verified_at
        +timestamp created_at
        +timestamp updated_at
        +belongsTo(Department) department()
        +hasMany(Attendance) attendances()
        +hasMany(ActivityLog) activityLogs()
        +todayAttendance() Attendance
        +hasRole(string role) bool
        +hasPermission(string permission) bool
    }

    class Department {
        +bigint id
        +string name
        +text description
        +boolean status
        +timestamp created_at
        +timestamp updated_at
        +hasMany(User) users()
        +hasMany(Attendance) attendances()
    }

    class Attendance {
        +bigint id
        +bigint user_id
        +bigint department_id
        +datetime clock_in_time
        +datetime clock_out_time
        +date attendance_date
        +decimal total_hours
        +boolean is_late
        +boolean is_early_departure
        +string status
        +timestamp created_at
        +timestamp updated_at
        +belongsTo(User) user()
        +belongsTo(Department) department()
        +calculateTotalHours() float
        +checkLateArrival() bool
        +checkEarlyDeparture() bool
        +scopeDateRange(query, start, end) query
        +scopeForUser(query, userId) query
    }

    class ActivityLog {
        +bigint id
        +bigint user_id
        +string subject_type
        +bigint subject_id
        +string description
        +json properties
        +timestamp created_at
        +timestamp updated_at
        +belongsTo(User) user()
        +morphTo() subject()
    }

    class AttendanceService {
        -User user
        -Attendance attendance
        +clockIn(User user) Attendance
        +clockOut(User user) Attendance
        +updateAttendance(Attendance, array, User) Attendance
        -calculateLateArrival(datetime) bool
        -calculateEarlyDeparture(datetime, float) bool
        -calculateTotalHours(datetime, datetime) float
    }

    class AnalyticsService {
        +getUserAnalytics(User, Carbon, Carbon) array
        +getAdminAnalytics(Carbon, Carbon) array
        -getWorkingDays(Carbon, Carbon) int
        -calculateOvertime(Collection) float
        -calculateBreakTime(Collection) float
    }

    class ClockIn {
        +bool canClockIn
        +mount() void
        +clockIn() void
        +render() View
    }

    class ClockOut {
        +bool canClockOut
        +Attendance todayAttendance
        +mount() void
        +clockOut() void
        +render() View
    }

    class UserDashboard {
        +string startDate
        +string endDate
        +array analytics
        +mount() void
        +updatedStartDate() void
        +updatedEndDate() void
        +loadAnalytics() void
        +render() View
    }

    class AdminDashboard {
        +string startDate
        +string endDate
        +array analytics
        +mount() void
        +updatedStartDate() void
        +updatedEndDate() void
        +loadAnalytics() void
        +render() View
    }

    class Login {
        +string email
        +string password
        +bool remember
        +login() RedirectResponse
        +render() View
    }

    User "1" --> "*" Attendance : has
    User "1" --> "1" Department : belongs to
    User "1" --> "*" ActivityLog : performs
    Department "1" --> "*" User : contains
    Department "1" --> "*" Attendance : has
    Attendance "1" --> "1" User : belongs to
    Attendance "1" --> "1" Department : belongs to
    Attendance "1" --> "*" ActivityLog : generates
    ActivityLog "1" --> "1" User : created by

    AttendanceService ..> User : uses
    AttendanceService ..> Attendance : creates/updates
    AttendanceService ..> ActivityLog : creates
    AttendanceService ..> Department : reads

    AnalyticsService ..> User : uses
    AnalyticsService ..> Attendance : queries
    AnalyticsService ..> Department : queries

    ClockIn ..> AttendanceService : uses
    ClockIn ..> User : reads
    ClockOut ..> AttendanceService : uses
    ClockOut ..> User : reads
    UserDashboard ..> AnalyticsService : uses
    AdminDashboard ..> AnalyticsService : uses
    Login ..> User : authenticates
```

## Class Descriptions

### Domain Models

#### User

-   **Purpose**: Represents system users (employees, admins, department heads)
-   **Key Methods**:
    -   `department()`: Get user's department
    -   `attendances()`: Get user's attendance records
    -   `todayAttendance()`: Get today's attendance record
    -   `hasRole()`: Check if user has specific role (Spatie Permission)
    -   `hasPermission()`: Check if user has specific permission

#### Department

-   **Purpose**: Represents organizational departments
-   **Key Methods**:
    -   `users()`: Get all users in department
    -   `attendances()`: Get all attendance records for department

#### Attendance

-   **Purpose**: Represents attendance records
-   **Key Methods**:
    -   `calculateTotalHours()`: Calculate hours between clock in/out
    -   `checkLateArrival()`: Check if clock-in is after 8:30 AM
    -   `checkEarlyDeparture()`: Check if clock-out is before 5 PM with < 8 hours
    -   `scopeDateRange()`: Query scope for date filtering
    -   `scopeForUser()`: Query scope for user filtering

#### ActivityLog

-   **Purpose**: Audit trail of system activities
-   **Key Methods**:
    -   `user()`: Get user who performed action
    -   `subject()`: Get polymorphic subject (Attendance, User, etc.)

### Service Classes

#### AttendanceService

-   **Purpose**: Business logic for attendance operations
-   **Key Methods**:
    -   `clockIn()`: Process clock-in with validation and calculations
    -   `clockOut()`: Process clock-out with hour calculations
    -   `updateAttendance()`: Admin function to edit attendance records
-   **Responsibilities**:
    -   Validate business rules
    -   Calculate metrics (late, early, hours)
    -   Create activity logs
    -   Manage database transactions

#### AnalyticsService

-   **Purpose**: Calculate analytics and statistics
-   **Key Methods**:
    -   `getUserAnalytics()`: Calculate user-level metrics
    -   `getAdminAnalytics()`: Calculate organization-level metrics
    -   `getWorkingDays()`: Calculate working days (Mon-Fri)
-   **Responsibilities**:
    -   Aggregate attendance data
    -   Calculate percentages and averages
    -   Generate rankings
    -   Compute department statistics

### Livewire Components

#### ClockIn

-   **Purpose**: User interface for clocking in
-   **Properties**:
    -   `canClockIn`: Boolean flag for clock-in availability
-   **Methods**:
    -   `mount()`: Initialize component state
    -   `clockIn()`: Handle clock-in action
    -   `render()`: Render view

#### ClockOut

-   **Purpose**: User interface for clocking out
-   **Properties**:
    -   `canClockOut`: Boolean flag for clock-out availability
    -   `todayAttendance`: Today's attendance record
-   **Methods**:
    -   `mount()`: Initialize component state
    -   `clockOut()`: Handle clock-out action
    -   `render()`: Render view

#### UserDashboard

-   **Purpose**: Display user's personal analytics
-   **Properties**:
    -   `startDate`: Filter start date
    -   `endDate`: Filter end date
    -   `analytics`: Calculated analytics data
-   **Methods**:
    -   `loadAnalytics()`: Fetch and calculate analytics
    -   `updatedStartDate()`: React to date change
    -   `updatedEndDate()`: React to date change

#### AdminDashboard

-   **Purpose**: Display organization-wide analytics
-   **Properties**: Similar to UserDashboard
-   **Methods**: Similar to UserDashboard but uses `getAdminAnalytics()`

#### Login

-   **Purpose**: Authentication interface
-   **Properties**:
    -   `email`: User email
    -   `password`: User password
    -   `remember`: Remember me flag
-   **Methods**:
    -   `login()`: Authenticate and redirect based on role

## Relationships

### Associations

-   **User ↔ Department**: Many-to-One (belongsTo)
-   **User ↔ Attendance**: One-to-Many (hasMany)
-   **Department ↔ Attendance**: One-to-Many (hasMany)
-   **User ↔ ActivityLog**: One-to-Many (hasMany)
-   **Attendance ↔ ActivityLog**: One-to-Many (polymorphic)

### Dependencies

-   **Services → Models**: Services use models for data access
-   **Components → Services**: Components use services for business logic
-   **Components → Models**: Components read model data for display

## Design Patterns

1. **Service Layer Pattern**: Business logic separated into service classes
2. **Repository Pattern**: Eloquent ORM acts as repository
3. **Active Record Pattern**: Models contain data and behavior
4. **Component Pattern**: Livewire components encapsulate UI and logic
5. **Dependency Injection**: Services injected into components
