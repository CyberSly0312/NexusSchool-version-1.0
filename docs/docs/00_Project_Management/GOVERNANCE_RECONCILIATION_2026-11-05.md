# NexusSchool Enterprise Platform

# Governance Reconciliation — 05 November 2026

**Document ID:** GOV-REC-2026-11-05

**Status:** Completed (documentation-only)

**Owner:** Architecture Team

**Date:** 05 November 2026

---

## 1. Purpose and Scope

This report records a controlled governance/documentation reconciliation performed
against the actual repository state on 05 November 2026, before any SPR-017
implementation.

Scope:

- Verify SPR-001 through SPR-017 individually against source code, migrations,
  tests, routes, configuration and existing reports.
- Verify SPR-014 implementation and the existing Definition of Done criteria.
- Verify SPR-016 Phase 1–6 claims without describing deferred work as implemented.
- Verify SPR-017 documentation and confirm that no implementation exists.
- Reconcile ADR-021, ADR-022, ADR-023, ADR-024 and related ADRs.
- Investigate the MariaDB `coefficients_school_class_subject_unique` index issue.
- Record whether Pest / PHPStan / Pint can run, and only claim results actually
  produced.
- Update only the existing governance documents:
  `MASTER_ROADMAP.md`, `CURRENT_POSITION.md`, `PROJECT_STATE.md`, and
  `SESSION_HANDOFF.md`.
- Create this report.

Explicitly out of scope and not performed:

- No application code, migration, route, configuration, dependency or business
  logic was changed.
- No ADR was edited and no ADR status was promoted.
- No file was deleted, reset, restored, cleaned or overwritten outside the
  authorized governance documents and this new report.
- SPR-017 was not implemented.

---

## 2. Baseline and Preservation Controls

Recorded before any edit:

| Item | Value |
|---|---|
| Branch | `main` |
| HEAD | `d42dbef3e32ad6e7a7a4bc70c68f805be60da480` |
| HEAD subject | `correction du login` |
| HEAD date | 2026-11-03 12:28:46 +0000 |
| `git status --short` | `?? 2_NexusSchool.zip` and `?? NexusSchool.zip` only |
| Tracked modifications | None |
| Staged changes | None |

The working tree was preserved. The two ZIP files were pre-existing untracked
files and were not touched.

This baseline supersedes older report baselines that referenced HEAD `ac8acc2`
and a large uncommitted refonte (for example the SPR-017 discovery report). The
repository is now committed at `d42dbef`; those older statements are historical,
not current.

---

## 3. Verification Method and Status Taxonomy

Evidence used:

- `app/Domains/**` bounded contexts and application/infrastructure files.
- `database/migrations/**`, `routes/**`, `config/**`, `composer.json`.
- `tests/Unit/Domains/**` and `tests/Feature/Domains/**`.
- `docs/ADR/ADR-*.md`.
- Existing sprint reports under `docs/docs/00_Project_Management/` and
  `docs/00_Project_Management/`.
- Commands executed in this reconciliation (Section 10).

Status taxonomy used in this report:

| Label | Meaning |
|---|---|
| **VERIFIED IMPLEMENTED** | Implementation exists in the repository and was inspected in this reconciliation. |
| **VERIFIED TESTED** | Relevant tests were executed in this reconciliation and passed. Historical test claims are labelled separately. |
| **DOCUMENTED ONLY** | A report, ADR or decision artifact exists, but no matching implementation was verified. |
| **DEFERRED** | A recorded decision intentionally postpones implementation or wiring. |
| **NOT IMPLEMENTED** | No implementation exists and none is recorded as deferred work with a defined boundary. |
| **UNVERIFIED** | Implementation or test evidence exists but was not executed or fully inspected in this reconciliation. |

Historical reports are treated as claims, not as independent proof. Where a
historical test count is cited, it is marked as historical.

---

## 4. Sprint Verification Matrix

