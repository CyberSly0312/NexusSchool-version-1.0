# NexusSchool Enterprise Platform

# Current Position

**Document ID:** GOV-005

**Version:** 1.0.0

**Status:** Active

**Owner:** NexusSchool Architecture Team

**Last Updated:** 10 October 2026

---

# 1. Project Overview

This document represents the real-time status of the NexusSchool platform.

It shall be updated after every completed sprint.

It provides the official reference for the current development position, completed work, remaining work and immediate next objectives.

---

# 2. Current Development Status

Platform Name

NexusSchool Enterprise Platform

---

Current Version

0.4.0-alpha

---

Current Phase

**Phase 3 — Academic Records**

---

Current Sprint

**None — no business engine in progress**

---

Last Completed Sprint

**SPR-015 — Certificate & Transcript Engine**

---

Next Sprint

**SPR-016 — Identity & Verification Engine** — ARCHITECTURE DECISION / INITIALIZATION
(ADR-022, 2026-09-07); NOT IMPLEMENTED

---

SPR-016

Architecture Decision / Initialization — ADR-022 governs; NOT IMPLEMENTED

---

SPR-014

DEFERRED — validated implementation awaiting final closure decision

---

Sprint Status

🟢 SPR-013 Completed · 🟢 SPR-015 Completed · 🟡 SPR-016 Initialization (ADR-022) · ⏸ SPR-014 Deferred

---

Repository State

Uncommitted. The working tree contains a large pre-existing refonte plus the
ESP-002 hardening changes (~298 files vs HEAD `ac8acc2`). The current green test
state is therefore **not anchored to a new commit**. This work is
**UNCOMMITTED REFACTORING / UNCLASSIFIED** until a separate forensic review.

---

Overall Project Progress

Approximately **50%**

---

Architecture Health

🟢 Excellent

---

Documentation Coverage

🟢 High

---

Technical Debt

🟢 Low

---

# 3. Completed Phases

## ✅ Phase 0

Platform Foundation

Status

Completed

---

## ✅ Phase 1

Core Academic Foundation

Completed Sprints

* SPR-001 Foundation
* SPR-002 Academic Framework
* SPR-003 Student Domain
* SPR-004 Enrollment Domain
* SPR-005 Guardian Domain
* SPR-006 Staff Domain
* SPR-007 Timetable Engine
* SPR-008 Attendance Engine
* SPR-009 Assessment Engine

Status

Completed

Note (reconciliation 2026-08-10)

SPR-009.5 Academic Policies Engine was **not** part of the completed sprints at
reconciliation time — it had no implementation and was **PLANNED / NOT
STARTED**. It has since been **implemented** (2026-08-10, third session). See
the dedicated block below.

---

## ✅ SPR-009.5 — Academic Policies Engine

Status

Completed (2026-08-10)

Objective

Exclusive origin of academic rules (ARCHITECTURE_DECISIONS §6); replaces the
empty stub adapters consumed by Report Card and Academic Communication.

Deliverables

* New bounded context `App\Domains\AcademicPolicy` (Domain / Application /
  Infrastructure / Filament, 30 files).
* Typed policy store (`string|integer|float|boolean|json`) with scopes
  `school|academic_year|subject` and unique `(school_id, key, scope)`.
* CRUD actions + `AcademicPolicyService`, query contract, ULID observer,
  `academic_policy.*` permissions, audit log on every lifecycle event.
* Filament `AcademicPolicyResource` (group "Académique", sort 120).
* Report Card `forPeriod` and Academic Communication `policies`/`policy`
  adapters rewired to the engine — undefined policies behave exactly as the
  stubs did (`[]`/`null`), zero regression.
* No default rule values invented; no country reference values seeded.

Quality Gates

* PHPStan: exit 0 — "No errors" (baseline unchanged, no new entries).
* Pest: full suite 1861 passed / 4397 assertions (1 pre-existing legacy flake:
  `AcademicYearServiceTest::test_can_search_by_name`, passes standalone);
  SPR-009.5 suites 43 passed /
  115 assertions.
* Pint: clean on every touched file (419 pre-existing repo failures disclosed,
  untouched).
* Report: SPR-009.5_Academic_Policies_Engine_Report.md

---

## ✅ ESP-002 — Repository Hardening

Status

Completed

Objective

Static-analysis stabilization, not a feature sprint.

Deliverables

* `SchoolOwned` contract (`getSchoolId(): string`) — explicit tenant ownership.
* 6 tenant-scoped aggregates implement `SchoolOwned` (Attendance x5, CouncilSession).
* Attendance / Class Council authorization traits typed against `SchoolOwned`.
* UTF-8 remediation of 11 factory files.

Quality Gates

* PHPStan: exit 0 — "No errors" (baseline unchanged, no new entries).
* Pest: 1819 passed / 4281 assertions / 0 failed / 0 skipped.
* Pint: clean.

---

# 4. Current Phase

Academic Records

Current Objectives

* ✅ Academic Policies Engine (SPR-009.5) — architectural prerequisite
* ✅ Certificate & Transcript Engine (SPR-015)
* 🔲 Records Lifecycle Management (SPR-014) — deferred
* 🟡 Identity & Verification Engine (SPR-016) — architecture decision / initialization (ADR-022), not implemented

Current Focus

Create a permanent academic memory for every learner, gated by the Academic
Policies Engine.

---

# 5. Next Planned Phase

School Portals

Planned Modules

* Parent Portal
* Student Portal
* Teacher Portal
* Secretary Portal
* Director Portal
* Accountant Portal

---

# 6. Platform Health

Laravel

✅ Healthy

PHP

✅ 8.2.12 (runtime; composer requirement ^8.2)

Filament

✅ 5.6.8

