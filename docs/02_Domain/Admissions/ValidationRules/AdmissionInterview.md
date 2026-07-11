# Admission Interview Validation Rules

## Purpose

Defines validation rules for Admission Interviews.

---

## Required Fields

- Applicant
- Interview Date
- Interview Panel
- Evaluation Template

---

## Validation Rules

- Interview Date must be in the future.
- Assigned Panel Members must exist.
- Applicant must have a valid Application.
- Evaluation Template must be active.

---

## Business Constraints

- Completed Interviews cannot be modified.
- Every Interview requires at least one Evaluation.

---

## Error Messages

- Invalid Interview Date.
- Missing Interview Panel.
- Applicant not eligible.
- Missing Evaluation.