| Sprint | Repository evidence | Status in this reconciliation |
|---|---|---|
| SPR-001 Foundation | Platform foundation, shared kernel, authentication/authorization, contracts, events, testing infrastructure present. | **VERIFIED IMPLEMENTED**; test execution in this pass **UNVERIFIED**. No dedicated report in the current report folders. |
| SPR-002 Academic Framework | `app/Domains/Academic`, `AcademicClass`, `AcademicYear`, `AcademicTerm`, `Subject`, `Program`, `Department`; tests present. | **VERIFIED IMPLEMENTED**; test execution **UNVERIFIED**. |
| SPR-003 Student Domain | `app/Domains/Student`; unit/feature tests present. | **VERIFIED IMPLEMENTED**; test execution **UNVERIFIED**. |
| SPR-004 Enrollment Domain | `app/Domains/StudentEnrollment`; tests present; `SPR-004_Enrollment_Engine_Report.md` (Status Complete). | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**; test execution **UNVERIFIED**. |
| SPR-005 Guardian Domain | `app/Domains/Guardian`; tests present. | **VERIFIED IMPLEMENTED**; test execution **UNVERIFIED**. Sprint report is missing (pre-existing gap). |
| SPR-006 Staff Domain | `app/Domains/Staff`; tests present; `SPR-006_Staff_Engine_Report.md` (Status Complete). | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**. |
| SPR-007 Timetable Engine | `app/Domains/Timetable`; tests present. | **VERIFIED IMPLEMENTED**; test execution **UNVERIFIED**. Sprint report is missing (pre-existing gap). |
| SPR-008 Attendance Engine | `app/Domains/Attendance`; tests present. | **VERIFIED IMPLEMENTED**; test execution **UNVERIFIED**. Sprint report is missing (pre-existing gap). |
| SPR-009 Assessment Engine | `app/Domains/Assessment`; tests present. | **VERIFIED IMPLEMENTED**; test execution **UNVERIFIED**. Sprint report is missing (pre-existing gap). |
| SPR-009.5 Academic Policies Engine | `app/Domains/AcademicPolicy`; 5 test files; `SPR-009.5_Academic_Policies_Engine_Report.md`. | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**; test execution **UNVERIFIED**. |
| SPR-010 Gradebook Engine | `app/Domains/Gradebook`; tests present; `SPR-010_Gradebook_Engine_Report.md` (Status Complete). | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**. |
| SPR-011 Report Card Engine | `app/Domains/ReportCard`; tests present; `SPR-011_Report_Card_Engine_Report.md` (Status Complete). | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**. |
| SPR-012 Academic Communication Engine | `app/Domains/AcademicCommunication`; tests present; `SPR-012_Academic_Communication_Engine_Report.md` (Status Complete). | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**. |
| SPR-013 Class Council Engine | `app/Domains/ClassCouncil`; tests present; `SPR-013_Class_Council_Engine_Report.md` (Status Complete). | **VERIFIED IMPLEMENTED**; report **DOCUMENTED ONLY**. |
| SPR-014 Records Lifecycle Management | `app/Domains/RecordsLifecycle` (9 files), migration, factory, 3 test files, three source-domain integrations. | **VERIFIED IMPLEMENTED**, **VERIFIED TESTED** (targeted), governance **DEFERRED** pending closure. See Section 5. |
| SPR-015 Certificate & Transcript Engine | `app/Domains/CertificateAndTranscript` (50 files), 2 migrations, 9 test files, report. ADR-021 still `Proposed`. | **VERIFIED IMPLEMENTED**, **VERIFIED TESTED** (targeted). See Section 6. |
| SPR-016 Identity & Verification Engine | `app/Domains/IdentityAndVerification` (28 files), 2 migrations, 12 test files, public route and rate limiter. Phases 1–5 present; Phase 6B deferred. | Phases 1–5 **VERIFIED IMPLEMENTED** and **VERIFIED TESTED** (targeted); Phase 6B **DEFERRED**; QR **DEFERRED**. See Section 7. |
| SPR-017 Archive & Cloud Storage Platform | ADR-024 `Proposed` and decision-board report only. No `App\Domains\Archive`, no archive/retention route or dedicated config. | **DOCUMENTED ONLY**; **NOT IMPLEMENTED**. See Section 8. |

Note on SPR-001/002/003 and SPR-005/007/008/009: the missing sprint reports are a
pre-existing documentation gap already acknowledged in `SESSION_HANDOFF.md`. This
reconciliation does not invent their contents.

---

## 5. SPR-014 — Detailed Verification and Definition of Done

### 5.1 Implementation verified