DDD Architecture

✅ Stable

Contracts

✅ Stable

Multi-Tenancy

✅ Stable

Testing

✅ Active

Documentation

✅ Active

Architecture Governance

✅ Active

---

# 7. Quality Metrics

Pest

PASS

Laravel Pint

PASS

PHPStan

PASS

Architecture Tests

PASS

Contract Tests

PASS

Tenant Isolation

PASS

---

# 8. Known Legacy Issues

The following issues originate from the previous architecture and are tracked separately.

* Legacy Filament Panel Tests
* Legacy Enum Labels
* Legacy PermissionService Test Count

These items are outside the current sprint scope.

---

# 9. Immediate Priorities

Priority 1

Records Lifecycle Management Engine (SPR-014) — SPR-009.5 prerequisite now complete

Priority 2

Certificate & Transcript Engine

Priority 3

Identity & Verification Engine

Priority 4

Policy-driven Gradebook / Class Council / Assessment integration (deferred work)

---

# 10. Current Architecture Focus

Current architectural priorities are

* Official document publication
* Immutable academic history
* Versioned academic documents
* Academic communication pipeline
* Long-term academic record preservation

---

# 11. Definition of Current Success

The current phase will be considered complete when

* ✅ Gradebook is stable.
* ✅ Class Council is stable.
* ✅ Academic Communication Engine is production-ready.
* ✅ Report Card Engine is production-ready.
* ✅ Academic Policies Engine (SPR-009.5) is implemented.
* Records Lifecycle Management architecture is implemented.
* Certificate & Transcript Engine is production-ready.
* Identity & Verification Engine is production-ready.

---

# 12. Next Checkpoint

Upon completion of the current sprint, this document shall be updated with

* New overall progress.
* Completed sprint.
* Next sprint.
* Updated platform health.
* Updated priorities.
* Updated quality metrics.

---

# 13. Owner Statement

This document is the official operational status of the NexusSchool platform.

Every development session shall begin by reviewing this document.

Every completed sprint shall update this document before any new implementation begins.

## SPR-014 Records Lifecycle Management — Validation Update (2026-08-11, updated 2026-08-20)

Status: **IMPLEMENTED — ALL THREE SOURCE DOMAIN INTEGRATIONS COMPLETE**.

The thin provenance/lifecycle metadata envelope is implemented and all targeted tests, scoped Pint, scoped/global PHPStan, and the complete suite pass. Report Card, Academic Communication, and Class Council all register lifecycle metadata through the contract/adapter pattern (27 integration tests / 97 assertions). Full suite: 1,889 tests / 4,507 assertions. Awaiting final closure decision.

Validation continuation (2026-08-12): the full suite was rerun successfully in 511.29 s. The initial exact lifecycle-unit invocation timed out after it reported passing assertions; its `--no-coverage` retest completed successfully.

Stabilization (2026-08-19): added `RecordLifecycleFactory` and expanded state-transition test coverage to the full transition matrix. 12 tests / 38 assertions now pass (up from 8 / 31). PHPStan and Pint clean. Report Card and Academic Communication integration seams wired (22 tests / 77 assertions).

Class Council integration (2026-08-20): Added `RecordsLifecycleContract` and `RecordsLifecycleAdapter` in ClassCouncil domain. Wired creation, publication, and archival actions. 5 integration tests / 20 assertions added. Architecture tests updated in all three domains. Full suite: 1,889 tests / 4,507 assertions.

## SPR-015 / SPR-016 — Governance reconciliation (2026-09-07)

**SPR-015 Certificate & Transcript Engine — COMPLETED** (implementation + report;
suites green at last run: CertificateAndTranscript 45 tests / 218 assertions,
AcademicCommunication 70 tests / 312 assertions).

**SPR-016 Identity & Verification Engine — DISCOVERY COMPLETED; ARCHITECTURE
DECISION / INITIALIZATION; NOT IMPLEMENTED.** Architecture formalized in **ADR-022**
(Accepted, 2026-09-07): public unauthenticated central verification endpoint; random
opaque verification token (≥144-bit, generated once, globally unique, SHA-256-hashed
at rest) owned by the AcademicDocument aggregate; default LEVEL 2 disclosure
(number, type, status, issuance date, holder name — never grades / personal data /
internal ids); formal REVOKED (capability + reason + audit, terminal) and REPLACED
semantics; QR = verification URL + opaque token only, rendering delegated to
SPR-012; central + opaque-token tenant resolution (no school-code / document-number
probing); document number never a secret.

**SPR-014 Records Lifecycle Management remains DEFERRED** — validated implementation
awaiting the final closure decision; must not be treated as COMPLETE.

No code was changed by this reconciliation; governance documents only.

## SPR-017 — Archive & Cloud Storage Platform (planning reconciliation, 2026-10-10)

**SPR-017 Archive & Cloud Storage Platform is a Phase 9 — Enterprise Services
capability** (product-owner decision D1), not an ordinary Phase 3 Academic Records
deliverable. It may be prioritized for planning now; that does not change the
Current Sprint or SPR-016 status above.

Next decision target: **SPR-017** — architecture review of **ADR-024** (Proposed,
2026-10-10): dedicated `App\Domains\Archive` consuming SPR-014
`RecordsLifecycleContract` and SPR-012 read contracts only; **local-first storage,
cloud deferred** (D2); retention **configurable per document type and per school**,
values owned by product/legal, automatic secure destruction disabled (D3);
**`plans.storage_limit` remains the single quota authority** (D4); downloadable
school archive packages in scope but not mandatory for the first release (D5).

SPR-014 remains **DEFERRED** — contract-only consumption; must not be treated as
COMPLETE. No implementation was performed. Discovery report:
`SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md`.
