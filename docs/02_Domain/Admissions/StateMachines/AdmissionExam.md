# Admission Exam State Machine

## Purpose

Defines the lifecycle of an Admission Examination.

---

## States

- Scheduled
- Registered
- InProgress
- Completed
- Graded
- Published
- Cancelled

---

## Initial State

Scheduled

---

## Final States

- Published
- Cancelled

---

## Allowed Transitions

Scheduled
→ Registered

Registered
→ InProgress

InProgress
→ Completed

Completed
→ Graded

Graded
→ Published

Scheduled
→ Cancelled

Registered
→ Cancelled

---

## Invalid Transitions

Published → Graded

Cancelled → Scheduled

Published → InProgress

---

## Business Rules

- Only completed Exams may be graded.
- Published Results become immutable.
- Cancelled Exams cannot be resumed.

---

## Events

- ExamScheduled
- CandidateRegistered
- ExamStarted
- ExamCompleted
- ExamGraded
- ResultPublished
- ExamCancelled