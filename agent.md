# MASTER PROMPT — SISTEM INFORMASI LAYANAN BIMBINGAN DAN KONSELING

## 1. PROJECT OVERVIEW

Build a production-ready **Web-Based Guidance and Counseling Information System (Sistem Informasi Layanan Bimbingan dan Konseling / BK)** for a junior high school environment.

The system is intended to digitize and integrate BK service management, student counseling requests, counseling records, student violations, achievements, student monitoring, reporting, and role-based access control.

The system must be designed with a strong focus on:

* Security and confidentiality of counseling data
* Flexible and fully dynamic RBAC
* Clean and maintainable architecture
* Responsive web interface
* Separation of business logic from framework-specific implementation
* Scalable database design
* Clear authorization boundaries between school roles

The system should support the following initial roles:

* Superadmin
* Kepala Sekolah
* Guru BK
* Wali Kelas
* Siswa

However, **DO NOT hardcode these roles into the authorization system**.

The Superadmin must be able to create, modify, delete, and configure roles and permissions dynamically.

---

# 2. TECHNOLOGY STACK

Use the following stack:

### Backend

* Laravel
* PHP
* MySQL
* Laravel Eloquent where appropriate

### Frontend

* Vue.js
* Inertia.js
* Tailwind CSS

### Architecture

* Clean Architecture
* Domain-driven separation
* Service/Use Case based business logic
* Repository abstraction
* Dependency inversion

### Development Environment

The application must be compatible with:

* Laravel Herd / Laragon
* MySQL
* Node.js + npm

Do not introduce unnecessary frameworks or libraries unless they solve a clearly defined requirement.

---

# 3. CLEAN ARCHITECTURE REQUIREMENT

Implement **Clean Architecture with clear separation of concerns**.

The system must NOT become a typical Laravel application where business logic is placed directly inside Controllers.

Use a structure similar to:

```text
app/
├── Domain/
│   ├── User/
│   ├── Role/
│   ├── Permission/
│   ├── Student/
│   ├── Teacher/
│   ├── Class/
│   ├── Counseling/
│   ├── Violation/
│   ├── Achievement/
│   ├── Report/
│   └── Notification/
│
├── Application/
│   ├── UseCases/
│   │   ├── Auth/
│   │   ├── User/
│   │   ├── Role/
│   │   ├── Permission/
│   │   ├── Counseling/
│   │   ├── Violation/
│   │   ├── Achievement/
│   │   ├── Monitoring/
│   │   └── Report/
│   │
│   └── DTOs/
│
├── Infrastructure/
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   └── Repositories/
│   ├── Authentication/
│   └── Services/
│
└── Presentation/
    ├── Http/
    │   ├── Controllers/
    │   ├── Requests/
    │   └── Middleware/
    └── Resources/
```

Frontend:

```text
resources/js/
├── Components/
├── Layouts/
├── Pages/
│   ├── Auth/
│   ├── Dashboard/
│   ├── Students/
│   ├── Counseling/
│   ├── Violations/
│   ├── Achievements/
│   ├── Reports/
│   ├── Users/
│   ├── Roles/
│   └── Permissions/
│
├── Composables/
├── Types/
└── Utils/
```

### Architectural rules

1. Controllers must remain thin.
2. Controllers should only:

   * Receive requests
   * Validate/transform input
   * Call application use cases
   * Return Inertia responses
3. Business rules must live inside Application/Domain layers.
4. Eloquent Models must not contain complex business logic.
5. Repositories must abstract persistence where appropriate.
6. Domain logic must not depend directly on Vue, Inertia, HTTP, or Eloquent.
7. Authorization decisions must be centralized.
8. Avoid duplicated business logic.
9. Use DTOs for transferring structured data between layers where beneficial.
10. Every major module should have clearly separated:

* Domain entity/value concepts
* Use cases
* Repository interfaces
* Infrastructure implementation
* Presentation layer

The architecture should remain understandable to another developer who joins the project later.

---

# 4. FULLY DYNAMIC RBAC

This is a critical requirement.

Implement a **fully dynamic Role-Based Access Control system**, conceptually similar to Discord's permission system.

Do NOT implement:

```php
if ($user->role === 'admin') { ... }
```

Do NOT hardcode authorization around role names.

Instead, authorization must be based on:

```text
User
   ↓
Role(s)
   ↓
Permission(s)
   ↓
Resource / Action
```

Example:

```text
Role: Guru BK

Permissions:
- student.view
- counseling.view
- counseling.create
- counseling.update
- counseling.approve
- counseling.reschedule
- counseling.record
- violation.view
- violation.create
- achievement.view
- achievement.create
- report.view
- report.export
```

Another role could have completely different permissions.

---

# 5. RBAC DATABASE DESIGN

Design RBAC using a many-to-many structure.

Minimum entities:

```text
users
roles
permissions
user_roles
role_permissions
```

Optional if needed:

```text
permission_groups
role_permission_overrides
```

Recommended permission structure:

```text
module.action
```

Examples:

```text
dashboard.view

student.view
student.create
student.update
student.delete

counseling.view
counseling.create
counseling.update
counseling.delete
counseling.approve
counseling.reschedule
counseling.record
counseling.view_private

violation.view
violation.create
violation.update
violation.delete

achievement.view
achievement.create
achievement.update
achievement.delete

report.view
report.generate
report.export

user.view
user.create
user.update
user.delete

role.view
role.create
role.update
role.delete

permission.view
permission.manage
```

The exact permission list should be extensible.

---

# 6. SUPERADMIN RBAC MANAGEMENT

Create a dedicated **Superadmin administration module**.

Superadmin must be able to:

### Role Management

* View roles
* Create role
* Rename role
* Edit role description
* Assign permissions
* Remove permissions
* Delete role
* Duplicate/clone role
* Assign users to roles

Example UI:

```text
Role Management

┌──────────────────────────────────────┐
│ Guru BK                              │
│ Counseling Management                │
│                                      │
│ [Edit Permissions] [Users] [Delete] │
└──────────────────────────────────────┘
```

### Permission Management

Superadmin can:

* View all permissions
* Create permissions if required
* Group permissions by module
* Enable/disable permissions for roles

Permission matrix:

```text
                    View   Create   Edit   Delete   Export
Students             ✓       ✓       ✓       ✓
Counseling            ✓       ✓       ✓       -       -
Violations            ✓       ✓       ✓       ✓
Achievements          ✓       ✓       ✓       ✓
Reports               ✓       -       -       -       ✓
```

The interface should make permission assignment intuitive.

Use grouped permissions:

```text
Students
  ├── View
  ├── Create
  ├── Update
  └── Delete

Counseling
  ├── View
  ├── Create
  ├── Update
  ├── Approve
  ├── Reschedule
  └── Record
```

---

# 7. RBAC SECURITY RULES

The system must enforce permissions on BOTH:

### Backend

Every protected action must be authorized server-side.

Example:

```text
POST /counseling/123/approve

→ authenticate user
→ resolve user's roles
→ resolve permissions
→ verify counseling.approve
→ execute use case
```

### Frontend

The UI should dynamically hide/disable features the user does not have permission to use.

For example:

```vue
<Button
    v-if="can('counseling.approve')"
>
    Approve
</Button>
```

But frontend checks are ONLY for UX.

Never rely on frontend authorization for security.

Backend authorization remains mandatory.

---

# 8. SUPERADMIN PROTECTION

The Superadmin role must have special protection.

Rules:

* Superadmin can manage all roles and permissions.
* Superadmin cannot accidentally remove their own final Superadmin access.
* Prevent deletion of the last Superadmin.
* Prevent removing critical permissions from the only Superadmin.
* Log sensitive RBAC changes.
* Role/permission changes should be auditable.

Do not rely only on a role name such as `"superadmin"` for security-critical logic.

Implement an explicit protected/system-level flag or equivalent mechanism.

---

# 9. AUTHENTICATION

Implement:

* Login
* Logout
* Session authentication
* Password hashing
* Password reset if appropriate
* Remember me
* Session expiration
* Authentication middleware

After login:

```text
User
 ↓
Load Roles
 ↓
Load Permissions
 ↓
Load Profile
 ↓
Determine accessible navigation
 ↓
Redirect to appropriate dashboard
```

