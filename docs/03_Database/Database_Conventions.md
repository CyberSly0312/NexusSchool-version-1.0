# Database Conventions

## Naming Convention

Tables

- snake_case
- plural names

Examples

students

teachers

academic_years

---

## Columns

Primary Key

id

Foreign Keys

student_id

teacher_id

class_id

---

## Timestamps

created_at

updated_at

deleted_at

---

## Pivot Tables

alphabetical order

student_subject

role_permission

---

## Boolean Columns

Use prefixes

is_active

is_deleted

is_locked

has_paid

---

## Enum Columns

Lowercase values only.

---

## Constraints

Every foreign key explicitly declared.

---

## Documentation

Every migration must include comments when necessary.s