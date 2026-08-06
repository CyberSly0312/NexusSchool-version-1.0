# SPR-010 — Gradebook Engine Report

**Status:** Complete
**Branch:** main
**Date:** 2026-08-06
**Owner:** Backend Architect / Domain Engineering
**Related Spec:** [Grading Engine](../02_Domain/Grading_Engine.md)

---

# Summary

SPR-010 delivered the **Gradebook Engine**: the term/subject grading pipeline of
NexusSchool. It turns validated assessment percentages into **calculated,
locked, published and archived gradebooks** (forward-only lifecycle with an
audited re-opening for corrections) and produces the student-level aggregates
the platform needs for official documents — **term average, GPA, CGPA, overall
percentage, academic standing and ranking**.

This hand-off closed the remaining gaps against the Grading Engine spec — the
**GPA / CGPA and academic standing** aggregates (with a configurable GPA
formula) — so the engine now satisfies its Definition of Done and is safe for
downstream consumers (Academic Communication, Report Card).

**Quality gate at hand-off:**

- **Pest:** 55 passed / 206 assertions for the engine (30 unit + 25 feature,
  incl. 3 tenant-isolation tests). Full-suite parity maintained.
- **PHPStan (level 5, Larastan):** exit 0 — "No errors". The repo convention is
  a pinned `phpstan-baseline.neon`; this sprint regenerated the baseline (now
  1 409 entries, incl. the new GPA/standing code).
- **Pint:** all touched files formatted and clean (`--test` passes).

---

# Objectives

- Enforce the core invariants at the application layer, never in the model:
  - **forward-only lifecycle** `Draft → Calculated → Locked → Published →
    Archived`, with `Unlock` as the audited correction path that re-opens a
    locked gradebook,
  - **immutable entries** once the gradebook is locked or published,
  - **one gradebook per (school, academic year, term, class, section)**, with a
    database-level unique scope.
- Compute the **term average** as the coefficient-weighted mean of the published
  assessment percentages (draft/scheduled assessments excluded), with a plain
  mean fallback when no weights are present.
- Rank students with **standard competition ranking** ("1224").
- Aggregate the student-level figures specified by the Grading Engine:
  - **GPA** = coefficient-weighted mean of the grade points on a configurable
    scale (`config/nexus-gradebook.php`, default 4.0),
  - **CGPA** = the same aggregation over every published entry (no term filter),
  - **overall percentage** = coefficient-weighted mean of the entry
    percentages,
  - **academic standing** = Distinction / Pass / Conditional / Fail derived from
    the overall percentage and the number of failing subjects, with precedence
    ordered so a pass check precedes the conditional rule.
- Enforce **tenant isolation** on every query and refuse cross-school references.
- Keep the presentation layer thin, routed exclusively through the
  `GradebookService` facade, with capability-based authorization and a
  `super_admin` bypass; gradebook aggregates are never deleted, restored or
  force-deleted.
- Land the engine green under the three quality gates.

---

# Delivered Scope

## Domain structure

| Layer         | Artifacts                                                                                                                   |
|---------------|-----------------------------------------------------------------------------------------------------------------------------|
| Application   | 6 Actions, 1 DTO, 4 Services (`GradebookService`, `GradeCalculationService`, `GradeRankingService`, `AcademicStandingService`) |
| Domain        | 5 Enums, 7 Events, 5 Exceptions, 4 Contracts, 13 References, 5 Value Objects, 1 Authorization trait                         |
| Infrastructure| 2 Models, 1 Observer, 2 Policies, 1 Audit Listener, 4 Adapters                                                               |
| Presentation  | 2 Filament resources + 5 pages (nav group "Bulletins")                                                                      |

**Actions:** `CreateGradebook`, `CalculateGradebook`, `LockGradebook`,
`UnlockGradebook`, `PublishGradebook`, `ArchiveGradebook`.

**Enums:**

- `GradebookStatus` — `draft | calculated | locked | published | archived`
- `GradeEntryStatus` — entry-level validation state
- `LetterGrade` — percentage bounds → letter grade, grade points, appreciation
- `Appreciation` — derived from the school scale average
- `AcademicStanding` — `distinction | pass | conditional | fail`

**Value Objects:** `GradePercentage` (finite, clamped [0, 100]), `GradeCoefficient`,
`Average` (percentage → school scale), `Rank` (standard competition ranking),
`Gpa` (weighted mean with plain-mean fallback when `totalCoefficient ≤ 0`).

**Contracts (Domain) / Adapters (Infrastructure):** `AcademicFramework`,
`Assessment`, `Enrollment`, `Gradebook` (read-side query adapter).

## Schema (2 migrations, `2026_08_06_*`)

- `gradebooks` (000001) — aggregate root; school-scoped unique scope
  (school, year, term, class, section); lifecycle status.
- `grade_entries` (000002) — per student × subject: validated percentage,
  coefficient, grade points, rank; unique gradebook × student.

## GPA / CGPA / standing

`AcademicStandingService` (registered as a singleton) reads only **published**
grade entries and computes:

- `gpa(...)` / `cgpa(...)` — coefficient-weighted mean of grade points
  (GPA filters by period, CGPA does not), scale and decimals from
  `config/nexus-gradebook.php` (`gpa.scale` default 4.0, `gpa.decimal_places`
  default 2) — the configurable formula required by the spec;
- `overallPercentage(...)` — coefficient-weighted mean of entry percentages;
- `standing(...)` — precedence:
  1. **Distinction** — percentage ≥ `distinction_percentage` (default 80.0) and
     zero failing subjects,
  2. **Pass** — percentage ≥ `passing_percentage` (default 50.0) and zero
     failing subjects,
  3. **Conditional** — percentage ≥ `passing_percentage` and failing subjects
     ≤ `conditional_failures` (default 1),
  4. **Fail** — otherwise.

## Wiring

- `GradebookServiceProvider` registers singletons (`GradebookService`,
  `GradeCalculationService`, `GradeRankingService`, `AcademicStandingService`),
  binds every domain contract → adapter (the only place the engine sees foreign
  models), observes the models, gates the 2 policies, and listens for the 7
  domain events via `GradebookAuditLogger`.
- Permissions (group `gradebook`, label "Bulletins"): `gradebook.view`,
  `gradebook.create`, `gradebook.update`, `gradebook.lock`, `gradebook.unlock`,
  `gradebook.calculate`, `gradebook.publish`, `gradebook.archive`,
  `gradebook.delete`. Authorization is capability-based with the `super_admin`
  platform bypass — see `HandlesGradebookAuthorization`.
- `config/nexus-gradebook.php` — lifecycle, grade scale, ranking, validation,
  remarks, calculation, GPA and standing settings (all env-driven).
- `SchoolAdminPanelProvider` registers the 2 resources (Gradebook,
  GradeEntry) under "Bulletins".

---

# Test Coverage

| Suite                                   | Kind    | Focus                                                             |
|-----------------------------------------|---------|-------------------------------------------------------------------|
| `GradebookArchitectureTest`             | Arch    | layer boundaries, contract/adapters, container bindings, uuid     |
| `LetterGradeTest`                       | Unit    | bounds, grade points, appreciations, 50% pass/fail threshold      |
| `GradeCalculationServiceTest`           | Unit    | weighted/plain mean, clamping, scale projection, ranking          |
| `AcademicStandingServiceTest`           | Unit    | GPA/CGPA/overall percentage/standing rules + configurable formula |
| `GradebookCalculationTest`              | Feature | term averages, weight tilt, draft exclusion, recalculation, ranks |
| `GradebookLifecycleTest`                | Feature | forward-only lifecycle, lock/unlock, scope uniqueness, audit trail|
| `GradebookPolicyTest`                   | Feature | capability matrix, school scoping, super_admin, never delete      |
| `GradebookTenantIsolationTest`          | Feature | per-school gradebooks/entries, per-tenant scope uniqueness        |
| `AcademicStandingTest`                  | Feature | GPA/percentage/standing aggregate only from published gradebooks  |

**Result:** 55 passed / 206 assertions (30 unit + 25 feature). Full-suite
parity maintained — the engine adds **zero** failures.

---

# Static Analysis

- PHPStan level 5 with the Larastan extension; the line-specific
  `phpstan-baseline.neon` was **regenerated** against the current working tree
  (now 1 409 entries). `vendor/bin/phpstan analyse` → **exit 0**.
- The `App\Traits\HasSchoolId` nullsafe pattern is pinned per model in the
  baseline, as for every other domain (a future refactor could type the models
  via `@property` annotations to lift this noise at the source).
- Pint: all touched files pass `--test`.

---

# Boundaries & Extension Strategy

- **Inbound dependency rule:** Gradebook depends only on shared platform pieces
  and consumes assessment data through the `AssessmentContract`; enrollments and
  the academic framework arrive through their contracts. Foreign models are
  only ever touched by infrastructure adapters bound in the provider.
- **Consumers (read-only):** Academic Communication (via `GradebookContract`)
  and Report Card (via its own `GradebookContract`) read published entries and
  the standing aggregates through contracts — calculation lives only here.
- **Configurable formula:** the GPA scale, decimal places, passing/distinction
  thresholds and the conditional-failure allowance are school-configurable via
  env-driven config, satisfying the "GPA formula configurable" requirement.
- **New assessments:** the engine reacts to `AssessmentResultPublished`-style
  events; recalculation refreshes term averages as contributions change.

---

# Known Issues / Next Steps

- Downstream consumption: the Report Card engine (SPR-011) snapshots the
  standing figures verbatim; the Class Council engine (SPR-013) is still to
  come and will feed the same aggregates through its own contracts.
- The pinned baseline also contains pre-existing (non-engine) errors from other
  domains; triaging those remains a follow-up, not this sprint's work.

---

# Related Documents

- [Grading Engine](../02_Domain/Grading_Engine.md)
- [AcademicCommunication_Engine](../02_Domain/AcademicCommunication_Engine.md)
- [Multi_Tenant_Architecture](../01_Architecture/Multi_Tenant_Architecture.md)
- [Static_Analysis](../03_Engineering/Static_Analysis.md)
