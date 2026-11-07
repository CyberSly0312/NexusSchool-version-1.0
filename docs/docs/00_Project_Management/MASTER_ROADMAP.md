# NexusSchool Enterprise Platform

## Master Roadmap

**Document ID:** GOV-001

**Version:** 1.0.0

**Status:** Active

**Owner:** NexusSchool Architecture Team

**Last Updated:** 05 November 2026

---

# 1. Purpose

This document defines the complete long-term roadmap of the NexusSchool platform.

It serves as the official architectural reference for all future development.

Every sprint, domain, module, service, and platform capability must align with this roadmap.

This roadmap has priority over implementation details whenever architectural decisions are required.

---

# 2. Vision

NexusSchool is an Enterprise Academic Management Platform designed for schools, colleges, universities, and educational organizations.

The platform is built using Domain-Driven Design (DDD), Clean Architecture, Event-Driven principles, and Multi-Tenant architecture.

Its objective is to become the reference Student Information System (SIS) for Africa and other French-speaking educational institutions.

---

# 3. Core Architectural Principles

The platform follows these immutable principles.

* Domain Driven Design (DDD)
* Clean Architecture
* SOLID Principles
* Event-Driven Architecture
* Contract-Based Integration
* Multi-Tenant by Design
* Immutable Academic Records
* Versioned Official Documents
* Test-Driven Development
* Documentation First
* Security by Default

---

# 4. Platform Development Phases

---

## Phase 0 — Platform Foundation

Status

✅ Completed

Main Deliverables

* Laravel Platform
* Multi-Tenant Foundation
* Authentication
* Authorization
* Contracts
* Domain Events
* Testing Infrastructure
* Documentation Standards
* Architecture Standards

---

## Phase 1 — Core Academic Foundation

Status

✅ Completed

Sprints

* SPR-001 Foundation
* SPR-002 Academic Framework
* SPR-003 Student Domain
* SPR-004 Enrollment Domain
* SPR-005 Guardian Domain
* SPR-006 Staff Domain
* SPR-007 Timetable Engine
* SPR-008 Attendance Engine
* SPR-009 Assessment Engine
* SPR-009.5 Academic Policies Engine — ✅ COMPLETED (2026-08-10, see note below)

Status

Completed

Note (reconciliation 2026-08-10)

SPR-009.5 Academic Policies Engine was **PLANNED / NOT STARTED** at the time of
the reconciliation — only empty stub adapters existed. It has since been
**implemented** (2026-08-10, third session): bounded context
`App\Domains\AcademicPolicy`, real adapter wiring for Report Card and Academic
Communication, typed policy store, Filament management surface. See
`SPR-009.5_Academic_Policies_Engine_Report.md`.

---

## Phase 2 — Academic Intelligence

Status

✅ Completed

Sprints

* SPR-010 Gradebook Engine
* SPR-011 Report Card Engine
* SPR-012 Academic Communication Engine
* SPR-013 Class Council Engine

Objective

Transform academic data into validated educational decisions.

---

## Phase 3 — Academic Records

Status

🟡 In Progress — implemented slices; SPR-014 closure pending

Sprints

* SPR-014 Records Lifecycle Management Engine — 🟡 IMPLEMENTED / DEFERRED (closure pending)
* SPR-015 Certificate & Transcript Engine — ✅ COMPLETED
* SPR-016 Identity & Verification Engine — 🟡 IN PROGRESS (Phases 1–5 implemented; Phase 6B deferred)

Objective

Create a permanent academic memory for every learner.

Note (reconciliation 2026-08-10)

SPR-009.5 Academic Policies Engine was **PLANNED / NOT STARTED** at the time of
the reconciliation — no implementation existed (only empty stub adapters). It is
now **COMPLETED** (2026-08-10, third session), lifting the architectural
prerequisite for Phase 3. SPR-014 Records Lifecycle Management is **PLANNED /
NOT STARTED** — no implementation, no migrations and no report exist. The next
implementation target is **SPR-014 Records Lifecycle Management Engine**.

---

