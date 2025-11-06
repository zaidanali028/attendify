# Data Flow Diagram - Level 0 (Context Diagram)

## System Overview

```mermaid
flowchart TD
    subgraph ExternalEntities["External Entities"]
        User["User/Employee"]
        Admin["Administrator"]
        DeptHead["Department Head"]
    end

    subgraph SystemBoundary["Attendify System"]
        System["System Core"]
    end

    subgraph DataStores["Data Stores"]
        Database[("MySQL Database")]
        ActivityLog[("Activity Log")]
    end

    User -->|Login Credentials| System
    User -->|Clock In/Out Requests| System
    User -->|View Attendance Data| System
    Admin -->|Login Credentials| System
    Admin -->|Manage Users/Departments| System
    Admin -->|View/Edit Attendance| System
    Admin -->|View Analytics| System
    DeptHead -->|Login Credentials| System
    DeptHead -->|Manage Employees| System
    DeptHead -->|View Department Data| System
    System -->|Attendance Records| User
    System -->|Dashboard/Analytics| User
    System -->|Management Interface| Admin
    System -->|Reports/Analytics| Admin
    System -->|Employee Management| DeptHead
    System -->|Store Data| Database
    Database -->|Retrieve Data| System
    System -->|Log Activities| ActivityLog
    ActivityLog -->|Audit Trail| System
```

## External Entities

1. **User/Employee**: Regular employees who clock in/out and view their attendance
2. **Administrator**: System administrators with full access
3. **Department Head**: Department managers who manage employees

## Data Stores

1. **MySQL Database**: Primary data storage for all entities
2. **Activity Log**: Audit trail of all system activities

## Data Flows

### Input Flows

-   Login credentials (email, password)
-   Clock in/out requests
-   User management operations
-   Department management operations
-   Attendance edit requests
-   Analytics date range filters

### Output Flows

-   Authentication responses
-   Attendance records
-   Dashboard data
-   Analytics and reports
-   Management interfaces
-   Activity logs
