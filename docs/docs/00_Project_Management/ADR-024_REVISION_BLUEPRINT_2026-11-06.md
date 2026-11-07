# NexusSchool Enterprise Platform

# ADR-024 Revision Blueprint & Decision Register - 06 November 2026

**Document ID:** ADR-BP-2026-11-06

**Status:** Completed (documentation-only) - revision blueprint, not an approval

**Owner:** Architecture Team

**Prepared by:** Architecture blueprint agent, on behalf of the NexusSchool Architecture Team

**Date:** 06 November 2026

**Subjects:** ADR-024 (Archive & Cloud Storage Platform Architecture), SPR-014 (Records Lifecycle Management Engine), SPR-017 (Archive & Cloud Storage Platform)

**Primary input:** `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md` (review outcome: **REVISION REQUIRED** for ADR-024; **REQUEST ADDITIONAL EVIDENCE** for SPR-014)

**Baseline:** branch `main`, HEAD `d42dbef3e32ad6e7a7a4bc70c68f805be60da480`, working tree carrying the pre-existing governance reconciliation changes described in section 2.1.

---

## 1. Executive Summary

### 1.1 Purpose

This blueprint converts the findings of the 06 November 2026 ADR-024 architecture review into:

- explicit architectural decisions that a revised ADR-024 must record;
- assigned decision ownership (roles, not named individuals);
- proposed technical directions supported by repository evidence;
- a four-part dependency map;
- testable acceptance criteria for a future ADR-024 revision;
- readiness conditions for SPR-017.

It is **not** an ADR, an approval, a sprint closure, or an implementation plan. It does not create code, migrations, contracts, routes, configuration, dependencies or business logic.

### 1.2 Outcome in one paragraph

ADR-024's direction is sound and should be preserved: a thin `App\Domains\Archive` bounded context, local-first storage, contract-only consumption, one lifecycle authority (SPR-014), no invented legal retention values, backup/archive separation, and a gated cloud future. ADR-024 is **not acceptance-ready** because the contract surface, quota mechanism, retention structure, audit integration, storage access design and encryption posture it relies on are either missing or stated at a level that would force implementation to invent architecture. This blueprint proposes the minimum decisions and evidence needed to make the revision acceptance-ready, while leaving all Product, Security, Operations and Legal choices with their owners.

### 1.3 Status declarations

| Item | Status | Evidence |
|---|---|---|
| **ADR-024** | Remains **Proposed** | `docs/ADR/ADR-024.md:5`; review section 13.4 |
| **SPR-014** | Remains **DEFERRED** | `SPR-014_Records_Lifecycle_Engine_Report.md`; no closure record; review section 4.5 |
| **SPR-017** | **NOT IMPLEMENTED** and **NOT AUTHORIZED** | No `App\Domains\Archive`, no migrations, routes or config; review sections 12.1-12.2 |
| **This blueprint** | Documentation only | Creates one report; changes no application or governance status |

### 1.4 What exists today versus what is proposed

| Capability area | Current state | Classification | Blueprint response |
|---|---|---|---|
| Archive bounded context | No `App\Domains\Archive`; no archive migration, route, config or permission | **NOT IMPLEMENTED** | Section 4.1 proposes boundary and contracts only |
| SPR-014 lifecycle engine | Envelope, 3 states, service, policy, 3 source-domain integrations, targeted tests | **VERIFIED IMPLEMENTED** (code); **DEFERRED** (governance) | Consume through a new read contract; do not extend it |
| SPR-012 document engine | Document generation, versions, `GeneratedFile` metadata, SHA-256 write | **VERIFIED IMPLEMENTED** | Add a read contract; do not import models |
| Lifecycle read capability | `findBySource()` returns the `RecordLifecycle` model; all published contracts are write-only | **PARTIALLY IMPLEMENTED** (service) / **NOT IMPLEMENTED** (contract) | Section 4.1.3 proposes `ArchivedLifecycleReadContract` |
| Document/file read capability | Command-only service; verification-specific state contract | **PARTIALLY IMPLEMENTED** / **NOT IMPLEMENTED** for archive | Section 4.1.3 proposes `ArchiveDocumentReadContract` / `ArchiveFileReadContract` |
| Storage abstraction | Direct `Storage::disk()` calls; no port/adapter | **NOT IMPLEMENTED** | Section 4.2 proposes provider-neutral `StoragePort` with local adapter |
| Storage quota | `plans.storage_limit` string column; no parser, resolver, metering or enforcement | **NOT IMPLEMENTED** as a mechanism; column is **VERIFIED IMPLEMENTED** data | Section 4.3 proposes quota contract and `report_only` first release |
| Retention | No schema, config, service or job | **NOT IMPLEMENTED** | Section 4.4 proposes mechanism; legal values deferred |
| Audit (generic) | `audit_logs` table + `AuditService`; 90-day cleanup scheduled daily | **VERIFIED IMPLEMENTED** | Section 4.5 reconciles categories and proposes archive catalogue |
| Audit (document-specific) | `document_audits` append-only journal; no cleanup reference | **VERIFIED IMPLEMENTED** | Reuse pattern; do not mutate existing cleanup jobs |
| Archive audit | No archive events | **NOT IMPLEMENTED** | Section 4.5.3 catalogue |
| Tenant scoping | `HasSchoolId`, `scopeForSchool`, school-aware policies | **VERIFIED IMPLEMENTED** (pattern) | Archive must add its own policy and negative tests |
| Archive permissions | No `archive.*` group or policy | **NOT IMPLEMENTED** | Section 4.6.2 |
| Integrity | SHA-256 written; no verification on read or scheduled job | **PARTIALLY IMPLEMENTED** | Section 4.6.4 |
| Encryption at rest | Local disk plain; DB `encrypt` commented; no `Crypt` usage found | **NOT IMPLEMENTED** (application-level); **UNVERIFIED** (infrastructure disk) | Section 4.6.5 |
| Backups | Doc describes encrypted off-site backups; `BackupService` writes a schema-only `.sql` placeholder and no files | **PARTIALLY IMPLEMENTED**; coverage/encryption/restore **UNVERIFIED** | Section 4.7 |
| Data residency | Local-first; no hosting record, DPA or residency decision | **UNVERIFIED** | Section 4.8 |
| Decision log | ADR-024, SPR-017 board report and `SESSION_HANDOFF` disagree on numbering and gate status | **CONFLICT** | Section 3.8 and section 8.3 |

### 1.5 The ten required decision areas and their primary gate

| # | Decision area | Primary gate | Decision IDs |
|---|---|---|---|
| 1 | Domain integration contracts | MUST DECIDE BEFORE ADR ACCEPTANCE | DR-01, DR-02, DR-03, DR-04 |
| 2 | Storage port and adapter boundary | MUST DECIDE BEFORE ADR ACCEPTANCE | DR-05 |
| 3 | Quota behavior | Split: contract before acceptance; enforcement before production | DR-06, DR-07, DR-08 |
| 4 | Retention configuration | Split: mechanism before acceptance; values before production | DR-09, DR-10, DR-11 |
| 5 | Audit catalogue and retention | MUST DECIDE BEFORE ADR ACCEPTANCE | DR-12, DR-13 |
| 6 | Permissions | MUST DECIDE BEFORE ADR ACCEPTANCE | DR-14, DR-26 |
| 7 | Local storage security | MUST DECIDE BEFORE ADR ACCEPTANCE | DR-15, DR-16 |
| 8 | Backup and recovery | MUST BE DECIDED BEFORE PRODUCTION | DR-18 |
| 9 | Cloud activation | MUST BE DECIDED BEFORE CLOUD ACTIVATION | DR-20, DR-22 |
| 10 | Data residency | Split: hosting record before production; cloud residency before cloud | DR-21, DR-22 |

### 1.6 Recommended sequence (for owner scheduling, not a decision)

1. Architecture resolves the contract boundary, storage port, ownership list and metadata model (DR-01 to DR-05).
2. Product, Architecture and Legal resolve quota first-release semantics, retention mechanism and audit-retention categories (DR-06, DR-07, DR-09, DR-12, DR-13).
3. Security resolves permissions, download authorization, integrity and the truthful encryption posture (DR-14, DR-15, DR-16, DR-26).
4. Architecture revises ADR-024 against section 9 acceptance criteria and the decision log reconciliation.
5. ADR-024 is re-reviewed. Acceptance remains an Architecture Team decision.
6. SPR-017 implementation may start only after ADR acceptance **and** the SPR-014 interlock gate (section 7).
7. Production and cloud gates remain separate and later.

### 1.7 What this blueprint deliberately does not do

- It does not accept or amend ADR-024.
- It does not close, complete or re-scope SPR-014.
- It does not authorize or begin SPR-017 implementation.
- It does not choose a cloud provider, region, price, retention period, encryption product or legal position.
- It does not treat passing tests as formal sprint closure.
- It does not treat a generic waiver as a substitute for staging evidence.

## 2. Repository Evidence and Source References

### 2.1 Baseline and preservation controls

Recorded before this blueprint was written:

| Item | Value |
|---|---|
| Branch | `main` |
| HEAD | `d42dbef3e32ad6e7a7a4bc70c68f805be60da480` |
| HEAD subject | `correction du login` |
| HEAD date | 2026-11-03 12:28:46 +0000 |
| Tracked modifications | 4 pre-existing governance documents |
| Untracked before this task | 2 ZIP archives, prior reconciliation report, TASK 02 review report |
| Staged changes | None |
| Destructive Git operations | None |

Pre-existing working-tree state, preserved unchanged:

```text
 M docs/docs/00_Project_Management/CURRENT_POSITION.md
 M docs/docs/00_Project_Management/MASTER_ROADMAP.md
 M docs/docs/00_Project_Management/PROJECT_STATE.md
 M docs/docs/SESSION_HANDOFF.md
?? 2_NexusSchool.zip
?? NexusSchool.zip
?? docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md
?? docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md
```

The four modified documents are the authorised output of the 05 November 2026 reconciliation. This task neither reverts nor extends them. The ZIP archives and the prior reports are untouched.

### 2.2 Source documents used

| Source | Role in this blueprint |
|---|---|
| `docs/ADR/ADR-024.md` | Subject ADR; Decisions 1-9, invariants, open decisions, approvals |
| `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md` | TASK 02 findings; primary input |
| `docs/docs/00_Project_Management/SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md` | Original D1-D10 discovery and product-decision list |
| `docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md` | SPR-014 specification, implementation and test evidence |
| `docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md` | Prior governance evidence and DoD assessment |
| `docs/docs/00_Project_Management/ARCHITECTURE_DECISIONS.md` | Section 12 archive rule |
| `docs/docs/00_Project_Management/DEPENDENCY_MAP.md` | Sections 10, 13 and 15 domain rules |
| `docs/docs/00_Project_Management/MASTER_ROADMAP.md` | Phase 9 placement; SPR-012 naming |
| `docs/docs/00_Project_Management/RISK_REGISTER.md` | OR-004, SA-001, CR-002, CR-003 |
| `docs/03_Database/Audit.md` | Audit retention claim (7 years) |
| `docs/08_Deployment/Backups.md` | Operational backup scope, schedule, encryption, RTO/RPO |
| `docs/07_Development/Development_Workflow.md` | Definition of Done |
| `docs/00_Project/Engineering_Principles.md` | Definition of Done and security-by-design principles |
| `docs/06_SaaS/13_Usage_Limits.md` | Draft quota/usage-limits design intent |
| `docs/06_SaaS/04_Plans_and_Features.md` | Draft plan/limit design intent |
| `docs/06_SaaS/Subscriptions.md` | Subscription lifecycle intent |

### 2.3 Evidence ledger

Identifiers `EV-xx` are inherited from the TASK 02 review. `BP-E-xx` identifiers are added by this blueprint. A classification describes the **current repository state**, not the desired architecture.

| ID | Fact | Path / evidence | Classification |
|---|---|---|---|
| EV-01 | No Archive bounded context | `app/Domains/` listing; `App\Domains\Archive` absent | **NOT IMPLEMENTED** |
| EV-02 | No archive route | `routes/api.php`, `console.php`, `tenant.php`, `verification.php`, `web.php` | **NOT IMPLEMENTED** |
| EV-03 | No archive/retention/quota config | `config/` listing | **NOT IMPLEMENTED** |
| EV-04 | Local disk root is private storage | `config/filesystems.php:35` -> `storage_path('app/private')` | **VERIFIED IMPLEMENTED** |
| EV-05 | Public disk root is public storage | `config/filesystems.php:43` -> `storage_path('app/public')` | **VERIFIED IMPLEMENTED** |
| EV-06 | S3 disk is defined but unused | `config/filesystems.php:50`; no domain usage | **PARTIALLY IMPLEMENTED** (config only) |
| EV-07 | Tenant suffixing covers local/public only; S3 commented out | `config/tenancy.php:139-142`; `// 's3'` at line 142 | **VERIFIED IMPLEMENTED** for local/public; **NOT IMPLEMENTED** for S3 |
| EV-08 | Single document disk switch | `config/nexus-academic-communication.php:25` | **VERIFIED IMPLEMENTED** |
| EV-09 | Official document path convention | `GenerateDocumentAction.php:212`; `RegenerateDocumentAction.php:95`; `PdfExportChannel.php:72` | **VERIFIED IMPLEMENTED** |
| EV-10 | SHA-256 checksum written at generation | `GenerateDocumentAction.php:236`; `RegenerateDocumentAction.php:118`; `PdfExportChannel.php:90` | **VERIFIED IMPLEMENTED** (write only) |
| EV-11 | File metadata columns exist | `database/migrations/2026_08_06_000010_create_generated_files_table.php:15-25` | **VERIFIED IMPLEMENTED** |
| EV-12 | File rows cascade on tenant/document delete | same migration, line 29 | **VERIFIED IMPLEMENTED** (risk carrier) |
| EV-13 | Lifecycle has exactly three states | `app/Domains/RecordsLifecycle/Domain/Enums/RecordLifecycleState.php` | **VERIFIED IMPLEMENTED** |
| EV-14 | Lifecycle service API is command-oriented | `RecordLifecycleService.php:23-49` | **VERIFIED IMPLEMENTED** |
| EV-15 | Published lifecycle contracts are write-only | `AcademicCommunication/Domain/Contracts/RecordsLifecycleContract.php:22,36,46` | **VERIFIED IMPLEMENTED** (write seam) |
| EV-16 | Lifecycle persistence schema | `database/migrations/2026_08_10_000000_create_record_lifecycles_table.php` | **VERIFIED IMPLEMENTED** |
| EV-17 | Lifecycle policy exists and delete is denied | `app/Domains/RecordsLifecycle/Infrastructure/Policies/RecordLifecyclePolicy.php` | **VERIFIED IMPLEMENTED** |
| EV-18 | No lifecycle domain events or audit writes | `RecordsLifecycleServiceProvider.php`; actions contain no `event()` or audit call | **NOT IMPLEMENTED** (lifecycle-owned audit) |
| EV-19 | Document status includes `Archived` | `app/Domains/AcademicCommunication/Domain/Enums/DocumentStatus.php` | **VERIFIED IMPLEMENTED** |
| EV-20 | Archive action updates document status and lifecycle | `app/Domains/AcademicCommunication/Application/Actions/ArchiveDocumentAction.php` | **VERIFIED IMPLEMENTED** |
| EV-21 | Archive-usable read contracts do not exist | All `RecordsLifecycleContract` variants; `AcademicDocumentVerificationContract` is verification-only | **NOT IMPLEMENTED** |
| EV-22 | `plans.storage_limit` is a string column | `database/migrations/2026_07_24_000001_create_plans_table.php:23` | **VERIFIED IMPLEMENTED** (data field) |
| EV-23 | Seeded storage values are strings | `database/seeders/PlanSeeder.php:25,40,55,70` (`500MB`, `2GB`, `10GB`, `50GB`) | **VERIFIED IMPLEMENTED** |
| EV-24 | No cast, parser or accessor for storage limit | `app/Models/Plan.php` (fillable only) | **NOT IMPLEMENTED** |
| EV-25 | No metering or enforcement service | repository search for `storage_limit` yields model, migration, seeder and Filament field only | **NOT IMPLEMENTED** |
| EV-26 | School is not directly linked to a plan | `app/Domains/School/Models/School.php` has no plan/subscription relation | **NOT IMPLEMENTED** |
| EV-27 | School-to-plan link is through subscriptions | `database/migrations/2026_07_24_000002_create_subscriptions_table.php:13-17` | **VERIFIED IMPLEMENTED** |
| EV-28 | Generic audit table exists | `database/migrations/2026_07_17_000070_create_audit_logs_table.php` | **VERIFIED IMPLEMENTED** |
| EV-29 | Audit doc claims immutable 7-year minimum retention | `docs/03_Database/Audit.md:74-79` | **DOCUMENTED INTENT** (not enforced) |
| EV-30 | Audit cleanup defaults to 90 days and is scheduled daily | `app/Console/Commands/System/SystemCleanupCommand.php:14,23-24`; `bootstrap/app.php:47` | **VERIFIED IMPLEMENTED** (contradicts EV-29) |
| EV-31 | Backup doc describes encrypted off-site backups, RTO < 2h, RPO < 15m | `docs/08_Deployment/Backups.md:21,62,70-78,94-98` | **DOCUMENTED INTENT** |
| EV-32 | No staging deployment record | `docs/08_Deployment/Development.md`, `CI_CD.md` describe process only | **UNVERIFIED** |
| EV-33 | No legal retention or residency evidence | repository search; only generic academic-framework references | **NOT IMPLEMENTED** |
| EV-34 | Coefficient index is already short-named | `2026_08_01_000011_create_coefficients_table.php:27` uses `coefficients_school_class_subject_unique` | **STALE ADR NOTE** |
| EV-35 | No Archive permission group | `app/Domains/Permission/Enums/PermissionGroup.php`; no `Archive` case | **NOT IMPLEMENTED** |
| EV-36 | ADR-024's S3 line reference is stale | ADR says line 115; actual `// 's3'` is `config/tenancy.php:142` | **STALE ADR NOTE** |
| EV-37 | ADR-024 status is Proposed | `docs/ADR/ADR-024.md:5` | **VERIFIED** |
| EV-38 | ADR-024 records D1-D5 as product-approved | `docs/ADR/ADR-024.md:99-106,687-694` | **VERIFIED** |
| EV-39 | SPR-017 board report records decisions as pending | `SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md:162-171,296,342-365` | **CONFLICT** with EV-38 |
| EV-40 | SPR-014 report has no approval, staging or closure record | `SPR-014_Records_Lifecycle_Engine_Report.md` ends at line 475 | **NOT RECORDED** |
| BP-E-41 | A read-contract precedent exists | `app/Domains/AcademicPolicy/Domain/Contracts/AcademicPolicyQueryContract.php` returns reference DTOs | **VERIFIED IMPLEMENTED** (pattern) |
| BP-E-42 | Verification read contract is deliberately narrow | `app/Domains/IdentityAndVerification/Domain/Contracts/AcademicDocumentVerificationContract.php` exposes only `stateForDocument()` | **VERIFIED IMPLEMENTED** (pattern) |
| BP-E-43 | Certificate/Transcript and ReportCard Academic Communication contracts are command-oriented | `app/Domains/CertificateAndTranscript/Domain/Contracts/AcademicCommunicationContract.php`; `app/Domains/ReportCard/Domain/Contracts/AcademicCommunicationContract.php` | **VERIFIED IMPLEMENTED** (commands only) |
| BP-E-44 | Payment domain contains only enums | `app/Domains/Payment/Enums/PaymentMethod.php`, `PaymentStatus.php` | **NOT IMPLEMENTED** (billing/quota service) |
| BP-E-45 | AI has a separate quota mechanism | `app/Domains/Ai/Services/AiService.php:138` (`checkQuotas`); `config/ai.php` | **VERIFIED IMPLEMENTED** (AI-only, not storage) |
| BP-E-46 | Document audit is append-only by design | `app/Domains/AcademicCommunication/Infrastructure/Models/DocumentAudit.php`; `2026_08_06_000009_create_document_audits_table.php` | **VERIFIED IMPLEMENTED** |
| BP-E-47 | Generic cleanup deletes audit, activity and health rows but not `document_audits` | `app/Console/Commands/System/SystemCleanupCommand.php` | **VERIFIED IMPLEMENTED** |
| BP-E-48 | Duplicate cleanup and backup command classes exist in two namespaces | `app/Console/Commands/SystemCleanupCommand.php` and `app/Console/Commands/System/SystemCleanupCommand.php`; same for backup | **PARTIALLY IMPLEMENTED / UNVERIFIED** (registration order) |
| BP-E-49 | Backup implementation writes a schema-only placeholder, does not copy files or encrypt | `app/Domains/System/Services/BackupService.php` (`executeBackup`) | **PARTIALLY IMPLEMENTED** |
| BP-E-50 | No application-level encryption usage found; DB `encrypt` is commented | `config/database.php:113`; search for `Crypt`, `Encrypter`, `openssl_encrypt` returned no application usage | **NOT IMPLEMENTED** (application); **UNVERIFIED** (host disk) |
| BP-E-51 | Backup restore only dispatches an event | `app/Domains/System/Services/BackupService.php` (`restore`) | **PARTIALLY IMPLEMENTED** |
| BP-E-52 | No scheduler entry for `system:backup` | `bootstrap/app.php:42-48` schedules analytics, health and cleanup only | **NOT IMPLEMENTED** (scheduled backup) |
| BP-E-53 | `SESSION_HANDOFF` says open decisions block cloud/production/packages, "not ADR acceptance" | `docs/docs/SESSION_HANDOFF.md:577-582` | **CONFLICT** with review section 13.2 |
| BP-E-54 | Definition of Done requires review approval and staging deployment | `docs/07_Development/Development_Workflow.md:46-54`; `docs/00_Project/Engineering_Principles.md:249-259` | **DOCUMENTED POLICY** |
| BP-E-55 | Archive action is unscoped by school and skips policy when actor is null | `app/Domains/RecordsLifecycle/Application/Actions/ArchiveRecordLifecycleAction.php` | **VERIFIED DEFECT CARRIER** (not archive-owned) |
| BP-E-56 | `findBySource()` is school-scoped but returns the model | `app/Domains/RecordsLifecycle/Application/Services/RecordLifecycleService.php` | **PARTIALLY IMPLEMENTED** (read surface) |
| BP-E-57 | `AcademicCommunicationService` exposes commands only | `app/Domains/AcademicCommunication/Application/AcademicCommunicationService.php` | **VERIFIED IMPLEMENTED** (commands only) |
| BP-E-58 | `GeneratedFile` exposes enough metadata for a first release | `app/Domains/AcademicCommunication/Infrastructure/Models/GeneratedFile.php` | **VERIFIED IMPLEMENTED** |
| BP-E-59 | `generated_files.disk` is a free string; `size_bytes` defaults to 0; checksum is required | `2026_08_06_000010_create_generated_files_table.php:20-24` | **PARTIALLY IMPLEMENTED** (needs hardening) |
| BP-E-60 | `document_audits` cascades on school and document deletion | `2026_08_06_000009_create_document_audits_table.php` | **VERIFIED IMPLEMENTED** (risk carrier) |