- Bounded context: `app/Domains/RecordsLifecycle` (9 files).
- Migration: `database/migrations/2026_08_10_000000_create_record_lifecycles_table.php`.
- States: `registered`, `published`, `archived`.
- Actions/services: register, publish, archive, policy-protected service.
- Contract/adapter integrations exist in:
  - `ReportCard`
  - `AcademicCommunication`
  - `ClassCouncil`
- Source domains integrate through their own `RecordsLifecycleContract` and
  `RecordsLifecycleAdapter`; they do not touch the `RecordLifecycle` model
  directly.

### 5.2 Tests executed in this reconciliation

| Suite | Result |
|---|---|
| `tests/Unit/Domains/RecordsLifecycle` | PASS — 6 tests / 19 assertions |
| RecordsLifecycle feature suite (included in combined run) | PASS (combined result below) |
| Combined targeted run: RecordsLifecycle feature + CertificateAndTranscript unit/feature + IdentityAndVerification unit/feature | PASS — 143 tests / 981 assertions, 89.14 s |

PHPStan scoped to RecordsLifecycle, CertificateAndTranscript and
IdentityAndVerification: exit 0, `[OK] No errors`.
Pint scoped to the same domains and their tests: passed.

The full repository Pest suite was **not** re-executed in this reconciliation.
Historical reports cite 1,889 tests / 4,507 assertions; that figure is historical,
not a result of this pass.

### 5.3 Definition of Done assessment

The repository-wide Definition of Done appears in
`docs/07_Development/Development_Workflow.md` and
`docs/00_Project/Engineering_Principles.md`. Applied to SPR-014:

| Criterion | Assessment |
|---|---|
| Code implemented | Satisfied. |
| Tests written and passing | Satisfied for the targeted suites executed in this pass. |
| Business rules documented | Satisfied (state machine, invariants, provenance boundaries). |
| Security considered | Satisfied by tenant scoping, policy authorization and targeted tests. |
| Documentation updated | Satisfied (sprint report and governance notes). |
| Audit requirements | Satisfied at the domain level through lifecycle metadata/events; not separately re-audited here. |
| Performance implications | Not separately evidenced in this pass. |
| Code review approved | **Not evidenced** in the repository. |
| Successfully deployed to staging | **Not evidenced**; no staging deployment record exists. |
| Sprint closure decision | **Not recorded**; SPR-014 remains governance-deferred. |

**Conclusion:** SPR-014 is technically implemented and its targeted tests pass, but
the full Definition of Done is **not satisfied** because review approval, staging
deployment and the explicit closure decision are absent. SPR-014 must remain
**DEFERRED / not COMPLETE**. This is a governance conclusion, not a code defect.

**Closure recommendation:** If the project applies the global DoD literally,
SPR-014 can only be closed after (a) an architecture/product closure decision is
recorded and (b) the review/staging gate is evidenced or explicitly waived. The
code and targeted test evidence are otherwise ready for that decision.

---

## 6. SPR-015 — Certificate & Transcript Engine

Verified:

- `app/Domains/CertificateAndTranscript` contains 50 files.
- Two migrations exist and nine test files are present (5 unit, 4 feature).
- The engine composes certificates/transcripts from published evidence and delegates
  official document lifecycle to Academic Communication through an adapter.
- SPR-015 reaches the SPR-014 lifecycle indirectly through
  `AcademicCommunicationService::generateDocument()` →
  `GenerateDocumentAction` → `RecordsLifecycleContract`. Certificate and Transcript
  does not own a separate `RecordsLifecycleContract`.
- Targeted CertificateAndTranscript tests were included in the 143-test / 981-assertion
  combined run and passed.

Documentation discrepancy:

- `SPR-015_Certificate_Transcript_Engine_Report.md` records the sprint as implemented.
- `ADR-021` is still marked **Proposed**, although the sprint it governs is
  implemented.
- This reconciliation does **not** change ADR-021 to `Accepted`; that requires an
  architecture review decision. The discrepancy is recorded for that review.

Status: **VERIFIED IMPLEMENTED**, **VERIFIED TESTED** (targeted); ADR-021 status
remains an open governance inconsistency.

---

## 7. SPR-016 — Identity & Verification Engine

### 7.1 Phases 1–5

Verified in source and tests:

- `app/Domains/IdentityAndVerification` contains 28 files.
- Two migrations exist; twelve test files are present.
- Public route: `GET /v/{token}`, named `verification.public`.
- Rate limiter `verification`: 60 requests/minute per IP and 10 requests/minute per
  token digest.
