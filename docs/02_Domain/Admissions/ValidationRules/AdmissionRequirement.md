# Admission Requirement Validation Rules

## Purpose

Defines validation rules for Admission Requirements.

---

## Required Fields

- Requirement Name
- Requirement Type
- Mandatory Status

---

## Validation Rules

- Requirement Name must be unique.
- Requirement Type must be valid.
- Mandatory flag is required.
- Associated Program must exist.

---

## Business Constraints

- Mandatory Requirements cannot be deleted while active.
- Archived Requirements are immutable.

---

## Error Messages

- Duplicate Requirement.
- Invalid Requirement Type.
- Missing Program assignment.