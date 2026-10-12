# NexusSchool Enterprise Platform

# Changelog

**Document ID:** GOV-010

**Version:** 1.0.0

**Status:** Active

**Owner:** NexusSchool Architecture Team

**Last Updated:** 10 October 2026

---

# 1. Purpose

This document is the official historical record of the NexusSchool platform.

It provides a chronological view of architectural decisions, sprint deliveries, major features, technology upgrades and platform milestones.

Unlike Git commit history, this changelog captures business and architectural evolution in a human-readable format.

Every completed sprint shall update this document.

---

# 2. Versioning Policy

The platform follows Semantic Versioning (SemVer).

```
MAJOR.MINOR.PATCH
```

### MAJOR

Breaking architectural or platform changes.

Examples

* Major platform redesign
* Multi-tenant architecture redesign
* Framework migration

---

### MINOR

New Business Engines.

Examples

* Gradebook Engine
* Attendance Engine
* Finance Engine
* Communication Engine

---

### PATCH

Bug fixes

Security improvements

Performance improvements

Documentation updates

Minor refactoring

---

# 3. Development Timeline

## Version 0.1.0-alpha

Platform Foundation

Status

Completed

Highlights

* Laravel 12
* PHP 8.2.12 (runtime; composer requirement ^8.2)
* Filament 5.6.8
* Multi-Tenant Foundation
* Authentication
* Authorization
* Documentation Governance
* Architecture Governance

---

## Version 0.2.0-alpha

Academic Foundation

Status

Completed

Highlights

* Academic Framework
* Student Domain
* Enrollment Domain
* Guardian Domain
* Staff Domain
* Timetable Engine
* Attendance Engine
* Assessment Engine

Note (reconciliation 2026-08-10)

Academic Policies Engine was listed in this version's highlights but was **never
implemented**. It is PLANNED / NOT STARTED (see the Governance Reconciliation
entry below).

---

## Version 0.3.0-alpha

Academic Intelligence

Status

Completed

Highlights

* Gradebook Engine
* Report Card Engine
* Academic Communication Engine
* Class Council Engine

---

## Version 0.4.0-alpha

Academic Records

Status

In Progress

Expected Deliverables

* Records Lifecycle Management
* Certificates
* Transcript Engine
* Academic Archive
* Identity Verification

---

## ESP-002 — Repository Hardening

Date

10 August 2026

Version

0.4.0-alpha (unchanged — patch-level stabilization)

Sprint Reference

ESP-002

Summary

* Introduced the `SchoolOwned` contract (`getSchoolId(): string`) making tenant
  ownership explicit and statically verifiable.
* 6 tenant-scoped aggregates (Attendance x5, `CouncilSession`) implement the
  contract.
* Attendance / Class Council authorization traits now read the school id through
  the contract instead of an implicit `school_id` magic property.
* UTF-8 remediation of 11 factory files.

Architectural Impact

* Type-safety improvement only — authorization semantics unchanged
  (`super_admin` bypass, cross-school denial, never-delete preserved).
* PHPStan baseline unchanged: no new entries, exit 0.
* Additive pattern available for future repository-wide hardening.

Documentation Updated

* ESP-002_Repository_Hardening_Report.md (new)
* CURRENT_POSITION.md
* PROJECT_STATE.md
* SESSION_HANDOFF.md
* Project Journal — 2026-08-10

---

## Governance Reconciliation — 2026-08-10

Date

10 August 2026

Version

0.4.0-alpha (unchanged)

Sprint Reference

None — governance state correction following the Global Synchronization Audit

Summary

* Corrected **SPR-009.5 Academic Policies** from Complete to **PLANNED / NOT
  STARTED** — no implementation exists (only empty stub adapters).
* Corrected **SPR-014 Records Lifecycle** from In Progress to **PLANNED / NOT
  STARTED** — no implementation, no report.
* Corrected the current phase to **Phase 3 — Academic Records** in documents
  that previously stated Phase 2.
* Corrected stack documentation: runtime **PHP 8.2.12** (composer `^8.2`) and
  **Filament 5.6.8**, previously stated as PHP 8.4 / Filament 4.
* Disclosed the uncommitted repository state: ~298 files of pre-existing
  refonte plus ESP-002 changes are **not anchored to a commit**; the green test
  state is therefore uncommitted.
* Recorded missing sprint reports for SPR-005, SPR-007, SPR-008, SPR-009 (as
  documentation debt — reports were never produced for completed engines).

Architectural Impact

* No application-code impact. Academic Policies remains a **required
  architectural dependency** (ARCHITECTURE_DECISIONS §6); no later ADR
  supersedes it.
* No feature was implemented or claimed. Governance now reflects repository
  reality.

Documentation Updated

