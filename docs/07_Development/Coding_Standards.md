# NexusSchool Coding Standards

**Version:** 1.0.0

**Status:** Approved

**Project:** NexusSchool

**Last Updated:** 2026-06-27

---

# 1. Purpose

This document defines the coding standards used throughout the NexusSchool project.

Every contributor must follow these rules to ensure consistency, maintainability, and scalability.

---

# 2. Development Philosophy

NexusSchool is developed as a long-term ERP platform.

The project prioritizes:

* Readability
* Maintainability
* Scalability
* Testability
* Documentation
* Security

Code should always be written for the next developer, not only for the current one.

---

# 3. Module Development Order

Every module must follow the same lifecycle:

1. Documentation
2. Database Design
3. Migration
4. Model
5. Enum
6. Factory
7. Seeder
8. Service
9. Form Request
10. Policy
11. Tests
12. Filament Resource
13. Git Commit

No step should be skipped without a documented reason.

---

# 4. Naming Conventions

## Tables

Plural, snake_case

Examples:

* students
* teachers
* academic_years

---

## Models

Singular, PascalCase

Examples:

* Student
* Teacher
* AcademicYear

---

## Services

Suffix:

Service

Example:

StudentService

---

## Form Requests

Suffix:

Request

Example:

StoreStudentRequest

---

## Enums

Suffix:

Enum

Example:

AcademicYearStatusEnum

---

# 5. Database Standards

Every business table should include:

* id
* uuid (future implementation)
* school_id (for multi-school support)
* timestamps

SoftDeletes should be used when historical records must be preserved.

---

# 6. Business Rules

Business logic must never be placed inside:

* Controllers
* Filament Resources
* Blade Views

Business logic belongs in Services.

---

# 7. Validation

All validation must be implemented using Form Request classes.

Validation rules should not be duplicated.

---

# 8. Enums

Every status or predefined type must use PHP Enums.

Examples:

* Active
* Inactive
* Closed
* Upcoming

Magic strings are forbidden.

---

# 9. Documentation

Every module must have a corresponding Markdown document inside the docs directory.

Documentation is the source of truth.

---

# 10. Git Workflow

One feature per commit.

Example:

* Add Academic Year model
* Add Academic Year seeder
* Add Academic Period module

Avoid large commits covering multiple unrelated features.

---

# 11. Testing

Each module must include automated tests before being considered complete.

---

# 12. Security

Never trust user input.

Always validate, sanitize, and authorize requests.

---

# 13. Future Standards

Future versions will define:

* API standards
* UI standards
* Performance standards
* Deployment standards
* CI/CD standards

---

# Change History

| Version | Date       | Description              |
| ------- | ---------- | ------------------------ |
| 1.0.0   | 2026-06-27 | Initial coding standards |
