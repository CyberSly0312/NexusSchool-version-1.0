# SPR-011 — Report Card Engine Report

**Status:** Complete
**Branch:** main
**Date:** 2026-08-06
**Owner:** Backend Architect / Domain Engineering
**Related Spec:** [Grading Engine](../02_Domain/Grading_Engine.md)

---

# Summary

SPR-011 delivered the **Report Card Engine**: the orchestration layer that
turns the validated upstream engines into the official bulletin of every
enrolled student. It snapshots one `ReportCard` run per (school, year, term,
class, section), tracks each enrollment as an entry, composes the bulletin
content from the **Gradebook** (including the standing aggregates),
**Attendance**, **Academic Policies** and **Class Council** figures, and renders
and publishes the official documents **exclusively through the Academic
Communication engine**.

The engine **never performs any calculation**: every figure that reaches a
bulletin is serialized verbatim from upstream references produced by contracts
+ adapters. It is deliberately a leaf in the dependency graph.

**Quality gate at hand-off:**

- **Pest:** 36 passed / 247 assertions for the engine (15 unit + 21 feature,
  incl. 3 tenant-isolation tests). Combined with the SPR-010 and Academic
  Communication suites: **156 passed / 742 assertions**, all green together.
- **PHPStan (level 5, Larastan):** exit 0 — "No errors". The pinned
  `phpstan-baseline.neon` was regenerated (now 1 409 entries) to include the new
  Report Card code.
- **Pint:** all touched files formatted and clean (`--test` passes).

---

# Objectives

- **Render and publish only** — the Report Card engine never calculates a
  grade, average, GPA, standing or attendance figure; it snapshots upstream
  values through contracts.
- Enforce the core invariants at the application layer:
  - **forward-only lifecycle** `Draft → Generated → Published → Archived`, with
    **Regenerate** (the Gradebook-Unlock counterpart) recomposing fresh
    bulletins and documents for a generated or published run — every
    regeneration is audited,
  - a **unique run scope** per school, year, term, class and section,
  - each active enrollment snapshotted into a `ReportCardEntry`
    (`pending | included | excluded`),
  - one official academic document per bulletin, delegated to the Academic
    Communication engine with `DocumentType::ReportCard` and
    `DocumentOrigin::ReportCard` (permanent identity, immutable versions,
    publication pipeline handled there).
- Integrate the upstream engines through **domain contracts + adapters only** —
  no foreign model is ever imported inside the domain; Class Council and
  Academic Policies are consumed through contracts (stubbed this sprint, see
  Boundaries).
- Enforce **tenant isolation** and refuse academic frames owned by another
  school; keep the presentation layer thin and routed through the
  `ReportCardService` facade; capability-based authorization with a
  `super_admin` bypass; runs are never deleted, restored or force-deleted.
- Land the engine green under the three quality gates.

---

# Delivered Scope

## Domain structure

| Layer         | Artifacts                                                                                                             |
|---------------|-----------------------------------------------------------------------------------------------------------------------|
| Application   | 5 Actions, 1 DTO, 2 Services (`ReportCardService`, `ReportCardContentComposer`)                                       |
| Domain        | 3 Enums, 5 Events, 3 Exceptions, 7 Contracts, 9 References, 1 Authorization trait                                     |
| Infrastructure| 2 Models, 1 Observer, 1 Policy, 1 Audit Listener, 7 Adapters                                                           |
| Presentation  | 1 Filament resource + 3 pages (nav group "Communication académique")                                                  |

**Actions:** `CreateReportCard`, `GenerateReportCard`, `RegenerateReportCard`,
`PublishReportCard`, `ArchiveReportCard`.

**Enums:**

- `ReportCardStatus` — `draft | generated | published | archived`
- `ReportCardType` — `term | annual`
- `ReportCardEntryStatus` — `pending | included | excluded`

**References (verbatim snapshots):** `StudentReference`,
`EnrollmentReference`, `AcademicPeriodReference`, `GradebookEntryReference`,
`GradebookStandingReference`, `AttendanceSummaryReference`,
`PolicyReference`, `CouncilDecisionReference`, `AcademicDocumentReference`.

**Contracts (Domain) / Adapters (Infrastructure):** `AcademicFramework`,
`Enrollment`, `Gradebook`, `Attendance`, `AcademicPolicies` (stub),
`ClassCouncil` (stub), `AcademicCommunication`.

## Schema (2 migrations, `2026_08_06_*`)

- `report_cards` (000013) — aggregate root; school-scoped unique scope
  (school, year, term, class, section); status; type; optional
  `academic_document_id` / `academic_document_number`; `content` json snapshot
  of the run.
- `report_card_entries` (000014) — one row per enrollment with `pending /
  included / excluded` status and the student bulletin `content` snapshot;
  unique `report_card_id + enrollment_id`.

Both tables reference `academic_documents` with `nullOnDelete` so a run can be
re-rendered through the communication engine while keeping its identity.