* MASTER_ROADMAP.md
* CURRENT_POSITION.md
* PROJECT_STATE.md
* SESSION_HANDOFF.md
* Project Journal — 2026-08-10 (second session)

---

## SPR-009.5 Academic Policies Engine — 2026-08-10

Date

10 August 2026

Version

0.4.0-alpha (unchanged)

Sprint Reference

SPR-009.5 — Academic Policies Engine (Complete)

Summary

* Implemented the **Academic Policies Engine**, the exclusive origin of
  academic rules mandated by ARCHITECTURE_DECISIONS §6.
* New bounded context `App\Domains\AcademicPolicy` (Domain / Application /
  Infrastructure / Filament): typed policy store (`string|integer|float|
  boolean|json`), scopes `school|academic_year|subject`, unique
  `(school_id, key, scope)`, soft deletes, CRUD actions + service, query
  contract, ULID entity observer, `academic_policy.*` permissions, audit log
  on every lifecycle event, Filament resource (Académique group).
* Rewired the two empty stub adapters: Report Card (`forPeriod`) and Academic
  Communication (`policies`/`policy`) now read real policies from the engine.
  Undefined policies behave exactly as the stubs did (`[]`/`null`) — zero
  regression.
* No default rule values were invented: schools define their policies; no
  country reference values are seeded.
* Quality gates: full suite **1859 passed / 4394 assertions**; PHPStan exit 0
  (baseline untouched); Pint clean on every touched file. 419 **pre-existing**
  Pint failures in the uncommitted refonte disclosed and left untouched.

Architectural Impact

* The SPR-009.5 architectural prerequisite (AD §6) is now implemented; SPR-014
  Records Lifecycle Management may proceed.
* Gradebook / Class Council / Assessment computation remains config-driven;
  policy-driven integration is tracked as deferred work (no code change).

Documentation Updated

* SPR-009.5_Academic_Policies_Engine_Report.md (new)
* MASTER_ROADMAP.md
* CURRENT_POSITION.md
* PROJECT_STATE.md
* SESSION_HANDOFF.md
* Project Journal — 2026-08-10 (third session)

---

## Version 0.5.0-alpha

Portal Ecosystem

Status

Planned

Expected Deliverables

* Parent Portal
* Student Portal
* Teacher Portal
* Director Portal
* Secretary Portal
* Accountant Portal

---

## Version 0.6.0-alpha

Operational Management

Status

Planned

Expected Deliverables

* Finance
* Library
* Inventory
* Transport
* Hostel
* Human Resources
* Payroll

---

## Version 1.0.0

Enterprise Release

Status

Planned

Expected Deliverables

* Production Ready
* Enterprise Stable
* Public APIs
* Marketplace
* Cloud Archive
* White Label
* Monitoring
* Disaster Recovery

---

# 4. Sprint History

| Sprint    | Name                          | Status |
| --------- | ----------------------------- | ------ |
| SPR-001   | Foundation                    | ✅      |
| SPR-002   | Academic Framework            | ✅      |
| SPR-003   | Student Domain                | ✅      |
| SPR-004   | Enrollment Domain             | ✅      |
| SPR-005   | Guardian Domain               | ✅      |
| SPR-006   | Staff Domain                  | ✅      |
| SPR-007   | Timetable Engine              | ✅      |
| SPR-008   | Attendance Engine             | ✅      |
| SPR-009   | Assessment Engine             | ✅      |
| SPR-009.5 | Academic Policies Engine      | 🔲      |
| SPR-010   | Gradebook Engine              | ✅      |
| SPR-011   | Report Card Engine            | ✅      |
| SPR-012   | Academic Communication Engine | ✅      |
| SPR-013   | Class Council Engine          | ✅      |
| SPR-014   | Records Lifecycle Management  | 🔲      |

Status: ✅ Complete · 🔲 Planned / Not Started (no implementation).

---

# 5. Major Architectural Milestones

## Foundation

* Multi-Tenant Architecture established.
* Domain-Driven Design adopted.
* Clean Architecture implemented.
* Event-Driven communication introduced.

---

## Academic Platform

* Academic Policies centralized — **corrected (reconciliation 2026-08-10):**
  not yet implemented; only stub adapters exist. Planned under SPR-009.5.
* Grade calculation separated from publication.
* Class Council validation introduced.
* Official documents versioned.

---

## Records Lifecycle

Planned

* Immutable academic records.
* Archive lifecycle.
* Retention policies.
* Cloud Archive.
* Long-term preservation.

---

# 6. Technology Evolution

| Component    | Current Version |
| ------------ | --------------- |
| Laravel      | 12              |
| PHP          | 8.2.12 (runtime) |
| Filament     | 5.6.8           |
| Pest         | Current         |
| PHPStan      | Level 5         |
| Larastan     | Enabled         |
| Laravel Pint | Enabled         |

Technology upgrades shall be recorded here.