### 2.4 Classification key for implementation state

| Label | Meaning in this blueprint |
|---|---|
| **VERIFIED IMPLEMENTED** | Current repository code, schema, config or test directly demonstrates the capability |
| **PARTIALLY IMPLEMENTED** | Some supporting mechanism exists, but a required part is missing, unenforced or unproven |
| **NOT IMPLEMENTED** | No repository artifact implements the capability |
| **UNVERIFIED** | No evidence either way; success must not be assumed |
| **PROPOSED** | A design direction in this blueprint; no code or decision exists yet |

### 2.5 Evidence limitations

- No staging environment, CI run, production database, cloud account, legal source or owner interview was available.
- No application test, PHPStan or Pint run was performed for this task: it is documentation-only and changes no code.
- Line numbers are from the reviewed working tree at HEAD `d42dbef` plus the uncommitted documentation state.
- Where a document states an intention (for example 7-year audit retention or encrypted backups), the ledger classifies the **document** as intent and the **implementation** separately. Intent is not evidence of control operation.
- The task brief calls SPR-012 the "Business Engine". Repository documentation names SPR-012 the **Academic Communication Engine** (`MASTER_ROADMAP.md:125`). This blueprint uses "SPR-012 / Academic Communication Engine" and treats the two labels as the same substrate.

## 3. Gap-by-Gap Analysis

Every finding in the TASK 02 review is analysed here. The coverage matrix in section 3.10 maps each finding identifier to the subsection that addresses it.

### 3.1 Domain integration contracts (review 5.A: A1-A3)

#### A1 - The lifecycle read seam does not exist

- **Finding:** ADR-024 says Archive consumes "SPR-014 via the `RecordsLifecycleContract` / read surface". Every published `RecordsLifecycleContract` is a write/gateway interface with `register`, `publish` and `archive` only. There is no read contract, no list/query contract and no projection suitable for Archive. `RecordLifecycleService::findBySource()` returns the `RecordLifecycle` model, which Archive Application code must not import under C1.
- **Current state:** **PARTIALLY IMPLEMENTED** at service level (`findBySource()` is school-scoped), **NOT IMPLEMENTED** at contract level.
- **Evidence:** EV-14, EV-15, EV-21, BP-E-56.
- **Why it blocks acceptance:** A revised ADR that still names a non-existent contract leaves implementation to invent one, violating the contract-only boundary and creating a risk that Archive imports `RecordLifecycle` directly (review RR-02).
- **Required decision:** DR-01, DR-03.
- **Proposed direction:** Add a read-only `ArchivedLifecycleReadContract` owned by SPR-014, returning immutable projection objects, tenant-scoped by `school_id`, with no model leakage. Archive must never call `RecordLifecycleService::archive()` or mutate `record_lifecycles`. The contract must expose archived records by school, source identity and archive date, plus a "find by source" lookup for idempotency.

#### A2 - No Academic Communication read contract for archive

- **Finding:** The only Academic Communication read-only contract is `AcademicDocumentVerificationContract`, whose projection is strictly verification state. Archive needs document identity, type, number, status, current version and current file pointer (disk, path, size, checksum, mime). `AcademicCommunicationService` exposes commands only.
- **Current state:** **NOT IMPLEMENTED** for archive; **VERIFIED IMPLEMENTED** for the narrow verification read pattern.
- **Evidence:** EV-21, BP-E-42, BP-E-57, BP-E-58.
- **Why it blocks acceptance:** Without a projection contract, Archive would either import `AcademicDocument` / `GeneratedFile` (forbidden) or issue a broad query through an internal service (coupling). Both undermine the stated boundary.
- **Required decision:** DR-02, DR-03.
- **Proposed direction:** Two narrow read contracts owned by SPR-012: `ArchiveDocumentReadContract` (document metadata and current version identity) and `ArchiveFileReadContract` (current file pointer and integrity metadata). A combined contract is acceptable if it returns two separate projections; the key rule is no model and no content/grades/personal data leakage. Reuse the `AcademicPolicyQueryContract` pattern of returning reference DTOs.

#### A3 - Ownership must be a closed list

- **Finding:** "Archive owns archive provenance..." is directionally correct but not testable. The ADR must enumerate what Archive owns and what it must never own or mutate.
- **Current state:** **PROPOSED** in ADR-024 at a high level; no closed list.
- **Evidence:** EV-01, EV-15, EV-16, EV-20; review section 5.A.
- **Why it blocks acceptance:** Without a closed list, scope creep (document rendering, lifecycle state, verification, backup) cannot be detected by review or architecture tests.
- **Required decision:** DR-03, DR-04.
- **Proposed direction:** Adopt the closed ownership list in section 4.1.5. Encode it as an architecture test requirement at implementation time: Archive Domain/Application must not import source infrastructure models and must not call lifecycle mutation methods.

### 3.2 Storage architecture (review 5.B: B1-B5)

#### B1 - Path partition is not authorization

- **Finding:** The ADR correctly says `school_id` in a path is not authorization. Implementation must resolve the tenant, authorize the policy, then load by `school_id` + id; it must never construct a path from user input.
- **Current state:** **VERIFIED IMPLEMENTED** as a platform pattern (`HasSchoolId`, school-aware policies); **NOT IMPLEMENTED** for Archive.
- **Evidence:** EV-07, EV-17, EV-35; review section 5.B.
- **Required decision:** DR-14, DR-15, DR-26.
- **Proposed direction:** Mandatory pipeline: resolve tenant -> authorize archive capability -> query archive entry by `school_id` and id -> canonicalize and allow-list disk/path -> verify checksum -> stream. Wrong tenant or missing object returns the same 404. No path input from the client.

#### B2 - Metadata is sufficient for a first release, with fixes

- **Finding:** `generated_files` has enough metadata for a first release, but `size_bytes` defaults to 0, `checksum` is required but never verified on read, and `disk` is a free string with no allow-list.
- **Current state:** **PARTIALLY IMPLEMENTED**.
- **Evidence:** EV-11, EV-12, BP-E-58, BP-E-59.
- **Required decision:** DR-05, DR-15.
- **Proposed direction:** Treat source metadata as untrusted until verified. Archive records its own `disk`, `path`, `size_bytes`, `checksum_sha256`, `mime_type` and source references. Add a disk allow-list and a canonical path rule. Verify the checksum before download and in a scheduled integrity job.

#### B3 - "S3-compatible abstraction" is premature as a mandatory seam

- **Finding:** No cloud vendor or protocol has been approved. Requiring S3 compatibility constrains the design before ODC1. The seam should be a provider-neutral storage port with a local adapter; an S3 adapter can implement it later if approved.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-03, EV-06, EV-07; ADR-024 Decision 2 and Decision 4.
- **Required decision:** DR-05, DR-20.
- **Proposed direction:** Define `StoragePort` in terms of operations (`put`, `getStream`, `exists`, `delete`, `checksum`, `size`), not an S3 protocol. First adapter is local. An S3-compatible adapter is an optional, gated future implementation. Remove "S3-compatible is the proposed seam" from the ADR; keep "provider-neutral port; S3 adapter optional and gated."

#### B4 - At-rest encryption is not evidenced

- **Finding:** The local disk stores plain files; `config/database.php` has `// 'encrypt' => ...` commented out; no application-level encryption or key-management service was found. "Follows existing platform defaults" is not a control statement.
- **Current state:** **NOT IMPLEMENTED** at application level; **UNVERIFIED** at host/disk level.
- **Evidence:** EV-04, BP-E-50; review section 5.B.
- **Required decision:** DR-16, DR-17.
- **Proposed direction:** State the truth in the revised ADR: no application-level encryption at rest is implemented for local archive objects; database transport encryption is commented; host disk encryption is unverified. Then require an explicit production decision: implement application-level envelope encryption with key management, or accept the risk with documented infrastructure controls. Do not claim encryption until evidence exists. Cloud key management remains open (ODC2).

#### B5 - Backup coverage is assumed

- **Finding:** `docs/08_Deployment/Backups.md` describes encrypted off-site backups and RTO/RPO, but there is no evidence archive paths are included or that restore has been tested. The `BackupService` implementation is not the documented pipeline.
- **Current state:** **PARTIALLY IMPLEMENTED**; archive coverage **NOT IMPLEMENTED**; restore **UNVERIFIED**.
- **Evidence:** EV-31, BP-E-49, BP-E-51, BP-E-52.
- **Required decision:** DR-18, DR-19.
- **Proposed direction:** Keep backup and archive separate. Add archive paths to backup scope explicitly, require a restore drill including one archived object, and record the RTO/RPO actually achieved. Do not claim the documented RTO/RPO until measured.

### 3.3 Storage quotas (review 5.D: D1-D5)

#### D1 - The ADR overstates the current mechanism

- **Finding:** "Quotas reuse `plans.storage_limit`" is true only as a data reference. There is no unit parser, canonical byte value, usage measurement, threshold evaluation, enforcement point or school-to-plan resolver.
- **Current state:** Column **VERIFIED IMPLEMENTED**; mechanism **NOT IMPLEMENTED**.
- **Evidence:** EV-22 to EV-27, BP-E-44, BP-E-45.
- **Required decision:** DR-06, DR-07.
- **Proposed direction:** Revise the wording to "the plan limit column is the intended authority; metering and enforcement are not implemented and are out of first release until ODQ closes." Add a `StorageQuotaContract` that returns `limit_bytes`, `used_bytes` (nullable until metering), `enforcement_mode` and source plan identity.

#### D2 - Unit ambiguity is a correctness risk

- **Finding:** `'1GB'` could mean 1,000,000,000 or 1,073,741,824 bytes. Values may be lowercase, spaced or invalid.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-22, EV-23, EV-51.
- **Required decision:** DR-06.
- **Proposed direction:** Canonical internal unit is bytes (integer). Choose binary (1024-based) or decimal (1000-based) explicitly in the ADR and document it. Accept `B`, `KB`, `MB`, `GB`, `TB` case-insensitively, reject invalid values, and fail closed on unparseable plan data. Record the parser contract as a testable acceptance criterion.

#### D3 - Subscription resolution is undefined

- **Finding:** A school may have zero, one or many subscriptions; expired/suspended/cancelled semantics are not specified. Which subscription supplies the plan on a given date? What happens after expiry?
- **Current state:** **PARTIALLY IMPLEMENTED** (schema exists), resolution **NOT IMPLEMENTED**.
- **Evidence:** EV-26, EV-27.
- **Required decision:** DR-06 (contract/resolution), DR-08 (production enforcement).
- **Proposed direction:** Define a deterministic resolver: select the active subscription whose date window contains the evaluation date; if multiple, apply a documented precedence (latest `starts_at`, then latest `expires_at`, then latest creation); if none active, fall back to the expired plan for report-only visibility and mark the school over-limit/expired; if still none, `limit_bytes = null` and no enforcement. The resolver must be in the platform/SaaS layer, not Archive.

#### D4 - Scope must stay per school

- **Finding:** Plan rows are global; the limit applies to the school's active subscription. Do not create a second quota table in Archive.
- **Current state:** **VERIFIED IMPLEMENTED** (tenant-scoped patterns exist); **NOT IMPLEMENTED** for quota.
- **Evidence:** EV-26, EV-27, EV-07.
- **Required decision:** DR-06.
- **Proposed direction:** Archive consumes a school-scoped `StorageQuotaContract`. It stores no quota values and no usage ledger. Any usage table belongs to the SaaS/platform layer if one is later approved.

#### D5 - Metering belongs to the platform/SaaS layer

- **Finding:** Archive should consume a quota projection. If usage is computed, it must include at least `generated_files.size_bytes` plus archive objects, with deduplication. Whether an archive copy double-counts is undecided.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-25, EV-11.
- **Required decision:** DR-07, DR-08.
- **Proposed direction:** First release is `report_only`: record sizes and expose usage, never block. Before metering, decide whether archived bytes double-count: recommended default is to count each physical byte once per school, with a stable deduplication key (source file identity + archive copy identity). Hard enforcement only after ODQ and Product approval.

### 3.4 Retention (review 5.C: C1-C5)

#### C1 - The mechanism is not specified

- **Finding:** No policy schema, scope precedence, clock start, disposition enum, legal-hold concept, jurisdiction field, evaluation trigger, dry-run mode or deletion certificate is defined. "Configurable" is a goal, not a design.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-03, EV-33.
- **Required decision:** DR-09.
- **Proposed direction:** Specify the mechanism in section 4.4: policy table, precedence, clock, disposition, legal hold, evaluation trigger, dry-run and audit. Keep values out.

#### C2 - Legal values are correctly absent

- **Finding:** ADR-024 does not invent legal periods, which is correct. The absence must remain an explicit gate, not an implicit default of "keep forever" or "delete after X".
- **Current state:** **CORRECTLY ABSENT**.
- **Evidence:** EV-33; ADR-024 Decision 6 and ODL.
- **Required decision:** DR-10.
- **Proposed direction:** No statutory period is asserted. "No policy" means preserve indefinitely, not destroy. Product and Legal must approve values per document type and jurisdiction before any destruction.

#### C3 - Destruction safety is stated, not enforced by design

- **Finding:** "Disabled by default" must be a config default, a permission and a code path that cannot run without an approval record. A comment is not a safeguard.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** ADR-024 Decision 6; EV-35.
- **Required decision:** DR-11.
- **Proposed direction:** Destruction requires a server-side feature flag default off, an `archive.destroy` permission, a dry-run report, a recorded approval, a two-person rule if Security requires it, and an audit/deletion certificate. First release implements no physical destruction.

#### C4 - Audit retention contradiction must be resolved before relying on audit logs for retention evidence

- **Finding:** A 90-day cleanup cannot support a 7-year immutable audit claim.
- **Current state:** **CONTRADICTION** (EV-29 versus EV-30).
- **Evidence:** EV-29, EV-30, BP-E-47, BP-E-48.
- **Required decision:** DR-13.
- **Proposed direction:** Separate audit categories by purpose and retention class (section 4.5). Do not use the generic 90-day `audit_logs` stream as retention evidence for archive events. Either write archive events to a dedicated append-only table with its own retention, or exempt them from generic cleanup. Do not modify the existing cleanup job in this task.

#### C5 - Clock start is ambiguous

- **Finding:** Retention could run from publication, document generation, archive entry or school closure. The ADR must choose one and define extension/review semantics.
- **Current state:** **NOT SPECIFIED**.
- **Evidence:** EV-13, EV-16, EV-20.
- **Required decision:** DR-09.
- **Proposed direction:** Default clock start is `archive_entries.archived_at`, with an optional `retention_starts_at` override. Publication or generation dates are inputs to policy matching, not the default clock. Extension/review must be explicit, audited and bounded.

### 3.5 Audit architecture (review 5.C C4, 5.E E5, O-1)

#### Existing audit mechanisms

| Mechanism | Purpose | Retention behaviour | Classification |
|---|---|---|---|
| `audit_logs` + `AuditService` | Generic platform audit (actor, action, entity, old/new values, IP, UA) | `system:cleanup --days=90` deletes older rows; scheduled daily in `bootstrap/app.php:47` | **VERIFIED IMPLEMENTED**; retention **NOT IMPLEMENTED** as documented |
| `document_audits` + `DocumentAudit` | SPR-012 append-only journal for document views, downloads, channel publication | No cleanup reference found; cascades on school/document delete | **VERIFIED IMPLEMENTED** (append-only by code comment), retention **NOT SPECIFIED** |
| `activity_logs` | Activity feed | Deleted by same 90-day cleanup | **VERIFIED IMPLEMENTED** |
| `system_health` | Health snapshots | Deleted by same 90-day cleanup | **VERIFIED IMPLEMENTED** |
| RecordsLifecycle metadata | Actor and timestamps on the lifecycle row | No domain event or generic audit write | **PARTIALLY IMPLEMENTED** (metadata only) |
| Archive events | None | None | **NOT IMPLEMENTED** |

#### The 7-year versus 90-day contradiction - resolution analysis

The two statements do **not** necessarily concern the same rows, but the repository does not currently distinguish the categories. Three interpretations are possible:

