# Admission Cycle Use Cases

## Purpose

Defines all Admission Cycle operations.

---

# Supported Use Cases

- Create Admission Cycle
- Update Admission Cycle
- Publish Admission Cycle
- Open Admission Cycle
- Close Admission Cycle
- Archive Admission Cycle

---

## Business Rules

- One active Cycle per intake.
- Closed Cycles reject Applications.
- Archived Cycles become read-only.

---

## Related Services

- AdmissionCycleService

---

## Related Events

- AdmissionCycleCreated
- AdmissionCycleOpened
- AdmissionCycleClosed