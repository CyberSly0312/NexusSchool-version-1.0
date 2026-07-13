# Database Tables

## Purpose

Define the classification and organization of database tables across Nexus School.

---

## Table Categories

### Core Tables

Contain the primary business entities.

Examples

- students
- teachers
- parents
- subjects
- classes
- programs

---

### Configuration Tables

Store configurable system settings.

Examples

- academic_years
- academic_terms
- education_levels
- grading_scales
- currencies

---

### Transaction Tables

Store business transactions.

Examples

- invoices
- payments
- attendance_records
- examination_results
- enrollments

---

### Pivot Tables

Represent many-to-many relationships.

Examples

- student_subject
- teacher_subject
- role_permission

---

### Audit Tables

Track system activities.

Examples

- activity_logs
- audit_logs

---

## General Rules

- Every table has a primary key.
- Every transactional table has timestamps.
- Foreign keys are explicitly defined.
- Naming follows snake_case.
- Tables remain normalized unless justified otherwise.