1. **Same rows:** `docs/03_Database/Audit.md` asserts a 7-year minimum for `audit_logs`, while `system:cleanup` deletes the same table at 90 days. This is a direct contradiction.
2. **Different categories:** the 7-year claim is intended for legal/records audit (for example archive, retention, destruction), while 90 days is an operational log-retention default. The repository does not encode this distinction, so the code contradicts the document by default.
3. **Documentation drift:** the 7-year claim is aspirational and no retention control exists. The code is the de facto behaviour.

**Classification:** **CONTRADICTION / AMBIGUOUS**, not a resolved design. This blueprint does not assert which interpretation is intended.

**Proposed resolution direction (for DR-13):**

- Define audit **retention classes** explicitly: `operational` (short-lived, for troubleshooting), `security` (authentication, access denial, administrative actions), `records` (archive entry, retention evaluation, disposition, destruction), and `compliance` (legal-hold and deletion certificates).
- Keep the generic `audit_logs` stream available for operational and security events with a configurable short retention.
- Write records/compliance archive events to a dedicated append-only store (for example `archive_events`) with its own retention, or mark them with a retention class that the cleanup job must never delete.
- Make the retention period per class a configuration value owned by Security and Legal; do not hard-code 7 years or 90 days in the ADR.
- Require a migration/backfill decision for existing `audit_logs` rows before changing cleanup behaviour.
- Do not modify `SystemCleanupCommand`, `bootstrap/app.php` or any audit table in this task.

#### Duplicate cleanup commands

Two classes share the `system:cleanup` signature: `App\Console\Commands\SystemCleanupCommand` (with `--dry-run`) and `App\Console\Commands\System\SystemCleanupCommand` (without). Both delete `audit_logs` at the default 90 days. Which one is registered last is **UNVERIFIED** and depends on discovery order. This is a pre-existing anomaly, not introduced by this task. The revised ADR's audit section must not depend on which command wins; it must define retention at the data/category level.

#### Archive audit event catalogue

Section 4.5.3 proposes the catalogue. The ADR must choose the storage strategy (dedicated table versus exempted generic rows) and the retention class before acceptance.

### 3.6 Security (review 5.E: E1-E7)

#### E1 - Archive needs its own permission surface

- **Finding:** No `archive.*` permission group or policy exists. Do not reuse `document.*` for archive management; the capabilities differ.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-35, EV-17, BP-E-57.
- **Required decision:** DR-14.
- **Proposed direction:** Add an `archive` permission group with `archive.view`, `archive.manage`, `archive.export`, `archive.retention.manage` and (deferred) `archive.destroy`. Policies must be school-scoped and must deny delete/force-delete. Super-admin break-glass must be explicit and audited (DR-26).

#### E2 - Download authorization must be explicit

- **Finding:** Define the controller/action, policy, tenant resolution, headers, rate limit, audit event and failure behaviour (uniform 404 for wrong tenant). No direct file serving from the public directory.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-02, EV-05, EV-35.
- **Required decision:** DR-15.
- **Proposed direction:** Private disk only; authorized streaming controller; `Content-Disposition: attachment`; no inline rendering; no public URL; no unauthenticated signed URL. If temporary URLs are introduced later, authorization must precede issuance and the URL must be short-lived and audited.

#### E3 - Integrity verification is missing

- **Finding:** Checksum is stored at generation but never verified on read. Store checksum at archive time, verify before download and on a schedule, fail closed on mismatch and raise an auditable incident.
- **Current state:** **PARTIALLY IMPLEMENTED** (write only).
- **Evidence:** EV-10, EV-11.
- **Required decision:** DR-15.
- **Proposed direction:** `checksum_sha256` on `archive_entries`; verify before stream; scheduled integrity job; quarantine status on mismatch; audit event; no silent fallback to a different file.

#### E4 - Encryption at rest needs a truthful decision

- **Finding:** Either implement application-level encryption for archived objects with key management, or record it as an accepted risk with the deployment's disk-level controls. Do not claim encryption because "Laravel defaults" exist.
- **Current state:** **NOT IMPLEMENTED** (application); **UNVERIFIED** (host).
- **Evidence:** BP-E-50, EV-04.
- **Required decision:** DR-16, DR-17.
- **Proposed direction:** The ADR states the current truth and requires an explicit production decision. Key material must never be committed. Cloud key management remains ODC2.

#### E5 - Audit retention must be fixed

- Covered in section 3.5 and DR-13.

#### E6 - Destruction requires a real safeguard chain

- **Finding:** Permission + approval record + dry-run + two-person rule (if required) + audit + deletion certificate. A config flag alone is insufficient.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** ADR-024 Decision 6/S3; EV-35.
- **Required decision:** DR-11.
- **Proposed direction:** Destruction is a workflow, not a toggle. First release implements no physical destruction. The ADR must reference ODL/ODD and the approval record shape.

#### E7 - Package exports are a high-risk surface

- **Finding:** Even though deferred, the boundary must define exactly one school per package, requester authorization, manifest hashes, expiry, cleanup and no partial artifacts.
- **Current state:** **NOT IMPLEMENTED** (correctly deferred).
- **Evidence:** ADR-024 Decision 8; no package code.
- **Required decision:** DR-24 (may be deferred).
- **Proposed direction:** Keep the boundary in the accepted ADR; leave format, contents, size, expiry values and delivery mechanism to ODP. No package machinery in first release.

### 3.7 Data residency (review 5.F: F1-F4)

#### F1 - Technical and legal questions must stay separate

- **Finding:** Local-first means the architecture does not require cross-border transfer. It does not prove compliance with any country's data-protection or records law.
- **Current state:** **UNVERIFIED**.
- **Evidence:** EV-33; `docs/01_Architecture/Academic_Framework_2.md` references Burkina Faso/Ghana as academic examples only.
- **Required decision:** DR-21, DR-22.
- **Proposed direction:** The ADR must say: "No legal compliance is claimed; local-first is a technical property, not a legal conclusion."

#### F2 - "Intended African market" is not a legal basis

- **Finding:** Market intent must not become a residency or retention claim. Country-specific obligations require named legal research (ODL) and, for hosting, a residency decision (ODC1).
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-33.
- **Required decision:** DR-10, DR-22.
- **Proposed direction:** Keep ODL and ODC1 separate. Add a jurisdiction field to retention policy and a legal review gate before destruction.

#### F3 - Deployment location is unknown

- **Finding:** The repository does not record where production or staging runs. A local-first design on a host outside the school's country is still a cross-border deployment.
- **Current state:** **UNVERIFIED**.
- **Evidence:** EV-32, EV-33.
- **Required decision:** DR-21.
- **Proposed direction:** Record actual hosting locations as deployment evidence at staging/production time; do not assume them architecturally. The revised ADR must not claim a jurisdiction.

#### F4 - Controller/processor roles are undefined

- **Finding:** If schools are data controllers and the platform is a processor, a data-processing agreement and sub-processor list are needed.
- **Current state:** **NOT IMPLEMENTED**.
- **Evidence:** EV-33; no DPA or sub-processor record found.
- **Required decision:** DR-22.
- **Proposed direction:** Product, Legal and Privacy must decide the controller/processor model before cloud activation and before any production compliance claim. This is outside the architecture's authority.

### 3.8 Decision log and stale evidence (review RV-11)

| Conflict | Evidence | Impact | Proposed reconciliation |
|---|---|---|---|
| D-numbering and approval status | ADR-024 records D1-D5 approved (EV-38); SPR-017 board report records decisions pending (EV-39); `SESSION_HANDOFF` says open decisions block cloud/production/packages, "not ADR acceptance" (BP-E-53) | Readers cannot tell which record is authoritative | The revised ADR becomes the single decision log; it restates the approved D1-D5 and maps the former pending items to DR/OD IDs. The SPR-017 board report remains historical evidence, not a competing authority. |
| Coefficient index note | ADR-024 context says a 65-character index exists; migration uses short name (EV-34) | Stale context undermines trust | Remove or correct the note in the revised ADR. |
| S3 line reference | ADR says line 115; actual `// 's3'` is line 142 (EV-36) | Stale evidence | Correct to `config/tenancy.php:142` or remove the line reference. |
| Audit retention | 7-year doc vs 90-day cleanup (EV-29/EV-30) | Contradiction | Resolve via DR-13 before relying on audit evidence. |
| Backup implementation | `Backups.md` claims encrypted off-site backups; `BackupService` writes a schema-only placeholder (BP-E-49) | Overstated operational control | Revise the ADR to state measured capability, not documented intent. |
| Database engine | `Backups.md` says PostgreSQL; `PROJECT_STATE` declares MySQL | Documentation inconsistency | Record as an operational documentation debt; do not let the ADR depend on either claim. |

**Classification:** **CONFLICT / STALE**. The revised ADR must correct these before acceptance; none is fixed by this blueprint.

### 3.9 SPR-014 observations O-1 to O-4

These are review observations, not new sprint findings. They matter to Archive because it consumes SPR-014.

| ID | Observation | Consequence for Archive | Proposed treatment |
|---|---|---|---|
| O-1 | Lifecycle service emits no domain events or generic audit rows | Archive cannot rely on a lifecycle-owned audit stream | Archive owns its own audit events; lifecycle read contract does not promise events |
| O-2 | `ArchiveRecordLifecycleAction` loads by ID without school scope and skips policy when actor is null | A direct call path exists that could mutate another school's record | Archive must never call the mutation service; it must use the tenant-scoped read contract. Record as a SPR-014 hardening observation, not an Archive fix. |
| O-3 | `DocumentStatus::Archived` and `RecordLifecycleState::Archived` are separate concepts | Divergence can mis-trigger or block archive entry | Archive triggers on lifecycle `archived` and verifies the document status; divergence becomes an auditable exception (DR-04/DR-09) |
| O-4 | No standalone SPR-014 sprint specification | Traceability of "scope agreed" vs "scope implemented" is weaker | Advisory only; SPR-014 closure evidence requirement remains (section 7) |

### 3.10 Review finding coverage matrix

| Review finding | Blueprint subsection | Decision(s) | Status of gap |
|---|---|---|---|
| A1 lifecycle read seam | 3.1, 4.1.3 | DR-01 | Open |
| A2 document read contract | 3.1, 4.1.3 | DR-02 | Open |
| A3 closed ownership list | 3.1, 4.1.5 | DR-03 | Open |
| B1 path is not authorization | 3.2, 4.6.2 | DR-14, DR-15 | Open |
| B2 metadata hardening | 3.2, 4.6.4 | DR-05, DR-15 | Open |
| B3 S3 seam premature | 3.2, 4.2 | DR-05 | Open |
| B4 encryption not evidenced | 3.2, 4.6.5 | DR-16, DR-17 | Open |
| B5 backup coverage assumed | 3.2, 4.7 | DR-18, DR-19 | Open |
| C1 retention mechanism unspecified | 3.4, 4.4 | DR-09 | Open |
| C2 legal values absent | 3.4, 4.4.6 | DR-10 | Open |
| C3 destruction safety | 3.4, 4.4.5 | DR-11 | Open |
| C4 audit retention contradiction | 3.4, 3.5, 4.5 | DR-13 | Open |
| C5 clock start ambiguous | 3.4, 4.4.3 | DR-09 | Open |
| D1 quota overstated | 3.3, 4.3 | DR-06, DR-07 | Open |
| D2 unit ambiguity | 3.3, 4.3.2 | DR-06 | Open |
| D3 subscription resolution | 3.3, 4.3.4 | DR-06, DR-07 | Open |
| D4 per-school scope | 3.3, 4.3.3 | DR-06 | Open |
| D5 metering ownership | 3.3, 4.3.5 | DR-08 | Open |
| E1 archive permissions | 3.6, 4.6.2 | DR-14 | Open |
| E2 download authorization | 3.6, 4.6.3 | DR-15 | Open |
| E3 integrity verification | 3.6, 4.6.4 | DR-15 | Open |
| E4 encryption decision | 3.6, 4.6.5 | DR-16, DR-17 | Open |
| E5 audit retention | 3.5, 4.5.4 | DR-13 | Open |
| E6 destruction safeguard | 3.6, 4.4.5 | DR-11 | Open |
| E7 package export risk | 3.6, 4.9 | DR-24 | Deferred |
| F1 technical vs legal | 3.7, 4.8 | DR-21, DR-22 | Open |
| F2 market is not legal basis | 3.7, 4.8 | DR-10, DR-22 | Open |
| F3 hosting unknown | 3.7, 4.8 | DR-21 | Open |
| F4 controller/processor | 3.7, 4.8 | DR-22 | Open |
| O-1 lifecycle audit gap | 3.9, 4.5.3 | DR-12 | Open |
| O-2 unscoped archive action | 3.9, 4.1.6 | DR-03 | Open (SPR-014 hardening) |
| O-3 dual archived concepts | 3.9, 4.4.3 | DR-04, DR-09 | Open |
| O-4 no standalone spec | 3.9, 7 | - | Advisory |
| RV-01 named read contracts | 4.1.3, 9 | DR-01, DR-02 | Open |
| RV-02 ownership list | 4.1.5, 9 | DR-03 | Open |
| RV-03 metadata model | 4.1.7, 9 | DR-04 | Open |
| RV-04 retention specification | 4.4, 9 | DR-09 | Open |
| RV-05 quota correction | 4.3, 9 | DR-06, DR-07 | Open |
| RV-06 provider-neutral storage | 4.2, 9 | DR-05 | Open |
| RV-07 download/access design | 4.6.3, 9 | DR-15 | Open |
| RV-08 audit catalogue/retention | 4.5, 9 | DR-12, DR-13 | Open |
| RV-09 encryption posture | 4.6.5, 9 | DR-16 | Open |
| RV-10 SPR-014 interlock | 7, 9 | DR-28 | Open |
| RV-11 decision log/stale evidence | 3.8, 9 | DR-27 | Open |
| RV-12 testable acceptance criteria | 9, 10 | DR-29 | Open |

## 4. Proposed Architectural Direction

All content in this section is **PROPOSED**. Nothing here is implemented, approved or binding until the named owner records a decision and, for architecture, ADR-024 is revised and accepted.

### 4.1 Domain integration contracts

#### 4.1.1 Capabilities Archive needs

| Capability | Minimum fields | Source owner | Why Archive needs it |
|---|---|---|---|
| Find archived lifecycle records | `school_id`, `source_domain`, `source_type`, `source_id`, `source_version`, `lifecycle_state`, `archived_at`, `created_by`, `origin_event` | SPR-014 | Entry trigger, provenance, idempotency |
| Read one lifecycle by source | same, keyed by school + source identity | SPR-014 | Duplicate detection and reconciliation |
| Read document metadata | `document_id`, `school_id`, `document_type`, `document_number`, `status`, `current_version_id`, `archived_at` | SPR-012 | Identity, eligibility and reconciliation with lifecycle state |
| Read current file pointer | `generated_file_id`, `academic_document_id`, `document_version_id`, `disk`, `path`, `file_name`, `mime_type`, `size_bytes`, `checksum`, `is_current` | SPR-012 | Object pointer and integrity |
| Resolve quota | `limit_bytes`, `used_bytes` (nullable), `enforcement_mode`, `plan_id`, `plan_name`, `subscription_id`, `resolved_at` | SaaS/platform layer | Quota reporting and future enforcement |
| Append audit event | event type, actor, school, subject, timestamp, outcome, metadata | Archive-owned audit sink | Evidence and traceability |

Archive does **not** need: document content, grades, personal data, rendered templates, verification identities, certificate issuance state, lifecycle mutation methods or a second billing model.

#### 4.1.2 Existing surfaces and reuse analysis

| Capability | Existing surface | Reusable? | Assessment |
|---|---|---|---|
| Lifecycle read | `RecordLifecycleService::findBySource()` (school-scoped, returns model) | Partially | The query semantics are reusable; the return type is not. A contract projection is required. |
| Lifecycle read precedent | `AcademicPolicyQueryContract` (returns `AcademicPolicyReference` DTOs) | Yes, as a pattern | Strongest precedent: read-only, school-scoped, reference DTOs |
| Document read precedent | `AcademicDocumentVerificationContract::stateForDocument()` | Yes, as a pattern | Proves a narrow cross-domain read contract is acceptable |
| Document read for archive | None | No | Command-only service and verification-only contract are insufficient |
| Document file metadata | `GeneratedFile` model | Partially | Fields exist; direct model access is forbidden in Archive Domain/Application |
| Certificate/Transcript contract | `CertificateAndTranscript\AcademicCommunicationContract` | No | Command-oriented generation seam, not a general read |
| ReportCard contract | `ReportCard\AcademicCommunicationContract` | No | Command-oriented generation seam, not a general read |
| Quota | `plans.storage_limit` string, `subscriptions` rows | Partially | Data exists; no resolver or contract |
| Audit | `audit_logs`, `document_audits` | Partially | Tables and services exist; no archive event stream |
| Storage | `Storage::disk()` calls | No | No port/adapter; direct usage must not leak into Archive Domain/Application |

**Conclusion:** a new read contract is justified because no existing contract exposes an archive-safe projection, and the existing model return types would violate the dependency rule. Reuse the *pattern* of `AcademicPolicyQueryContract`, not its subject matter.

#### 4.1.3 Proposed minimum contract set

Design-level only. Names are proposals; the revised ADR must fix them before implementation starts.

| Contract | Direction | Proposed methods (semantics, not signatures) | Must not expose |
|---|---|---|---|
| `ArchivedLifecycleReadContract` | Archive -> SPR-014 | `findArchivedBySource(schoolId, sourceDomain, sourceType, sourceId, sourceVersion?)`; `listArchived(schoolId, filters, page)`; `findArchivedById(schoolId, lifecycleId)` | `RecordLifecycle` model, cross-school rows, mutation methods, raw query builder |
| `ArchiveDocumentReadContract` | Archive -> SPR-012 | `findDocument(schoolId, documentId)`; `listDocuments(schoolId, filters, page)`; `currentVersion(schoolId, documentId)` | AcademicDocument model, grades, content, personal data, mutation methods |
| `ArchiveFileReadContract` | Archive -> SPR-012 | `currentFile(schoolId, documentVersionId)`; `fileById(schoolId, generatedFileId)` | `GeneratedFile` model, raw storage handle, public URL |
| `StorageQuotaContract` | Archive -> SaaS/platform | `resolve(schoolId, onDate?)` returning limit, usage, mode, source | Pricing rules, billing logic, second quota table, writes |
| `ArchiveAuditContract` | Archive -> audit infrastructure | `append(event)`; `listForSubject(schoolId, subjectType, subjectId)` (optional) | Mutable rows, generic cleanup coupling, silent drops |
| `StoragePort` | Archive Domain -> infrastructure | `put`, `getStream`, `exists`, `delete`, `checksum`, `size` | Provider-specific types, credentials, public URLs |

**Binding rules for all contracts:**

- Every method is tenant-scoped by an explicit `schoolId`; no ambient tenant assumption in Domain code.
- Projections are immutable reference/value objects owned by the **providing** domain, not Eloquent models.
- No contract exposes a write path for `record_lifecycles`, `academic_documents`, `generated_files` or source state.
- No contract returns a public URL or a storage handle usable outside the port.
- Pagination is explicit and bounded; no unbounded "list all".
- Contract failures are typed and do not leak other tenants' existence.

#### 4.1.4 Projection fields

**Lifecycle projection (`ArchivedLifecycleReference`):** `lifecycleId`, `schoolId`, `sourceDomain`, `sourceType`, `sourceId`, `sourceVersion`, `state`, `academicContext` (only approved keys), `createdBy`, `originEvent`, `publishedAt`, `archivedAt`.

**Document projection (`ArchiveDocumentReference`):** `documentId`, `schoolId`, `documentType`, `documentNumber`, `status`, `currentVersionId`, `publishedAt`, `archivedAt`.