- Tokens are stored as SHA-256 digests; plaintext tokens are not persisted.
- Revocation and replacement semantics are implemented (`Active → Revoked`,
  `Active → Replaced`).
- Verification URL capability exists (`VerificationUrl`,
  `verificationUrlForToken`).
- No QR dependency exists in `composer.json`; the QR consumer is not implemented.
- Targeted IdentityAndVerification tests were included in the 143-test / 981-assertion
  combined run and passed.

### 7.2 Phase 6B — production issuance

- `SPR-016_Phase6B_Verification_Issuance_Report.md` records verdict
  **D. DEFERRED — ISSUANCE NOT YET JUSTIFIED**.
- `issueForDocument()` exists only inside `VerificationIdentityService`.
- No caller outside `IdentityAndVerification` invokes it; no production issuance seam
  is wired into the Academic Communication lifecycle.
- Phase 6B therefore remains **DEFERRED**, not implemented.

Status: Phases 1–5 **VERIFIED IMPLEMENTED** and **VERIFIED TESTED** (targeted);
Phase 6B **DEFERRED**; QR **DEFERRED**; production issuance **NOT IMPLEMENTED**.
SPR-016 as a whole is **IN PROGRESS**, not complete.

---

## 8. SPR-017 — Archive & Cloud Storage Platform

Verified:

- `docs/ADR/ADR-024.md` exists with **Status: Proposed** (2026-10-10).
- Product decisions D1–D5 are recorded:
  - D1: Phase 9 Enterprise Services scope.
  - D2: local-first storage; cloud deferred.
  - D3: retention configurable per document type and per school; automatic
    destruction disabled.
  - D4: `plans.storage_limit` remains the single quota authority.
  - D5: downloadable school archive packages in architectural scope, not mandatory
    for first release.
- `SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md` exists.
- There is **no** `App\Domains\Archive`, no archive/retention bounded context, no
  archive route, and no dedicated archive/cloud configuration.
- The pre-existing `backups` table/migration and `config/system.php` backup settings
  belong to the legacy System area. They are **not** the SPR-017 Archive domain and
  do not implement ADR-024.
- ADR-023 does not exist. It remains reserved for SPR-016 Phase 6B and was not
  created, renamed or repurposed.

Status: SPR-017 is **DOCUMENTED ONLY** / **NOT IMPLEMENTED**. ADR-024 remains
**Proposed** and was not marked `Accepted`.

---

## 9. ADR Reconciliation

| ADR | Current status | Reconciliation finding |
|---|---|---|
| ADR-021 Certificate & Transcript | **Proposed** | SPR-015 is implemented and tested, but the governing ADR was never promoted. Do not silently mark it Accepted; architecture review must decide. |
| ADR-022 Identity & Verification | **Accepted** (2026-09-07) | Matches SPR-016 Phases 1–5. Phase 6B deferral is consistent with ADR-022; no ADR-023 was authored. |
| ADR-023 | **Absent** | Reserved by the SPR-016 Phase 6B deferred brief. Not created in this reconciliation. |
| ADR-024 Archive & Cloud Storage | **Proposed** (2026-10-10) | Documents SPR-017 architecture and D1–D5. Architecture proposals and open decisions still require review. Not accepted. |
| ADR-001 | 0 bytes | Empty file; pre-existing governance defect. No content invented. |
| ADR-002–005, 007–011, 014–020 | 0 bytes | Empty files; pre-existing governance debt. No content invented. |
| ADR-006, ADR-012, ADR-013 | Accepted, with content | Content present and consistent with completed domains; no change required in this pass. |

No ADR file was modified by this reconciliation. Status promotion, if any, belongs
to an explicit architecture review.

---

## 10. MariaDB `coefficients` Index Investigation and Quality Gates

### 10.1 `coefficients` index

Historical records (ADR-024, SPR-016 Phase 6B §14, SPR-017 report §10 and the
CHANGELOG) cite a MariaDB failure caused by a 65-character auto-generated unique
index name on `coefficients`.

Current repository evidence:

- `database/migrations/2026_08_01_000011_create_coefficients_table.php` explicitly
  names the index `coefficients_school_class_subject_unique` (40 characters).
