# Admission Acceptance State Machine

## Purpose

Defines the lifecycle of an Admission Acceptance.

---

## States

- Pending
- Accepted
- Declined
- EnrollmentInitiated
- Completed
- Cancelled

---

## Initial State

Pending

---

## Final States

- Completed
- Cancelled

---

## Allowed Transitions

Pending
→ Accepted

Pending
→ Declined

Accepted
→ EnrollmentInitiated

EnrollmentInitiated
→ Completed

Pending
→ Cancelled

Accepted
→ Cancelled

---

## Invalid Transitions

Completed → Pending

Declined → Accepted

Cancelled → Accepted

---

## Business Rules

- Acceptance must occur before Offer expiration.
- Completed Acceptance creates a Student.
- Cancelled Acceptances remain in history.

---

## Events

- AcceptanceReceived
- AcceptanceDeclined
- EnrollmentStarted
- EnrollmentCompleted
- AcceptanceCancelled