# Attendify System Diagrams

This directory contains comprehensive system diagrams for the Attendify Attendance Management System.

## Diagram Index

1. **[Entity Relationship Diagram (ERD)](01-entity-relationship-diagram.md)**

    - Database schema and relationships
    - Entity attributes and constraints
    - Foreign key relationships

2. **[Data Flow Diagram - Level 0](02-data-flow-diagram-level-0.md)**

    - System context diagram
    - External entities and data stores
    - High-level data flows

3. **[Data Flow Diagram - Level 1](03-data-flow-diagram-level-1.md)**

    - Detailed system processes
    - Process decomposition
    - Data store interactions

4. **[System Architecture Diagram](04-system-architecture-diagram.md)**

    - Overall system architecture
    - Technology stack
    - Component layers and interactions
    - Deployment architecture

5. **[Use Case Diagram](05-use-case-diagram.md)**

    - System use cases by actor
    - Use case descriptions
    - Actor roles and permissions

6. **[Sequence Diagram - Clock In](06-sequence-diagram-clock-in.md)**

    - Clock-in process flow
    - Component interactions
    - Error handling

7. **[Sequence Diagram - Clock Out](07-sequence-diagram-clock-out.md)**

    - Clock-out process flow
    - Hour calculations
    - Early departure detection

8. **[Sequence Diagram - Login](08-sequence-diagram-login.md)**

    - Authentication flow
    - Role-based redirects
    - Security features

9. **[Component Diagram](09-component-diagram.md)**

    - System components and dependencies
    - Layer architecture
    - Component interactions

10. **[Activity Diagram - Attendance](10-activity-diagram-attendance.md)**

    - Complete attendance workflow
    - Decision points
    - Error conditions

11. **[Deployment Diagram](11-deployment-diagram.md)**

    - Docker container architecture
    - Network configuration
    - Volume management
    - Port mappings

12. **[Class Diagram](12-class-diagram.md)**
    - System classes and relationships
    - Methods and properties
    - Design patterns

## Diagram Format

All diagrams are created using **Mermaid** syntax, which can be rendered in:

-   GitHub/GitLab markdown files
-   Documentation tools (MkDocs, Docusaurus, etc.)
-   VS Code with Mermaid extensions
-   Online Mermaid editors
-   Many documentation platforms

## Viewing Diagrams

### Option 1: GitHub/GitLab

Simply view the markdown files on GitHub or GitLab - they will automatically render Mermaid diagrams.

### Option 2: VS Code

Install the "Markdown Preview Mermaid Support" extension to view diagrams in VS Code.

### Option 3: Online Editor

1. Copy the Mermaid code from any diagram file
2. Paste into [Mermaid Live Editor](https://mermaid.live/)
3. View and export as PNG/SVG

### Option 4: Command Line

Install Mermaid CLI:

```bash
npm install -g @mermaid-js/mermaid-cli
mmdc -i diagram.md -o diagram.png
```

## Diagram Categories

### Structural Diagrams

-   Entity Relationship Diagram
-   Class Diagram
-   Component Diagram
-   Deployment Diagram

### Behavioral Diagrams

-   Use Case Diagram
-   Sequence Diagrams
-   Activity Diagram
-   Data Flow Diagrams

### Architectural Diagrams

-   System Architecture Diagram
-   Deployment Diagram

## Key System Concepts

### Roles

-   **User/Employee**: Regular employees who clock in/out
-   **Administrator**: Full system access
-   **Department Head**: Manages employees in their department

### Core Processes

-   **Clock In**: Record arrival time (8:30 AM threshold for late)
-   **Clock Out**: Record departure time (5 PM threshold for early departure)
-   **Analytics**: Calculate metrics and statistics
-   **Activity Logging**: Audit trail of all actions

### Key Metrics

-   **Total Hours**: Calculated from clock in/out times
-   **Late Arrival**: Clock-in after 8:30 AM
-   **Early Departure**: Clock-out before 5 PM with < 8 hours
-   **Attendance Percentage**: Days attended / Working days
-   **Overtime Hours**: Hours worked beyond 8 hours per day

## Technology Stack

-   **Backend**: Laravel 12, PHP 8.2
-   **Frontend**: Livewire 3, Tailwind CSS 4, Alpine.js
-   **Database**: MySQL 8.0
-   **Authentication**: Laravel Sanctum
-   **Authorization**: Spatie Laravel Permission
-   **Containerization**: Docker & Docker Compose
-   **Build Tool**: Vite

## Maintenance

When updating diagrams:

1. Keep Mermaid syntax valid
2. Update related diagrams if relationships change
3. Maintain consistency across diagrams
4. Update this README if adding new diagrams

## Export Options

To export diagrams as images:

1. Use Mermaid Live Editor (export as PNG/SVG)
2. Use Mermaid CLI (command line)
3. Use VS Code extensions with export features
4. Use online converters

## Related Documentation

-   Main README: `/README.md`
-   User Interface Guide: See main README
-   API Documentation: See code comments
-   Database Schema: See migration files
