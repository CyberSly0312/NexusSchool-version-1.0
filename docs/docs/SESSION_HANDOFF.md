# NexusSchool Enterprise Platform

# Session Handoff

**Document ID:** OPS-001

**Version:** 1.0.0

**Status:** Active

**Owner:** Architecture Team

**Last Updated:** 10 October 2026

---

# 1. Purpose

This document serves as the official handoff point between development sessions.

Its objective is to preserve complete project continuity regardless of:

* AI assistant used
* Developer involved
* Time elapsed between sessions
* Sprint currently in progress

Every development session SHALL begin by reviewing this document.

Every completed session SHALL update this document before ending.

---

# 2. Current Development Status

Platform

NexusSchool Enterprise Platform

Current Development Phase

Phase 3 — Academic Records

Current Sprint

SPR-014 — Records Lifecycle Management (In Progress — Class Council Integration Complete)

Current Status

✅ SPR-009.5 Academic Policies Engine implemented — architectural prerequisite
(AD §6) lifted. ✅ SPR-014 first slice implemented (metadata envelope, state
machine, actions, policy, 12 tests / 38 assertions, PHPStan + Pint clean).
✅ Report Card integration complete (RecordsLifecycleContract + Adapter, 5 integration tests).
✅ Academic Communication integration complete (RecordsLifecycleContract + Adapter, 5 integration tests).
✅ Class Council integration complete (RecordsLifecycleContract + Adapter, 5 integration tests).
All three domains register lifecycle metadata through the contract/adapter pattern.
27 tests / 97 assertions total for the RecordsLifecycle integration surface.
Full suite: 1,889 tests / 4,507 assertions.

---

# 3. Current Sprint Objective

SPR-009.5 — Academic Policies Engine — is **Complete** (2026-08-10, third
session): the exclusive origin of academic rules (ARCHITECTURE_DECISIONS §6) is
now implemented as bounded context `App\Domains\AcademicPolicy`, and the Report
Card / Academic Communication stub adapters read real policies from the engine.

SPR-013 (Class Council Engine) and ESP-002 (Repository Hardening) remain
**Complete**; the Governance Reconciliation is **Complete**.

Academic Intelligence (Phase 2) is finished: Gradebook calculates, Class Council
validates, Report Card composes and Academic Communication publishes — all wired
through contracts. Academic Policies now feeds those contracts.

The next implementation target is **SPR-014 — Records Lifecycle Management
Engine** (Phase 3 — Academic Records), which no longer waits on a policy
decision. An optional follow-up is the policy-driven Gradebook / Class Council /
Assessment integration tracked as deferred work in the SPR-009.5 report.

No Business Engine may override council decisions outside the Class Council
layer.

---

# 4. Recently Completed Work

The following major milestones have been completed.

## Platform Foundation

* Multi-Tenant Architecture
* Authentication
* Authorization
* Shared Kernel
* Architecture Governance

---

## Academic Foundation

* Academic Framework
* Student Domain
* Enrollment Domain
* Guardian Domain
* Staff Domain
* Timetable Engine
* Attendance Engine
* Assessment Engine

Note (2026-08-10): Academic Policies Engine (SPR-009.5) was **not** completed at
reconciliation time — it was Planned / Not Started. It has since been
**implemented** (2026-08-10, third session); see the dedicated block below.

---

## Academic Intelligence (Phase 2 — Complete)

* Gradebook Engine (SPR-010)
* Report Card Engine (SPR-011)
* Academic Communication Engine (SPR-012)
* Class Council Engine (SPR-013)

---

## Engineering Governance

* PHPStan + Larastan
* Pest Test Suite
* Laravel Pint
* Architecture Tests
* ADR Collection
* Governance Documentation

---

## Repository Hardening (ESP-002 — Complete)

* `SchoolOwned` contract (`getSchoolId(): string`) — explicit tenant ownership.
* 6 tenant-scoped aggregates implement `SchoolOwned` (Attendance x5,
  CouncilSession).
