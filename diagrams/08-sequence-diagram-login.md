# Sequence Diagram - Login/Authentication Process

## Login Flow

```mermaid
sequenceDiagram
    participant User
    participant LoginComponent as Login Component<br/>(Livewire)
    participant AuthService as Laravel Auth
    participant UserModel as User Model
    participant PermissionService as Spatie Permission
    participant Database as MySQL Database
    participant Session as Session Store

    User->>LoginComponent: Enter Email & Password
    User->>LoginComponent: Click "Login" Button

    LoginComponent->>LoginComponent: Validate Input<br/>(email, password required)

    alt Validation Fails
        LoginComponent->>User: Show Validation Errors
    else Validation Passes
        LoginComponent->>AuthService: attempt(email, password, remember)

        AuthService->>Database: SELECT user WHERE email = ?
        Database-->>AuthService: Return user record

        alt User Not Found
            AuthService-->>LoginComponent: Return false
            LoginComponent->>User: Show Error: "Invalid credentials"
        else User Found
            AuthService->>AuthService: Verify password hash
            AuthService->>AuthService: Check password match

            alt Password Invalid
                AuthService-->>LoginComponent: Return false
                LoginComponent->>User: Show Error: "Invalid credentials"
            else Password Valid
                AuthService->>Session: Create session
                AuthService->>UserModel: Set authenticated user
                AuthService-->>LoginComponent: Return true (authenticated)

                LoginComponent->>UserModel: Get authenticated user
                UserModel-->>LoginComponent: Return User object

                LoginComponent->>PermissionService: Check user roles
                PermissionService->>Database: SELECT roles WHERE user_id = ?
                Database-->>PermissionService: Return user roles
                PermissionService-->>LoginComponent: Return roles array

                LoginComponent->>LoginComponent: Determine redirect based on role

                alt User has 'Admin' role
                    LoginComponent->>LoginComponent: Redirect to admin.dashboard
                    LoginComponent->>User: Redirect to /admin/dashboard
                else User has 'Department Head' role
                    LoginComponent->>LoginComponent: Redirect to dept-head.employees.index
                    LoginComponent->>User: Redirect to /dept-head/employees
                else User has 'User' role (default)
                    LoginComponent->>LoginComponent: Redirect to user.dashboard
                    LoginComponent->>User: Redirect to /dashboard
                end
            end
        end
    end
```

## Key Steps

1. **User Input**: User enters email and password
2. **Validation**: Component validates required fields
3. **Authentication**: Laravel Auth attempts login
4. **Database Lookup**: Finds user by email
5. **Password Verification**: Compares hashed password
6. **Session Creation**: Creates authenticated session
7. **Role Check**: Retrieves user roles from Spatie Permission
8. **Role-Based Redirect**: Redirects based on user role

## Role-Based Redirects

| Role            | Redirect Route              | URL                    |
| --------------- | --------------------------- | ---------------------- |
| Admin           | `admin.dashboard`           | `/admin/dashboard`     |
| Department Head | `dept-head.employees.index` | `/dept-head/employees` |
| User (default)  | `user.dashboard`            | `/dashboard`           |

## Security Features

-   **Password Hashing**: Uses bcrypt/argon2 for password storage
-   **Session Management**: Laravel Sanctum handles session tokens
-   **Remember Me**: Optional persistent session via remember token
-   **CSRF Protection**: Laravel's built-in CSRF token validation
-   **Rate Limiting**: Prevents brute force attacks (if configured)

## Error Handling

-   **Validation Errors**: Client-side and server-side validation
-   **Invalid Credentials**: Generic error message (security best practice)
-   **Account Lockout**: Can be implemented for multiple failed attempts
-   **Session Expiry**: Automatic logout after inactivity

## Data Flow

-   **Input**: Email, Password, Remember Me (optional)
-   **Processing**:
    -   Email lookup
    -   Password hash verification
    -   Role retrieval
-   **Output**:
    -   Authenticated session
    -   User object in session
    -   Redirect to appropriate dashboard
