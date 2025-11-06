# Component Diagram

## System Components and Dependencies

```mermaid
graph TB
    subgraph PresentationLayer["Presentation Layer"]
        LivewireComponents["Livewire Components"]
        BladeViews["Blade Templates"]
        TailwindCSS["Tailwind CSS"]
        AlpineJS["Alpine.js"]
    end

    subgraph ApplicationLayer["Application Layer"]
        Controllers["HTTP Controllers"]
        Middleware["Middleware<br/>- Auth<br/>- Role Check<br/>- Permission Check"]
        Routes["Route Definitions"]
    end

    subgraph BusinessLogicLayer["Business Logic Layer"]
        AttendanceService["AttendanceService"]
        AnalyticsService["AnalyticsService"]
    end

    subgraph DomainLayer["Domain Layer"]
        UserModel["User Model"]
        AttendanceModel["Attendance Model"]
        DepartmentModel["Department Model"]
        ActivityLogModel["ActivityLog Model"]
    end

    subgraph InfrastructureLayer["Infrastructure Layer"]
        EloquentORM["Eloquent ORM"]
        DatabaseDriver["Database Driver"]
        SessionManager["Session Manager"]
        CacheManager["Cache Manager"]
    end

    subgraph ExternalServices["External Services"]
        SpatiePermission["Spatie Permission<br/>RBAC Package"]
        LaravelSanctum["Laravel Sanctum<br/>Authentication"]
    end

    subgraph DataStorage["Data Storage"]
        MySQL[("MySQL Database")]
        SessionStore[("Session Store")]
        CacheStore[("Cache Store")]
    end

    %% Presentation to Application
    LivewireComponents --> Controllers
    LivewireComponents --> BladeViews
    BladeViews --> TailwindCSS
    BladeViews --> AlpineJS

    %% Application Layer
    Routes --> Controllers
    Routes --> Middleware
    Controllers --> LivewireComponents

    %% Application to Business Logic
    Controllers --> AttendanceService
    Controllers --> AnalyticsService
    LivewireComponents --> AttendanceService
    LivewireComponents --> AnalyticsService

    %% Business Logic to Domain
    AttendanceService --> UserModel
    AttendanceService --> AttendanceModel
    AttendanceService --> DepartmentModel
    AttendanceService --> ActivityLogModel

    AnalyticsService --> UserModel
    AnalyticsService --> AttendanceModel
    AnalyticsService --> DepartmentModel

    %% Domain to Infrastructure
    UserModel --> EloquentORM
    AttendanceModel --> EloquentORM
    DepartmentModel --> EloquentORM
    ActivityLogModel --> EloquentORM

    %% Infrastructure to Storage
    EloquentORM --> DatabaseDriver
    DatabaseDriver --> MySQL

    Middleware --> SessionManager
    SessionManager --> SessionStore

    Middleware --> CacheManager
    CacheManager --> CacheStore

    %% External Services
    UserModel --> SpatiePermission
    Controllers --> SpatiePermission
    Middleware --> SpatiePermission

    Controllers --> LaravelSanctum
    Middleware --> LaravelSanctum
    SessionManager --> LaravelSanctum
```

## Component Descriptions

### Presentation Layer

#### Livewire Components

-   **Admin Components**: AdminDashboard, AllAttendance, EditClock, Departments/Index, Users/Index, ActivityLogs
-   **User Components**: UserDashboard, ClockIn, ClockOut, MyAttendance
-   **Department Head Components**: Employees/Index
-   **Auth Components**: Login

#### Blade Templates

-   View templates for each Livewire component
-   Layout templates (app.blade.php)
-   Component templates

#### Frontend Technologies

-   **Tailwind CSS**: Utility-first CSS framework
-   **Alpine.js**: Lightweight JavaScript framework

### Application Layer

#### HTTP Controllers

-   Base Controller class
-   Handles HTTP requests and responses

#### Middleware

-   **Authentication Middleware**: Verifies user is logged in
-   **Role Middleware**: Checks user has specific role
-   **Permission Middleware**: Checks user has specific permission

#### Routes

-   Web route definitions
-   Route groups with middleware
-   Named routes for navigation

### Business Logic Layer

#### AttendanceService

**Responsibilities**:

-   Clock in/out operations
-   Attendance record updates
-   Late arrival detection
-   Early departure detection
-   Total hours calculation

**Dependencies**: User, Attendance, Department, ActivityLog models

#### AnalyticsService

**Responsibilities**:

-   User-level analytics calculation
-   Admin-level analytics calculation
-   Department statistics
-   Employee rankings

**Dependencies**: User, Attendance, Department models

### Domain Layer

#### Models

-   **User**: Represents system users
-   **Attendance**: Represents attendance records
-   **Department**: Represents organizational departments
-   **ActivityLog**: Represents audit trail entries

**Relationships**:

-   User belongsTo Department
-   User hasMany Attendances
-   User hasMany ActivityLogs
-   Department hasMany Users
-   Department hasMany Attendances
-   Attendance belongsTo User
-   Attendance belongsTo Department
-   ActivityLog belongsTo User
-   ActivityLog morphTo (polymorphic)

### Infrastructure Layer

#### Eloquent ORM

-   Database abstraction layer
-   Query builder
-   Relationship management
-   Model lifecycle hooks

#### Database Driver

-   MySQL PDO driver
-   Connection management
-   Query execution

#### Session Manager

-   Session storage and retrieval
-   Session security
-   Session expiration

#### Cache Manager

-   Application cache
-   Query result caching
-   Configuration caching

### External Services

#### Spatie Permission

-   Role management
-   Permission management
-   Role-permission assignments
-   User-role assignments

#### Laravel Sanctum

-   Session-based authentication
-   API token authentication
-   CSRF protection

### Data Storage

#### MySQL Database

-   Primary data storage
-   Relational data management
-   Transaction support

#### Session Store

-   User session data
-   Flash messages
-   CSRF tokens

#### Cache Store

-   Application cache
-   Configuration cache
-   Route cache

## Component Interactions

1. **User Request Flow**:

    - Browser → Routes → Middleware → Controller/Livewire → Service → Model → Database

2. **Authentication Flow**:

    - Login → Auth Service → Session Manager → Sanctum → Database

3. **Authorization Flow**:

    - Request → Middleware → Spatie Permission → Database → Allow/Deny

4. **Data Flow**:
    - Service → Model → Eloquent → Database Driver → MySQL

## Dependency Direction

-   **High-level components** depend on **low-level components**
-   **Business logic** depends on **domain models**
-   **Domain models** depend on **infrastructure**
-   **External services** are injected via dependency injection
