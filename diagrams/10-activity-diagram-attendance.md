# Activity Diagram - Attendance Management Process

## Complete Attendance Workflow

```mermaid
flowchart TD
    Start([Start: User Logs In]) --> CheckRole{Check User Role}

    subgraph UserFlowSection["User Flow"]
        CheckRole -->|User/Admin| UserFlow["User Flow"]
        UserFlow --> CheckDept{Has Department?}
        CheckDept -->|No| Error1["Error: Must be assigned<br/>to department"]
        CheckDept -->|Yes| ClockInAction["Click Clock In"]
        ClockInAction --> CheckClockIn{Already Clocked In<br/>Today?}
        CheckClockIn -->|Yes| Error2["Error: Already<br/>clocked in"]
        CheckClockIn -->|No| RecordClockIn["Record Clock In Time"]
        RecordClockIn --> CheckLate{Clock In Time<br/>> 8:30 AM?}
        CheckLate -->|Yes| SetLate["Set Status: Late<br/>Set is_late: true"]
        CheckLate -->|No| SetPresent["Set Status: Present<br/>Set is_late: false"]
        SetLate --> CreateAttendance["Create Attendance Record"]
        SetPresent --> CreateAttendance
        CreateAttendance --> LogActivity1["Log Activity: Clocked In"]
        LogActivity1 --> ShowSuccess1["Show Success Message"]
        ShowSuccess1 --> WaitForClockOut["Wait for Clock Out"]
        WaitForClockOut --> ClockOutAction["Click Clock Out"]
        ClockOutAction --> CheckClockOut{Has Clocked In<br/>Today?}
        CheckClockOut -->|No| Error3["Error: Must clock in<br/>first"]
        CheckClockOut -->|Yes| CheckAlreadyOut{Already Clocked<br/>Out?}
        CheckAlreadyOut -->|Yes| Error4["Error: Already<br/>clocked out"]
        CheckAlreadyOut -->|No| RecordClockOut["Record Clock Out Time"]
        RecordClockOut --> CalculateHours["Calculate Total Hours<br/>clock_out - clock_in"]
        CalculateHours --> CheckEarly{Clock Out < 5 PM<br/>AND Hours < 8?}
        CheckEarly -->|Yes| SetEarlyDeparture["Set is_early_departure: true"]
        CheckEarly -->|No| SetNormal["Set is_early_departure: false"]
        SetEarlyDeparture --> UpdateAttendance["Update Attendance Record"]
        SetNormal --> UpdateAttendance
        UpdateAttendance --> SetStatus["Set Status: Completed"]
        SetStatus --> LogActivity2["Log Activity: Clocked Out"]
        LogActivity2 --> ShowSuccess2["Show Success Message"]
        ShowSuccess2 --> ViewDashboard["View Dashboard/Analytics"]
    end

    subgraph AdminFlowSection["Admin Flow"]
        CheckRole -->|Admin| AdminFlow["Admin Flow"]
        AdminFlow --> AdminOptions{Admin Action}
        AdminOptions -->|View All| ViewAllAttendances["View All Attendances"]
        AdminOptions -->|Edit| EditAttendance["Edit Attendance Record"]
        AdminOptions -->|Analytics| ViewAnalytics["View Admin Dashboard"]
        AdminOptions -->|Manage Users| ManageUsers["User Management"]
        AdminOptions -->|Manage Depts| ManageDepts["Department Management"]
        AdminOptions -->|View Logs| ViewLogs["View Activity Logs"]
        EditAttendance --> SelectRecord["Select Attendance Record"]
        SelectRecord --> ModifyTimes["Modify Clock In/Out Times"]
        ModifyTimes --> ValidateTimes{Valid Times?<br/>clock_out > clock_in}
        ValidateTimes -->|No| Error5["Error: Invalid times"]
        ValidateTimes -->|Yes| Recalculate["Recalculate Metrics"]
        Recalculate --> UpdateRecord["Update Record"]
        UpdateRecord --> LogEdit["Log Activity: Record Updated<br/>with old/new data"]
        LogEdit --> ShowSuccess3["Show Success Message"]
    end

    subgraph DeptHeadFlowSection["Department Head Flow"]
        CheckRole -->|Dept Head| DeptHeadFlow["Dept Head Flow"]
        DeptHeadFlow --> ViewEmployees["View Department Employees"]
        ViewEmployees --> AddEmployee["Add New Employee"]
        AddEmployee --> CreateUser["Create User Account"]
        CreateUser --> AssignDept["Assign to Department"]
        AssignDept --> AssignRole["Assign User Role"]
        AssignRole --> ShowSuccess4["Show Success Message"]
    end

    ViewDashboard --> End([End])
    ViewAllAttendances --> End
    ShowSuccess3 --> End
    ManageUsers --> End
    ManageDepts --> End
    ViewLogs --> End
    ShowSuccess4 --> End
    Error1 --> End
    Error2 --> End
    Error3 --> End
    Error4 --> End
    Error5 --> End
```

## Process Descriptions

### User Clock In Process

1. **Check Department**: Verify user has department assignment
2. **Check Duplicate**: Ensure not already clocked in today
3. **Record Time**: Capture current timestamp
4. **Check Late**: Determine if arrival is after 8:30 AM
5. **Create Record**: Insert attendance record with status
6. **Log Activity**: Record clock-in action
7. **User Feedback**: Display success message

### User Clock Out Process

1. **Check Clock In**: Verify user has clocked in today
2. **Check Duplicate**: Ensure not already clocked out
3. **Record Time**: Capture current timestamp
4. **Calculate Hours**: Compute total hours worked
5. **Check Early**: Determine if early departure (before 5 PM AND < 8 hours)
6. **Update Record**: Update attendance with clock-out data
7. **Log Activity**: Record clock-out action
8. **User Feedback**: Display success message

### Admin Edit Attendance Process

1. **Select Record**: Choose attendance record to edit
2. **Modify Times**: Update clock in/out times
3. **Validate**: Ensure clock-out is after clock-in
4. **Recalculate**: Recompute all metrics (late, early, hours)
5. **Update Record**: Save changes to database
6. **Log Activity**: Record edit with old/new data
7. **User Feedback**: Display success message

### Department Head Employee Management

1. **View Employees**: Display employees in department
2. **Add Employee**: Create new user account
3. **Assign Department**: Link to department head's department
4. **Assign Role**: Set role to "User"
5. **User Feedback**: Display success message

## Decision Points

-   **Has Department?**: Required for clock-in
-   **Already Clocked In?**: Prevents duplicates
-   **Is Late?**: Determines status (late vs present)
-   **Has Clocked In?**: Required for clock-out
-   **Already Clocked Out?**: Prevents duplicates
-   **Is Early Departure?**: Before 5 PM AND < 8 hours
-   **Valid Times?**: Clock-out must be after clock-in

## Error Conditions

1. **No Department**: User must be assigned to department
2. **Duplicate Clock In**: Already clocked in today
3. **No Clock In**: Must clock in before clocking out
4. **Duplicate Clock Out**: Already clocked out today
5. **Invalid Times**: Clock-out must be after clock-in

## Success Conditions

1. **Clock In Success**: Attendance record created
2. **Clock Out Success**: Attendance record updated with hours
3. **Edit Success**: Attendance record updated with new times
4. **Add Employee Success**: User account created and assigned