---

# 7. Documentation Milestones

Governance documents

✅ Active

Architecture documentation

✅ Active

ADR collection

✅ Active

Sprint reports

Active

Database documentation

Active

Blueprint documentation

Planned

API documentation

Planned

User documentation

Planned

---

# 8. Release Milestones

## Alpha

Current

Primary objective

Architecture stabilization.

---

## Beta

Planned

Primary objective

Complete functional validation.

---

## Release Candidate

Planned

Primary objective

Production readiness.

---

## Version 1.0

Planned

Primary objective

Enterprise deployment.

---

# 9. Change Management Rules

Every entry added to this document shall include

* Date
* Version
* Sprint reference
* Summary of changes
* Architectural impact
* Documentation updated

---

# 10. Future History

Future updates shall record

* New Business Engines
* Infrastructure improvements
* Performance optimizations
* Security enhancements
* Architectural decisions
* Public releases
* Customer-impacting changes

This document shall remain the authoritative history of the NexusSchool platform.

---

# 11. Approval

This document is the official Changelog of the NexusSchool platform.

Every official release, sprint and architectural milestone shall be recorded here to preserve the complete evolution of the platform.

## 2026-08-11 — SPR-014 validation

- Validated the RecordsLifecycle metadata envelope: lifecycle, authorization, tenant isolation, architecture, migration, PHPStan, Pint, domain regressions, and full test suite.
- Full suite passed: 1,870 tests and 4,429 assertions.
- Status set to **IMPLEMENTED — TARGETED GATES PASS**; not COMPLETE because no Report Card / Academic Communication integration seam is currently wired.

## 2026-08-19 — SPR-014 stabilization

- Added `RecordLifecycleFactory` following project conventions.
- Expanded state-transition test coverage to the full transition matrix (`Published → Archived`, `Archived → *`, `Registered → Registered`).
- Test count: 12 tests / 38 assertions (up from 8 / 31).
- PHPStan exit 0, Pint clean on all scoped files.
- Source-domain integration seams remain unimplemented.

## 2026-08-20 — SPR-014 Class Council integration

- Added `RecordsLifecycleContract` and `RecordsLifecycleAdapter` in the ClassCouncil bounded context.
- Wired `CreateCouncilSessionAction`, `PublishCouncilSessionAction`, and `ArchiveCouncilSessionAction` to register, publish, and archive lifecycle metadata via the contract/adapter pattern.
- Updated architecture tests in all three domains (ClassCouncil, ReportCard, AcademicCommunication) to include `RecordsLifecycleContract`.
- Added 5 ClassCouncil integration tests covering provenance, transitions, publish-then-archive, and tenant isolation.
- Test count: 1,889 tests / 4,507 assertions (full suite).
- PHPStan 0 errors, Pint clean on all touched files.
- All three source domains (Report Card, Academic Communication, Class Council) now register lifecycle metadata through the contract/adapter pattern.

## 2026-10-10 — SPR-017 architecture decision (ADR-024, Proposed)

Date

10 October 2026

Version

0.4.0-alpha (unchanged — documentation only)

Sprint Reference

SPR-017 — Archive & Cloud Storage Platform (Phase 9 — Enterprise Services; decision phase)

Summary

* Completed the SPR-017 discovery / architecture decision board and report —
  `SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md` (verdict: NEEDS
  PRODUCT-OWNER DECISIONS).
* Authored **ADR-024: Archive & Cloud Storage Platform Architecture (SPR-017)** —
  Status: **Proposed** (awaiting NexusSchool Architecture Team review).
* Recorded approved product decisions D1–D5: **Phase 9 placement** (planning may
  start now); **local-first storage, cloud deferred**; **retention configurable per
  document type and per school**, values owned by product/legal, automatic secure
  destruction disabled; **`plans.storage_limit` remains the single quota
  authority**; **downloadable school archive packages in scope but not mandatory
  for the first release**.

Architectural Impact

* **Documentation / decision artifact only.** No code, migration, schema, route,
  config, dependency, test, UI, cloud configuration or secret was created or
  changed; no implementation exists for SPR-017.
* Proposes a dedicated `App\Domains\Archive` bounded context consuming SPR-014
  `RecordsLifecycleContract` and SPR-012 read contracts only — never a second
  lifecycle engine, never a creator/owner of business records; the legacy
  generated-document pipeline remains excluded.
* SPR-014 stays **DEFERRED** (contract-only consumption; not marked COMPLETE).
* The pre-existing, unrelated MariaDB `coefficients` index identifier-length defect
  (65 chars > 64) remains a separate tracked issue; not part of SPR-017.

Documentation Updated

* docs/ADR/ADR-024.md (new)
* MASTER_ROADMAP.md
* CURRENT_POSITION.md
* SESSION_HANDOFF.md