## Phase 4 — School Portals

Status

Planned

Modules

* Parent Portal
* Student Portal
* Teacher Portal
* Secretary Portal
* Director Portal
* Accountant Portal

---

## Phase 5 — School Operations

Status

Planned

Modules

* Finance
* Library
* Inventory
* Hostel
* Transport
* Health
* Human Resources
* Payroll

---

## Phase 6 — Communication Platform

Status

Planned

Modules

* Notification Engine
* Email
* SMS
* WhatsApp
* Push Notifications
* School Announcements
* Calendar

---

## Phase 7 — Business Intelligence

Status

Planned

Modules

* Dashboards
* Analytics
* KPIs
* Predictive Reports
* Executive Reports

---

## Phase 8 — Platform Services

Status

Planned

Modules

* REST API
* Public API
* Webhooks
* Plugin SDK
* Marketplace
* Developer Portal

---

## Phase 9 — Enterprise Services

Status

Planned

Modules

* Cloud Archive — **SPR-017 Archive & Cloud Storage Platform** (Phase 9; architecture in ADR-024, Proposed)
* Backup
* Disaster Recovery
* Audit Center
* Billing
* Subscription Management
* White Label
* Licensing

---

# 5. Current Position

Current Phase

Phase 3 — Academic Records

Last Completed Sprint

SPR-015 — Certificate & Transcript Engine (COMPLETED)

ESP-002

Completed (hardening activity; committed in repository history)

Current Sprint

None — governance reconciliation only. SPR-016 remains IN PROGRESS at Phases 1–5
(ADR-022 governs); Phase 6B production issuance is DEFERRED.

Next Sprint

SPR-017 planning / ADR-024 architecture review (Phase 9 — Enterprise Services).
SPR-014 closure decision is an interlock.

SPR-014

IMPLEMENTED — VERIFIED; governance-DEFERRED pending closure decision

Overall Progress

Approximately 50%

Architecture Health

Excellent

Technical Debt

Low

---

# 6. Long-Term Objectives

The final NexusSchool platform will provide

* Academic Management
* Student Information System
* Administrative Management
* Financial Management
* Human Resource Management
* Digital Document Management
* Records Lifecycle Management
* Communication Platform
* Analytics Platform
* API Platform
* Enterprise Services

---

# 7. Guiding Rule

Every new feature must satisfy the following conditions.

* Respect Domain Boundaries.
* Respect Existing Contracts.
* Never duplicate business logic.
* Never violate immutable academic records.
* Never bypass the Communication Engine for official documents.
* Never bypass Academic Policies for academic rules.

---

# 8. Roadmap Maintenance

This roadmap is a living document.

Every completed sprint shall update

* Current Position
* Overall Progress
* Completed Sprints
* Planned Sprints

Major architectural changes require a new Architecture Decision Record (ADR).

---

# 9. Approval

This document is the official strategic roadmap of the NexusSchool platform.

Every future development must remain aligned with this roadmap.

## SPR-014 — Records Lifecycle Management (validation update, 2026-08-11)

**IMPLEMENTED — TARGETED GATES PASS**. Preserve the current thin metadata scope. Before marking complete, add and validate the narrow Report Card / Academic Communication contract-based registration or state-sync integration; do not duplicate their business logic.

## SPR-015 / SPR-016 — Governance reconciliation (2026-09-07)

**SPR-015 Certificate & Transcript Engine — COMPLETED** (implementation, report;
suites green at last run: CertificateAndTranscript 45 tests / 218 assertions,
AcademicCommunication 70 tests / 312 assertions).

**SPR-016 Identity & Verification Engine — DISCOVERY COMPLETED; ARCHITECTURE
DECISION / INITIALIZATION; NOT IMPLEMENTED.** Architecture formalized in **ADR-022**
(Accepted, 2026-09-07): public unauthenticated central verification endpoint; random
opaque verification token (≥144-bit, generated once, globally unique, SHA-256-hashed
at rest) owned by the AcademicDocument aggregate; default LEVEL 2 disclosure; formal
REVOKED/REPLACED semantics; QR = verification URL + opaque token (rendering delegated
to SPR-012); central + opaque-token tenant resolution; document number never a
secret. Phase 3 next implementation target is SPR-016, governed by ADR-022.