* Attendance / Class Council authorization traits typed against `SchoolOwned`.
* UTF-8 remediation of 11 factory files.
* PHPStan exit 0 (baseline unchanged); Pest 1819 passed / 4281 assertions;
  Pint clean. No commit created; working tree uncommitted.

---

## Academic Policies Engine (SPR-009.5 — Complete)

* New bounded context `App\Domains\AcademicPolicy` — exclusive origin of
  academic rules (ARCHITECTURE_DECISIONS §6).
* Typed policy store (`string|integer|float|boolean|json`), scopes
  `school|academic_year|subject`, unique `(school_id, key, scope)`, soft
  deletes, ULID observer, `academic_policy.*` permissions, audit on every
  lifecycle event.
* CRUD actions + `AcademicPolicyService`, repository + query contract,
  `SchoolOwned`-typed authorization trait.
* Filament `AcademicPolicyResource` (Académique group) registered in the
  School Admin panel.
* Report Card `forPeriod` + Academic Communication `policies`/`policy` stub
  adapters rewired to the engine — zero regression on undefined policies.
* Full suite 1859 passed / 4394 assertions; PHPStan exit 0 (baseline
  unchanged); Pint clean on all touched files (419 pre-existing repo failures
  disclosed, untouched). No commit created.

---

# 5. Work In Progress

SPR-014 Records Lifecycle Management — first slice is implemented and validated
(metadata envelope, state machine, 3 actions, policy, factory, 12 tests / 38
assertions, PHPStan exit 0, Pint clean). The source-domain integration seams
are not wired: neither Report Card nor Academic Communication currently
registers lifecycle metadata through the RecordsLifecycle envelope. The working
tree also carries a large **pre-existing uncommitted refonte**, the ESP-002
changes, and the SPR-009.5 additions (~300+ files vs HEAD `ac8acc2`) —
**UNCOMMITTED REFACTORING / UNCLASSIFIED** until a separate forensic review.

---

# 6. Immediate Next Sprint

SPR-014

Records Lifecycle Management Engine

Status

In Progress — first slice implemented and validated.

Next Increment

Wire the source-domain integration seam: when Report Card generates / publishes /
archives a run, register a lifecycle record via `RecordLifecycleService`. When
Academic Communication publishes / archives a document, register a lifecycle
record. This requires a new `RecordsLifecycleContract` in the ReportCard and
AcademicCommunication domain contract layers, adapters in the infrastructure
layer, and event listeners in the service providers.

---

# 7. Upcoming Major Sprints

SPR-014

Records Lifecycle Management (must wait for the SPR-009.5 dependency decision)

SPR-015

Certificate & Transcript Engine

SPR-016

Identity & Verification Engine

SPR-017

Archive & Cloud Storage Platform — **Phase 9 Enterprise Services** (ADR-024
Proposed, 2026-10-10; see the 2026-10-10 handoff note below)

---

# 8. Architectural Decisions Made

The following decisions are considered immutable.

* Academic rules belong exclusively to Academic Policies.
* Grade calculations belong exclusively to Gradebook.
* Academic validation belongs exclusively to Class Council.
* Business Engines never generate PDFs.
* Academic Communication Engine is the single publication layer.
* Published documents are immutable.
* Historical records are never modified.
* Corrections always create new versions.

---

# 9. Active Design Decisions

Current architectural discussions include

* Long-term archive lifecycle
* Cloud archive service
* Downloadable school archive packages
* Storage quotas
* Paid archive extensions
* Parent visibility policies
* Student visibility policies
* Publication approval workflow
* Academic council report publication
* Official meeting minutes publication

---

# 10. Pending Decisions

The following topics require future architectural review.

* Cloud object storage provider
* Email provider
* SMS provider
* Push notification provider
* Digital signature implementation
* Government verification services
* Public document verification portal

---

# 11. Files Modified During Current Sprint

## SPR-013 (Complete) — Class Council Engine

