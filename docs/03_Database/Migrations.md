# Migration Strategy

## Purpose

Provide a consistent approach for database schema evolution.

---

## Principles

- One migration per logical change.
- Migrations are immutable.
- Rollbacks always supported.

---

## Naming Convention

create_students_table

add_student_number_to_students_table

create_fee_structures_table

---

## Execution Order

1. Reference Tables
2. Core Tables
3. Transaction Tables
4. Pivot Tables
5. Views
6. Seeders

---

## Best Practices

- Always define foreign keys.
- Always define indexes.
- Add comments when necessary.
- Never modify existing migrations after production deployment.

---

## Rollback Policy

Every migration must be reversible whenever technically possible.