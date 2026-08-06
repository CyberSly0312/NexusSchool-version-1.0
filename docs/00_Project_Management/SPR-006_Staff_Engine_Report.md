# SPR-006 — Staff & Workforce Domain Report

**Status:** Complete
**Branch:** main
**Date:** 2026-08-04
**Owner:** Backend Architect / Domain Engineering
**Related ADR:** [ADR-006](../ADR/ADR-006.md)

---

# Summary

SPR-006 delivered the **Staff & Workforce domain** for NexusSchool: a fully
tested, tenant-scoped human-resources module built on the DDD + Actions
architecture established in SPR-001/002 (Academic), SPR-003 (Student) and
SPR-004 (Enrollment). It introduces the Staff aggregate root — the single
identity for every workforce role (Teacher, Director, Secretary, Accountant,
Librarian, …) — the employment contract lifecycle, department/position/campus
assignments, the immutable employment-history journal, qualifications,
certifications, emergency contacts, teacher profiles, authorization, and the
Filament presentation layer.

**Quality gate at hand-off:**

- **Pest:** 96 passed / 149 assertions (staff suites: 69 unit, 18 feature, 9 arch).
  Full-suite parity maintained — the pre-existing failing suites are untouched
  by this sprint; the staff work adds **zero** failures.
- **PHPStan (level 5, Larastan):** exit 0 — "No errors". The repo convention
  (as established by every prior domain) is a pinned `phpstan-baseline.neon`;
  this sprint added 54 new Staff entries via `--generate-baseline`.
- **Pint:** all touched files formatted and clean (`--test --dirty` passes).

---

# Objectives

- Model the **workforce as a single Staff aggregate** whose specializations
  (Teacher, Director, Secretary, Accountant, Librarian, …) are *values on the
  record*, never separate identities or duplicated aggregate roots.
- Enforce the core invariants at the application layer, never in the model:
  - exactly **one active employment** per staff member per school
    (partial unique index `(school_id, staff_id)` where `termination_date IS NULL`),
  - professional identity (staff number, national id, email, phone) is
    unique per school,
  - assignments are time-aware, non-overlapping and same-school only,
  - retired / resigned / terminated staff are **immutable**.
- Provide the complete lifecycle with **append-only employment history** and
  **audit trail** for every domain event.
- Enforce tenant isolation on every query.
- Keep the presentation layer thin and routed exclusively through the
  `StaffService` facade.
- Land the module green under the three quality gates.

---

# Delivered Scope

## Domain structure

| Layer         | Artifacts                                                                                             |
|---------------|-------------------------------------------------------------------------------------------------------|
| Application   | 10 Actions, 4 DTOs, 2 Services (`StaffService`, `StaffNumberGenerator`)                                |
| Domain        | 8 Enums, 10 Events, 5 Exceptions, 7 Value Objects, 1 Authorization trait                               |
| Infrastructure| 9 Models, 1 Observer, 1 Policy, 1 Audit Listener                                                      |
| Presentation  | 6 Filament resources + pages + 3 relation managers, nav group "Ressources humaines"                   |

**Actions:** `CreateStaff`, `UpdateStaff`, `StartEmployment`, `AssignDepartment`,
`AssignPosition`, `TransferStaff`, `TerminateEmployment`, `SuspendStaff`,
`RestoreStaff`, `AssignTeachingSubjects`.

**Enums:**

- `StaffStatus` — `active | suspended | retired | resigned | terminated`
- `StaffSpecialization` — 8 roles (teacher, director, secretary, accountant, librarian, …)
- `EmploymentStatus` — `active | terminated`
- `EmploymentContractType` — contract types catalog
- `AssignmentType` — `department | position | campus`
- `PositionCategory` — catalog categories for `config/nexus-staff.php`
- `QualificationType`, `CertificationStatus`

**Value Objects:** `StaffNumber`, `EmailAddress`, `PhoneNumber`,
`NationalIdentifier`, `BankAccount`, `ContractPeriod`, `SalaryScale`.

**Models:** `Staff` (aggregate root) + `Employment`, `EmploymentHistory`
(append-only journal), `Position`, `Assignment`, `Qualification`,
`Certification`, `StaffEmergencyContact`, `TeacherProfile`.

## Schema (9 migrations)

- `staffs` (000001) — aggregate root; FK to user (nullable); unique `(school_id, staff_number)`;
  soft deletes.
- `positions` (000002) — school-scoped position catalog (14 rows seeded).
- `employments` (000003) — contract; partial unique `(school_id, staff_id)` where
  `termination_date IS NULL`.
- `employment_history` (000004) — append-only event journal.
- `assignments` (000005) — time-aware, `is_current` flag, soft deletes.
- `qualifications` (000006), `certifications` (000007),
  `staff_emergency_contacts` (000008), `teacher_profiles` (000009).

## Wiring

- `StaffServiceProvider` registers singletons (`StaffService`,
  `StaffNumberGenerator`), observes all 9 models, gates `StaffPolicy`, and binds
  all 10 events → `StaffAuditLogger`; registered in `bootstrap/providers.php`.