**File projection (`ArchiveFileReference`):** `generatedFileId`, `academicDocumentId`, `documentVersionId`, `disk`, `path`, `fileName`, `mimeType`, `sizeBytes`, `checksum`, `isCurrent`.

**Quota projection (`StorageQuotaReference`):** `schoolId`, `planId`, `planName`, `subscriptionId`, `limitBytes` (nullable), `usedBytes` (nullable), `enforcementMode` (`report_only` | `soft_limit` | `hard_limit`), `resolvedAt`, `reason` (for no-plan or invalid-unit cases).

#### 4.1.5 Closed ownership list

| Archive owns | Archive may read (contracts only) | Archive must never own or mutate |
|---|---|---|
| `archive_entries` (provenance + object pointer) | Lifecycle state and source identity | `record_lifecycles` |
| `archive_retention_policies` (mechanism, not legal values) | Document metadata and current version identity | `academic_documents`, `academic_document_versions` |
| `archive_events` (or an approved dedicated audit stream) | Current file pointer and integrity metadata | `generated_files` |
| Retention evaluation outcomes and dry-run reports | Tenant identity and authorization context | Approvals, signatures, verification identities |
| Package request/job metadata (deferred) | Plan/subscription quota projection | Certificates and transcripts |
| Archive operational configuration (no secrets) | Audit sink append capability | Source-domain state transitions |
| | | Backup/recovery pipeline |
| | | Legacy generated-document pipeline |

#### 4.1.6 Prohibited behaviours and imports

Archive must not:

- generate, render, number or publish documents;
- call `RecordLifecycleService::register()`, `publish()` or `archive()`;
- import `RecordLifecycle`, `AcademicDocument`, `AcademicDocumentVersion`, `GeneratedFile`, verification or certificate models into Domain/Application;
- own a second lifecycle state machine;
- own backup, restore or disaster recovery;
- decide academic validity, grades or personal data;
- expose a public or unauthenticated file URL;
- delete source records;
- revive `App\Models\GeneratedDocument`, `App\Models\DocumentTemplate`, `App\Services\DocumentGenerationService`, `App\Domains\Document`, DocumentCenter views or `pdf_enable_qr_code`.

**SPR-014 hardening note (O-2):** `ArchiveRecordLifecycleAction` is unscoped by school and skips policy when the actor is null. This is a source-domain defect carrier. Archive must never call it. A separate SPR-014 hardening task may be warranted; it is not part of SPR-017.

#### 4.1.7 Proposed archive metadata model

Design-level only; no migration is created.

**`archive_entries`**

| Column | Purpose |
|---|---|
| `id`, `uuid` | Internal and public identifiers |
| `school_id` | Tenant key; required and indexed |
| `source_domain`, `source_type`, `source_id`, `source_version` | Source identity and idempotency key |
| `record_lifecycle_id` | Link to SPR-014 envelope (no FK mutation) |
| `academic_document_id`, `document_version_id`, `generated_file_id` | Source references |
| `disk`, `path`, `size_bytes`, `checksum_sha256`, `mime_type` | Object pointer and integrity |
| `archived_at`, `archived_by` | Archive provenance |
| `retention_policy_id` | Matched policy (nullable) |
| `disposition_class` | `preserve` or `destroy` (from policy) |
| `retention_due_at` | Computed; nullable |
| `legal_hold`, `legal_hold_reason` | Overrides retention |
| `status` | `active`, `missing`, `quarantined`, `disposed` |
| `metadata` | Small JSON for non-critical context |
| `created_at`, `updated_at`, `deleted_at` | Audit and soft delete |

**Uniqueness:** `(school_id, source_domain, source_type, source_id, source_version)` makes archive entry idempotent. Re-running entry creation must not duplicate rows or overwrite provenance.

**Immutability:** official archived objects are application-immutable in the first release. Metadata status transitions are audited. Physical deletion is not part of the first release.

**Tenant deletion:** unlike `generated_files` (cascade on tenant delete), archive metadata must not cascade blindly. The offboarding/retention decision (DR-09/DR-10) must define suspend, retain, export or dispose behaviour. This is an explicit design constraint derived from EV-12 and BP-E-60.

### 4.2 Storage port and adapter boundary

#### 4.2.1 Current state

- Local disk root: `storage_path('app/private')` (EV-04).
- Public disk root: `storage_path('app/public')` (EV-05).
- Tenant suffixing: `local` and `public` (EV-07); `s3` commented at `config/tenancy.php:142`.
- Document disk switch: `config/nexus-academic-communication.php:25` (EV-08).
- No storage abstraction: direct `Storage::disk()` calls (review 5.B).
- S3 is defined but unused (EV-06).

**Classification:** local-first storage **VERIFIED IMPLEMENTED**; provider-neutral abstraction **NOT IMPLEMENTED**; cloud activation **NOT IMPLEMENTED**.

#### 4.2.2 Proposed `StoragePort`

| Operation | Semantics | Notes |
|---|---|---|
| `put(disk, path, stream, options)` | Write a new object | Reject overwrite of an existing archive path by default |
| `getStream(disk, path)` | Read a stream | No public URL; caller must have authorization |
| `exists(disk, path)` | Existence check | Used before metadata reconciliation |
| `delete(disk, path)` | Delete an object | Disabled in first release; separate gated capability |
| `checksum(disk, path, algorithm)` | Compute digest | SHA-256 default; used for verification |
| `size(disk, path)` | Object size | Compare with stored metadata |

**Rules:**

- Domain and Application code depend on `StoragePort`, never on `Storage::disk()` or a provider SDK.
- The port receives a logical disk name from an allow-list (`local`, and later an approved cloud disk). It must never accept a raw path from an HTTP request.
- The first adapter is `LocalStorageAdapter` on the existing tenant-suffixed private disk.
- A future `S3CompatibleStorageAdapter` is optional and gated by DR-20; the port is not an S3 commitment.
- No credentials, bucket names, regions or endpoints are introduced by the ADR.

#### 4.2.3 Proposed archive path scheme

`archive/{school_id}/{document_type}/{academic_year}/{document_number}-v{n}.pdf`

- `school_id` comes from the resolved tenant, not user input.
- `document_number` and `document_type` are validated against the document projection.
- Path components are normalized and rejected if they contain traversal sequences, absolute paths or unexpected separators.
- The path is a partition key, never an authorization proof.

#### 4.2.4 Consistency and failure behaviour

- Metadata write and object write are not a single ACID transaction across storage and database. Use: write object -> compute checksum -> persist metadata in a transaction -> mark entry `active`. If metadata persistence fails, the orphan object is recorded for cleanup, not silently ignored.
- If the object is missing at read time, mark the entry `missing`, audit the event and fail closed.
- If the checksum mismatches, mark `quarantined`, audit and refuse download.
- Disk unavailable or full: fail the operation with a typed error; never produce a partial success entry.
- Soft-delete metadata only in the first release; no physical deletion.

### 4.3 Storage quota architecture

#### 4.3.1 Existing versus proposed

| Layer | Existing | Proposed | Classification |
|---|---|---|---|
| Plan limit data | `plans.storage_limit` string, seeded values | Keep as authoritative plan field | **VERIFIED IMPLEMENTED** |
| Unit parsing | None | Strict parser to bytes; binary/decimal decided | **PROPOSED** |
| School -> plan resolution | `subscriptions` table only | Deterministic resolver contract | **PROPOSED** |
| Usage measurement | No aggregation; `generated_files.size_bytes` per file | Report-only projection first | **PROPOSED** |
| Enforcement | None | Deferred until ODQ; `report_only` first | **PROPOSED** |
| Pricing/paid extension | None | Out of scope; Product/Finance | **NOT IMPLEMENTED** |
| Second quota authority | None (correct) | Prohibited | **CONSTRAINT** |

#### 4.3.2 Unit definition

- Canonical internal unit: **bytes** (unsigned integer, 64-bit).
- Parser accepts `B`, `KB`, `MB`, `GB`, `TB`, case-insensitive, optional whitespace.
- The ADR must choose **binary (1024)** or **decimal (1000)** and state it explicitly. This blueprint does not choose; Product/Architecture must (DR-06).
- Invalid, negative, empty or over-precision values fail validation at plan write time and fail closed at read time.
- Display formatting is a presentation concern; it must not change the canonical value.
- Numeric plan limits should eventually be stored as bytes or a validated value object; the string column can remain for compatibility if the parser is authoritative.

#### 4.3.3 School-level quota

- Quota is always resolved per school.
- Plan rows are global; the school's active subscription supplies the plan.
- Archive stores no quota value and no usage ledger.
- A school with no plan/subscription receives `limitBytes = null` and `enforcementMode = report_only` with an explicit `reason`. It must not be treated as unlimited by accident; the null must be visible and audited in quota reports.

#### 4.3.4 Subscription resolution

Proposed deterministic algorithm (to be confirmed by Product/Architecture):

1. Select subscriptions for the school where `status = active` and `starts_at <= onDate` and (`expires_at` is null or `expires_at >= onDate`).
2. If more than one, choose the latest `starts_at`; tie-break on latest `expires_at`, then latest creation timestamp. Record the tie-break in the audit/report.
3. If none active, select the most recent expired subscription for **report-only visibility** and mark `expired`.
4. If none exists, return `limitBytes = null`, `usedBytes` as computed or null, `enforcementMode = report_only`, `reason = no_subscription`.
5. Suspended/cancelled subscriptions never grant capacity; they may still be reported.
6. The resolver is owned by the SaaS/platform layer, not Archive.
7. A future change to subscription semantics must update the contract, not Archive code.

#### 4.3.5 Usage measurement

First release is **report-only**:

- `used_bytes` may be `null` when metering does not exist.
- If computed, the minimum inputs are `generated_files.size_bytes` for the school plus archive object sizes.
- Deduplication rule must be explicit before metering: count each physical byte once per school, using a stable identity (source file id + archive entry id), or count archive copies separately if Product decides archiving consumes quota.
- No blocking of archive operations in the first release.
- Usage reporting must be tenant-scoped and must not leak another school's totals.

#### 4.3.6 Enforcement points (future, after ODQ)

Candidate enforcement points, to be decided:

- archive entry creation (the primary new write path);
- document generation (existing SPR-012 write path, outside Archive);
- manual administrative upload (if one is ever added);
- restore/export package generation (deferred).

Archive must not become the enforcement authority for the whole platform. If enforcement is added, it belongs to the platform/SaaS layer and Archive calls the contract before accepting a new object. The contract must define a consistent failure mode (reject, queue, or warn) and race handling.

#### 4.3.7 Plan changes and over-quota behaviour

- Upgrade: immediate; new limit applies from the confirmed change date; no data deletion.
- Downgrade: applies at the documented effective date; existing usage may exceed the new limit. The school becomes over-quota but data is not deleted automatically.
- Expiry/suspension: reads remain available per the subscription policy; new archive writes may be blocked only if Product chooses hard enforcement.
- Over-quota first-release behaviour: report and notify; do not block.
- Over-quota future behaviour: decide `soft_limit` (warn and allow) versus `hard_limit` (reject new writes) per operation. Reads and legal/retention operations must never be blocked by quota.
- Paid extension: Product/Finance decision; not an Archive feature.

#### 4.3.8 Concurrent uploads and consistency

- Usage is eventually consistent unless a reservation mechanism is added.
- If hard enforcement is later required, use an atomic reservation/ledger in the SaaS layer (`reserve`, `commit`, `release`) or a database transaction with row locking. Do not rely on a read-then-write check in Archive.
- Archive entry creation must be idempotent, so a retry after a quota race cannot create duplicates.
- A concurrent upload may temporarily exceed the limit under report-only; this is acceptable and must be visible.
- The ADR must state the consistency model; it must not claim precise real-time enforcement.

#### 4.3.9 First-release quota summary

| Behaviour | First release | After ODQ |
|---|---|---|
| Resolve plan limit | Yes (contract) | Yes |
| Report usage | Yes, if available; nullable | Yes |
| Block archive writes | No | Per Product decision |
| Paid extension | No | Per Product/Finance |
| Second quota table | No | No |
| Quota values in Archive | No | No |

### 4.4 Retention architecture

#### 4.4.1 Design principles

1. The **mechanism** is architecture; the **values** are Product/Legal.
2. No statutory duration is invented or implied.
3. No policy means **preserve indefinitely**, never destroy.
4. Permanent preservation and secure destruction are distinct disposition classes.
5. Destruction is disabled by default and is not implemented in the first release.
6. Every policy match, evaluation, extension, hold and disposition is audited.
7. A policy change must not retroactively destroy records without an explicit, audited migration decision.

#### 4.4.2 Document categories and scope

Retention policies must be selectable by:

- `school_id` (nullable = platform default);
- `document_type` (nullable = all types);
- `jurisdiction` (nullable; required before a `destroy` policy is activated);
- optional `academic_context` filters only if Product requires them (not assumed).

**Suggested category axis (for Product/Legal confirmation, not a decision):**

| Category | Examples in repository | Notes |
|---|---|---|
| Official academic document | `AcademicDocument` types generated by SPR-012 | Numbered, published, immutable |
| Report card / transcript / certificate | SPR-012 / ReportCard / CertificateAndTranscript outputs | May share document type or be separate |
| Class council record | ClassCouncil session outputs | Academic governance record |
| Administrative record | Finance, HR, library, inventory (future) | Not in SPR-017 first release |
| Operational audit | Generic platform logs | Retention class separate from records |

The ADR must not hard-code this list; it establishes that categories are data, not enums in code.

#### 4.4.3 Policy schema (proposed)

**`archive_retention_policies`**

| Column | Notes |
|---|---|
| `id` | Primary key |
| `school_id` | Nullable = platform default |
| `document_type` | Nullable = all types |
| `jurisdiction` | Nullable; required for `destroy` |
| `retention_months` | Nullable = indefinite |
| `disposition` | `preserve` or `destroy` |
| `legal_basis` | Required for `destroy`; text |
| `is_active` | Boolean |
| `effective_from` | Date/time |
| `created_by` | Actor |
| `created_at`, `updated_at` | Audit |

**Precedence (most specific wins):**

1. school + document type
2. school + all types
3. platform + document type
4. platform + all types

If two policies at the same specificity conflict, the most recent `effective_from` wins and the conflict is audited. If no policy matches, `preserve` with `retention_months = null`.

**Clock start:** default `archive_entries.archived_at`; optional `retention_starts_at` override. The ADR must state the default and the allowed overrides. Publication and generation dates are inputs to policy matching, not the default clock.

**Evaluation triggers:** on archive entry; scheduled periodic evaluation; manual dry-run. Evaluation is idempotent and records policy id, computed due date, outcome and actor.

#### 4.4.4 Legal hold

- `legal_hold` boolean plus `legal_hold_reason`, `placed_by`, `placed_at`, `released_by`, `released_at`.
- A legal hold always wins over retention and destruction.
- Only an authorized role may place or release a hold; both actions are audited.
- Hold release does not automatically destroy; it returns the record to normal evaluation.
- The ADR must define whether holds are per entry, per source record or per school; the proposed default is per entry, with an optional school-wide hold if Legal requires it.

#### 4.4.5 Eligibility, destruction authorization and permanent deletion

**Eligibility for archival:** a record is eligible when the lifecycle read returns `RecordLifecycleState::Archived`, the source document status is consistent, and the tenant is resolved. Divergence between lifecycle and document status is an auditable exception, not a silent archive.

**Eligibility for destruction:** a record is eligible only when:

1. a matching policy has `disposition = destroy` and a legal basis;
2. the retention period has elapsed;
3. no legal hold is active;
4. a dry-run has been produced and reviewed;
5. an approval record exists;
6. the operator holds `archive.destroy`;
7. the two-person rule is satisfied if Security requires it;
8. the deletion certificate is generated and audited.

**Destruction authorization:** a workflow, not a flag. The ADR must define the approval record (who, what scope, why, evidence, expiry) and the separation of duties.

**Permanent deletion:** first release performs no physical deletion. A later release may implement it only after ODL/ODD and Product approval. Metadata soft-delete alone is not destruction. Backup copies must not be used to resurrect destroyed records without an approved restore procedure.

#### 4.4.6 Values requiring Product and Legal approval

The following require explicit, recorded approval. **None is set by this blueprint.**

| Value / rule | Owner | Why |
|---|---|---|
| Retention duration per document type | Product + Legal | Statutory and policy obligation |
| Jurisdiction applicability | Product + Legal + Privacy | Cross-border and local law |
| Legal basis for destruction | Legal/Compliance | Lawfulness |
| Permanent preservation categories | Product + Legal | Records-management policy |
| Legal-hold authority and release | Legal/Compliance | Litigation/investigation control |
| Destruction approval matrix and two-person rule | Product + Security + Operations + Legal | Safeguard design |
| School-level override rights | Product + Legal | Contractual and legal constraints |
| Offboarding/school-closure behaviour | Product + Legal + Operations | Retention and erasure duties |

#### 4.4.7 Audit evidence for retention

Every evaluation records: policy id, scope, matched rule, clock start, computed due date, outcome (`preserve`, `due`, `held`, `destroy_approved`, `destroyed`, `error`), actor/system, timestamp and correlation id. Every policy change records old and new values. Every hold records placement/release. Every destruction records a certificate and the approval reference.

### 4.5 Audit architecture

#### 4.5.1 Existing categories and the contradiction

See section 3.5 for the evidence. The ADR must **classify** audit rows before setting retention. It must not assume `audit_logs` is a records system of record.

#### 4.5.2 Proposed retention classes

| Class | Examples | Proposed retention owner | Store |
|---|---|---|---|
| `operational` | health checks, routine activity feed | Operations | Existing logs / `system_health` |
| `security` | login, access denial, permission change, super-admin break-glass | Security | `audit_logs` or dedicated security stream |
| `records` | archive entry, retention evaluation, hold, disposition, restoration | Legal/Compliance + Product | Dedicated append-only archive stream |
| `compliance` | destruction certificate, legal-hold placement/release, approval record | Legal/Compliance | Dedicated append-only archive stream |

The **durations** are not set here. The ADR must record who owns each class and require the duration to be configured, not hard-coded. The generic 90-day cleanup may continue to apply only to classes it is authorized to delete.

#### 4.5.3 Archive event catalogue (proposed)

| Event | Trigger | Minimum payload | Class |
|---|---|---|---|
| `archive.uploaded` | An object is accepted into the archive | school, entry, source, actor, size, checksum, outcome | records |
| `archive.downloaded` | Authorized download succeeds | school, entry, actor, IP/UA, checksum verified, outcome | records/security |
| `archive.access_denied` | Wrong tenant, missing permission, missing object, path rejection | school, requested id/path hash, actor, reason, IP/UA | security |
| `archive.metadata_modified` | Archive metadata or provenance changes | school, entry, actor, old/new values | records |
| `archive.retention_policy_modified` | Policy created/updated/deactivated | school/default, policy, actor, old/new values | compliance |
| `archive.archived` | Entry created from an archived lifecycle record | school, entry, lifecycle id, source identity | records |
| `archive.restored` | Entry or source restored under an approved procedure | school, entry, actor, reason, approval | records |
| `archive.deletion_requested` | Destruction workflow starts | school, entry, requester, reason, approval | compliance |
| `archive.destroyed` | Physical or logical destruction completes | school, entry, operator, certificate id | compliance |
| `archive.quota_evaluated` | Quota projection read/evaluated | school, plan, limit, used, mode, outcome | records |
| `archive.quota_exceeded` | A quota threshold or limit is hit | school, plan, usage, threshold, action taken | records/security |
| `archive.integrity_failed` | Checksum mismatch or missing object | school, entry, expected/actual, action | security |
| `archive.admin_action` | Any privileged archive administration | school, actor, action, scope, result | security |
| `archive.export_requested` / `archive.export_completed` | Package request and delivery (deferred) | school, requester, manifest hash, expiry | compliance |

