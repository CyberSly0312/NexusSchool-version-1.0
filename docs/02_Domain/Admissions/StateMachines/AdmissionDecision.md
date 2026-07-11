# Admission Decision State Machine

## Purpose

Defines the lifecycle of an Admission Decision.

---

## States

- Pending
- UnderReview
- Approved
- Rejected
- Waitlisted
- Published
- Archived

---

## Initial State

Pending

---

## Final States

- Published
- Archived

---

## Allowed Transitions

Pending
→ UnderReview

UnderReview
→ Approved
→ Rejected
→ Waitlisted

Approved
→ Published

Rejected
→ Published

Waitlisted
→ Approved
→ Rejected

Published
→ Archived

---

## Invalid Transitions

Published → Approved

Archived → Published

Rejected → UnderReview

---

## Business Rules

- Published Decisions cannot be modified.
- Only Approved Decisions generate Offers.
- Archived Decisions remain available for auditing.

---

## Events

- DecisionSubmitted
- DecisionReviewed
- DecisionApproved
- DecisionRejected
- DecisionWaitlisted
- DecisionPublished
- DecisionArchived