**SPR-014 Records Lifecycle Management remains DEFERRED** — validated implementation
awaiting the final closure decision; must not be considered COMPLETE before that
closure.

## SPR-017 — Phase 9 placement & ADR-024 (reconciliation 2026-10-10)

**SPR-017 Archive & Cloud Storage Platform is a Phase 9 — Enterprise Services
capability** (product-owner decision D1, 2026-10-10). It may be prioritized for
**planning** now, but it is **not** an ordinary Phase 3 Academic Records sprint;
any earlier roadmap/handoff reference implying otherwise is superseded by this note.

Architecture is proposed in **ADR-024** (Status: **Proposed**, 2026-10-10):
dedicated `App\Domains\Archive` bounded context consuming SPR-014
`RecordsLifecycleContract` and SPR-012 read contracts only; **local-first storage
with cloud deferred** (D2); retention **configurable per document type and per
school**, mechanism built now with values owned by product/legal and automatic
secure destruction disabled (D3); **`plans.storage_limit` stays the single quota
authority** (D4); downloadable school archive packages in architectural scope but
not mandatory for the first release (D5).

Interlock: SPR-014 remains **DEFERRED** — consumed by contract only, must not be
marked COMPLETE. No implementation was performed.

Discovery / decision board:
`SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md`.

### Note — §5 Current Position staleness (SPR-016)

The §5 "Current Sprint" field still names SPR-016 at **ARCHITECTURE DECISION /
INITIALIZATION; NOT IMPLEMENTED**, while later handoffs (`SESSION_HANDOFF.md`
2026-09-07 … 2026-09-08) show SPR-016 Phases 1–5 implemented. Current reality:
**SPR-016 IN PROGRESS (Phases 1–5 implemented, ADR-022 governs)**. This
reconciliation records that by reference without rewriting the phase body above.

## 2026-11-05 — Governance reconciliation (pre-SPR-017)

Full evidence and status taxonomy:
`GOVERNANCE_RECONCILIATION_2026-11-05.md`.

**Repository baseline:** branch `main`, HEAD `d42dbef` (2026-11-03), clean working
tree except two pre-existing untracked ZIP files. Older references to HEAD
`ac8acc2` and a large uncommitted refonte are historical.

**Sprint status:**

* **SPR-014** — implemented and targeted-tested; governance-**DEFERRED** pending
  the closure decision. It must not be marked COMPLETE.
* **SPR-015** — implemented and targeted-tested; reported COMPLETED. ADR-021 remains
  **Proposed** and requires architecture review.
* **SPR-016** — Phases 1–5 implemented and targeted-tested; Phase 6B production
  issuance **DEFERRED**; QR **DEFERRED**. No production caller of
  `issueForDocument()` exists.
* **SPR-017** — **DOCUMENTED ONLY / NOT IMPLEMENTED**. ADR-024 remains **Proposed**;
  no `App\Domains\Archive`, archive migration, route or dedicated config exists.
  ADR-023 remains absent/reserved.

**Quality evidence from this reconciliation (targeted only):** RecordsLifecycle unit
6 tests / 19 assertions; combined RecordsLifecycle feature + CertificateAndTranscript
unit/feature + IdentityAndVerification unit/feature 143 tests / 981 assertions.
Scoped PHPStan exit 0; scoped Pint passed. Full-repository Pest/PHPStan/Pint were not
re-executed.

**MariaDB `coefficients`:** the historical 65-character index defect is resolved in
code by commit `1696851`, which explicitly names the index
`coefficients_school_class_subject_unique` (40 characters). The migration was not
modified by this reconciliation.

**Next action:** convene the architecture review of ADR-024 (Proposed), recording the
SPR-014 closure decision as an interlock. Do not start SPR-017 implementation until
ADR-024 is accepted or revised.
