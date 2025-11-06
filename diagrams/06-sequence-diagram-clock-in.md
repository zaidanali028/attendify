# Sequence Diagram - Clock In Process

## Clock In Flow

```mermaid
sequenceDiagram
    participant User
    participant ClockInComponent as ClockIn Component<br/>(Livewire)
    participant AuthService as Auth Service
    participant AttendanceService as AttendanceService
    participant AttendanceModel as Attendance Model
    participant ActivityLogModel as ActivityLog Model
    participant Database as MySQL Database

    User->>ClockInComponent: Click "Clock In" Button
    ClockInComponent->>ClockInComponent: Check canClockIn flag

    alt Already Clocked In
        ClockInComponent->>User: Show Error: "Already clocked in"
    else Not Clocked In
        ClockInComponent->>AuthService: Get Current User
        AuthService-->>ClockInComponent: Return User Object

        ClockInComponent->>ClockInComponent: Check if user has department

        alt No Department Assigned
            ClockInComponent->>User: Show Error: "Must be assigned to department"
        else Has Department
            ClockInComponent->>AttendanceService: clockIn(user)

            AttendanceService->>Database: Check existing attendance for today
            Database-->>AttendanceService: Return attendance record (if exists)

            alt Attendance Already Exists
                AttendanceService-->>ClockInComponent: Throw Exception: "Already clocked in"
                ClockInComponent->>User: Show Error Message
            else No Existing Attendance
                AttendanceService->>AttendanceService: Get current time (Carbon::now())
                AttendanceService->>AttendanceService: Calculate if late (> 8:30 AM)
                AttendanceService->>AttendanceService: Determine status (late/present)

                AttendanceService->>Database: Begin Transaction

                AttendanceService->>AttendanceModel: Create attendance record
                AttendanceModel->>Database: INSERT INTO attendances
                Database-->>AttendanceModel: Return new attendance ID
                AttendanceModel-->>AttendanceService: Attendance object

                AttendanceService->>ActivityLogModel: Create activity log
                ActivityLogModel->>Database: INSERT INTO activity_logs
                Database-->>ActivityLogModel: Return log ID
                ActivityLogModel-->>AttendanceService: ActivityLog object

                AttendanceService->>Database: Commit Transaction
                AttendanceService-->>ClockInComponent: Return Attendance object

                ClockInComponent->>ClockInComponent: Set canClockIn = false
                ClockInComponent->>ClockInComponent: Dispatch 'attendance-updated' event
                ClockInComponent->>User: Show Success: "Clocked in successfully!"
            end
        end
    end
```

## Key Steps

1. **User Action**: User clicks "Clock In" button
2. **Validation**: Component checks if user can clock in
3. **Department Check**: Verifies user has department assignment
4. **Duplicate Check**: Service checks for existing attendance today
5. **Time Calculation**: Determines if arrival is late (after 8:30 AM)
6. **Database Transaction**: Creates attendance record atomically
7. **Activity Logging**: Records the clock-in action
8. **User Feedback**: Displays success/error message

## Data Flow

-   **Input**: User click action
-   **Processing**:
    -   Current timestamp
    -   Late arrival calculation
    -   Status determination
-   **Output**:
    -   Attendance record with clock_in_time
    -   Activity log entry
    -   Success confirmation

## Error Handling

-   **Already Clocked In**: Prevents duplicate clock-ins
-   **No Department**: Requires department assignment
-   **Database Errors**: Transaction rollback on failure