**Rules:** append-only; actor + timestamp + school + correlation id; no silent success on failure; no event may contain grades, content or unnecessary personal data; denial events must not leak whether another tenant's object exists.

#### 4.5.4 Storage strategy decision (DR-12/DR-13)

Options:

1. Dedicated `archive_events` append-only table with its own retention and no generic cleanup. **Proposed default.**
2. `audit_logs` with a `retention_class` column and a cleanup exemption. Simpler, but requires modifying existing cleanup behaviour and risks accidental deletion.
3. Existing `document_audits` extended. Attractive because it is already append-only, but it is owned by SPR-012 and cascades on document deletion; using it for retention evidence couples Archive to a mutable source lifecycle. **Not recommended without an ownership change.**

The ADR must choose one and state the retention duration owner. This blueprint does not modify any audit table or cleanup job.

### 4.6 Storage security

#### 4.6.1 Control matrix

| Control | Current state | Classification | Required decision |
|---|---|---|---|
| Tenant scoping | `HasSchoolId`, `scopeForSchool`, school-aware policies | **VERIFIED IMPLEMENTED** (pattern) | DR-14, DR-15 |
| Archive authorization | No `archive.*` group or policy | **NOT IMPLEMENTED** | DR-14 |
| Document authorization | `HandlesDocumentAuthorization` with `document.*` | **VERIFIED IMPLEMENTED** (not reusable for archive) | DR-14 |
| Private storage | `local` root `storage_path('app/private')` | **VERIFIED IMPLEMENTED** | DR-15 |
| Public exposure | `public` disk exists; no archive route | **NOT APPLICABLE** today; must stay closed | DR-15 |
| Integrity | SHA-256 written; no verification on read | **PARTIALLY IMPLEMENTED** | DR-15 |
| Encryption at rest | Plain local disk; DB `encrypt` commented; no `Crypt` usage | **NOT IMPLEMENTED** (app), **UNVERIFIED** (host) | DR-16, DR-17 |
| Key management | No key service | **NOT IMPLEMENTED** | DR-17 (local), DR-20 (cloud) |
| Audit log | Generic + document audit exist; no archive events | **PARTIALLY IMPLEMENTED** | DR-12, DR-13 |
| Deletion controls | Lifecycle/document delete denied; no archive path | **PARTIALLY IMPLEMENTED** | DR-11 |
| Rate limiting | Verification routes have a limiter; no archive routes | **NOT IMPLEMENTED** for archive | DR-15 |
| Super-admin bypass | Platform-wide pattern | **VERIFIED IMPLEMENTED**; archive intent unstated | DR-26 |
| Path canonicalization | No archive path logic | **NOT IMPLEMENTED** | DR-15 |
| Cross-tenant negative tests | No archive tests | **NOT IMPLEMENTED** | DR-15 |

#### 4.6.2 Permission model (proposed)

| Permission | Capability | Notes |
|---|---|---|
| `archive.view` | List and read archive metadata | School-scoped |
| `archive.download` | Download an authorized object | Separate from view if Security prefers |
| `archive.manage` | Create/reconcile archive entries, manage metadata | No source mutation |
| `archive.retention.manage` | Create/update retention policies | Requires compliance training/role |
| `archive.export` | Request/download packages (deferred) | High-risk capability |
| `archive.destroy` | Approve/execute destruction (deferred) | Highest-risk; two-person rule |

Add an `Archive` case to `PermissionGroup` and seed permissions through the existing permission mechanism. Do not reuse `document.*`. Policies must deny `delete`, `restore` and `forceDelete` except through the explicit destruction workflow (which is off in first release).

#### 4.6.3 Download authorization pipeline (proposed)

1. Resolve tenant from the authenticated request.
2. Authorize `archive.view`/`archive.download` for the school.
3. Load the archive entry by `school_id` + id (never by path).
4. Validate `disk` against the allow-list and canonicalize `path`.
5. Verify the object exists and the checksum matches.
6. Stream with `Content-Disposition: attachment`, a safe content type and no inline rendering.
7. Audit success or failure.
8. Return a uniform 404 for wrong tenant, missing object or unauthorized id (no existence leak).
9. Apply rate limiting and, if needed, a short-lived signed URL only after authorization; the URL must be audited and expire quickly.

#### 4.6.4 Integrity

- SHA-256 at archive entry; compare with the source `generated_files.checksum` when available.
- Verify before every download.
- Scheduled integrity job across active entries; quarantine on mismatch.
- Audit `archive.integrity_failed` and surface it to operations.
- Never silently serve a different file or a stale version.

#### 4.6.5 Encryption at rest and key management

**Truthful current state:** no application-level encryption at rest is implemented for local archive objects. `config/database.php:113` has `// 'encrypt' => ...` commented. No `Crypt`/`Encrypter`/`openssl_encrypt` usage was found in application code. Host or volume encryption is **UNVERIFIED**.

**Decision required (DR-16):** state whether the first release:

- (a) requires application-level envelope encryption for archive objects, with keys managed outside the repository; or
- (b) accepts the risk with documented infrastructure controls and a production gate; or
- (c) requires host/volume encryption evidenced by Operations.

**Do not claim encryption until the chosen control is evidenced.** Cloud key management and rotation (platform-held versus school-held, tenant key separation) remain ODC2 and block cloud activation.

### 4.7 Backup and recovery

#### 4.7.1 Documented intent versus implementation

| Aspect | `Backups.md` intent | Repository implementation | Assessment |
|---|---|---|---|
| Scope | PostgreSQL, uploaded files, school documents, config, Docker volumes, logs | `BackupService` writes a `.sql` file containing table comments and placeholder `CREATE TABLE` statements; no data dump, no file copy | **PARTIALLY IMPLEMENTED / OVERSTATED** |
| Schedule | Daily incremental, weekly full, monthly archive | No `system:backup` schedule in `bootstrap/app.php` | **NOT IMPLEMENTED** |
| Encryption | All backups encrypted before storage | No encryption in `BackupService` | **NOT IMPLEMENTED** |
| Retention | 30d / 12w / 12m | No retention job found | **NOT IMPLEMENTED** |
| Off-site | At least one copy off-site | Backup path `storage/app/backups`, `disk = local` | **NOT IMPLEMENTED** |
| Restore | Verify restore capability quarterly | `restore()` only dispatches `BackupRestored`; no restore execution | **NOT IMPLEMENTED** |
| RTO/RPO | < 2h / < 15m | No measurement | **UNVERIFIED** |
| Audit | All backup operations audited | Events exist; audit persistence unverified | **PARTIALLY IMPLEMENTED** |
| Engine | PostgreSQL | `PROJECT_STATE` declares MySQL | **CONFLICT** |

#### 4.7.2 Required direction

- Archive and backup remain separate pipelines (ADR-024 Decision 9).
- Archived objects and archive metadata must be explicitly included in backup scope.
- A restore drill including at least one archived object is required before production.
- Backup encryption must be evidenced, not assumed.
- The documented RTO/RPO must be measured or restated.
- Backup retention is not archival retention; deleting a backup must not be presented as destruction.
- No backup schedule or snapshot machinery is created by SPR-017.

**Classification:** backup/archive separation **PROPOSED**; archive backup coverage **NOT IMPLEMENTED**; restore **UNVERIFIED**.

### 4.8 Data residency and cloud activation

#### 4.8.1 Residency position

- Local-first is a **technical** property, not a legal conclusion.
- The repository does not record production or staging hosting location.
- No data-protection impact assessment, DPA, sub-processor list or country-specific residency requirement was found.
- No cloud provider, region, credential, bucket or IAM policy is configured.

**Classification:** **UNVERIFIED** for hosting and legal position; **NOT IMPLEMENTED** for cloud.

#### 4.8.2 Required residency record before production

- Actual hosting location(s) for application, database, object storage and backups.
- Controller/processor roles and any DPA/sub-processor obligations.
- Whether any cross-border transfer occurs.
- The jurisdiction(s) used by retention policy.
- A statement of what compliance is **not** claimed.

#### 4.8.3 Cloud activation gate (proposed, preserving ADR-024's five items)

Cloud storage may be enabled only when **all** are recorded:

1. Product approval of provider and region (data residency).
2. Architecture approval of the provider-neutral port and the tenant-isolation design for shared objects.
3. Security approval of encryption key management and rotation.
4. An approved SLO/availability/durability/cost baseline.
5. Operations confirmation of tenant-aware cloud disk configuration and a migration/rollback plan.

Additional proposed gates:

6. Legal/Privacy approval of the residency record, DPA and sub-processors.
7. A tested adapter conformance suite (same contract, local and cloud).
8. A data migration plan that preserves checksums and tenant isolation.
9. An incident/rollback plan that can return to local storage.
10. Evidence that no cloud credentials are committed.

Until then, "cloud archive" is an abstract provider-neutral capability only.

### 4.9 Explicitly out of scope for the proposed direction

- A second lifecycle engine or lifecycle state extension (`read_only`, `cold_storage`) - deferred to ODC/DR-23.
- Document generation, rendering, numbering or publication.
- Backup, snapshot or disaster recovery implementation.
- Legal retention periods or legal conclusions.
- Cloud provider selection, region, credentials, SDK, IAM or infrastructure.
- Package format, contents, size, expiry, delivery or UI (deferred to ODP/DR-24).
- Any change to source domains, existing migrations, models, routes or config.
- Revival of the legacy document pipeline.

## 5. Decision Register with Owners

### 5.1 How to read the register

- **Owner** is a role, not a named person. The applicable governance process assigns names.
- **Proposed direction** is architecture input, not a decision made by this blueprint.
- **Priority** uses one of the four required classifications. Where a topic needs both an acceptance-level decision and a production-level decision, it is split into separate rows so every row has one classification.
- A decision is not closed until the owner records it in an authoritative document (revised ADR, policy record, or signed operational decision). This blueprint records none of them as closed.

### 5.2 Decision register

| ID | Decision | Proposed direction | Owner | Priority |
|---|---|---|---|---|
| **DR-01** | Lifecycle read contract | Add a read-only `ArchivedLifecycleReadContract` owned by SPR-014, returning tenant-scoped immutable projections; no model leakage; no mutation methods | Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-02** | Document and file read contracts | Add `ArchiveDocumentReadContract` and `ArchiveFileReadContract` (or one contract with two projections) owned by SPR-012; metadata only, no content/grades/personal data | Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-03** | Archive ownership and prohibited imports | Adopt the closed ownership list in section 4.1.5 and encode prohibited imports/behaviours as architecture-test requirements | Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-04** | Archive provenance and metadata model | Adopt the `archive_entries` design, idempotency key, immutability, soft-delete and tenant-deletion semantics in section 4.1.7 | Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-05** | Storage port and adapter boundary | Provider-neutral `StoragePort` with a local adapter first; S3-compatible adapter optional and gated; no provider types in Domain/Application | Architecture Team + Security Lead | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-06** | Quota contract, units and subscription resolution | `StorageQuotaContract`; canonical bytes; explicit binary/decimal choice; deterministic active-subscription resolver; no second quota authority | Architecture Team + Product Owner | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-07** | Quota first-release behaviour | `report_only`: resolve and report, nullable usage, never block archive operations; no quota values stored in Archive | Product Owner + Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-08** | Quota metering, enforcement and paid extensions | Define usage aggregation, deduplication, thresholds, hard/soft enforcement, over-quota behaviour and commercial rules | Product Owner + Architecture Team + Finance (if paid) | **MUST BE DECIDED BEFORE PRODUCTION** |
| **DR-09** | Retention mechanism | Adopt the policy schema, precedence, clock, legal hold, evaluation, dry-run, disposition and audit semantics in section 4.4; no legal values | Architecture Team + Legal/Compliance | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-10** | Retention values and jurisdiction | Approve retention periods, jurisdiction applicability, legal basis and permanent-preservation categories per document type | Product Owner + Legal/Compliance | **MUST BE DECIDED BEFORE PRODUCTION** |
| **DR-11** | Destruction authorization and safeguards | Approve the approval matrix, two-person rule, dry-run evidence, permission and deletion certificate; destruction remains off in first release | Product Owner + Security Lead + Operations Lead + Legal/Compliance | **MUST BE DECIDED BEFORE PRODUCTION** |
| **DR-12** | Archive audit event catalogue and store | Adopt the catalogue in section 4.5.3 and choose a dedicated append-only store (proposed) versus an exempted generic stream | Architecture Team + Security Lead | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-13** | Audit retention classes and cleanup reconciliation | Define operational/security/records/compliance classes, owners and durations; resolve the 7-year versus 90-day contradiction; do not rely on generic cleanup for records evidence | Security Lead + Legal/Compliance + Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-14** | Archive permissions, roles and policies | Add an `archive` permission group (`view`, `download`, `manage`, `retention.manage`, `export`, `destroy`) and school-scoped policies; do not reuse `document.*` | Security Lead + Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-15** | Local storage security | Private disk only; authorized streaming; tenant resolution; path canonicalization/allow-list; uniform 404; rate limits; checksum verification; negative tests | Security Lead + Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-16** | At-rest encryption posture for local storage | State the truthful current state and require an explicit choice: application-level encryption, evidenced host/volume encryption, or accepted risk with controls | Security Lead + Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-17** | At-rest encryption implementation and key management (local) | Implement the chosen control, key custody/rotation and evidence before production | Security Lead + Operations Lead | **MUST BE DECIDED BEFORE PRODUCTION** |
| **DR-18** | Backup and recovery including archive objects | Add archive paths/metadata to backup scope; evidence encryption; perform and record a restore drill including an archived object; measure or restate RTO/RPO | Operations Lead | **MUST BE DECIDED BEFORE PRODUCTION** |
| **DR-19** | Backup versus archive separation | Keep the pipelines separate; archive is not backup; backup retention is not archival retention; archived objects remain in backup scope | Architecture Team + Operations Lead | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-20** | Cloud activation gate | Require all five ADR-024 gates plus residency/DPA, adapter conformance, migration/rollback, incident plan and credential hygiene | Product Owner + Architecture Team + Security Lead + Operations Lead | **MUST BE DECIDED BEFORE CLOUD ACTIVATION** |
| **DR-21** | Production hosting location and residency record | Record actual hosting locations and whether cross-border transfer occurs; state that local-first is not a legal compliance claim | Product Owner + Legal/Compliance + Operations Lead | **MUST BE DECIDED BEFORE PRODUCTION** |
| **DR-22** | Cloud data residency, DPA and controller/processor roles | Approve provider/region, DPA, sub-processor list and controller/processor model before any cloud use | Product Owner + Legal/Compliance + Privacy/DPO + Architecture Team | **MUST BE DECIDED BEFORE CLOUD ACTIVATION** |
| **DR-23** | `read_only` / `cold_storage` lifecycle states | Defer; keep `archived` as the trigger; any state extension requires a lifecycle-authority amendment or successor ADR | Architecture Team + Product Owner | **MAY BE DEFERRED** |
| **DR-24** | Downloadable archive package specifics | Defer format, contents, size, expiry, requesters and delivery; keep the security boundary (one school per package, manifest, expiry, audit) in the ADR | Product Owner + Architecture Team | **MAY BE DEFERRED** |
| **DR-25** | Cloud SLO/durability/cost baseline | Defer until cloud activation is proposed; then require an approved baseline before any claim | Product Owner + Operations Lead + Architecture Team | **MUST BE DECIDED BEFORE CLOUD ACTIVATION** |
| **DR-26** | Super-admin break-glass for archive | Define whether super-admin can read/export across schools, under what approval, and require audit; no silent bypass | Security Lead + Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-27** | Decision log reconciliation and stale evidence | Make the revised ADR the single authoritative decision log; correct the coefficient note and S3 line reference; reconcile with the SPR-017 board report | Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-28** | SPR-014 interlock gate | State that SPR-017 implementation is gated on a recorded SPR-014 closure decision or an explicit owner-signed waiver; acceptance of ADR-024 does not close SPR-014 | Product Owner + Architecture Team (+ Operations Lead for staging) | **MUST DECIDE BEFORE ADR ACCEPTANCE** |
| **DR-29** | Testable acceptance criteria and test plan | Adopt section 9 criteria and the SPR-017 test/DoD requirements; require architecture, security, integration and operations tests | Architecture Team | **MUST DECIDE BEFORE ADR ACCEPTANCE** |

### 5.3 Decisions by classification

#### MUST DECIDE BEFORE ADR ACCEPTANCE

| ID | Decision | Why it blocks acceptance |
|---|---|---|
| DR-01 | Lifecycle read contract | The named contract does not exist; implementation would invent it |
| DR-02 | Document/file read contracts | No archive-safe projection exists; model imports would violate C1 |
| DR-03 | Ownership and prohibited imports | Without a closed list, scope and duplication cannot be tested |
| DR-04 | Metadata model and idempotency | No implementable schema or invariant set |
| DR-05 | Storage port and adapter | The current S3-mandatory wording over-constrains before a provider decision |
| DR-06 | Quota contract/units/resolution | The column is not a mechanism; resolution and units are undefined |
| DR-07 | Quota first-release behaviour | Otherwise the ADR implies enforcement it cannot deliver |
| DR-09 | Retention mechanism | "Configurable" is not a design |
| DR-12 | Archive audit catalogue/store | No archive audit integration exists |
| DR-13 | Audit retention classes | The 7-year versus 90-day contradiction must be resolved before audit is used as evidence |
| DR-14 | Archive permissions/policies | No archive authorization surface exists |
| DR-15 | Local storage security/download design | Access control, integrity and failure behaviour are unspecified |
| DR-16 | At-rest encryption posture | The ADR currently implies a control that is not evidenced |
| DR-19 | Backup/archive separation | Must remain an explicit invariant in the accepted ADR |
| DR-26 | Super-admin break-glass | Cross-tenant privileged access must be intentional and audited |
| DR-27 | Decision log/stale evidence | The authoritative record must be unambiguous |
| DR-28 | SPR-014 interlock gate | The ADR must make the implementation gate explicit |
| DR-29 | Testable acceptance criteria | Current criteria cannot be verified |

#### MAY BE DEFERRED

| ID | Decision | Deferral condition |
|---|---|---|
| DR-23 | `read_only` / `cold_storage` states | Additive; separate lifecycle-authority amendment |
| DR-24 | Package specifics | Deferred release; security boundary stays in the ADR |

#### MUST BE DECIDED BEFORE PRODUCTION

| ID | Decision | Why production blocks |
|---|---|---|
| DR-08 | Metering/enforcement/paid extensions | Cannot enforce quotas or bill without approved semantics |
| DR-10 | Retention values and jurisdiction | Cannot enforce or claim retention without law |
| DR-11 | Destruction safeguards | Cannot enable deletion safely |
| DR-17 | At-rest encryption implementation/keys | Protects archived records at rest |
| DR-18 | Backup/recovery including archive | Prevents an unrecoverable archive |
| DR-21 | Hosting/residency record | Prevents an unknown or unlawful deployment location |

#### MUST BE DECIDED BEFORE CLOUD ACTIVATION

| ID | Decision | Why cloud blocks |
|---|---|---|
| DR-20 | Cloud activation gate | No cloud without provider, security, operations and legal readiness |
| DR-22 | Cloud residency/DPA/controller-processor | Prevents unlawful transfer and unclear responsibility |
| DR-25 | Cloud SLO/durability/cost baseline | Prevents unsupported availability/durability/cost claims |

### 5.4 Human approval matrix

