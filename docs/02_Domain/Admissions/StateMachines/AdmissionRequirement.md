# Admission Requirement State Machine

## States

- Draft
- Active
- Inactive
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
→ Active

Active
→ Inactive

Inactive
→ Active

Inactive
→ Archived

---

## Invalid Transitions

Archived → Active

Draft → Archived

---

## Business Rules

- Active Requirements apply to current Admission Cycles.
- Archived Requirements remain available for audit.

---

## Events

- RequirementActivated
- RequirementDeactivated
- RequirementArchived