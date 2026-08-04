# ADR-004
# Enrollment Lifecycle Architecture

**Status:** Accepted

**Date:** 2026-08-03

**Decision Makers:**

- Solution Architect
- Backend Architect
- Academic Domain Expert

---

# Context

An enrollment in NexusSchool is a **temporal, per-academic-year assignment** of a
student to a class (and optionally a section). Over the life of a student the
school must record, without losing the past:

- new enrollments and re-enrollments
- promotions and repetitions between years
- internal and outgoing transfers
- suspensions and restorations
- withdrawals and graduations

History must be reliable and auditable: past records must never be silently
overwritten, and every lifecycle action must be traceable to an actor.

SPR-001/002 (Academic) and SPR-003 (Student) established the DDD + Actions
structure and the tenant-isolation conventions that this decision must follow.

---

# Problem Statement

How should the Enrollment domain model the student lifecycle so that:

- a student has **exactly one active enrollment** per academic year;
- **transitions are controlled** and invalid moves are rejected;
- **history is append-only** and immutable once closed;
- every action is **school-scoped** and **authorization-gated**;
- the presentation layer stays a thin consumer of the domain?

The model must not leak state transitions into the Eloquent model, and must not
allow the database to be bypassed by the Filament presentation layer.

---

# Decision

Nexus adopts a **status-driven enrollment aggregate** with **append-only history**
and a **single service facade** over explicit application actions.

```text
Enrollment (aggregate root)
   ├─ EnrollmentPromotion
   ├─ EnrollmentTransfer
   ├─ EnrollmentGraduation
   └─ EnrollmentHistory (append-only journal)

EnrollmentStatus: active <-> suspended -> withdrawn | graduated | archived
```

- The `Enrollment` model is an aggregate root; it holds **no transition logic**.
- All invariants and transitions live in **application Actions**, invoked only
  through the `EnrollmentService` facade.
- The lifecycle is a state machine over `EnrollmentStatus`
  (`active | suspended | graduated | withdrawn | archived`).
- Every domain mutation dispatches a **domain event** consumed by an
  `EnrollmentAuditLogger` listener, which appends to the immutable
  `enrollment_history` table.
- **Promote/repeat** archive the source row and create a new enrollment row
  (new academic year, new class). **Internal transfers** keep the row active and
  update class/section. **Outgoing transfers** archive the row.
- **Archived** rows are terminal and immutable (`isImmutable()`).
- Authorization is centralized in `EnrollmentPolicy` with dedicated lifecycle
  permissions; `super_admin` bypasses scoping and lifecycle checks.

---

# Alternatives Considered

## Option 1

Store a student's history as denormalized snapshot fields on the Student model.

### Advantages

- Simple to read.

### Disadvantages

- Loses the temporal per-year structure.
- No single source of truth for "the current enrollment".
- History overwrites or drifts.
- Audit/traceability impossible.

---

## Option 2

Reuse one persistent row and mutate status in place.

### Advantages

- One row per student.

### Disadvantages

- Past assignments disappear or must be reconstructed.
- Promotion/repetition history is lost.
- Violates the append-only audit requirement.

---

## Option 3 (Chosen)

Aggregate root + per-year rows + append-only history journal.

### Advantages

- One active enrollment per student per year is provable.
- Transitions are explicit and validated.
- History is immutable and auditable.
- Tenant isolation is applied on every query.
- Presentation stays thin behind the service facade.

### Disadvantages

- More tables and more code.
- Promotion/repeat create new rows, so read paths must follow the link.
- Transition rules must be kept in the actions, not the model.

---

# Consequences

All enrollment flows in NexusSchool now go through `EnrollmentService`.

This concerns:

- Filament resources (Enrollment, Bulk Enrollment, History, Promotion, Transfer, Graduation)
- Authorization and permission definitions
- Academic year rollover (promote / repeat)
- Reporting and analytics that read enrollment history

New enrollment features must preserve the state machine and append-only journal.

---

# Positive Impacts

- Proven invariant: one active enrollment per student per year.
- Complete, auditable lifecycle with actor traceability.
- No silent history loss.
- Tenant isolation enforced at query level.
- Testable, deterministic transitions (113 enrollment tests).

---

# Negative Impacts

- Larger schema and more moving parts than a flat model.
- Reading "current state" requires navigating multiple rows.
- Rule maintenance is centralized in the application layer.

---

# Implementation Notes

Implemented under `app/Domains/Enrollment`:

- `Infrastructure/Models/Enrollment` — aggregate root (scopes, status helpers).
- `Application/Actions/*` — 9 explicit transition actions.
- `Application/Services/EnrollmentService` — the only public entry point.
- `Domain/Enums/EnrollmentStatus` — the state machine.
- `Domain/Events/*` + `Infrastructure/Listeners/EnrollmentAuditLogger` — audit journal.
- `Domain/Rules/*` — `ActiveAcademicYearRule`, `UniqueActiveEnrollmentRule`.
- `Infrastructure/Policies/EnrollmentPolicy` — permission-gated authorization.
- `EnrollmentServiceProvider` — observers, policy, event bindings, singletons.

Schema: 5 migrations (enrollments + promotions + transfers + graduations + history).

---

# Related Documents

- SPR-004 Enrollment Domain Report
- 02_Domain/Student
- 02_Domain/Academic
- ADR-002-AcademicFramework
- Multi_Tenant_Isolation
- Coding_Standards
- Naming_Conventions