Do not assume that a user's dashboard should be determined only by role name.

It should be possible to determine available modules from permissions.

---

# 10. USER MANAGEMENT

Superadmin should be able to manage system users.

Features:

* List users
* Search users
* Filter by role
* Create user
* Edit user
* Activate/deactivate user
* Reset password
* Assign roles
* Remove roles
* View user's permissions

User profile should support the appropriate school identity:

```text
Name
NIS/NISN
Email
Phone
Role(s)
Class
Status
```

Do not expose sensitive student/counseling information unnecessarily.

---

# 11. STUDENT MODULE

Implement student management.

Data should include appropriate fields such as:

```text
Student
- ID
- NIS/NISN
- Name
- Gender
- Date of Birth
- Address
- Parent/Guardian
- Contact
- Class
- Status
```

Features:

* List students
* Search
* Filter by class
* View profile
* Update profile
* Student monitoring

Access must be permission-based.

---

# 12. CLASS MANAGEMENT

Implement:

* Academic year
* Classes
* Class members
* Homeroom teacher / Wali Kelas
* Student-class relationships

Important:

A Wali Kelas should only be able to access monitoring data belonging to the class assigned to them.

This is a **data-level authorization rule**, not merely a UI permission.

For example:

```text
Wali Kelas A
→ Class 7A
→ Can monitor students in 7A

Cannot access:
→ 7B
→ 8A
→ 9A
```

---

# 13. COUNSELING MODULE

Based on the requirements in the source document, students must be able to submit counseling requests online, including counseling type, preferred schedule, and initial private complaint/notes.

Counseling types:

```text
Pribadi
Sosial
Belajar
Karier
```

Student flow:

```text
Student
 ↓
Choose Counseling Type
 ↓
Choose Preferred Schedule
 ↓
Write Initial Complaint
 ↓
Submit Request
 ↓
Waiting for BK
 ↓
BK Approves / Reschedules
 ↓
Scheduled
 ↓
Counseling Session
 ↓
Counseling Record
 ↓
Follow-up / Recommendation
 ↓
Completed
```

Counseling status should be explicit:

```text
pending
approved
rescheduled
rejected
scheduled
completed
cancelled
```

---

# 14. COUNSELING RECORD

Guru BK can record counseling results.

Possible fields:

```text
Counseling Session
- Student
- Counselor
- Date
- Counseling Type
- Problem / Discussion
- Counseling Notes
- Assessment / Diagnosis
- Recommendation
- Follow-up
- Status
```

Important:

Counseling records are confidential.

The system must distinguish between:

### General student information

and

### Confidential counseling information

For example:

```text
Wali Kelas
✓ Student profile
✓ Achievement summary
✓ Violation summary
✓ Counseling status

✗ Confidential counseling notes
✗ Private complaint
✗ Detailed counseling record
```

The source document explicitly states that Wali Kelas monitoring should not expose confidential counseling records.

Implement this as both:

* Permission-level authorization
* Data-level authorization

---

# 15. VIOLATION MODULE

Guru BK can manage student disciplinary violations.

Features:

* Violation categories
* Violation points
* Record violation
* Edit violation
* Delete violation
* View student violation history
* Calculate total points
* Filter by date/class/category

Example:

```text
Violation Category
- Attendance
- Discipline
- Uniform
- Behavior
- Other
```

The categories must remain configurable rather than hardcoded.

---

# 16. ACHIEVEMENT MODULE

Implement student achievement management.

Features:

* Achievement category
* Achievement name
* Level
* Date
* Description
* Certificate/document if required
* Student
* Verification status

Possible levels:

```text
School
District
Provincial
National
International
```

Keep these configurable where practical.

---

# 17. MONITORING DASHBOARD

Create dashboards based on the user's permissions and data scope.

### Student Dashboard

Show:

* Counseling request status
* Counseling history
* Personal achievements
* Personal violation summary
* Follow-up information

### Wali Kelas Dashboard

Show only the assigned class:

* Number of students
* Counseling status summary
* Achievement summary
* Violation summary
* Student development overview

Do not show confidential counseling records.