| Approver role | Decisions they own or must sign | What they must not delegate to the architecture team |
|---|---|---|
| **Product Owner** | DR-07, DR-08, DR-10 (with Legal), DR-11 (with Security/Ops/Legal), DR-20, DR-21, DR-22, DR-24, DR-28 | Scope, pricing, retention values, package policy, cloud business case |
| **Architecture Team** | DR-01 to DR-05, DR-06 (with Product), DR-09 (with Legal), DR-12, DR-13 (with Security/Legal), DR-14 (with Security), DR-15 (with Security), DR-16 (with Security), DR-19, DR-23, DR-26 (with Security), DR-27, DR-29 | Contract shapes, boundaries, data model, storage seam |
| **Security Lead** | DR-05, DR-11, DR-12, DR-13, DR-14, DR-15, DR-16, DR-17, DR-20, DR-26 | Permissions, encryption, threat model, break-glass |
| **Operations Lead** | DR-11, DR-17, DR-18, DR-20, DR-21, DR-25 | Backup/restore evidence, hosting, staging, SLO measurement |
| **Legal/Compliance** | DR-09 (mechanism review), DR-10, DR-11, DR-13, DR-21, DR-22 | Retention law, legal basis, holds, destruction, residency, DPA |
| **Privacy/DPO** (if appointed) | DR-10, DR-21, DR-22 | Controller/processor, DPA, cross-border transfer, data-subject obligations |
| **Finance** (if paid extensions) | DR-08 | Pricing, billing, grace, commercial rules |

**No approval is granted by this blueprint.** A row in this table is a responsibility, not a signature.

## 6. Dependency Map

### 6.1 Legend

| Tag | Meaning |
|---|---|
| **[ARCH]** | Architectural decision or boundary |
| **[TECH]** | Technical implementation, schema, contract or test |
| **[OPS]** | Operational process, environment, backup, staging or runbook |
| **[SEC]** | Security control, authorization, encryption or threat model |
| **[LEGAL]** | Legal, compliance, privacy or data-residency obligation |

Arrows mean "is a prerequisite for". An item is not ready until every upstream item is satisfied or an owner records an explicit waiver.

### 6.2 Prerequisites for ADR-024 acceptance

```text
[ARCH] DR-27 decision-log reconciliation
   |
[ARCH] DR-03 ownership list ----+
[ARCH] DR-01 lifecycle contract |
[ARCH] DR-02 document contracts +--> [ARCH] Revised ADR-024 draft
[ARCH] DR-04 metadata model     |          |
[ARCH] DR-05 storage port ------+          |
[ARCH] DR-09 retention mechanism           |
[ARCH] DR-12 audit catalogue ----+          |
[ARCH] DR-13 audit retention ---+          |
[SEC]  DR-14 permissions --------+          |
[SEC]  DR-15 download security --+          |
[SEC]  DR-16 encryption posture -+          |
[SEC]  DR-26 break-glass --------+          |
[ARCH] DR-19 backup separation --+          |
[ARCH] DR-28 SPR-014 interlock --+          |
[ARCH] DR-29 acceptance criteria +----------+--> [ARCH] Architecture review
                                                         |
                                                         v
                                              ADR-024 accepted (or revised again)
```

| ID | Prerequisite | Type | Owner | Satisfied by | Current state |
|---|---|---|---|---|---|
| ACC-01 | Ownership closed list and prohibited imports | **[ARCH]** | Architecture Team | DR-03 | **NOT IMPLEMENTED** |
| ACC-02 | Named lifecycle read contract | **[ARCH]** + **[TECH]** | Architecture Team | DR-01 | **NOT IMPLEMENTED** |
| ACC-03 | Named document/file read contracts | **[ARCH]** + **[TECH]** | Architecture Team | DR-02 | **NOT IMPLEMENTED** |
| ACC-04 | Archive metadata model and idempotency | **[ARCH]** + **[TECH]** | Architecture Team | DR-04 | **NOT IMPLEMENTED** |
| ACC-05 | Provider-neutral storage port | **[ARCH]** | Architecture Team + Security | DR-05 | **NOT IMPLEMENTED** |
| ACC-06 | Quota contract, units and subscription resolution | **[ARCH]** + **[TECH]** | Architecture Team + Product | DR-06 | **NOT IMPLEMENTED** |
| ACC-07 | Quota first-release semantics | **[ARCH]** | Product + Architecture | DR-07 | **NOT IMPLEMENTED** |
| ACC-08 | Retention mechanism specification | **[ARCH]** + **[LEGAL]** | Architecture + Legal | DR-09 | **NOT IMPLEMENTED** |
| ACC-09 | Archive audit catalogue and store choice | **[ARCH]** + **[SEC]** | Architecture + Security | DR-12 | **NOT IMPLEMENTED** |
| ACC-10 | Audit retention classes and contradiction resolution | **[SEC]** + **[LEGAL]** | Security + Legal + Architecture | DR-13 | **NOT IMPLEMENTED** |
| ACC-11 | Archive permission surface and policies | **[SEC]** | Security + Architecture | DR-14 | **NOT IMPLEMENTED** |
| ACC-12 | Download/access security design | **[SEC]** | Security + Architecture | DR-15 | **NOT IMPLEMENTED** |
| ACC-13 | Truthful encryption posture | **[SEC]** | Security + Architecture | DR-16 | **NOT IMPLEMENTED** (no claim exists) |
| ACC-14 | Super-admin break-glass rule | **[SEC]** | Security + Architecture | DR-26 | **NOT IMPLEMENTED** |
| ACC-15 | Backup/archive separation invariant | **[ARCH]** + **[OPS]** | Architecture + Operations | DR-19 | **PARTIALLY IMPLEMENTED** (principle only) |
| ACC-16 | SPR-014 interlock gate wording | **[ARCH]** + **[LEGAL]** | Product + Architecture + Ops | DR-28 | **NOT RECORDED** |
| ACC-17 | Testable acceptance criteria and test plan | **[ARCH]** + **[TECH]** | Architecture | DR-29 | **PROPOSED** in this blueprint |
| ACC-18 | Single authoritative decision log | **[ARCH]** | Architecture | DR-27 | **CONFLICT** today |
| ACC-19 | Formal architecture review and approval | **[ARCH]** | Architecture Team | Revised ADR + review | **PENDING** |

### 6.3 Prerequisites for SPR-017 implementation

```text
ADR-024 accepted
   |
   +--> SPR-014 closure decision or explicit waiver  [governance gate]
   |        |
   |        +--> read contracts implemented and bound
   |        +--> storage port + local adapter
   |        +--> archive metadata migration
   |        +--> archive permissions/policy
   |        +--> audit sink + event catalogue
   |        +--> retention mechanism + policy evaluation
   |        +--> quota contract (report-only)
   |        +--> tests (unit/feature/security/arch/integration)
   |        +--> staging deployment + smoke test
   v
SPR-017 implementation authorized
```

| ID | Prerequisite | Type | Owner | Depends on | Current state |
|---|---|---|---|---|---|
| IMP-01 | Revised ADR-024 accepted | **[ARCH]** | Architecture Team | ACC-01 to ACC-19 | **NOT ACCEPTED** |
| IMP-02 | SPR-014 closure decision or owner-signed waiver | **[LEGAL]/[ARCH]** | Product + Architecture (+ Ops) | Section 7 evidence package | **NOT RECORDED** |
| IMP-03 | `ArchivedLifecycleReadContract` implemented and bound | **[TECH]** | Engineering | IMP-01, IMP-02 | **NOT IMPLEMENTED** |
| IMP-04 | `ArchiveDocumentReadContract` / `ArchiveFileReadContract` implemented | **[TECH]** | Engineering | IMP-01 | **NOT IMPLEMENTED** |
| IMP-05 | `StoragePort` + local adapter + conformance tests | **[TECH]** + **[SEC]** | Engineering | IMP-01 | **NOT IMPLEMENTED** |
| IMP-06 | Archive tables migration (`archive_entries`, `archive_retention_policies`, audit sink) | **[TECH]** | Engineering | IMP-01, DR-04, DR-09, DR-12 | **NOT IMPLEMENTED** |
| IMP-07 | Archive permission group, roles, policies, seed data | **[SEC]** + **[TECH]** | Engineering + Security | IMP-01, DR-14 | **NOT IMPLEMENTED** |
| IMP-08 | Authorized download path, path canonicalization, rate limiting, checksum verification | **[SEC]** + **[TECH]** | Engineering + Security | IMP-01, DR-15 | **NOT IMPLEMENTED** |
| IMP-09 | Retention evaluation service + dry-run + legal hold | **[TECH]** + **[LEGAL]** | Engineering + Legal | IMP-01, DR-09 | **NOT IMPLEMENTED** |
| IMP-10 | Archive audit events wired to the chosen sink | **[TECH]** + **[SEC]** | Engineering | IMP-01, DR-12, DR-13 | **NOT IMPLEMENTED** |
| IMP-11 | Quota contract consumer, report-only | **[TECH]** | Engineering | IMP-01, DR-06, DR-07 | **NOT IMPLEMENTED** |
| IMP-12 | Test suites and architecture tests | **[TECH]** | Engineering | IMP-03 to IMP-11 | **NOT IMPLEMENTED** |
| IMP-13 | Staging deployment, seed, smoke test, rollback note | **[OPS]** | Operations | IMP-01 to IMP-12 | **UNVERIFIED** |
| IMP-14 | Documentation, runbooks and permissions updated | **[OPS]** + **[TECH]** | Engineering + Operations | IMP-13 | **NOT IMPLEMENTED** |
| IMP-15 | Code review approval by named reviewers | **[ARCH]** | Architecture + Security + Ops | IMP-12 to IMP-14 | **PENDING** |

### 6.4 Prerequisites for local-first production readiness

```text
SPR-017 implementation complete (staging)
   |
   +--> DR-10 retention values approved .............. [LEGAL]
   +--> DR-11 destruction safeguards approved ........ [SEC/OPS/LEGAL]
   +--> DR-08 metering/enforcement approved .......... [PRODUCT]
   +--> DR-17 at-rest encryption implemented/evidenced [SEC/OPS]
   +--> DR-18 backup coverage + restore drill ........ [OPS]
   +--> DR-21 hosting/residency record ............... [LEGAL/OPS]
   +--> DR-13 audit retention implemented ............ [SEC/LEGAL]
   +--> staging evidence + ops runbooks .............. [OPS]
   +--> capacity monitoring + incident response ...... [OPS]
   v
Local-first production readiness
```

| ID | Prerequisite | Type | Owner | Why it blocks production | Current state |
|---|---|---|---|---|---|
| PRD-01 | Retention values and jurisdiction approved | **[LEGAL]** | Product + Legal | Cannot enforce or claim retention | **NOT APPROVED** |
| PRD-02 | Destruction safeguards approved and implemented (or explicitly kept off) | **[SEC]** + **[OPS]** + **[LEGAL]** | Product + Security + Ops + Legal | Cannot safely enable deletion | **NOT IMPLEMENTED** |
| PRD-03 | Quota metering/enforcement decision implemented | **[TECH]** + **[OPS]** | Product + Engineering | Cannot enforce quotas or paid extensions | **NOT IMPLEMENTED** |
| PRD-04 | At-rest encryption implemented and evidenced, or risk accepted in writing | **[SEC]** | Security + Operations | Protects official records at rest | **NOT IMPLEMENTED/UNVERIFIED** |
| PRD-05 | Backup includes archive objects; encryption evidenced; restore drill recorded | **[OPS]** | Operations | Prevents unrecoverable archive | **NOT IMPLEMENTED/UNVERIFIED** |
| PRD-06 | Hosting location and residency record recorded | **[LEGAL]** + **[OPS]** | Product + Legal + Operations | Prevents unknown/unlawful location | **UNVERIFIED** |
| PRD-07 | Audit retention classes implemented; records evidence preserved | **[SEC]** + **[LEGAL]** | Security + Legal | Prevents loss of evidence | **CONTRADICTION** |
| PRD-08 | Staging deployment evidence and smoke tests | **[OPS]** | Operations | Proves the pipeline | **UNVERIFIED** |
| PRD-09 | Capacity monitoring and alerting (distinct from quota) | **[OPS]** | Operations | Prevents silent storage exhaustion | **NOT IMPLEMENTED** |
| PRD-10 | Incident response, rollback and runbooks | **[OPS]** | Operations | Enables safe failure | **NOT IMPLEMENTED** |
| PRD-11 | Security review sign-off (tenant isolation, download, break-glass) | **[SEC]** | Security Lead | Prevents cross-tenant exposure | **PENDING** |
| PRD-12 | Legal/compliance sign-off on retention and residency statements | **[LEGAL]** | Legal/Compliance | Prevents unlawful claims | **PENDING** |

### 6.5 Prerequisites for future cloud activation

```text
Local-first production stable
   |
   +--> DR-20 cloud activation gate approved ......... [ARCH/SEC/OPS]
   +--> DR-22 provider/region/DPA/sub-processors ..... [LEGAL/PRODUCT]
   +--> DR-25 SLO/durability/cost baseline ........... [OPS/PRODUCT]
   +--> Cloud tenant-isolation design ................ [ARCH/SEC]
   +--> Cloud key management/rotation ................ [SEC]
   +--> Adapter conformance suite .................... [TECH]
   +--> Migration + rollback plan .................... [OPS/TECH]
   +--> Incident/DR plan for cloud ................... [OPS]
   v
Cloud activation authorized
```

| ID | Prerequisite | Type | Owner | Why it blocks cloud | Current state |
|---|---|---|---|---|---|
| CLD-01 | Provider and region approved | **[ARCH]** + **[LEGAL]** | Product + Architecture | Residency and vendor choice | **NOT DECIDED** |
| CLD-02 | Tenant isolation for shared objects approved (bucket/prefix/credentials) | **[ARCH]** + **[SEC]** | Architecture + Security | Prevents cross-tenant exposure | **NOT DECIDED** |
| CLD-03 | Encryption key management and rotation approved | **[SEC]** | Security + Architecture | Prevents key loss/exposure | **NOT DECIDED** |
| CLD-04 | SLO/availability/durability/cost baseline approved | **[OPS]** + **[ARCH]** | Operations + Product | Prevents unsupported claims | **NOT DECIDED** |
| CLD-05 | Tenant-aware cloud disk configuration confirmed | **[OPS]** | Operations | `s3` is not tenant-suffixed today | **NOT IMPLEMENTED** |
| CLD-06 | Data residency, DPA, sub-processor list and controller/processor roles | **[LEGAL]** | Product + Legal + Privacy | Prevents unlawful transfer | **NOT DECIDED** |
| CLD-07 | Adapter conformance tests (same contract, local/cloud) | **[TECH]** | Engineering | Proves portability | **NOT IMPLEMENTED** |
| CLD-08 | Migration and rollback plan preserving checksums/tenancy | **[OPS]** + **[TECH]** | Engineering + Operations | Prevents data loss/lock-in | **NOT IMPLEMENTED** |
| CLD-09 | Cloud incident/DR plan and runbooks | **[OPS]** | Operations | Enables recovery | **NOT IMPLEMENTED** |
| CLD-10 | Credential hygiene and secret-management evidence | **[SEC]** | Security + Operations | Prevents credential leakage | **UNVERIFIED** |

### 6.6 Critical path

The shortest defensible path to an accepted ADR is:

```text
DR-27 (decision log)
  -> DR-01/DR-02 (read contracts)
  -> DR-03/DR-04 (ownership + metadata)
  -> DR-05 (storage port)
  -> DR-06/DR-07 (quota contract + first-release semantics)
  -> DR-09 (retention mechanism)
  -> DR-12/DR-13 (audit catalogue + retention classes)
  -> DR-14/DR-15/DR-16/DR-26 (security surface)
  -> DR-19/DR-28/DR-29 (invariants + interlock + criteria)
  -> revised ADR review
  -> acceptance
```

The shortest path to implementation adds: SPR-014 closure/waiver -> contracts and adapter implementation -> archive schema -> permissions and audit -> tests -> staging.

The shortest path to local-first production adds: retention values -> encryption -> backup/restore -> audit retention -> hosting record -> staging evidence -> sign-offs.

Cloud activation is intentionally off the critical path and must not be used to unblock acceptance or production.

## 7. SPR-014 Interlock

### 7.1 Status

**SPR-014 remains DEFERRED.** This blueprint does not close, complete, promote or re-scope it. The 05 November 2026 reconciliation and the 06 November 2026 review both classify the implementation as technically complete for its slice but governance-incomplete.

### 7.2 What is implemented

| Element | Evidence | Classification |
|---|---|---|
| Bounded context and state enum | `app/Domains/RecordsLifecycle/`; `RecordLifecycleState` = `registered`, `published`, `archived` | **VERIFIED IMPLEMENTED** |
| Service API | `register`, `publish`, `archive`, `findBySource` | **VERIFIED IMPLEMENTED** |
| Persistence | `record_lifecycles` migration with school, source identity, state, context, actor and timestamps | **VERIFIED IMPLEMENTED** |
| Policy | `RecordLifecyclePolicy`; `records_lifecycle.*`; delete/restore/forceDelete denied | **VERIFIED IMPLEMENTED** |
| Source integrations | Academic Communication, Report Card, Class Council contract/adapter seams; targeted integration tests | **VERIFIED IMPLEMENTED** (targeted) |
| Targeted test evidence | 6 unit tests / 19 assertions; combined targeted run 143 tests / 981 assertions (2026-11-05 reconciliation) | **VERIFIED** (targeted, not full-suite) |
| Full-suite evidence | Historical 1,889 tests / 4,507 assertions (2026-09-07 handoff); not re-run by the reconciliation | **HISTORICAL / UNVERIFIED** |
| PHPStan/Pint | Scoped runs recorded as clean/0 errors | **VERIFIED** (scoped) |

### 7.3 What is outstanding

| # | Requirement | Evidence of absence | Classification |
|---|---|---|---|
| 1 | Formal review approval record (named approver, date, scope) | No approval block, signature or reviewer date in `SPR-014_Records_Lifecycle_Engine_Report.md` or governance docs | **NOT SATISFIED** |
| 2 | Successful staging deployment evidence | `docs/08_Deployment/Development.md` and `CI_CD.md` describe process only; no environment, commit SHA, migration set, smoke test or rollback note | **UNVERIFIED / NOT SATISFIED** |
| 3 | Recorded sprint closure decision | No closure record; all governance docs say `DEFERRED` | **NOT SATISFIED** |

### 7.4 Minimum evidence package for closure

1. A named approver (Product Owner and/or Architecture Team, as the owner decides) and an approval date, recorded in the SPR-014 report or a signed closure record.
2. A staging deployment record: environment, commit SHA, deployment date, executed migration set, smoke-test result and rollback note.
3. A recorded closure decision that explicitly accepts the scope, evidence and residual observations (review O-1 to O-4).
4. If staging evidence will not be produced, an explicit, owner-signed waiver that states the risk and the compensating controls. The waiver must be recorded as a waiver, not as a passing gate.

### 7.5 Tests are not closure

Passing tests demonstrate code behaviour. They do **not** satisfy:

- formal review approval;
- staging deployment;
- a recorded closure decision;
- the project Definition of Done (`Development_Workflow.md:46-54`; `Engineering_Principles.md:249-259`).

The targeted test figures are evidence for the implementation slice. They are not a substitute for the three governance items above.

### 7.6 Waiver rules

- A waiver is valid only if it is explicit, owner-signed, scoped and dated.
- A generic project waiver ("tests pass, proceed") does not automatically replace staging evidence.
- A waiver must state which requirement is waived, why, the risk, the compensating controls and the expiry/review date.
- A waiver of staging evidence does not waive security, retention or legal gates for SPR-017 production.
- This blueprint records no waiver.

### 7.7 Interlock gate for SPR-017

ADR-024 invariant L4 says SPR-017 does not mark SPR-014 complete and does not reopen it; the interlock is contract-only consumption. The review adds the governance interpretation, adopted here:

- ADR-024 may be **reviewed and revised** while SPR-014 is deferred.
- SPR-017 **implementation** must not begin until SPR-014's closure decision is recorded, or until the project owner records an explicit waiver of that interlock.
- Accepting ADR-024 must not be treated as closing SPR-014.
- Closing SPR-014 must not be treated as accepting ADR-024.
- SPR-017 must consume SPR-014 only through the read contract; it must never call the mutation service or import the model.

### 7.8 Recommended owner actions

| Action | Owner | Output |
|---|---|---|
| Record formal review approval | Product Owner + Architecture Team | Approval block/closure record |
| Produce staging evidence or a signed waiver | Operations Lead + Product Owner | Deployment record or waiver |
| Record closure decision | Product Owner + Architecture Team | Closure record with residual observations |
| Confirm interlock wording in revised ADR | Architecture Team | DR-28 text in ADR-024 |

---

## 8. Risks and Unresolved Questions

### 8.1 Risk register

Severity and likelihood are planning estimates for review purposes, not formal risk acceptance.

| ID | Risk | Severity | Likelihood | Evidence | Mitigation | Owner |
|---|---|---|---|---|---|---|
| RR-01 | SPR-014 is implicitly treated as closed because ADR-024 is accepted | High | Medium | Three DoD items missing; no waiver | Keep interlock explicit; separate decisions | Product + Architecture |
| RR-02 | Archive imports source models because no read contract exists | High | High | EV-15, EV-21 | Contract-first; architecture test | Architecture |
| RR-03 | `plans.storage_limit` is mistaken for enforcement | High | High | EV-22 to EV-27 | Correct wording; quota contract; report-only first release | Product + Architecture |
| RR-04 | Retention values invented or auto-destruction enabled | Critical | Low | No legal values; destruction off by default | ODL/ODD gates; dry-run; approval chain | Product + Legal |
| RR-05 | Audit evidence deleted by 90-day cleanup | High | High | EV-29/EV-30 | Dedicated/exempt records stream; retention classes | Security + Legal |
| RR-06 | Local-only archive presented as resilient/durable | High | Medium | Single local disk; backup implementation partial | Disclosure; backup inclusion; restore test | Operations |
| RR-07 | At-rest encryption assumed but absent | High | Medium | Plain local disk; DB encrypt commented | Explicit decision; implement or accept risk | Security |
| RR-08 | Cross-tenant archive read via path/IDOR | Critical | Medium | No archive policy/route; path-only risk | Policy + tenant resolution + negative tests | Security |
| RR-09 | Dual `archived` semantics diverge | High | Medium | EV-19/EV-20 | Reconciliation invariant + audit exception | Architecture |
| RR-10 | Subscription/plan resolution ambiguous | Medium | High | Multiple/expired subscriptions possible | Deterministic resolver + reported reason | Product + Architecture |
| RR-11 | S3-mandatory seam creates premature lock-in | Medium | Medium | No provider decision | Provider-neutral port | Architecture |
| RR-12 | Documentation drift (stale coefficient note, line refs, D-numbering) | Medium | High | EV-34, EV-36, EV-39 | Revise ADR; single decision log | Architecture |
| RR-13 | Archive not included in backup/restore | High | Medium | Backup doc generic; implementation partial | Ops change + restore drill | Operations |
| RR-14 | No archive permissions, so access defaults to super_admin | Medium | High | EV-35 | Add `archive.*` permissions/policy | Security |
| RR-15 | Package export leaks cross-tenant data | High | Low (deferred) | No package design | One-school manifest, auth, expiry, audit | Security + Product |
| RR-16 | Legal hold/litigation hold unsupported | High | Low | No legal-hold concept | Add hold fields and precedence | Legal + Architecture |
| RR-17 | Tenant deletion cascades destroy archive metadata | High | Medium | EV-12 cascade on `generated_files`; `document_audits` cascade | Restrict/soft-delete; retention-aware offboarding | Architecture + Operations |
| RR-18 | Duplicate command classes cause the wrong cleanup implementation to run | Medium | Medium | BP-E-48 | Consolidate commands in a separate task; do not rely on registration order | Engineering |
| RR-19 | Backup documentation overstates encryption/off-site capability | High | Medium | BP-E-49 to BP-E-52 | Correct the ADR and operational docs; measure | Operations |
| RR-20 | Quota enforcement race allows silent over-limit writes | Medium | Medium | No reservation mechanism | Report-only first; atomic reservation if hard enforcement is approved | Architecture |
| RR-21 | Audit `document_audits` is not a substitute for retention evidence because it cascades with source/documents | High | Medium | BP-E-60 | Dedicated archive stream; do not reuse without ownership change | Architecture + Security |
| RR-22 | Archive retention policy changes retroactively destroy records | Critical | Low | No policy versioning specified | Effective dates, versioning, dry-run, approval, audit | Legal + Architecture |
| RR-23 | Hosting location is unknown, creating a silent cross-border deployment | High | Medium | EV-32/EV-33 | Residency record before production | Product + Legal + Ops |
| RR-24 | Cloud migration breaks tenant isolation or integrity | High | Medium | No adapter/migration design | Conformance tests, checksums, rollback plan | Architecture + Operations |

### 8.2 Unresolved questions

Each question is assigned to a decision. None is answered by this blueprint unless explicitly stated.

| ID | Question | Owner | Decision |
|---|---|---|---|
| UQ-01 | Which state is authoritative when `AcademicDocument.status = archived` and `RecordLifecycleState != archived` (or vice versa)? | Architecture + Product | DR-04, DR-09 |
| UQ-02 | Does retention start at publication, generation, archive entry or school closure? Can a school override? | Product + Legal | DR-09 |
| UQ-03 | Is the archive a system of record or a derived copy? Are archived bytes authoritative? | Product + Architecture | DR-04 |
| UQ-04 | Are archived objects immutable at the storage layer (WORM/object lock) or only application-immutable? | Security + Architecture | DR-05, DR-15 |
| UQ-05 | What happens to archive objects and metadata when a school is deleted, suspended or a subscription expires? | Product + Legal + Operations | DR-09, DR-10 |
| UQ-06 | Are legal holds supported, and who can place/release them? | Legal + Product | DR-09, DR-11 |
| UQ-07 | What is the canonical storage unit (decimal vs binary), and how are invalid plan values handled? | Product + Architecture | DR-06 |
| UQ-08 | How are multiple/expired/suspended subscriptions resolved for quota purposes? | Product + Architecture | DR-06, DR-07 |
| UQ-09 | Does an archive copy double-count against the plan quota? | Product | DR-08 |
| UQ-10 | Which audit events are mandatory, and for how long must each class be retained? | Security + Legal | DR-12, DR-13 |
| UQ-11 | Is local at-rest encryption required for the first release, or accepted as a risk? Where are keys stored/rotated? | Security + Product | DR-16, DR-17 |
| UQ-12 | What is the production hosting location, and does it satisfy any residency obligation? | Product + Legal + Operations | DR-21, DR-22 |
| UQ-13 | What are the archive-specific RPO/RTO, and has a restore been tested? | Operations | DR-18 |
| UQ-14 | How are already-archived lifecycle rows backfilled into Archive, and are duplicates/idempotency guaranteed? | Architecture + Engineering | DR-04 |
| UQ-15 | Who may request a package, and what approval/identity proof is required? | Product + Security | DR-24 |
| UQ-16 | Are packages subject to retention, or strictly temporary? | Product + Legal | DR-24 |
| UQ-17 | What is the accepted behaviour when checksum verification fails during download? | Security + Architecture | DR-15 |
| UQ-18 | Is `super_admin` allowed to read/export any school's archive without a second approval (break-glass)? | Security + Product | DR-26 |
| UQ-19 | What is the archive's behaviour when the storage disk is unavailable or full? | Architecture + Operations | DR-05, DR-15 |
| UQ-20 | Which decision record is authoritative when ADR-024 and the SPR-017 board report disagree? | Architecture | DR-27 |
| UQ-21 | Does a retention policy change apply to existing records, and from when? | Legal + Architecture | DR-09, DR-22 |
| UQ-22 | Who is the data controller and who is the processor; is a DPA required? | Legal + Privacy + Product | DR-22 |
| UQ-23 | Should the generic `audit_logs` retention be changed, or should archive events use a separate store? | Security + Legal | DR-13 |
| UQ-24 | What is the archive-specific meaning of "restore" (metadata only, object only, or source record)? | Product + Architecture | DR-09, DR-23 |

### 8.3 Conflict register

| ID | Conflict | Evidence | Impact | Resolution owner |
|---|---|---|---|---|
| CF-01 | ADR-024 records D1-D5 approved; SPR-017 board report says pending; `SESSION_HANDOFF` says open decisions do not block ADR acceptance | EV-38, EV-39, BP-E-53 | Unclear authority and gate | Architecture (DR-27) |
| CF-02 | Audit docs say 7 years; cleanup deletes at 90 days | EV-29, EV-30 | Audit evidence may be lost | Security + Legal (DR-13) |
| CF-03 | Backup docs claim encrypted off-site backups; implementation is a schema-only placeholder | EV-31, BP-E-49 | Overstated recovery capability | Operations (DR-18) |
| CF-04 | Backup docs say PostgreSQL; project state says MySQL | EV-31, `PROJECT_STATE` | Operational confusion | Operations |
| CF-05 | ADR-024 coefficient note is stale | EV-34 | Stale context | Architecture (DR-27) |
| CF-06 | ADR-024 S3 line reference is stale | EV-36 | Stale evidence | Architecture (DR-27) |
| CF-07 | Duplicate `system:cleanup` and `system:backup` command classes | BP-E-48 | Registration ambiguity | Engineering |
| CF-08 | `SESSION_HANDOFF` says ADS open decisions do not block acceptance, but the review classifies several as acceptance-blocking | BP-E-53, review section 13.2 | Governance ambiguity | Architecture + Product (DR-27, DR-28) |

## 9. ADR-024 Revision Acceptance Criteria

### 9.1 How to use these criteria

These are the conditions a **future revision** of ADR-024 must satisfy to be considered acceptance-ready. They are testable by document inspection: each criterion names what must appear, how it will be verified, and which review finding and decision it closes.

They are not met today. ADR-024 remains `Proposed` until its owner runs a revision and a review records acceptance. This blueprint does not perform that revision.

| Legend | Meaning |
|---|---|
| **Verification: Document** | A reviewer can confirm the criterion by reading the revised ADR |
| **Verification: Evidence** | The revised ADR must cite a repository artifact, test, deployment record or signed decision |
| **Verification: Cross-check** | The criterion must be consistent with a named source document or decision record |

### 9.2 Acceptance criteria

| ID | Criterion | Verification method | Closes | Decision |
|---|---|---|---|---|
| **AC-01** | The revised ADR names every cross-domain contract it relies on and marks each as **EXISTING**, **TO BE ADDED** or **OUT OF SCOPE**. No contract is referenced as if it already exists when it does not. | Document; Evidence (cross-check against `app/Domains/*/Domain/Contracts/`) | RV-01, A1, A2 | DR-01, DR-02 |
| **AC-02** | The lifecycle read contract is specified: owner, direction, semantics, tenant scoping, projection fields and prohibited leakage. It is explicitly read-only. | Document | A1, RV-01 | DR-01 |
| **AC-03** | The document and file read contracts are specified with projection fields limited to archive-safe metadata; content, grades and personal data are explicitly excluded. | Document | A2, RV-01 | DR-02 |
| **AC-04** | A closed ownership list states what Archive owns, what it may read, and what it must never own or mutate; the prohibited-import list is explicit. | Document; Evidence (architecture-test requirement is named) | A3, RV-02 | DR-03 |
| **AC-05** | The archive metadata model is described with identity, tenant key, source identity, idempotency key, object pointer, checksum, retention fields, legal hold, status and lifecycle semantics. No SQL is required in the ADR, but the invariants are. | Document | RV-03 | DR-04 |
| **AC-06** | The ADR defines tenant-deletion and source-deletion behaviour for archive metadata and objects, and does not rely on a silent cascade. | Document; Cross-check with `generated_files` and `document_audits` cascade behaviour | RV-03, EV-12, BP-E-60 | DR-04, DR-09 |
| **AC-07** | Storage is expressed as a provider-neutral port with a local adapter first. S3 compatibility is optional and gated, not mandatory. No provider type, credential, bucket or region appears in the accepted architecture. | Document | B3, RV-06 | DR-05 |
| **AC-08** | The ADR states the quota mechanism truthfully: `plans.storage_limit` is a data field, not an implemented quota. It defines the quota contract, canonical unit, parsing rules and subscription-resolution semantics, or explicitly defers them with a named gate. | Document; Evidence (cross-check `plans` migration and seeder) | D1, D2, D3, RV-05 | DR-06 |
| **AC-09** | The first-release quota behaviour is stated as `report_only` (or another explicit mode), with usage nullable, no blocking, and no second quota authority in Archive. | Document | D5, RV-05 | DR-07 |
| **AC-10** | The retention **mechanism** is specified: policy scope, precedence, clock start, disposition classes, legal hold, evaluation trigger, dry-run, approval and audit. No statutory period is asserted. | Document; Cross-check for absence of invented legal values | C1, C2, C5, RV-04 | DR-09 |
| **AC-11** | Destruction is described as a gated workflow, not a config toggle; it is off in the first release and requires permission, dry-run, approval, separation of duties and a deletion certificate. | Document | C3, E6 | DR-11 |
| **AC-12** | The ADR defines audit retention classes and states which store holds records/compliance evidence. It does not rely on the generic 90-day cleanup for archive evidence, and it acknowledges the 7-year versus 90-day contradiction. | Document; Cross-check `Audit.md` and `SystemCleanupCommand` | C4, E5, RV-08 | DR-12, DR-13 |
| **AC-13** | An archive audit event catalogue is present, with minimum payloads and a rule that denial events do not leak other tenants' existence. | Document | RV-08, O-1 | DR-12 |
| **AC-14** | The permission surface is named (`archive.view`, `download`, `manage`, `retention.manage`, `export`, `destroy` or an explicit equivalent), school-scoped, and does not reuse `document.*`. | Document; Evidence (cross-check `PermissionGroup`) | E1, RV-07 | DR-14 |
| **AC-15** | The download and access design is specified: private disk, authorization before object lookup, no path input from the client, uniform 404, attachment disposition, rate limiting, integrity check, audit. | Document | B1, B2, E2, E3, RV-07 | DR-15 |
| **AC-16** | The encryption-at-rest posture is truthful. It states what is **not** implemented, does not claim host encryption without evidence, and records the production decision (implement, evidence infrastructure control, or accept risk). | Document; Evidence (cross-check `config/database.php:113` and absence of `Crypt` usage) | B4, E4, RV-09 | DR-16, DR-17 |
| **AC-17** | Backup and archive are explicitly separate. Archive objects and metadata are in backup scope, restore must be drilled, and documented RTO/RPO are measured or restated as targets rather than achieved facts. | Document; Evidence (cross-check `Backups.md` and `BackupService`) | B5, RV-06 | DR-18, DR-19 |
| **AC-18** | The super-admin break-glass rule for archive is defined, including approval and audit. Cross-tenant privileged access is not silent. | Document | E1 | DR-26 |
| **AC-19** | Data residency is stated as an unresolved operational/legal matter: local-first is a technical property, not a compliance claim; hosting location, controller/processor roles and DPA are named as gates. | Document; Cross-check `docs/03_Database/Audit.md`, deployment docs | F1, F2, F3, F4 | DR-21, DR-22 |
| **AC-20** | The cloud activation gate lists all required approvals (provider/region, tenant isolation, key management, SLO/durability/cost, legal/DPA, conformance, migration/rollback, incident, credential hygiene) and states that none is currently satisfied. | Document; Evidence (cross-check `config/tenancy.php:142` and absence of cloud config) | RV-06, F1-F4 | DR-20, DR-22, DR-25 |
| **AC-21** | The SPR-014 interlock is explicit: ADR acceptance does not close SPR-014; SPR-017 implementation is gated on a closure decision or a signed waiver. | Document; Cross-check `SPR-014_Records_Lifecycle_Engine_Report.md` | RV-10 | DR-28 |
| **AC-22** | The decision log is reconciled: D1-D5 approval status is unambiguous, the revised ADR is declared authoritative, and the stale coefficient and S3 line references are corrected or removed. | Document; Cross-check `SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md`, `config/tenancy.php:142` | RV-11, EV-34, EV-36 | DR-27 |
| **AC-23** | Out-of-scope items are listed explicitly: no second lifecycle engine, no document generation, no backup implementation, no legal values, no cloud provider, no package format, no source-domain change, no legacy document revival. | Document | RV-12 | DR-29 |
| **AC-24** | The acceptance criteria themselves are testable and are included in the revised ADR or referenced by ID, so a reviewer can verify them without re-reading the review report. | Document | RV-12 | DR-29 |

### 9.3 Traceability of acceptance criteria to review requirements

| Review RV ID | Acceptance criteria | Closed? |
|---|---|---|
| RV-01 named read contracts | AC-01, AC-02, AC-03 | No - ADR not revised |
| RV-02 ownership list | AC-04 | No |
| RV-03 metadata model | AC-05, AC-06 | No |
| RV-04 retention specification | AC-10, AC-11 | No |
| RV-05 quota correction | AC-08, AC-09 | No |
| RV-06 provider-neutral storage and backup | AC-07, AC-17, AC-20 | No |
| RV-07 download/access and permissions | AC-14, AC-15 | No |
| RV-08 audit catalogue/retention | AC-12, AC-13 | No |
| RV-09 encryption posture | AC-16 | No |
| RV-10 SPR-014 interlock | AC-21 | No |
| RV-11 decision log/stale evidence | AC-22 | No |
| RV-12 testable acceptance criteria | AC-23, AC-24 | No |

### 9.4 Acceptance decision

| Question | Answer |
|---|---|
| Is ADR-024 acceptance-ready today? | **No.** Criteria AC-01 to AC-24 are unmet. |
| Does this blueprint accept ADR-024? | **No.** Acceptance is an Architecture Team decision after a revised ADR and a new review. |
| Can SPR-017 implementation start after acceptance alone? | **No.** The SPR-014 interlock gate (section 7) also applies. |
| Does meeting these criteria prove production readiness? | **No.** Production gates are separate (section 6.4). |
| Does meeting these criteria prove legal compliance? | **No.** Legal decisions are owned outside architecture (section 11). |

---

## 10. SPR-017 Readiness

### 10.1 Verdict

**SPR-017 is NOT READY to implement.**

Three independent gates are closed:

1. ADR-024 is `Proposed` and not acceptance-ready (section 9).
2. SPR-014 is `DEFERRED` with outstanding governance evidence (section 7).
3. No implementation substrate exists: no Archive domain, migration, route, config or permission (EV-01, EV-02, EV-03, EV-35).

Readiness is not a judgement about the quality of the ADR's direction. The direction is sound; the prerequisites are absent.

### 10.2 Readiness matrix

| Dimension | Ready? | Evidence | Blocking item |
|---|---|---|---|
| Architecture decision | No | ADR-024 `Proposed`; 24 acceptance criteria unmet | DR-01 to DR-05, DR-09, DR-12 to DR-16, DR-19, DR-26 to DR-29 |
| Source-domain contracts | No | No read contracts; command-only services | DR-01, DR-02 |
| Lifecycle interlock | No | SPR-014 `DEFERRED`; no closure/waiver | DR-28, section 7 |
| Storage abstraction | No | Direct `Storage::disk()`; no port | DR-05 |
| Schema | No | No archive table | DR-04, DR-09, DR-12 |
| Permissions | No | No `archive.*` group/policy | DR-14 |
| Audit integration | No | No archive events; audit contradiction | DR-12, DR-13 |
| Quota | No | Column only; no resolver/contract | DR-06, DR-07 |
| Retention | No | No mechanism | DR-09 |
| Security design | No | No download path, encryption decision or break-glass rule | DR-15, DR-16, DR-26 |
| Operations | No | Backup partial; no staging evidence; no runbooks | DR-18, section 7 |
| Legal | No | No retention values, residency record or DPA | DR-10, DR-21, DR-22 |
| Test strategy | No | No archive tests or architecture tests | DR-29 |

