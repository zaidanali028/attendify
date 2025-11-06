# Entity Relationship Diagram (ERD)

## Database Schema Relationships

```mermaid
erDiagram
    USERS ||--o{ ATTENDANCES : "has many"
    USERS }o--|| DEPARTMENTS : "belongs to"
    USERS ||--o{ ACTIVITY_LOGS : "performs"
    DEPARTMENTS ||--o{ ATTENDANCES : "has many"
    ATTENDANCES ||--o{ ACTIVITY_LOGS : "generates"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        bigint department_id FK
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    DEPARTMENTS {
        bigint id PK
        string name
        text description
        boolean status
        timestamp created_at
        timestamp updated_at
    }

    ATTENDANCES {
        bigint id PK
        bigint user_id FK
        bigint department_id FK
        datetime clock_in_time
        datetime clock_out_time
        date attendance_date
        decimal total_hours
        boolean is_late
        boolean is_early_departure
        string status
        timestamp created_at
        timestamp updated_at
    }

    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string subject_type
        bigint subject_id
        string description
        json properties
        timestamp created_at
        timestamp updated_at
    }

    ROLES {
        bigint id PK
        string name UK
        string guard_name
        timestamp created_at
        timestamp updated_at
    }

    PERMISSIONS {
        bigint id PK
        string name UK
        string guard_name
        timestamp created_at
        timestamp updated_at
    }

    MODEL_HAS_ROLES {
        bigint role_id FK
        string model_type
        bigint model_id
    }

    MODEL_HAS_PERMISSIONS {
        bigint permission_id FK
        string model_type
        bigint model_id
    }

    USERS ||--o{ MODEL_HAS_ROLES : "has"
    USERS ||--o{ MODEL_HAS_PERMISSIONS : "has"
    ROLES ||--o{ MODEL_HAS_ROLES : "assigned to"
    PERMISSIONS ||--o{ MODEL_HAS_PERMISSIONS : "assigned to"
```

## Key Relationships

1. **User → Department**: Many-to-One (Each user belongs to one department)
2. **User → Attendance**: One-to-Many (Each user has many attendance records)
3. **Department → Attendance**: One-to-Many (Each department has many attendance records)
4. **User → ActivityLog**: One-to-Many (Each user generates many activity logs)
5. **Attendance → ActivityLog**: One-to-Many (Each attendance can generate multiple logs)
6. **User → Roles**: Many-to-Many (Users can have multiple roles via Spatie Permission)
7. **User → Permissions**: Many-to-Many (Users can have direct permissions)

## Constraints

-   Unique constraint: `(user_id, attendance_date)` - One attendance record per user per day
-   Foreign key constraints with CASCADE on delete for data integrity
-   Email uniqueness constraint on users table