### Guru BK Dashboard

Show:

* Counseling requests
* Upcoming counseling
* Counseling statistics
* Student monitoring
* Violation statistics
* Achievement statistics
* Service reports

### Kepala Sekolah Dashboard

Show school-level aggregated information:

* Total students
* Counseling statistics
* Violation statistics
* Achievement statistics
* Trends
* Periodic reports

The source requirements describe school-level monitoring for Guru BK/Kepala Sekolah and class-specific monitoring for Wali Kelas.

---

# 18. REPORTING

Implement report generation.

Reports should support:

```text
Monthly
Semester
Custom date range
```

Possible reports:

* Counseling service report
* Violation report
* Achievement report
* Student monitoring report
* School summary report

Allow:

```text
Preview
Generate
Download PDF
```

The original requirements specify periodic BK reports and PDF export.

---

# 19. NOTIFICATION SYSTEM

Implement notifications for relevant events.

Examples:

```text
Student submits counseling request
        ↓
Guru BK notification

Guru BK approves request
        ↓
Student notification

Guru BK reschedules
        ↓
Student notification

Counseling follow-up created
        ↓
Student notification
```

Use a centralized notification mechanism.

Notifications should be permission-aware and user-specific.

---

# 20. AUDIT LOG

Because the system handles sensitive student and counseling information, implement audit logging.

Track important events:

```text
Login
Logout
Create
Update
Delete
Permission changes
Role changes
Counseling access
Counseling record changes
Report generation
```

Example:

```text
User:
Guru BK - Budi

Action:
Updated counseling record

Resource:
Counseling #123

Timestamp:
2026-10-01 10:30

IP:
xxx.xxx.xxx.xxx
```

Audit logs should be immutable for ordinary users.

Superadmin can view audit logs according to a dedicated permission.

---

# 21. DATABASE DESIGN

Create a normalized relational database.

At minimum consider:

```text
users
roles
permissions
role_permissions
user_roles

students
teachers
classes
class_students
teacher_classes

counseling_types
counseling_requests
counseling_sessions
counseling_records
counseling_follow_ups

violation_categories
violations

achievement_categories
achievements

notifications
audit_logs
```

Use proper:

* Primary keys
* Foreign keys
* Indexes
* Unique constraints
* Nullable fields
* Timestamps
* Soft deletes where appropriate

Avoid unnecessary duplication.

---

# 22. DATA AUTHORIZATION

Authorization must operate at two levels:

### Action authorization

```text
Can the user perform this action?

counseling.update
```

### Resource/data authorization

```text
Which counseling records can the user access?
```

Example:

```text
Permission:
counseling.view

Does NOT automatically mean:

User can view every counseling record.
```

The system must additionally verify the user's data scope.

Examples:

```text
Student
→ only own records

Wali Kelas
→ only assigned class monitoring

Guru BK
→ counseling records according to assigned access

Kepala Sekolah
→ aggregated school-level information

Superadmin
→ system administration, but sensitive counseling content
   should still follow explicit permission rules
```

---

# 23. UI/UX REQUIREMENTS

Use a modern, clean school administration dashboard.

Technology:

```text
Vue.js
Inertia.js
Tailwind CSS
```

Design principles:

* Responsive
* Clean
* Accessible
* Minimal visual clutter
* Clear hierarchy
* Consistent spacing
* Reusable components
* Mobile-friendly
* Desktop-friendly

Create reusable components:

```text
Button
Input
Select
Textarea
Modal
Dialog
Dropdown
Table
Pagination
Badge
Alert
Toast
Card
Tabs
Breadcrumb
DatePicker
DataTable
PermissionMatrix
```

Do not duplicate UI implementation unnecessarily.

---

# 24. DYNAMIC NAVIGATION

Sidebar navigation must be generated based on permissions.

Example:

```text
Dashboard
Students
Counseling
  ├── Requests
  ├── Sessions
  └── History
Violations
Achievements
Reports
Administration
  ├── Users
  ├── Roles
  ├── Permissions
  └── Audit Logs
```

If the user does not have:

```text
report.view
```

then Reports should not appear in navigation.

However, hiding navigation is NOT sufficient.

