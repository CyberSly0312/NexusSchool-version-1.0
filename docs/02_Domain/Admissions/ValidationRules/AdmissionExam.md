# Admission Exam Validation Rules

## Purpose

Defines validation rules for Admission Examinations.

---

## Required Fields

- Admission Application
- Examination Session
- Examination Date
- Examiner

---

## Validation Rules

- Applicant must have an eligible Admission Application.
- Examination Date must fall within the Admission Cycle.
- Examination Session must exist.
- Examiner must be assigned.
- Examination Template must be active.

---

## Business Constraints

- An Applicant cannot register twice for the same Examination.
- Published Results cannot be modified.
- Cancelled Examinations cannot be graded.

---

## Error Messages

- Applicant not eligible.
- Invalid Examination Date.
- Duplicate Examination Registration.
- Examiner not assigned.