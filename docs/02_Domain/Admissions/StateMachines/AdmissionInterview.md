# Admission Interview State Machine

## States

- Scheduled
- Confirmed
- InProgress
- Completed
- Reviewed
- Cancelled

---

## Initial State

Scheduled

---

## Final States

- Reviewed
- Cancelled

---

## Allowed Transitions

Scheduled
→ Confirmed

Confirmed
→ InProgress

InProgress
→ Completed

Completed
→ Reviewed

Scheduled
→ Cancelled

Confirmed
→ Cancelled

---

## Invalid Transitions

Reviewed → InProgress

Cancelled → Scheduled

---

## Business Rules

- Completed Interviews cannot be edited.
- Reviewed Interviews are locked.

---

## Events

- InterviewScheduled
- InterviewConfirmed
- InterviewStarted
- InterviewCompleted
- InterviewReviewed
- InterviewCancelled