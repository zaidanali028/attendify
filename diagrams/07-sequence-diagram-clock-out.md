# Sequence Diagram - Clock Out Process

## Clock Out Flow

```mermaid
sequenceDiagram
    participant User
    participant ClockOutComponent as ClockOut Component<br/>(Livewire)
    participant AuthService as Auth Service
    participant AttendanceService as AttendanceService
    participant AttendanceModel as Attendance Model
    participant ActivityLogModel as ActivityLog Model
    participant Database as MySQL Database

    User->>ClockOutComponent: Click "Clock Out" Button
    ClockOutComponent->>ClockOutComponent: Check canClockOut flag

    alt Not Clocked In
        ClockOutComponent->>User: Show Error: "Must clock in first"
    else Clocked In
        ClockOutComponent->>AuthService: Get Current User
        AuthService-->>ClockOutComponent: Return User Object

        ClockOutComponent->>ClockOutComponent: Get today's attendance

        alt Already Clocked Out
            ClockOutComponent->>User: Show Error: "Already clocked out"
        else Not Clocked Out
            ClockOutComponent->>AttendanceService: clockOut(user)

            AttendanceService->>Database: Find today's attendance
            Database-->>AttendanceService: Return attendance record

            alt No Attendance Found
                AttendanceService-->>ClockOutComponent: Throw Exception: "Must clock in first"
                ClockOutComponent->>User: Show Error Message
            else Attendance Found
                AttendanceService->>AttendanceService: Get current time (Carbon::now())
                AttendanceService->>AttendanceService: Calculate total hours<br/>(clock_out - clock_in)
                AttendanceService->>AttendanceService: Check early departure<br/>(< 5 PM AND < 8 hours)
                AttendanceService->>AttendanceService: Update status to 'completed'

                AttendanceService->>Database: Begin Transaction

                AttendanceService->>AttendanceModel: Update attendance record
                AttendanceModel->>Database: UPDATE attendances SET<br/>clock_out_time, total_hours,<br/>is_early_departure, status
                Database-->>AttendanceModel: Return updated record
                AttendanceModel-->>AttendanceService: Updated Attendance object

                AttendanceService->>ActivityLogModel: Create activity log
                ActivityLogModel->>Database: INSERT INTO activity_logs<br/>(clock_out_time, total_hours,<br/>is_early_departure)
                Database-->>ActivityLogModel: Return log ID
                ActivityLogModel-->>AttendanceService: ActivityLog object

                AttendanceService->>Database: Commit Transaction
                AttendanceService-->>ClockOutComponent: Return updated Attendance object

                ClockOutComponent->>ClockOutComponent: Set canClockOut = false
                ClockOutComponent->>ClockOutComponent: Refresh todayAttendance
                ClockOutComponent->>ClockOutComponent: Dispatch 'attendance-updated' event
                ClockOutComponent->>User: Show Success: "Clocked out successfully!"
            end
        end
    end
```

## Key Steps

1. **User Action**: User clicks "Clock Out" button
2. **Validation**: Component checks if user can clock out
3. **Attendance Check**: Verifies user has clocked in today
4. **Time Calculation**:
    - Calculates total hours worked
    - Checks for early departure (before 5 PM AND < 8 hours)
5. **Database Transaction**: Updates attendance record atomically
6. **Activity Logging**: Records the clock-out action with details
7. **User Feedback**: Displays success message with hours worked

## Data Flow

-   **Input**: User click action
-   **Processing**:
    -   Current timestamp
    -   Total hours calculation: `diffInHours(clock_out, clock_in)`
    -   Early departure check: `(clock_out < 17:00) AND (total_hours < 8)`
-   **Output**:
    -   Updated attendance record with:
        -   clock_out_time
        -   total_hours
        -   is_early_departure flag
        -   status = 'completed'
    -   Activity log entry
    -   Success confirmation

## Calculations

### Total Hours

```
total_hours = clock_out_time - clock_in_time (in hours, rounded to 2 decimals)
```

### Early Departure Detection

```
is_early_departure = (clock_out_time < 17:00) AND (total_hours < 8.00)
```

## Error Handling

-   **Not Clocked In**: Requires clock-in before clock-out
-   **Already Clocked Out**: Prevents duplicate clock-outs
-   **Database Errors**: Transaction rollback on failure
