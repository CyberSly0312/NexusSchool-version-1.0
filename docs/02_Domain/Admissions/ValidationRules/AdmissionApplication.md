# Admission Application Validation Rules

## Purpose

Defines all validation rules for Admission Applications.

---

## Required Fields

- Applicant
- Admission Cycle
- Academic Program
- Academic Level
- Submission Date

---

## Validation Rules

- Applicant must exist.
- Admission Cycle must be Open.
- Program must be active.
- Applicant must satisfy age requirements.
- Only one active Application per Applicant and Admission Cycle.
- All mandatory fields must be completed.
- Required Documents must be uploaded before submission.

---

## Business Constraints

- Duplicate Applications are prohibited.
- Submitted Applications become read-only.
- Withdrawn Applications cannot be resubmitted.

---

## Error Messages

- Applicant already has an active Application.
- Admission Cycle is closed.
- Missing required Documents.
- Invalid Program selection.