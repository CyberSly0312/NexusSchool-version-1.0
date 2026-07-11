# Admission Cycle Validation Rules

## Purpose

Defines validation rules for Admission Cycles.

---

## Required Fields

- Name
- Academic Year
- Start Date
- End Date

---

## Validation Rules

- Start Date must precede End Date.
- Academic Year must exist.
- Only one Open Cycle per intake.
- Cycle name must be unique within the tenant.

---

## Business Constraints

- Closed Cycles cannot accept Applications.
- Archived Cycles cannot be modified.

---

## Error Messages

- Invalid date range.
- Duplicate Admission Cycle.
- Active Cycle already exists.