## Lifecycle

```text
Draft -> Generated -> Published -> Archived
               ^            |
               \___ Regenerate (audited, recomposes bulletins + documents)
```

- `Draft` accepts only generation (no publish / archive / regenerate).
- `Generated` may be published or regenerated.
- `Published` may be archived or regenerated (each regeneration issues a new
  immutable version of the bulletins through the communication engine).
- `Archived` is final; no transition leads back to `Draft`.

## Wiring

- `ReportCardServiceProvider` registers the singleton `ReportCardService` and
  `ReportCardContentComposer`, binds every domain contract → adapter (the only
  place the engine sees foreign models, e.g. `AcademicCommunicationService` for
  the communication adapter), observes the 2 models, gates the policy, and
  listens for the 5 domain events via `ReportCardAuditLogger`.
- Permissions (group `report-card`, label "Bulletins"): `report-card.view`,
  `report-card.create`, `report-card.update`, `report-card.generate`,
  `report-card.regenerate`, `report-card.publish`, `report-card.delete` —
  dedicated capabilities for the two document-producing operations.
  Authorization is capability-based with the `super_admin` platform bypass —
  see `HandlesReportCardAuthorization`. Delete / restore / force-delete are
  always denied.
- `config/nexus-report-card.php` — `document_type` (`report_card`) and `origin`
  (`report_card`) used to delegate to the Academic Communication engine.
- `SchoolAdminPanelProvider` registers the `ReportCardResource` + 3 pages under
  "Communication académique".
- 2 factories (`database/factories/ReportCard/*`).

---

# Test Coverage

| Suite                               | Kind    | Focus                                                                 |
|-------------------------------------|---------|-----------------------------------------------------------------------|
| `ReportCardArchitectureTest`        | Arch    | layer boundaries, contract/adapters, container bindings, uuid         |
| `ReportCardStatusTest`              | Unit    | forward-only transitions, immutability, invalid transition throws     |
| `ReportCardContentComposerTest`     | Unit    | upstream figures serialized verbatim (contract fakes)                 |
| `ReportCardLifecycleTest`           | Feature | draft → generate → publish → archive → regenerate; scope uniqueness; delegation to the communication engine |
| `ReportCardPolicyTest`              | Feature | capability matrix, dedicated generate/regenerate/publish permissions, school scoping, super_admin, never delete |
| `ReportCardTenantIsolationTest`     | Feature | per-school runs/entries, per-tenant scope uniqueness, cross-tenant academic frame rejected |

**Result:** 36 passed / 247 assertions (15 unit + 21 feature). Full-suite
parity maintained — the engine adds **zero** failures.

---

# Static Analysis

- PHPStan level 5 with the Larastan extension; the line-specific
  `phpstan-baseline.neon` was **regenerated** against the current working tree
  (now 1 409 entries). `vendor/bin/phpstan analyse` → **exit 0**.
- The `App\Traits\HasSchoolId` nullsafe pattern is pinned per model in the
  baseline, as for every other domain.
- Pint: all touched files pass `--test`.

---

# Boundaries & Extension Strategy

- **Inbound dependency rule:** Report Card depends only on shared platform
  pieces and on its 7 domain contracts. Upstream data (academic framework,
  enrollments, gradebook figures and standing, attendance, policies, council
  decisions) and the publication target (Academic Communication) arrive
  exclusively through adapters bound in the provider.
- **Consumers (read-only):** none. The engine is a leaf; it is deliberately
  depended on by nobody, keeping the dependency graph acyclic.
- **Never calculates:** the `ReportCardContentComposer` serializes references
  verbatim; GPA/CGPA/standing live in the Gradebook engine
  (`AcademicStandingService`), read through the Gradebook adapter.
- **Class Council / Academic Policies are stubs:** the contracts return `[]`
  this sprint (the Class Council engine is a later sprint). Bulletins render
  without council decisions or policy extras until those engines land; the
  integration points and tests are already shaped by the contracts.
- **New upstream figure:** add a reference + a contract method + an adapter; the
  composer forwards it verbatim. New document behaviour stays in the Academic
  Communication engine.

---

# Known Issues / Next Steps

- The `AcademicPolicies` and `ClassCouncil` adapters return empty data until
  their engines are built (SPR-013 — Class Council per the roadmap).
- The engine delegates PDF rendering fidelity and digital publication delivery
  to the Academic Communication engine, as designed.
- The pinned baseline also contains pre-existing (non-engine) errors from other
  domains; triaging those is a follow-up, not this sprint's work.

---

# Related Documents

- [Grading Engine](../02_Domain/Grading_Engine.md)
- [AcademicCommunication_Engine](../02_Domain/AcademicCommunication_Engine.md)
- [Multi_Tenant_Architecture](../01_Architecture/Multi_Tenant_Architecture.md)
- [Static_Analysis](../03_Engineering/Static_Analysis.md)
