# SPR-004 — Enrollment Domain Report

**Status:** Complete
**Branch:** main
**Date:** 2026-08-03
**Owner:** Backend Architect / Domain Engineering
**Related ADR:** [ADR-004-EnrollmentLifecycle](../06_ADR/ADR-004-EnrollmentLifecycle.md)

---

# Summary

SPR-004 delivered the **Enrollment domain** for NexusSchool: a fully tested,
tenant-scoped enrollment module built on the DDD + Actions architecture
established in SPR-001/002 (Academic) and SPR-003 (Student). It introduces the
Enrollment aggregate root, the full enrollment lifecycle (enroll, update,
promote, repeat, transfer, graduate, withdraw, suspend, restore), append-only
history/audit, authorization, and the Filament presentation layer.

**Quality gate at hand-off:**

- **Pest:** 113 passed / 253 assertions (enrollment suites), full-suite parity at **198 failed / 1086 passed**.
- **PHPStan (level 5, Larastan):** exit 0 — "No errors" (684 pre-existing errors pinned in `phpstan-baseline.neon`).
- **Pint:** touched files formatted (single_quote / no_unused_imports); repo-wide debt (446 files) left untouched intentionally.

---

# Objectives

- Model an enrollment as a **temporal, per-academic-year assignment** of a student to a class (optionally a section).
- Enforce the core invariants at the application layer, never in the model:
  - one active enrollment per student per academic year,
  - an active academic year is required,
  - school-scoped uniqueness of the enrollment number,
  - an enrollment date inside the academic year.
- Provide the complete lifecycle with **append-only history** and **audit trail**.
- Enforce tenant isolation on every query.
- Keep the presentation layer thin and routed exclusively through the domain service.
- Land the module green under the three quality gates.

---

# Delivered Scope

## Domain structure

| Layer        | Artifacts                                                                                          |
|--------------|----------------------------------------------------------------------------------------------------|
| Application  | 9 Actions, 5 DTOs, 2 Services (`EnrollmentService`, `EnrollmentNumberGenerator`)                    |
| Domain       | 5 Enums, 9 Events, 5 Exceptions, 2 Rules, 3 Value Objects, 1 Authorization trait                    |
| Infrastructure| 5 Models, 1 Observer, 1 Policy, 1 Audit Listener                                                  |
| Presentation | 6 Filament resources + pages + relation managers (Bulk, History, Graduation, Promotion, Transfer)   |

**Actions:** `CreateEnrollment`, `UpdateEnrollment`, `PromoteStudent`, `RepeatStudent`,
`TransferEnrollment`, `GraduateStudent`, `WithdrawStudent`, `SuspendEnrollment`, `RestoreEnrollment`.

**Enums:**

- `EnrollmentStatus` — `active | suspended | graduated | withdrawn | archived`
- `EnrollmentType` — `new | re_enrollment | promotion | repetition | transfer_in`
- `PromotionDecision` — `promoted | repeated`
- `GraduationDecision` — always terminal
- `TransferType` — internal / outgoing

**Value Objects:** `EnrollmentNumber`, `AcademicResult`, `TransferReason`.

**Models:** `Enrollment` (aggregate root) + `EnrollmentPromotion`, `EnrollmentTransfer`,
`EnrollmentGraduation`, `EnrollmentHistory` (append-only child records).

## Schema (5 migrations)

- `enrollments` (000014) — aggregate root; FK to student / academic year / program / level / class / optional section; unique `(school_id, enrollment_number)`; soft deletes.
- `enrollment_promotions` (000015)
- `enrollment_transfers` (000016)
- `enrollment_graduations` (000017)
- `enrollment_history` (000018) — append-only event journal.

## Wiring

- `EnrollmentServiceProvider` (new, auto-discovered) registers singletons, model observers,
  the `EnrollmentPolicy`, and all 9 event → `EnrollmentAuditLogger` listeners.
- Permissions added to `PermissionGroup` / `PermissionService` (enrollment view/create/lifecycle permissions).
- `SchoolAdminPanelProvider` / `bootstrap/providers.php` updated to register the domain.

## Enrollment number

`ENR-{SCHOOL_PREFIX}-{SEQUENCE}`, where the prefix derives from the school id and the
sequence is count-based per school. Because enrollments are only soft-deleted, numbers
are never re-used within a school.

## Lifecycle

```text
active <-> suspended        (suspend / restore)
active  -> withdrawn        (exit reason required)
active  -> graduated        (terminal, diploma recorded)
any     -> archived         (terminal; archived rows are immutable)

promote / repeat            archives the source row and creates a new row
transfer (internal)         keeps the row active, updates class/section
transfer (outgoing)         archives the row
```

---

# Test Coverage

| Suite                                        | Kind      | Focus                                                                 |
|----------------------------------------------|-----------|-----------------------------------------------------------------------|
| `CreateEnrollmentActionTest`                 | Unit      | action invariants (number, duplicates, year, school scoping, dates)   |
| `LifecycleActionTest`                        | Unit      | promote/repeat/transfer/graduate/withdraw/suspend/restore transitions |
| `EnrollmentServiceTest`                      | Unit      | service facade + pagination/filtering/search                          |
| `EnrollmentRulesTest`                        | Unit      | `ActiveAcademicYearRule`, `UniqueActiveEnrollmentRule`                |
| `EnrollmentEnumsTest`                        | Unit      | enum labels/colors/state/immutability                                 |
| `EnrollmentNumberGeneratorTest`              | Unit      | sequential, school-scoped numbers, format                             |
| `EnrollmentValueObjectsTest`                 | Unit      | `EnrollmentNumber`, `AcademicResult`, `TransferReason`                |
| `EnrollmentPolicyTest`                       | Feature   | permission-gated access + school scoping + `super_admin` bypass       |
| `EnrollmentAuditTest`                        | Feature   | audit logging + actor tracing per event                               |
| `HistoryIntegrityTest`                       | Feature   | append-only history, immutability of archived rows                    |
| `TenantIsolationTest`                        | Feature   | per-school isolation at query and database level                      |
| `EnrollmentDomainArchitectureTest`           | Arch      | layer boundaries, final/readonly contracts, enum/case counts          |

**Result:** 113 passed / 253 assertions. Full suite parity maintained at 198 failed / 1086 passed
(the 198 failures are pre-existing, unrelated suites; the enrollment work adds **zero** failures).

---

# Static Analysis

- Enabled the **Larastan** extension in `phpstan.neon` (level 5) and generated a
  line-specific `phpstan-baseline.neon` for 684 pre-existing errors.
- One non-ignorable error was fixed in Filament core wiring:
  `ConfigAlertsWidget::$view` changed from `static` to instance (Filament v5 contract).
- `vendor/bin/phpstan analyse` → **exit 0**.

Full report: [Static_Analysis.md](../03_Engineering/Static_Analysis.md).

---

# Known Issues / Next Steps

- Full-suite parity is still **198 failed** — the pre-existing failing suites must be
  triaged in a future sprint before the suite can go green.
- Pint repo-wide debt: 446 unformatted files (intentionally untouched this sprint).
- PHPStan is pinned at level 5; the roadmap (6 → 7 → 8 → max) is documented in
  `Static_Analysis.md`.
- `storage/phpstan` (analysis cache) is now gitignored and untracked.

---

# Related Documents

- [ADR-004-EnrollmentLifecycle](../06_ADR/ADR-004-EnrollmentLifecycle.md)
- [Static_Analysis.md](../03_Engineering/Static_Analysis.md)
- [Student domain](../02_Domain/Student.md)
- [Academic Framework ADR](../06_ADR/ADR-002-AcademicFramework.md)
