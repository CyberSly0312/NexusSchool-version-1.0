# Admission Application State Machine

## Purpose

Defines the lifecycle of an Admission Application.

---

## States

- Draft
- Submitted
- UnderReview
- PendingCorrection
- Approved
- Rejected
- Withdrawn
- Archived

---

## Initial State

Draft

---

## Final States

- Approved
- Rejected
- Withdrawn
- Archived

---

## Allowed Transitions

Draft
→ Submitted

Submitted
→ UnderReview

UnderReview
→ PendingCorrection
→ Approved
→ Rejected

PendingCorrection
→ Submitted
→ Withdrawn

Approved
→ Archived

Rejected
→ Archived

Withdrawn
→ Archived

---

## Invalid Transitions

Approved → Draft

Rejected → Submitted

Archived → Any State

---

## Business Rules

- Only Draft Applications are editable.
- Approved Applications become immutable.
- Archived Applications are read-only.

---

## Events

- ApplicationSubmitted
- ApplicationReviewed
- ApplicationApproved
- ApplicationRejected
- ApplicationArchived