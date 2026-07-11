# Admission Cycle State Machine

## States

- Draft
- Published
- Open
- Closed
- Archived

---

## Initial State

Draft

---

## Final State

Archived

---

## Allowed Transitions

Draft
→ Published

Published
→ Open

Open
→ Closed

Closed
→ Archived

---

## Invalid Transitions

Closed → Open

Archived → Any State

---

## Business Rules

- Only one Open Cycle per intake.
- Closed Cycles reject new Applications.

---

## Events

- CyclePublished
- CycleOpened
- CycleClosed
- CycleArchived