The backend route must still reject unauthorized access.

---

# 25. PERMISSION HELPER

Create a centralized authorization mechanism.

Frontend example:

```ts
can('student.view')
can('student.create')
can('counseling.approve')
can('report.export')
```

Backend equivalent:

```php
$this->authorize('counseling.approve');
```

or an equivalent centralized authorization service.

Avoid scattering authorization logic throughout the application.

---

# 26. SUPERADMIN ROLE UI

Create a Discord-inspired permission management experience.

Example:

```text
ROLE: GURU BK

General
☑ View Dashboard

Students
☑ View Students
☑ Create Students
☑ Update Students
☐ Delete Students

Counseling
☑ View Counseling
☑ Create Counseling
☑ Approve Counseling
☑ Reschedule Counseling
☑ Record Counseling
☐ Delete Counseling

Reports
☑ View Reports
☑ Generate Reports
☑ Export Reports
```

Allow:

* Search permissions
* Group permissions
* Select all module permissions
* Clear all module permissions
* Save changes
* Show unsaved changes warning

---

# 27. SYSTEM CONFIGURATION

Superadmin should be able to configure appropriate master data without changing source code.

Examples:

```text
Counseling Types
Violation Categories
Achievement Categories
Academic Years
Classes
```

Avoid hardcoding business configuration into PHP/JavaScript.

---

# 28. VALIDATION

Use robust server-side validation.

Frontend validation can improve UX but must never replace backend validation.

Handle:

* Required fields
* Format validation
* Duplicate records
* Invalid IDs
* Unauthorized access
* Invalid status transitions
* Invalid relationships
* File validation if uploads are implemented

Return user-friendly validation errors.

---

# 29. ERROR HANDLING

Implement centralized error handling.

Handle:

```text
401 Unauthorized
403 Forbidden
404 Not Found
422 Validation Error
429 Too Many Requests
500 Internal Server Error
```

Provide friendly UI states.

Never expose stack traces or sensitive internal information to end users in production.

---

# 30. SECURITY

Implement appropriate security controls:

* CSRF protection
* Authentication
* Authorization
* Password hashing
* Input validation
* SQL injection prevention
* XSS protection
* Mass assignment protection
* Secure file upload
* Rate limiting where appropriate
* Session security
* Sensitive data access control

Pay particular attention to confidentiality of counseling records.

---

# 31. TESTING

Create automated tests for critical functionality.

At minimum:

### Authentication

* Login
* Logout
* Unauthorized access

### RBAC

* Role creation
* Permission assignment
* Permission removal
* Unauthorized action
* Multi-role user
* Superadmin protection

### Counseling

* Student submits request
* BK approves
* BK reschedules
* BK records counseling
* Student sees own history
* Unauthorized user cannot access confidential record

### Data scope

Test:

```text
Wali Kelas A cannot access Class B
Student A cannot access Student B
```

### Reports

* Permission validation
* Correct date filtering
* PDF generation

---

# 32. SEEDER

Create realistic development seed data.

Example roles:

```text
Superadmin
Kepala Sekolah
Guru BK
Wali Kelas
Siswa
```

Create comprehensive permissions.

Seed users:

```text
superadmin@example.com
kepala@example.com
bk@example.com
walikelas@example.com
siswa@example.com
```

Use secure development passwords and clearly document them.

Seed:

* Classes
* Students
* Counseling types
* Violation categories
* Achievement categories
* Example counseling requests
* Example achievements
* Example violations

---

# 33. MIGRATIONS

Create clean Laravel migrations.

Migration order must respect foreign-key dependencies.

RBAC migrations should be established before role-dependent seeders.

Use:

```bash
php artisan migrate
php artisan db:seed
```

The project must be reproducible from a fresh database.

---

# 34. API / SERVER COMMUNICATION

Since this is an Inertia application, do not unnecessarily build a separate REST API layer for every operation.

Use:

```text
Vue
 ↓
Inertia
 ↓
Laravel Controller
 ↓
Application Use Case
 ↓
Domain
 ↓
Repository
 ↓
Database
```

If an external API or future mobile application requires an API, design the Application layer so that an API Presentation layer can be added without rewriting business logic.

