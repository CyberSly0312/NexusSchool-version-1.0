# Admission Decision Validation Rules

## Purpose

Defines validation rules for Admission Decisions.

---

## Required Fields

- Admission Application
- Decision Status
- Decision Date
- Decision Authority

---

## Validation Rules

- Application must be complete.
- Mandatory Documents must be approved.
- Interview must be completed when required.
- Examination must be completed when required.
- Decision Authority must exist.

---

## Business Constraints

- Only one active Decision per Application.
- Published Decisions cannot be edited.
- Approved Decisions generate Admission Offers.

---

## Error Messages

- Missing Admission Requirements.
- Decision already published.
- Invalid Decision Authority.
- Duplicate Admission Decision.