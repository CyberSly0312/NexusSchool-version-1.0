# Database Constraints

## Purpose

Maintain database integrity through enforced constraints.

---

## Primary Keys

Every table must define a primary key.

---

## Foreign Keys

Every relationship uses explicit foreign key constraints.

---

## Unique Constraints

Examples

- student_number
- employee_number
- invoice_number
- email
- tenant_domain

---

## Check Constraints

Examples

- amount >= 0
- capacity > 0
- start_date <= end_date

---

## Nullable Columns

Only nullable when business rules allow missing values.

---

## Default Values

Use sensible defaults.

Examples

- is_active = true
- status = pending

---

## Data Integrity

Database constraints complement application validation and must never be bypassed.