---

# 35. PERFORMANCE

Follow reasonable performance practices:

* Pagination
* Eager loading
* Query optimization
* Database indexing
* Avoid N+1 queries
* Lazy loading where appropriate
* Cache suitable configuration/permission data
* Avoid unnecessarily large Inertia payloads

The source requirements specify a target of no more than approximately 3 seconds for main page/navigation response under stable network conditions.

---

# 36. RESPONSIVENESS

The application must work on:

```text
Desktop
Laptop
Tablet
Mobile
```

Prioritize usability for:

* Students using smartphones
* Teachers using laptops/desktops
* Administrators using desktop devices

---

# 37. DEVELOPMENT WORKFLOW

Do NOT attempt to generate the entire application blindly in one step.

Implement incrementally.

Recommended sequence:

### Phase 1

Project setup

### Phase 2

Clean Architecture foundation

### Phase 3

Authentication

### Phase 4

RBAC infrastructure

### Phase 5

Superadmin role & permission management

### Phase 6

User management

### Phase 7

Student/class management

### Phase 8

Counseling module

### Phase 9

Violation module

### Phase 10

Achievement module

### Phase 11

Monitoring dashboards

### Phase 12

Reporting/PDF

### Phase 13

Notifications

### Phase 14

Audit logging

### Phase 15

Testing

### Phase 16

Security and performance review

---

# 38. IMPORTANT IMPLEMENTATION RULE

Before writing a feature:

1. Identify its business rule.
2. Identify its permission requirements.
3. Identify its data scope.
4. Identify its domain entities.
5. Identify the use case.
6. Identify repository requirements.
7. Implement backend authorization.
8. Implement frontend permission-aware UI.
9. Write tests.
10. Only then move to the next feature.

Do not take shortcuts by placing business logic inside controllers.

---

# 39. EXPECTED DELIVERABLE

Produce a fully functional application, not merely a UI prototype.

The final project should contain:

```text
✓ Laravel application
✓ Vue + Inertia frontend
✓ Tailwind styling
✓ MySQL database
✓ Clean Architecture
✓ Authentication
✓ Fully dynamic RBAC
✓ Superadmin permission management
✓ User management
✓ Student management
✓ Class management
✓ Counseling management
✓ Confidential counseling records
✓ Violation management
✓ Achievement management
✓ Monitoring dashboards
✓ Reports
✓ PDF export
✓ Notifications
✓ Audit logs
✓ Validation
✓ Error handling
✓ Automated tests
✓ Seeders
✓ Documentation
```

---

# 40. FINAL QUALITY STANDARD

Before considering the system complete, perform a final review against these questions:

### Architecture

* Is business logic separated from Laravel controllers?
* Are domain/application/infrastructure/presentation responsibilities clear?
* Can the business logic be reused by another presentation layer?

### RBAC

* Can Superadmin create a new role without modifying source code?
* Can Superadmin assign arbitrary permissions?
* Can a user have multiple roles?
* Are permissions checked server-side?
* Are frontend permissions only used for UX?
* Are resource-level access rules enforced?
* Can Superadmin accidentally lock themselves out?

### Security

* Can students access another student's private data?
* Can Wali Kelas access another class?
* Can unauthorized users access counseling records?
* Are sensitive operations logged?

### Maintainability

* Are components reusable?
* Are queries optimized?
* Is authorization centralized?
* Are business rules testable?
* Is configuration separated from code?

### UX

* Is the interface responsive?
* Are loading states implemented?
* Are validation errors understandable?
* Are empty states handled?
* Are confirmation dialogs used for destructive actions?

Do not declare the project complete until these requirements have been verified.

---

## PRIMARY GOAL

Build a secure, maintainable, scalable, and production-ready **Sistem Informasi Layanan Bimbingan dan Konseling** using:

**Vue + Inertia + Laravel + Tailwind**

with **Clean Architecture** and a **fully dynamic RBAC system** where Superadmin has granular control over roles and permissions, similar conceptually to Discord's role/permission management.

The system must prioritize **confidentiality of counseling data, data-level authorization, maintainability, and separation of concerns**.