- Commit `1696851` ("Fix routing security and domain-based tenancy", 2026-10-15)
  changed the migration from an unnamed unique constraint to the explicit 40-character
  name.
- The current code therefore does not reproduce the 65-character identifier defect.

Conclusion: the defect is **resolved / not applicable to the current HEAD**. The
migration was not modified by this reconciliation. Historical reports were left
unchanged and their statements should be read as historical.

### 10.2 Quality gates executed in this reconciliation

| Gate | Command scope | Result |
|---|---|---|
| Pest | `tests/Unit/Domains/RecordsLifecycle` | PASS — 6 tests / 19 assertions |
| Pest | RecordsLifecycle feature + CertificateAndTranscript unit/feature + IdentityAndVerification unit/feature | PASS — 143 tests / 981 assertions, 89.14 s |
| PHPStan | `app/Domains/RecordsLifecycle`, `CertificateAndTranscript`, `IdentityAndVerification` | exit 0, `[OK] No errors` |
| Pint | Same three domains and their test directories | passed |

Environment: PHP 8.2.12, Composer 2.10.1, `pdo_sqlite` and `pdo_mysql` available.
`phpunit.xml` forces SQLite `:memory:` for tests; the `.env` MySQL database is not
used by the test run.

Not executed in this pass: the full repository Pest suite, full-repository PHPStan,
and full-repository Pint. No claim is made that they passed in this reconciliation.

---

## 11. Documentation Changes, Unresolved Items and Recommendation

### 11.1 Governance documents updated

Only these existing documents were updated, preserving their existing structure and
appending dated reconciliation notes rather than erasing history:

- `docs/docs/00_Project_Management/MASTER_ROADMAP.md`
- `docs/docs/00_Project_Management/CURRENT_POSITION.md`
- `docs/docs/00_Project_Management/PROJECT_STATE.md`
- `docs/docs/SESSION_HANDOFF.md`

This report is the only new file:
`docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md`.

### 11.2 Unresolved items

1. **SPR-014 closure:** implemented and targeted-tested, still governance-deferred;
   review/staging/closure evidence is missing.
2. **ADR-021 status:** remains `Proposed` although SPR-015 is implemented.
   Architecture review must decide.
3. **ADR-024 status:** remains `Proposed`; SPR-017 cannot be implemented before an
   explicit architecture decision. Open decisions (cloud provider, residency, key
   management, legal retention, destruction safeguards, metering/billing, package
   format, tenant isolation/DR, SLO/cost) remain open.
4. **ADR-023:** absent/reserved; production verification issuance remains deferred
   pending a real consumer and a future ADR.
5. **SPR-016 Phase 6B:** no production issuance wiring; `issueForDocument()` has no
   production caller.
6. **QR:** not implemented; no QR dependency; `qr_code_enabled` remains unused.
7. **Empty ADRs:** ADR-001 and many related ADRs are 0 bytes.
8. **Missing sprint reports:** SPR-001, 002, 003, 005, 007, 008 and 009 have no
   dedicated report in the current report folders.
9. **Historical report drift:** older reports still cite HEAD `ac8acc2` and a large
   uncommitted refonte. The current repository is clean at `d42dbef`; the historical
   reports were not rewritten.
10. **Stale MariaDB blocker references:** resolved in code by `1696851`; historical
    reports were not rewritten.
11. **Full quality-gate coverage:** only targeted Pest, scoped PHPStan and scoped Pint
    were executed in this pass.

### 11.3 Definition of Done summary

- SPR-014: technical implementation and targeted tests are satisfied; full DoD is
  **not** satisfied because review approval, staging deployment and explicit closure
  are not evidenced. Do not mark complete.
- SPR-015: implementation and targeted tests are satisfied; ADR-021 governance status
  remains open.
- SPR-016: Phases 1–5 implemented and targeted-tested; Phase 6B and QR are deferred;
  the sprint is not complete.
- SPR-017: documentation only; not implemented.

### 11.4 Recommended next action

**Convene the architecture review that dispositions ADR-024 (Proposed), with the
SPR-014 closure decision recorded as its interlock. Do not start SPR-017
implementation until ADR-024 is explicitly accepted or revised.**

This is the exact next action because SPR-017 is the stated implementation target and
its governing architecture is still `Proposed`; separately, SPR-014 remains an
interlock that must not be marked complete by implication while SPR-017 consumes its
contract.
