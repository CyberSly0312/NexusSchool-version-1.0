# Nexus School

# Folder Structure

**Document ID:** NS-ARCH-004

**Version:** 1.0.0

**Owner:** Nexus Technologies

**Status:** Active

---

# Purpose

This document defines the physical folder organization of the Nexus School source code.

The objective is to organize the project around business domains rather than framework components.

This structure improves scalability, maintainability, and developer productivity.

---

# Guiding Principles

The folder structure follows these principles:

- Domain First
- Feature Based
- Modular
- Scalable
- Framework Independent
- Easy Navigation

Every folder should represent a business capability.

---

# High-Level Project Structure

```
NexusSchool/

├── app/
├── bootstrap/
├── config/
├── database/
├── docs/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/
└── artisan
```

---

# Application Structure

```
app/

├── Core/
├── Domains/
├── Shared/
├── Infrastructure/
├── Console/
├── Exceptions/
├── Providers/
└── Support/
```

---

# Core

Contains platform-wide services.

```
Core/

Authentication/
Authorization/
Audit/
Media/
Notification/
Settings/
Localization/
Configuration/
AI/
```

These modules are shared across the platform.

---

# Domains

Business modules live here.

```
Domains/

Academic/
Student/
Teacher/
Parent/
Curriculum/
Classes/
Attendance/
Timetable/
Examination/
Finance/
Library/
Transport/
Hostel/
HR/
Inventory/
Analytics/
Reports/
SaaS/
```

Each folder represents a bounded context.

---

# Standard Domain Structure

Every domain follows exactly the same internal structure.

Example:

```
Student/

Actions/
DTOs/
Enums/
Events/
Exceptions/
Http/
Jobs/
Listeners/
Mail/
Models/
Notifications/
Observers/
Policies/
Repositories/
Requests/
Resources/
Rules/
Services/
Traits/
ValueObjects/
```

Every domain should remain self-contained.

---

# Shared

Contains reusable components.

```
Shared/

Contracts/
Enums/
Helpers/
Traits/
ValueObjects/
Interfaces/
Exceptions/
```

No business logic should exist here.

---

# Infrastructure

Responsible for technical implementation.

```
Infrastructure/

Database/
Cache/
Queue/
Storage/
Mail/
SMS/
External/
Payments/
Logging/
```

Infrastructure depends on the domain, never the opposite.

---

# Database Structure

```
database/

factories/

migrations/

seeders/

states/
```

Migration names should clearly identify their owning module.

Example

```
student_create_students_table.php

finance_create_fee_categories_table.php

attendance_create_student_attendance_table.php
```

---

# Resource Structure

```
resources/

views/

css/

js/

lang/

images/
```

Filament components remain organized by module.

---

# Routes

```
routes/

web.php

api.php

console.php

channels.php

health.php
```

Large modules may register their own route files.

Example

```
routes/student.php

routes/finance.php

routes/api/student.php
```

---

# Tests

```
tests/

Unit/

Feature/

Architecture/

Performance/

Security/
```

Each domain should have matching Feature and Unit tests.

---

# Documentation

```
docs/

00_Project/

01_Architecture/

02_Domain/

03_Database/

04_API/

05_UI_UX/

06_SaaS/

07_AI/

08_Deployment/

09_Testing/

10_Benchmark/

ADR/
```

Documentation is considered part of the project.

---

# Naming Conventions

Folders use:

PascalCase

Examples

```
Student

Academic

Attendance

Notification
```

Files should follow Laravel naming conventions.

Classes should have descriptive names.

Examples

```
CreateStudentAction

StudentRepository

StudentService

GenerateReportCardAction
```

---

# Dependency Rules

Allowed

```
Presentation

↓

Application

↓

Domain

↓

Infrastructure
```

Forbidden

```
Student Module

↓

Finance Database

↓

Direct Access
```

Communication must occur through services or events.

---

# Future Growth

The structure allows new domains without reorganizing existing code.

Examples

```
Learning/

Research/

Alumni/

Marketplace/

Government/

Admissions/
```

No existing folders should require restructuring.

---

# Folder Structure Summary

The folder organization reflects business capabilities rather than framework conventions.

Every module is isolated, predictable, and independently maintainable.

This structure enables multiple development teams to work simultaneously with minimal conflicts.

---

# Conclusion

A well-organized folder structure reduces cognitive load, improves navigation, and supports the long-term evolution of Nexus School.

The structure defined in this document shall be followed consistently across the entire codebase.

---

**End of Document**