* app/Domains/ClassCouncil/*
* app/Domains/ReportCard/Infrastructure/Contracts/ClassCouncilAdapter.php
* app/Domains/AcademicCommunication/Infrastructure/Contracts/ClassCouncilAdapter.php
* config/nexus-class-council.php
* database/migrations/2026_08_06_000015..000017
* database/factories/ClassCouncil/*
* tests/Unit/Domains/ClassCouncil/*
* tests/Feature/Domains/ClassCouncil/*
* docs/ADR/ADR-013.md
* docs/00_Project_Management/SPR-013_Class_Council_Engine_Report.md
* phpstan-baseline.neon

## ESP-002 (Complete) — Repository Hardening

* app/Contracts/SchoolOwned.php (new)
* app/Traits/HasSchoolId.php
* app/Domains/Attendance/Domain/Traits/HandlesAttendanceAuthorization.php
* app/Domains/ClassCouncil/Domain/Traits/HandlesClassCouncilAuthorization.php
* app/Domains/Attendance/Infrastructure/Models/{AttendanceAlert,AttendanceCorrection,AttendanceRecord,AttendanceSession,AttendanceSummary}.php
* app/Domains/ClassCouncil/Infrastructure/Models/CouncilSession.php
* database/factories/* (11 files — UTF-8 remediation)
* docs/docs/00_Project_Management/ESP-002_Repository_Hardening_Report.md (new)
* docs/docs/00_Project_Management/CURRENT_POSITION.md
* docs/docs/00_Project_Management/PROJECT_STATE.md
* docs/docs/00_Project_Management/CHANGELOG.md
* docs/docs/99_Project_Journal/2026/08-August/2026-08-10.md (new)

## Governance Reconciliation (2026-08-10)

* docs/docs/00_Project_Management/MASTER_ROADMAP.md
* docs/docs/00_Project_Management/CURRENT_POSITION.md
* docs/docs/00_Project_Management/PROJECT_STATE.md
* docs/docs/00_Project_Management/CHANGELOG.md
* docs/docs/00_Project_Management/SPR-009.5_Academic_Policies_Engine_Report.md (new)
* docs/docs/SESSION_HANDOFF.md
* docs/docs/99_Project_Journal/2026/08-August/2026-08-10.md (second + third sessions)

## SPR-014 (In Progress — First Slice Implemented)

* app/Domains/RecordsLifecycle/* (bounded context: Domain, Application, Infrastructure, ServiceProvider)
* database/migrations/2026_08_10_000000_create_record_lifecycles_table.php
* database/factories/RecordsLifecycle/RecordLifecycleFactory.php
* tests/Unit/Domains/RecordsLifecycle/RecordLifecycleStateTest.php
* tests/Unit/Domains/RecordsLifecycle/RecordLifecycleArchitectureTest.php
* tests/Feature/Domains/RecordsLifecycle/RecordLifecycleFeatureTest.php
* app/Domains/Permission/Enums/PermissionGroup.php (RecordsLifecycle case added)
* app/Domains/Permission/Services/PermissionService.php (records_lifecycle actions added)
* tests/Pest.php (RecordsLifecycle test directories registered)
* bootstrap/providers.php (RecordsLifecycleServiceProvider registered)
* docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md

**Note:** the working tree still carries ~300 files changed against `HEAD`
(`ac8acc2`) from the pre-existing refonte, ESP-002 and SPR-009.5 — **UNCOMMITTED
REFACTORING / UNCLASSIFIED**, outside this sprint. No commit was created.

---

# 12. Quality Status

Current Quality Gates

✅ Pest

✅ PHPStan

✅ Larastan

✅ Laravel Pint

✅ Architecture Tests

✅ Contract Tests

✅ Tenant Isolation

---

# 13. Known Issues

* Current legacy issues inherited from previous architecture remain outside the
  sprint scope (legacy Filament tests, legacy enum labels, historical test
  harness — tracked in TECHNICAL_DEBT).
* Missing sprint reports for completed engines: SPR-005 (Guardian), SPR-007
  (Timetable), SPR-008 (Attendance), SPR-009 (Assessment). SPR-009.5 now has a
  report (SPR-009.5_Academic_Policies_Engine_Report.md).
* Repository is uncommitted (~300 files vs HEAD `ac8acc2`): pre-existing refonte
  + ESP-002 + SPR-009.5 changes are not anchored to a commit.
* Full-repository Pint reports 419 pre-existing failing files (legacy
  `app/Models`, `app/Filament`, Plugin/Assessment legacy pages, seeders,
  migrations, legacy tests) — untouched, tracked in TECHNICAL_DEBT. All
  SPR-009.5-touched files are Pint-clean.

No regression introduced by current work.

---

# 14. Session Resume Checklist

Before continuing development

* Read MASTER_ROADMAP.md
* Read CURRENT_POSITION.md
* Read PROJECT_STATE.md
* Read this Session Handoff
* Review current Sprint Report
* Review current ADRs
* Verify Quality Dashboard

---

# 15. Session Closing Checklist

Before ending a working session

* Update Sprint Report
* Update Current Position
* Update Project State
* Update Changelog
* Update this Session Handoff
* Run Pest
* Run PHPStan
* Run Laravel Pint
* Commit changes

---

# 16. OpenCode Prompt Continuity

Every implementation prompt shall include

* Current Sprint
* Sprint Scope
* Architectural Constraints
* Contracts to respect
* Quality Gates
* Documentation requirements

No implementation may continue without explicit sprint context.

---

# 17. Next Session Starting Point

Resume development from the current sprint.

Do not redesign completed architecture.

Do not duplicate existing business logic.

Respect all Architecture Decisions and Domain Contracts.

Continue implementation according to the Master Roadmap.

---

# 18. Final Statement

This document is the operational memory of the NexusSchool project.

Its purpose is to ensure that every development session begins with complete architectural awareness and ends with sufficient context for the next contributor to continue seamlessly.

Project continuity is considered a core engineering requirement of NexusSchool.

## 2026-08-11 — SPR-014 validation handoff

SPR-014 is **IMPLEMENTED — TARGETED GATES PASS**. The full `php artisan test` suite passed (1,870 tests, 4,429 assertions, 771.88 s); targeted lifecycle/feature/architecture tests, PHPStan, and Pint also passed. Do not mark it COMPLETE until a narrow source-domain seam actually registers/synchronizes lifecycle metadata from Report Card and/or Academic Communication. No code changes were made during validation.

## 2026-08-19 — SPR-014 stabilization handoff

SPR-014 first slice stabilized: 12 tests / 38 assertions (up from 8 / 31). `RecordLifecycleFactory` added. Full state-transition matrix now covered. Report Card and Academic Communication integration seams wired (22 tests / 77 assertions). The next increment is to wire the Class Council domain to register lifecycle metadata through `RecordsLifecycleContract` adapter.

## 2026-08-20 — SPR-014 Class Council integration handoff

SPR-014 Class Council integration complete: 5 integration tests / 20 assertions added. `RecordsLifecycleContract` and `RecordsLifecycleAdapter` created in ClassCouncil domain. `CreateCouncilSessionAction`, `PublishCouncilSessionAction`, and `ArchiveCouncilSessionAction` wired to register/publish/archive lifecycle metadata. Architecture tests updated in all three domains to include `RecordsLifecycleContract`. Full suite: 1,889 tests / 4,507 assertions. PHPStan 0 errors. Pint clean on all touched files. All three source domains (Report Card, Academic Communication, Class Council) now register lifecycle metadata through the contract/adapter pattern.

## 2026-09-07 — SPR-015 COMPLETED; SPR-016 architecture initialization handoff

**SPR-015 Certificate & Transcript Engine — COMPLETED** (implementation + report).
Suites green at last run: CertificateAndTranscript 45 tests / 218 assertions;
AcademicCommunication 70 tests / 312 assertions. Working tree remains uncommitted.

**SPR-016 Identity & Verification Engine — DISCOVERY COMPLETED; ARCHITECTURE
DECISION / INITIALIZATION; NOT IMPLEMENTED.** The Phase 1 discovery verdict was
**NEEDS ARCHITECTURAL DECISION**; that decision is now formalized in **ADR-022**
(Accepted, 2026-09-07): public unauthenticated central verification endpoint;
random opaque verification token (≥144-bit, generated once, globally unique,
SHA-256-hashed at rest) owned by the AcademicDocument aggregate; default LEVEL 2
disclosure (number, type, status, issuance date, holder name — never grades/personal
data/internal ids); formal REVOKED (capability + reason + audit, terminal) and
REPLACED semantics; QR payload = verification URL + opaque token only, rendering
delegated to the SPR-012 substrate; central + opaque-token tenant resolution (no
school-code / document-number probing); document number never a secret.

**SPR-014 Records Lifecycle Management remains DEFERRED** — implementation is
validated (all three source domain integrations complete) but awaits the final
closure decision; it must not be treated as COMPLETE.

**ADR-001 is an empty file (0 bytes).** Governance follow-up: restore its content
or formally deprecate it — do not silently invent content. ADR-022 does not depend
on ADR-001.

Governance documents updated 2026-09-07 (MASTER_ROADMAP, CURRENT_POSITION,
PROJECT_STATE, this handoff). No SPR-016 code exists anywhere in the tree.

Next implementation phase: **SPR-016 implementation governed by ADR-022**
(constraints C1–C9 and invariants I1–I13/P/T/L).

## 2026-09-07 — SPR-016 Phase 4 (Revocation & Replacement) handoff

**SPR-016 Phase 4 — Revocation & Replacement: REVOCATION & REPLACEMENT COMPLETE.**
Additive, terminal-state invalidation for verification identities (Active → Revoked
/ Active → Replaced), ACME-guarded (gate order: focused tests → full suite → PHPStan
2G → Pint scoped). Full suite passed 2,008 tests / 5,157 assertions (up from
1,986 / 5,057); VerificationLifecycleTest 19 tests / 51 assertions;
VerificationStatusTest transition matrix expanded; PHPStan 0 errors; Pint clean on
all touched files; worktree diff vs Phase 4 baseline shows no pre-existing file
changed beyond the authorized `PermissionService` document.revoke action.

No new routes (public verdict endpoint unchanged; reason/successor never public).
Old token stays valid forever; verdict text changes only. No SoftDeletes, no token
rotation, no migration on MariaDB (sqlite-validated only). Audit via
`App\Models\AuditLog` through domain events + `IdentityAndVerificationAuditLogger`.
SPR-016 remains **IN PROGRESS** (Phases 1–3 earlier: white-list disclosure,
central endpoint, opaque token storage). Working tree remains uncommitted.

**Blockers carried forward:** MariaDB `migrate` still fails on the pre-existing
`academic_documents` context constraint from SPR-009.5 (untouched, no rollback).
ADR-001 empty (0 bytes). SPR-014 stays DEFERRED (do not mark COMPLETE).

## 2026-09-08 — SPR-016 Phase 5 (Verification URL & QR Integration) handoff

**SPR-016 Phase 5 — Verification URL & QR Integration: VERIFICATION URL COMPLETE
— QR CONSUMER DEFERRED.** Canonical verification URL capability implemented
without moving QR rendering into SPR-016. URL payload `{base}/v/{token}`, a pure
deterministic function of the held plaintext token (ADR-022 Decision 5): base =
`config('app.url')` (APP_URL), path = existing named route `verification.public`
(`route(..., absolute: false)`), so it never inherits a tenant/school host. New
`Domain/ValueObjects/VerificationUrl` (pure composition + base64url token
validation, `InvalidArgumentException` on non-conforming tokens) and
`VerificationIdentityService::verificationUrlForToken(string $token)` on the
real invocation path. URL stable by construction across regeneration, versioning,
archival, revocation and replacement (token immutable per identity; only the
public verdict changes) — proven by `VerificationUrlStabilityTest` (6 tests /
31 assertions) using the real `RegenerateDocumentAction`, `ArchiveDocumentAction`,
`revoke`/`replaceWith` paths.

Security: plaintext token remains never-persisted/never-logged (ADR-022 I11 —
identity stores only the SHA-256 digest); the identity record itself can never
yield the URL; URL carries only the token (no document/school/student/PII/digest);
validated by `VerificationUrlServiceTest` (4/16) and value-object tests (7/19).
Architecture gates: new Domain/Application files introduce no foreign-domain
imports; new architecture test asserts I&V renders NO QR, ships NO QR dependency
and has no `Infrastructure/Renderers`. SPR-012 consumer NOT wired: no binding in
`GenerateDocumentAction`/`HtmlDocumentRenderer`, `qrCodeEnabled` stays an unused
flag, no QR library in `composer.json` — QR consumer integration DEFERRED (SPR-012
has no token-bearing consumer boundary; post-issuance reconstruction would violate
I11 and require a new ADR). No route, no migration, no config, no composer change.
Gates: focused I&V 92/708, full suite passed with 2 isolated pre-existing
faker-collision flakes (`ApiClientServiceTest`, `AcademicYearServiceTest` — both
pass alone, unrelated to SPR-016), PHPStan 0 errors, Pint clean on all touched
files, worktree status byte-identical to the Phase 5 baseline (0 new lines; all
Phase 5 files live under already-untracked I&V directories). SPR-016 remains
**IN PROGRESS**. Working tree remains uncommitted.

## 2026-10-10 — SPR-017 Archive & Cloud Storage Platform: ADR-024 proposed handoff

**SPR-017 Archive & Cloud Storage Platform — Phase 9 Enterprise Services
capability** (product-owner decision D1); may be prioritized for planning now, but
is **not** an ordinary Phase 3 Academic Records sprint. Discovery / architecture
decision board completed:
`SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md` (verdict: NEEDS
PRODUCT-OWNER DECISIONS). Architecture now proposed in **ADR-024** (Status:
**Proposed**, 2026-10-10) — awaiting NexusSchool Architecture Team review. **No
implementation exists; no code, migration, route, config, dependency, test or UI
was changed by this session (documentation/decision artifacts only).**

**Approved product decisions (2026-10-10):**

* **D1 — scope/phase:** Phase 9 — Enterprise Services; planning may start now,
  strategic classification stays Phase 9.
* **D2 — storage:** local-first; cloud storage and every cloud decision deferred
  (no provider/region/credentials/SDK/config/infra).
* **D3 — retention:** configurable per document type and per school; mechanism now,
  values owned by product/legal; no statutory durations invented; permanent
  preservation vs secure destruction distinct; automatic destruction disabled.
* **D4 — quotas:** `plans.storage_limit` remains the single authoritative plan
  storage limit; SPR-017 creates no second quota authority.
* **D5 — packages:** downloadable school archive packages in architectural scope,
  not mandatory for the first release.

**Boundary:** new `App\Domains\Archive` consuming SPR-014 `RecordsLifecycleContract`
and SPR-012 read contracts only; never creates/owns business records; never replaces
Records Lifecycle; legacy `GeneratedDocument` / `DocumentTemplate` /
`DocumentGenerationService` / `app/Domains/Document` / DocumentCenter /
`pdf_enable_qr_code` are excluded.

**Open decisions (see ADR-024 "Open Decisions"):** cloud provider/region/residency;
cloud key management/rotation; legal retention periods by document type/jurisdiction;
secure-destruction authorization/safeguards; storage metering + paid-extension
pricing/billing; package format/contents/size/requesters; cloud tenant isolation +
DR; any SLO/availability/cost baseline. These block cloud activation / production /
packages — **not** ADR acceptance.

**SPR-014 remains DEFERRED** — contract-only consumption; must not be marked
COMPLETE. **ADR-023 remains reserved** (SPR-016 Phase 6B) and was **not** created,
renamed or repurposed; SPR-017 uses **ADR-024**.

The pre-existing, unrelated MariaDB `coefficients` index identifier-length defect
(65 chars > 64) remains tracked separately and was not touched. The working tree
continues to carry the large pre-existing uncommitted refonte; this session added
only `ADR-024.md` and the documented governance notes above.
