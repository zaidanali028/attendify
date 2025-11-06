# Data Flow Diagram - Level 1 (System Decomposition)

## Detailed System Processes

```mermaid
flowchart TD
    subgraph ExternalEntities["External Entities"]
        User["User/Employee"]
        Admin["Administrator"]
        DeptHead["Department Head"]
    end

    subgraph SystemProcesses["System Processes"]
        P1["1.0 Authentication<br/>Process"]
        P2["2.0 Attendance<br/>Management"]
        P3["3.0 User<br/>Management"]
        P4["4.0 Department<br/>Management"]
        P5["5.0 Analytics<br/>Processing"]
        P6["6.0 Activity<br/>Logging"]
    end

    subgraph DataStores["Data Stores"]
        D1[("D1: Users")]
        D2[("D2: Attendances")]
        D3[("D3: Departments")]
        D4[("D4: Activity Logs")]
        D5[("D5: Roles &<br/>Permissions")]
    end

    %% User Flows
    User -->|Login Request| P1
    User -->|Clock In/Out| P2
    User -->|View Dashboard| P5
    P1 -->|Session| User
    P2 -->|Confirmation| User
    P5 -->|Analytics Data| User

    %% Admin Flows
    Admin -->|Login Request| P1
    Admin -->|Manage Users| P3
    Admin -->|Manage Departments| P4
    Admin -->|Edit Attendance| P2
    Admin -->|View Analytics| P5
    P1 -->|Session| Admin
    P3 -->|User List| Admin
    P4 -->|Department List| Admin
    P2 -->|Attendance Records| Admin
    P5 -->|Analytics| Admin

    %% Dept Head Flows
    DeptHead -->|Login Request| P1
    DeptHead -->|Manage Employees| P3
    P1 -->|Session| DeptHead
    P3 -->|Employee List| DeptHead

    %% Process to Data Store Flows
    P1 -->|Read User Data| D1
    P1 -->|Read Roles| D5
    P1 -->|Write Session| D1
    P2 -->|Read User/Dept| D1
    P2 -->|Read/Write Attendance| D2
    P2 -->|Read Department| D3
    P2 -->|Trigger Log| P6
    P3 -->|Read/Write Users| D1
    P3 -->|Read Departments| D3
    P3 -->|Read/Write Roles| D5
    P3 -->|Trigger Log| P6
    P4 -->|Read/Write Departments| D3
    P4 -->|Read Users| D1
    P4 -->|Trigger Log| P6
    P5 -->|Read Attendances| D2
    P5 -->|Read Users| D1
    P5 -->|Read Departments| D3
    P5 -->|Calculate Metrics| P5
    P6 -->|Write Log Entry| D4
    P2 -->|User ID, Action| P6
    P3 -->|User ID, Action| P6
    P4 -->|User ID, Action| P6
```

## Process Descriptions

### 1.0 Authentication Process

-   **Input**: Email, Password, Remember Me
-   **Process**: Validate credentials, check roles/permissions
-   **Output**: Session token, redirect based on role
-   **Data Stores**: D1 (Users), D5 (Roles & Permissions)

### 2.0 Attendance Management

-   **Input**: Clock in/out requests, attendance edits
-   **Process**:
    -   Validate user has department
    -   Check for existing attendance
    -   Calculate late/early departure
    -   Calculate total hours
    -   Update attendance records
-   **Output**: Attendance confirmation, updated records
-   **Data Stores**: D1, D2, D3
-   **Triggers**: P6 (Activity Logging)

### 3.0 User Management

-   **Input**: Create/Edit/Delete user requests
-   **Process**:
    -   Validate user data
    -   Assign roles and departments
    -   Hash passwords
    -   Manage user relationships
-   **Output**: User list, confirmation messages
-   **Data Stores**: D1, D3, D5
-   **Triggers**: P6 (Activity Logging)

### 4.0 Department Management

-   **Input**: Create/Edit/Delete department requests
-   **Process**:
    -   Validate department data
    -   Manage department status (active/inactive)
    -   Handle employee assignments
-   **Output**: Department list, confirmation messages
-   **Data Stores**: D3, D1
-   **Triggers**: P6 (Activity Logging)

### 5.0 Analytics Processing

-   **Input**: Date range, user/department filters
-   **Process**:
    -   Aggregate attendance data
    -   Calculate metrics (hours, late arrivals, etc.)
    -   Generate department statistics
    -   Rank employees
-   **Output**: Analytics data, reports
-   **Data Stores**: D2, D1, D3

### 6.0 Activity Logging

-   **Input**: User actions, subject data
-   **Process**:
    -   Capture action details
    -   Store old/new data for updates
    -   Record timestamp and user
-   **Output**: Activity log entries
-   **Data Store**: D4

## Data Store Contents

-   **D1: Users**: User accounts, credentials, department assignments
-   **D2: Attendances**: Clock in/out times, hours, status, dates
-   **D3: Departments**: Department information, status
-   **D4: Activity Logs**: Audit trail of all system activities
-   **D5: Roles & Permissions**: RBAC configuration (Spatie Permission)