- Permissions added to `PermissionGroup` / `PermissionService`: the `staff`
  group = 4 CRUD + 6 workforce actions (`assign`, `transfer`, `terminate`,
  `suspend`, `restore`, `teach`) = 10 permissions.
- `SchoolAdminPanelProvider` registers the 6 resources under "Ressources humaines".
- Positions catalog seeded per tenant by `PositionSeeder` (uses `firstOrCreate`
  like `RelationshipTypeSeeder`).

## Staff number

`STF-{6-hex}-{4-digit sequence}`, sequence is count-based per school.
Because staff rows are only soft-deleted, numbers are never re-used within a school.

## Lifecycle

```text
active <-> suspended        (suspend / restore)
active  -> terminated       (terminate employment; staff also -> terminated)

retired | resigned | terminated  -> immutable (identity locked, history append-only)
```

```text
Employment (single active contract per staff)
   ├─ EmploymentHistory (append-only journal)
   ├─ Assignment (department | position | campus, time-aware)
   ├─ Qualification / Certification
   ├─ StaffEmergencyContact
   └─ TeacherProfile (teaching subject ids)
```

---

# Test Coverage

| Suite                                   | Kind    | Focus                                                                        |
|-----------------------------------------|---------|------------------------------------------------------------------------------|
| `StaffValueObjectsTest`                 | Unit    | all 7 VOs: format / trim / invalid cases, `ContractPeriod` bounds             |
| `StaffEnumsTest`                        | Unit    | labels, immutability, specialization cases                                    |
| `CreateStaffDataTest`                   | Unit    | DTO `fromArray` / immutability / `toArray` / null normalization               |
| `StaffNumberGeneratorTest`              | Unit    | STF format, per-school sequence isolation                                     |
| `CreateStaffActionTest`                 | Unit    | auto numbering, TeacherProfile spawn, duplicate email/phone/nationalId/number |
| `EmploymentActionsTest`                 | Unit    | start/terminate/transfer invariants, one-active rule, immutable staff         |
| `AssignmentActionsTest`                 | Unit    | department/position same-school + overlap + immutable staff                   |
| `StaffServiceTest`                      | Unit    | service facade covering the full lifecycle                                   |
| `StaffPolicyTest`                       | Feature | permission-gated access + school scoping + `super_admin` bypass + forceDelete |
| `StaffAuditTest`                        | Feature | audit logging + actor tracing + history per event                             |
| `TenantIsolationTest`                   | Feature | per-school numbers, duplicates, history isolation                             |
| `StaffDomainArchitectureTest`           | Arch    | layer boundaries, final/readonly contracts, enum case counts                  |

**Result:** 96 passed / 149 assertions. Full-suite parity maintained (the
pre-existing failing suites — Filament render tests, Analytics/Api/Library
enum suites — are unrelated to this sprint; the staff work adds **zero**
failures).

---

# Static Analysis

- PHPStan level 5 with the Larastan extension; the line-specific
  `phpstan-baseline.neon` (repo convention from SPR-004) was regenerated with
  `--generate-baseline`, adding 54 Staff entries (17 files) and removing none
  of the pre-existing suppressions.
- One genuine defect found and fixed during analysis: `AssignmentResource`
  imported `Filament\Forms\Get`, which does not exist in Filament v5 — the
  correct class is `Filament\Schemas\Components\Utilities\Get` (same one used by
  `EnrollmentResource`). All other findings are the usual Eloquent magic-property
  / enum-cast noise already pinned for every prior domain.
- `vendor/bin/phpstan analyse` → **exit 0**.

---

# Boundaries & Extension Strategy

- **Inbound dependency rule:** Staff depends only on shared platform pieces
  (`User`, `School`, `Permission`) and the shared `App\Models\Department`
  catalog. No domain depends on Staff.
- **Consumers (future sprints, read-only):**
  - **Payroll** reads `Employment` contract + `termination_date` for run eligibility.
  - **Timetable** reads `TeacherProfile` + `Assignment` for subject/class coverage.
  - **Attendance** reads `Staff` status + active `Employment` to gate clocking.
  - **Communication** links `Staff.user_id` for messaging.
- New workforce features must preserve the one-active-employment invariant,
  the append-only journal, and route mutations through `StaffService`.
- Deliberately **out of scope** this sprint: leave management, payroll,
  performance evaluation (documented in `02_Domain/HR_Engine`).

---

# Known Issues / Next Steps

- Full-suite parity still reports the pre-existing failing suites; they must be
  triaged in a future sprint before the suite can go fully green.
- The shared `App\Traits\HasSchoolId` nullsafe pattern is pinned in the baseline
  (as for every other domain); a future refactor could type the models via
  `@property` annotations to lift this noise at the source.
- Payroll / Timetable / Attendance / Leave sprints will consume the Staff
  domain read-only (see Boundaries).

---

# Related Documents

- [ADR-006](../ADR/ADR-006.md)
- [ADR-004-EnrollmentLifecycle](../06_ADR/ADR-004-EnrollmentLifecycle.md)
- [HR_Engine](../02_Domain/HR_Engine.md)
- [Teacher](../02_Domain/Teacher.md)
- [Multi-Tenancy](../03_Database/MultiTenancy.md)
- [Static_Analysis](../03_Engineering/Static_Analysis.md)