### 10.3 Implementation readiness conditions

Every condition must be satisfied or explicitly waived before the first SPR-017 implementation commit. Ordered by dependency, not by importance.

| ID | Condition | Type | Owner | Evidence required |
|---|---|---|---|---|
| **R-01** | ADR-024 revised and accepted | **[ARCH]** | Architecture Team | Accepted ADR with AC-01 to AC-24 satisfied |
| **R-02** | SPR-014 closure decision recorded, or interlock waiver signed | **[ARCH]** + **[OPS]** + **[LEGAL]** | Product + Architecture (+ Ops) | Closure record or scoped waiver |
| **R-03** | `ArchivedLifecycleReadContract` designed, approved and implemented | **[ARCH]** + **[TECH]** | Architecture + Engineering | Contract file, projection DTOs, tests |
| **R-04** | `ArchiveDocumentReadContract` / `ArchiveFileReadContract` implemented | **[TECH]** | Engineering | Contract files, tests, no model leakage |
| **R-05** | `StoragePort` and local adapter implemented with conformance tests | **[TECH]** + **[SEC]** | Engineering + Security | Port interface, adapter, tests |
| **R-06** | Archive schema migrated in a reversible migration | **[TECH]** | Engineering | Migration, rollback test, schema review |
| **R-07** | `archive.*` permission group, roles and policies seeded | **[SEC]** | Security + Engineering | Enum change, policy, seed, tests |
| **R-08** | Authorized download path with canonicalization, uniform 404, rate limit and checksum verification | **[SEC]** + **[TECH]** | Engineering + Security | Controller, policy, feature tests including cross-tenant negative tests |
| **R-09** | Retention mechanism and dry-run implemented; destruction off | **[TECH]** + **[LEGAL]** | Engineering + Legal | Policy service, evaluation job, dry-run output |
| **R-10** | Archive audit sink and event catalogue implemented | **[TECH]** + **[SEC]** | Engineering + Security | Events, sink, retention class, tests |
| **R-11** | Quota contract consumer implemented in `report_only` mode | **[TECH]** | Engineering | Contract, consumer, nullable usage handling |
| **R-12** | Architecture tests prevent forbidden imports and mutation calls | **[TECH]** | Engineering | Pest arch tests that fail on violation |
| **R-13** | Tenant-isolation, integrity, failure-mode and idempotency test suite passes | **[TECH]** + **[SEC]** | Engineering | Test report, named suite |
| **R-14** | Staging deployment with seed, smoke test and rollback note | **[OPS]** | Operations | Deployment record, commit SHA, results |
| **R-15** | Operations runbook, permissions documentation and incident procedure updated | **[OPS]** | Engineering + Operations | Runbook, docs, permission matrix |

### 10.4 First-release guardrails

The first implementation release must respect these limits. They are derived from the ADR direction and the review findings; they are not negotiated by the implementation team.

| Guardrail | Rule |
|---|---|
| No destruction | No physical deletion of archived objects. Metadata soft-delete only. |
| No quota blocking | Quota is report-only. Reads, retention evaluation and legal-hold operations are never blocked. |
| No cloud | Local adapter only. No cloud SDK, credential, bucket or region. |
| No legal claims | No retention period, residency claim or compliance statement without owner approval. |
| No source mutation | Archive never writes to `record_lifecycles`, `academic_documents`, `generated_files` or source state. |
| No public exposure | Private disk, authorized streaming, no public URL. |
| No silent failure | Missing object, checksum mismatch and path rejection are audited and fail closed. |
| No legacy revival | Do not revive `GeneratedDocument`, `DocumentTemplate`, `DocumentGenerationService`, `App\Domains\Document`, DocumentCenter views or `pdf_enable_qr_code`. |
| No scope creep | Packages, cloud, destruction, metering and lifecycle-state extensions are separate approvals. |

### 10.5 What "ready" will look like

SPR-017 is ready to start when a reviewer can confirm, in writing:

- ADR-024 is `Accepted` and the acceptance record references AC-01 to AC-24;
- the SPR-014 interlock is closed or explicitly waived;
- every R-01 to R-15 condition is met or waived with a named owner;
- the first-release guardrails are reflected in the ADR and the implementation plan;
- no condition relies on a passing test suite as a substitute for staging or approval.

## 11. Limitations and Human-Approval Decisions

### 11.1 Scope limitations of this blueprint

| Limitation | Consequence |
|---|---|
| Documentation only | No code, migration, contract, interface, route, config, dependency or business logic was created or modified. |
| No ADR revision | ADR-024 is unchanged and remains `Proposed`. This blueprint proposes; it does not edit the ADR. |
| No sprint governance action | SPR-014 is unchanged and remains `DEFERRED`. No closure, waiver or re-scope is recorded. |
| No implementation authorization | SPR-017 remains unimplemented and unauthorized. |
| No staging or production access | No deployment evidence was produced or verified. |
| No test execution | No Pest, PHPStan or Pint run was performed for this task. Any test figures quoted come from prior reports and are labelled accordingly. |
| No legal research | No country-specific retention period, residency rule or data-protection obligation was determined. |
| No security assessment | No penetration test, threat model or encryption audit was performed. |
| No owner interviews | Owner roles are proposed; no named approver signed anything. |
| Static snapshot | Line references reflect the working tree at the recorded baseline and may drift. |

### 11.2 What this blueprint must not be read as

- It is not an ADR, an ADR amendment or an acceptance record.
- It is not a sprint closure or a Definition-of-Done sign-off.
- It is not a production, security, legal or privacy approval.
- It is not a commitment to a cloud provider, region, price or timeline.
- It is not evidence that encryption, backup, retention or audit controls operate.
- It is not a waiver of any gate.

### 11.3 Decisions reserved to humans

The following must be made by the named human role through the project's governance process. No agent, including this one, can make them.

| # | Decision | Reserved to | Why it cannot be delegated to this document |
|---|---|---|---|
| H-01 | Accept, reject or further revise ADR-024 | Architecture Team | Formal architecture authority |
| H-02 | Approve retention periods and permanent-preservation categories | Product Owner + Legal/Compliance | Statutory and contractual obligation |
| H-03 | Approve the legal basis for destruction | Legal/Compliance | Lawfulness |
| H-04 | Approve legal-hold policy and authority | Legal/Compliance | Litigation/investigation control |
| H-05 | Approve the destruction approval matrix and two-person rule | Product + Security + Operations + Legal | Safeguard design and separation of duties |
| H-06 | Accept or reject the at-rest encryption posture and residual risk | Security Lead (+ Product Owner for risk acceptance) | Risk acceptance |
| H-07 | Approve key-management and rotation design | Security Lead + Operations Lead | Custody of key material |
| H-08 | Approve quota semantics, over-quota behaviour and paid extensions | Product Owner (+ Finance) | Commercial and product policy |
| H-09 | Approve cloud provider, region and data residency | Product Owner + Legal/Compliance + Privacy/DPO | Vendor, transfer and residency law |
| H-10 | Approve DPA, sub-processor list and controller/processor roles | Legal/Compliance + Privacy/DPO | Contractual/legal role allocation |
| H-11 | Approve production hosting location | Product Owner + Operations Lead + Legal | Operational and legal exposure |
| H-12 | Approve backup scope, encryption, RTO/RPO and restore evidence | Operations Lead (+ Security) | Operational risk |
| H-13 | Approve super-admin break-glass for archive | Security Lead + Product Owner | Privileged access risk |
| H-14 | Approve package export policy, recipients and expiry | Product Owner + Security Lead | Cross-tenant data exposure |
| H-15 | Record SPR-014 closure or sign an interlock waiver | Product Owner + Architecture Team (+ Operations Lead for staging) | Governance and residual risk |
| H-16 | Approve staging evidence as sufficient | Operations Lead + Product Owner | Operational verification |
| H-17 | Declare production readiness | Product Owner + Architecture + Security + Operations (+ Legal) | Multi-disciplinary release authority |
| H-18 | Authorize cloud activation | Product Owner + Architecture + Security + Operations + Legal | Highest-risk gate |

### 11.4 Conflict-of-interest and independence note

This blueprint was prepared by an architecture agent on behalf of the Architecture Team. It analyses evidence and proposes directions; it does not approve its own proposals. Where the blueprint recommends a direction that an owner later rejects, the owner's recorded decision prevails and the ADR must be revised accordingly.

### 11.5 Handling of uncertainty

Where evidence was missing, this blueprint marks the item **UNVERIFIED** rather than assuming success. Where documents conflicted, it records a **CONFLICT** entry rather than choosing a winner. Where a decision belongs to another role, it records the question (UQ-xx) and the owner rather than answering it. This is deliberate: a blueprint that silently resolves uncertainty would create false confidence at exactly the gates that need evidence.

---

## 12. Files Created/Modified and Git State

### 12.1 Deliverable

| Action | Path | Nature |
|---|---|---|
| **Created** | `docs/docs/00_Project_Management/ADR-024_REVISION_BLUEPRINT_2026-11-06.md` | Documentation-only blueprint and decision register |

No other file was created, modified, moved or deleted by this task.

### 12.2 Explicitly unchanged

| Item | State |
|---|---|
| `docs/ADR/ADR-024.md` | Unchanged; status still `Proposed` |
| `docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md` | Unchanged; no closure record added |
| `docs/docs/00_Project_Management/SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md` | Unchanged |
| `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md` | Unchanged (TASK 02 input) |
| `docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md` | Unchanged (pre-existing untracked) |
| `CURRENT_POSITION.md`, `MASTER_ROADMAP.md`, `PROJECT_STATE.md`, `SESSION_HANDOFF.md` | Unchanged (pre-existing modified) |
| `2_NexusSchool.zip`, `NexusSchool.zip` | Unchanged (pre-existing untracked) |
| Application code, migrations, contracts, routes, config, dependencies | Unchanged |

### 12.3 Git baseline and expected final state

Baseline recorded before writing:

```text
Branch: main
HEAD:   d42dbef3e32ad6e7a7a4bc70c68f805be60da480
```

Pre-existing state (preserved):

```text
 M docs/docs/00_Project_Management/CURRENT_POSITION.md
 M docs/docs/00_Project_Management/MASTER_ROADMAP.md
 M docs/docs/00_Project_Management/PROJECT_STATE.md
 M docs/docs/SESSION_HANDOFF.md
?? 2_NexusSchool.zip
?? NexusSchool.zip
?? docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md
?? docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md
```

Expected change from this task: one additional untracked file, the blueprint. No tracked file is modified. The exact final `git status --short` is recorded in section 12.4 after verification.

### 12.4 Final verification

Recorded at the end of this task:

```text
(see section 12.5)
```

### 12.5 Final Git status (recorded)

```text
 M docs/docs/00_Project_Management/CURRENT_POSITION.md
 M docs/docs/00_Project_Management/MASTER_ROADMAP.md
 M docs/docs/00_Project_Management/PROJECT_STATE.md
 M docs/docs/SESSION_HANDOFF.md
?? 2_NexusSchool.zip
?? NexusSchool.zip
?? docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md
?? docs/docs/00_Project_Management/ADR-024_REVISION_BLUEPRINT_2026-11-06.md
?? docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md
```

`git diff --stat` (tracked files only, all pre-existing):

```text
 .../docs/00_Project_Management/CURRENT_POSITION.md |  72 +++++++++---
 docs/docs/00_Project_Management/MASTER_ROADMAP.md  |  58 ++++++++--
 docs/docs/00_Project_Management/PROJECT_STATE.md   |  65 +++++++++--
 docs/docs/SESSION_HANDOFF.md                       | 126 ++++++++++++++-------
 4 files changed, 246 insertions(+), 75 deletions(-)
```

Interpretation:

- The only addition attributable to this task is the blueprint (`??`).
- The four `M` entries are the preserved 05 November 2026 governance reconciliation.
- The other two `??` reports and the two `??` ZIPs predate this task.
- No tracked file changed, so `git diff --stat` shows no new entries for this task.

### 12.6 Change classification

| Category | Count | Detail |
|---|---|---|
| Files created by this task | 1 | This blueprint |
| Files modified by this task | 0 | - |
| Files deleted by this task | 0 | - |
| Application/code changes | 0 | - |
| Migrations | 0 | - |
| Contracts/interfaces | 0 | - |
| Routes | 0 | - |
| Config changes | 0 | - |
| Dependency changes | 0 | - |
| Destructive Git operations | 0 | - |
| Governance status changes | 0 | ADR-024 still `Proposed`; SPR-014 still `DEFERRED`; SPR-017 still not implemented |

---

## 13. Appendix

### 13.1 Classification key (consolidated)

| Label | Definition | How to challenge it |
|---|---|---|
| **VERIFIED IMPLEMENTED** | Repository code, schema, config or an executed test directly demonstrates the capability | Check the cited path/line |
| **PARTIALLY IMPLEMENTED** | A supporting mechanism exists, but a required part is missing, unenforced or unproven | Identify the missing part in the cited file |
| **NOT IMPLEMENTED** | No repository artifact implements the capability | Search for the artifact |
| **UNVERIFIED** | No evidence either way; success must not be assumed | Produce a record or test |
| **PROPOSED** | A design direction in this blueprint; no code or decision exists | Owner must record a decision |
| **CONFLICT** | Two or more records disagree | Owner must reconcile |
| **STALE** | A document references a state that the repository has moved past | Correct the reference |
| **DOCUMENTED INTENT** | A document states a goal or policy; no implementation evidence | Produce implementation evidence |
| **NOT RECORDED** | A required governance record is absent | Create the record |

### 13.2 Review finding coverage summary

| Group | Findings | Addressed in | Decision IDs |
|---|---|---|---|
| A - Domain contracts | A1-A3 | 3.1, 4.1 | DR-01 to DR-04 |
| B - Storage | B1-B5 | 3.2, 4.2, 4.6, 4.7 | DR-05, DR-15 to DR-19 |
| C - Retention/audit | C1-C5 | 3.4, 3.5, 4.4, 4.5 | DR-09 to DR-13 |
| D - Quota | D1-D5 | 3.3, 4.3 | DR-06 to DR-08 |
| E - Security | E1-E7 | 3.6, 4.6 | DR-11, DR-14 to DR-17, DR-24, DR-26 |
| F - Residency | F1-F4 | 3.7, 4.8 | DR-21, DR-22 |
| O - SPR-014 observations | O-1 to O-4 | 3.9, 7 | DR-12, DR-28 |
| RV - Revision requirements | RV-01 to RV-12 | 9 | All acceptance-blocking DRs |

### 13.3 Decision register index

| Classification | Decision IDs | Count |
|---|---|---|
| MUST DECIDE BEFORE ADR ACCEPTANCE | DR-01, DR-02, DR-03, DR-04, DR-05, DR-06, DR-07, DR-09, DR-12, DR-13, DR-14, DR-15, DR-16, DR-19, DR-26, DR-27, DR-28, DR-29 | 18 |
| MAY BE DEFERRED | DR-23, DR-24 | 2 |
| MUST BE DECIDED BEFORE PRODUCTION | DR-08, DR-10, DR-11, DR-17, DR-18, DR-21 | 6 |
| MUST BE DECIDED BEFORE CLOUD ACTIVATION | DR-20, DR-22, DR-25 | 3 |
| **Total** | DR-01 to DR-29 | **29** |

### 13.4 Required decision-area coverage check

| Required area | Covered in register | Register IDs |
|---|---|---|
| Domain integration contracts | Yes | DR-01, DR-02, DR-03, DR-04 |
| Storage port/adapter boundary | Yes | DR-05 |
| Quota behavior | Yes | DR-06, DR-07, DR-08 |
| Retention configuration | Yes | DR-09, DR-10, DR-11 |
| Audit catalogue/retention | Yes | DR-12, DR-13 |
| Permissions | Yes | DR-14, DR-26 |
| Local storage security | Yes | DR-15, DR-16, DR-17 |
| Backup/recovery | Yes | DR-18, DR-19 |
| Cloud activation | Yes | DR-20, DR-25 |
| Data residency | Yes | DR-21, DR-22 |

### 13.5 Glossary

| Term | Meaning in this blueprint |
|---|---|
| **ADR** | Architecture Decision Record |
| **SPR-012** | Academic Communication Engine (also called "Business Engine" in some task wording) |
| **SPR-014** | Records Lifecycle Management Engine |
| **SPR-017** | Archive & Cloud Storage Platform |
| **Archive entry** | A row recording that an official record was archived, with provenance and object pointer |
| **Archive object** | The stored file (PDF or other) referenced by an archive entry |
| **Projection / reference DTO** | An immutable read model returned by a contract; never an Eloquent model |
| **Storage port** | Provider-neutral interface for storage operations |
| **Adapter** | A concrete implementation of a port (for example local disk) |
| **Disposition** | The retention outcome: preserve permanently or destroy after a period |
| **Legal hold** | An override that prevents destruction while an investigation or litigation is active |
| **Deletion certificate** | A recorded, auditable proof that a specific record was destroyed under an approval |
| **Break-glass** | A privileged, audited mechanism for exceptional cross-tenant access |
| **Report-only** | A quota mode that reports usage and limits but never blocks an operation |
| **DoD** | Definition of Done |
| **DPA** | Data Processing Agreement |
| **RTO / RPO** | Recovery Time Objective / Recovery Point Objective |
| **WORM** | Write Once Read Many storage semantics |

### 13.6 Reviewer checklist

Use this to verify the blueprint itself, not the ADR.

- [ ] Does every important conclusion cite a repository path, test, or document?
- [ ] Are implementation state and proposed architecture kept separate?
- [ ] Does the decision register contain all ten required areas?
- [ ] Does every decision have one of the four required classifications?
- [ ] Does every decision have a role-based owner?
- [ ] Is ADR-024 still `Proposed`?
- [ ] Is SPR-014 still `DEFERRED`?
- [ ] Is SPR-017 still unimplemented and unauthorized?
- [ ] Are legal, security, product and operational decisions left to their owners?
- [ ] Is encryption at rest described truthfully (not claimed without evidence)?
- [ ] Is the 7-year versus 90-day audit contradiction recorded rather than silently resolved?
- [ ] Are passing tests explicitly not treated as sprint closure?
- [ ] Is a generic waiver explicitly not treated as staging evidence?
- [ ] Does the dependency map cover acceptance, implementation, production and cloud?
- [ ] Are limitations and human approvals explicit?
- [ ] Does the Git section show only one new file and the preserved pre-existing state?

### 13.7 Document conventions

- Identifiers: `EV-xx` and `BP-E-xx` for evidence, `A/B/C/D/E/F/O` and `RV-xx` for review findings, `DR-xx` for decisions, `ACC-xx`/`IMP-xx`/`PRD-xx`/`CLD-xx` for dependency nodes, `AC-xx` for acceptance criteria, `R-xx` for readiness conditions, `RR-xx` for risks, `UQ-xx` for questions, `CF-xx` for conflicts, `H-xx` for human approvals.
- Status words in bold (`VERIFIED IMPLEMENTED`, `NOT IMPLEMENTED`, etc.) are classifications, not approvals.
- "Proposed" means design direction only.
- Role names are placeholders for the governance process to assign named individuals.

---

**End of blueprint.**

This document records analysis and proposals only. ADR-024 remains `Proposed`, SPR-014 remains `DEFERRED`, and SPR-017 remains unimplemented and unauthorized.
