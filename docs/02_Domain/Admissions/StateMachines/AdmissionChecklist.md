# Admission Checklist State Machine

## Purpose

Defines the lifecycle of an Admission Checklist.

---

## States

- Created
- InProgress
- PendingVerification
- Approved
- EnrollmentAuthorized
- Archived

---

## Initial State

Created

---

## Final State

Archived

---

## Allowed Transitions

Created
→ InProgress

InProgress
→ PendingVerification

PendingVerification
→ Approved

Approved
→ EnrollmentAuthorized

EnrollmentAuthorized
→ Archived

---

## Invalid Transitions

Approved → InProgress

Archived → Approved

EnrollmentAuthorized → PendingVerification

---

## Business Rules

- Every mandatory item must be completed before Approval.
- Enrollment requires an Approved Checklist.
- Archived Checklists remain available for audit.

---

## Events

- ChecklistCreated
- ChecklistUpdated
- ChecklistVerified
- ChecklistApproved
- EnrollmentAuthorized
- ChecklistArchived