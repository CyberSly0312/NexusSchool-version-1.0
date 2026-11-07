# NexusSchool Enterprise Platform

# ADR-024 Architecture Review - 06 November 2026

**Document ID:** ADR-REV-2026-11-06

**Status:** Completed (documentation-only) - review recommendation, not an approval

**Owner:** Architecture Team

**Reviewer:** Architecture review agent, on behalf of the NexusSchool Architecture Team

**Date:** 06 November 2026

**Subjects:** ADR-024 (Archive & Cloud Storage Platform Architecture), SPR-014 (Records Lifecycle Management Engine) closure, SPR-017 (Archive & Cloud Storage Platform) readiness

---

## 1. Purpose, Authority and Limitations

This report records a formal architectural review of ADR-024 and an evidence-based
closure assessment of SPR-014. It is the TASK 02 deliverable.

This report is **advisory**. It does **not**:

- approve, accept, promote or amend ADR-024 (which remains `Proposed`);
- close, complete or re-scope SPR-014 (which remains `DEFERRED`);
- authorize, plan or begin SPR-017 implementation;
- approve a cloud provider, region, retention period, budget, encryption design or
  legal position;
- substitute for a named human owner's signature.

The review is based on repository evidence only. No staging environment, CI system,
cloud account, production database, legal source or owner interview was available.
Where evidence is absent, the report records the absence; it does not infer success.

**Documentation-only constraint honoured:** no application code, migration, model,
contract, route, configuration, dependency or business rule was created or modified.
The only file created by this task is this report. Pre-existing uncommitted work was
preserved.

---

## 2. Baseline and Preservation Controls

Recorded before this review:

| Item | Value |
|---|---|
| Branch | `main` |
| HEAD | `d42dbef3e32ad6e7a7a4bc70c68f805be60da480` |
| HEAD subject | `correction du login` |
| HEAD date | 2026-11-03 12:28:46 +0000 |
| Tracked modifications | 4 governance documents (from GOVERNANCE RECONCILIATION_2026-11-05) |
| Untracked | Prior reconciliation report and two ZIP archives |
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
?? docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md
```

The four modified governance documents are the authorised output of the immediately
preceding reconciliation task. This review neither reverts nor extends them.

---

## 3. Executive Summary

### 3.1 SPR-014

SPR-014 is **technically implemented and targeted-tested**. Its lifecycle envelope,
three-state machine, policy enforcement and the three source-domain contract seams
(Report Card, Academic Communication, Class Council) are present in the repository,
and the current targeted validation passes (6 unit tests / 19 assertions; combined
targeted run 143 tests / 981 assertions, as recorded by the 2026-11-05 reconciliation).

SPR-014 is **not closable under the project Definition of Done**. Three governance
requirements are missing:

1. a formal review approval record;
2. documented successful staging deployment evidence;
3. a recorded sprint closure decision.

**Recommendation: REQUEST ADDITIONAL EVIDENCE.** SPR-014 remains `DEFERRED` until the
evidence is supplied or an authorised owner records an explicit waiver.

### 3.2 ADR-024

The architectural direction of ADR-024 is sound and well aligned with the repository:
a thin `App\Domains\Archive` bounded context, local-first storage, contract-only
consumption, no second lifecycle authority, no invented legal retention values,
backup/archive separation, and cloud activation gated behind explicit decisions.

ADR-024 is **not ready for acceptance in its current form**. The review identified
material specification gaps that would force implementation to invent architecture:

- the read contracts Archive would consume do **not exist**;
- `plans.storage_limit` is a free-text column, **not** a working quota mechanism;
- the retention mechanism is asserted but not structurally specified;
- there is no audit event catalogue, and existing audit-log cleanup contradicts the
  documented 7-year retention;
- the at-rest encryption posture of local storage is stated as "existing defaults"
  although no local at-rest encryption control is evidenced;
- the decision log conflicts with the SPR-017 decision-board report, and some
  repository evidence is stale.

**Outcome: REVISION REQUIRED.** ADR-024 remains `Proposed`. The required revisions
are enumerated in section 13.2.

### 3.3 SPR-017

SPR-017 is **NOT READY** for implementation. No Archive domain, migration, route or
config exists, and the architectural prerequisites (ADR acceptance, SPR-014 closure,
read contracts, quota read model, storage port, retention schema, audit contract,
archive permissions, staging path) are not in place.

### 3.4 Top blockers

| # | Blocker | Owner | Severity |
|---|---|---|---|
| B1 | SPR-014 has no approval, staging evidence or closure record | Product Owner + Architecture + Ops | High (governance) |
| B2 | No read contracts for Archive to consume | Architecture | High |
| B3 | `plans.storage_limit` is not a working quota mechanism | Product + Architecture | High |
| B4 | Retention mechanism unspecified; legal values absent | Product + Legal + Architecture | Critical (if destruction enabled) |
| B5 | Audit events and audit-retention contradiction | Architecture + Security | High |
| B6 | Local at-rest encryption posture unproven | Security + Architecture | High |
| B7 | Decision-log numbering/provenance conflict | Architecture | Medium |
| B8 | No archive permissions, tests, migrations or staging plan | Architecture + Ops | Medium |

---

## 4. Part A - SPR-014 Closure Review

### 4.1 Scope and sources reviewed

- `docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md`
  (the only SPR-014 specification/report in the repository; no separate sprint
  specification file exists).
- `app/Domains/RecordsLifecycle/` (state enum, service, actions, model, policy,
  service provider).
- `app/Domains/AcademicCommunication/`, `app/Domains/ReportCard/`,
  `app/Domains/ClassCouncil/` contract and adapter seams.
- `tests/Unit/Domains/RecordsLifecycle/`,
  `tests/Feature/Domains/RecordsLifecycle/`, and the three named integration tests.
- `database/migrations/2026_08_10_000000_create_record_lifecycles_table.php`.
- `docs/07_Development/Development_Workflow.md`,
  `docs/00_Project/Engineering_Principles.md` (Definition of Done).
- `docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md`,
  `CURRENT_POSITION.md`, `PROJECT_STATE.md`, `SESSION_HANDOFF.md`.
- ADR-021, ADR-024 (SPR-014 interlock references).

### 4.2 Definition of Done baseline

Two documents define "done" in this repository:

| Source | Criteria |
|---|---|
| `docs/07_Development/Development_Workflow.md` | Code implemented; tests written; documentation updated; review approved; successfully deployed to staging |
| `docs/00_Project/Engineering_Principles.md` | Business rules documented; code follows standards; tests implemented; security considered; documentation updated; audit requirements satisfied; performance implications acceptable |

SPR-014's own report adds sprint-scope completion criteria (three source-domain
integrations, architecture tests, no source-model imports) and a governance closure
decision. This review applies both DoD sources plus the sprint scope, because the
task requires a full evidence-based closure assessment rather than a narrow reading.

### 4.3 Closure matrix

Classification key: **SATISFIED** = direct repository evidence; **NOT SATISFIED** =
requirement is unmet or contradicted; **UNVERIFIED** = no evidence either way, so
success must not be assumed; **NOT APPLICABLE** = criterion does not apply to this
slice.

| # | DoD criterion | Evidence reviewed | Assessment | Classification |
|---|---|---|---|---|
| 1 | Code implemented | `RecordLifecycleState`, `RecordLifecycleService`, 3 actions, `RecordLifecycle` model, policy, provider, migration `2026_08_10_000000` | All present and wired | **SATISFIED** |
| 2 | Unit tests written and passing | `tests/Unit/Domains/RecordsLifecycle` re-run in the 2026-11-05 reconciliation | PASS - 6 tests / 19 assertions (state matrix + architecture) | **SATISFIED** |
| 3 | Feature/security tests written and passing | `tests/Feature/Domains/RecordsLifecycle/RecordLifecycleFeatureTest.php`; included in combined targeted run | PASS within combined 143 tests / 981 assertions; historical isolated figure 6 tests / 19 assertions | **SATISFIED** |
| 4 | Report Card integration | `ReportCard/Domain/Contracts/RecordsLifecycleContract.php`, adapter, `Generate/Publish/ArchiveReportCardAction`; `ReportCardRecordsLifecycleIntegrationTest.php` | PASS - 5 tests / 20 assertions (historical report) | **SATISFIED** |
| 5 | Academic Communication integration | `AcademicCommunication/Domain/Contracts/RecordsLifecycleContract.php`, adapter, `Generate/Publish/ArchiveDocumentAction`; integration test | PASS - 5 tests / 19 assertions (historical report) | **SATISFIED** |
| 6 | Class Council integration | `ClassCouncil/Domain/Contracts/RecordsLifecycleContract.php`, adapter, `Create/Publish/ArchiveCouncilSessionAction`; integration test | PASS - 5 tests / 20 assertions (historical report) | **SATISFIED** |
| 7 | Architecture tests (namespace/dependency direction) | `RecordLifecycleArchitectureTest.php` | PASS - 1 test / 10 assertions | **SATISFIED** |
| 8 | Business rules documented | Report sections 5-8, 10, 14; enum transition matrix; model invariants | State machine, boundaries and tenant rules documented | **SATISFIED** |
| 9 | Code follows project standards | PHPStan and Pint gates recorded in the report; reconciliation re-ran scoped PHPStan/Pint | PASS - 0 PHPStan errors; Pint clean (scoped) | **SATISFIED** |
| 10 | Security considered | `HasSchoolId`, `RecordLifecyclePolicy` (view/create/update/publish/archive), tenant-scoped queries, feature/security tests | School scoping and policy checks present; no separate security review report | **SATISFIED** (domain scope) |
| 11 | Documentation updated | SPR-014 report; prior governance reconciliation | Report and governance status consistent on `DEFERRED` | **SATISFIED** |
| 12 | Audit requirements satisfied | Lifecycle metadata stores `created_by`, `origin_event`, `published_at`, `archived_at`; **no** RecordsLifecycle domain events or `audit_logs` listener | Metadata-level provenance exists; platform audit-log integration is absent | **SATISFIED** (domain-level, with observation O-1) |
| 13 | Performance implications acceptable | No benchmark, load test, or query-plan evidence in any SPR-014 artifact | No evidence produced | **UNVERIFIED** |
| 14 | Formal code/review approval | Search of governance docs and reports | No approval record, signature, reviewer name or approval date exists; approval is **pending** | **NOT SATISFIED** |
| 15 | Successfully deployed to staging | `docs/08_Deployment/Development.md` and `CI_CD.md` describe a staging process only; no deployment record, release note, smoke-test result or environment evidence | No evidence; success must not be assumed | **UNVERIFIED** |
| 16 | Sprint closure decision recorded | `CURRENT_POSITION.md`, `PROJECT_STATE.md`, `SESSION_HANDOFF.md`, ADR-024 all state `DEFERRED`; no closure decision record | Not recorded | **NOT SATISFIED** |

**Gate audits, not repeated here:** the 2026-11-05 reconciliation re-ran the targeted
suites (143 tests / 981 assertions), scoped PHPStan and Pint. It explicitly did
**not** re-run the full 1,889-test repository suite. Historical full-suite figures are
therefore context, not fresh evidence.

### 4.4 Observations

**O-1 - Audit integration gap (non-blocking for the three governance items).**
`RecordLifecycleService` and its actions perform database writes with no domain event
and no `audit_logs` write. The lifecycle row itself records actor and timestamps, and
source domains emit their own events (for example `DocumentArchived`) that their audit
listeners persist. That is an acceptable metadata-level trail for this slice, but it
is not a RecordsLifecycle-owned audit stream. If SPR-017 relies on auditability of
archive transitions, the audit event contract must be defined at ADR level.

**O-2 - Cross-tenant service edge.** `ArchiveRecordLifecycleAction` loads
`RecordLifecycle::findOrFail($recordId)` without a school constraint and skips the
policy check entirely when `$actor === null`. The supported contract/adapter path
first resolves the record by `school_id`, so normal integration is safe. SPR-017 must
be forbidden from calling `RecordLifecycleService::archive()` directly and must use a
tenant-scoped read/contract path.

**O-3 - Two `archived` concepts.** `DocumentStatus` also has an `Archived`
case (`DocumentStatus::Archived`), and `ArchiveDocumentAction` updates both the
document status and the lifecycle envelope in one transaction. There is no
model-level invariant guaranteeing the two never diverge. ADR-024 declares the
lifecycle `archived` state to be the archive trigger, but does not resolve what
happens when the two disagree.

**O-4 - No standalone sprint specification.** The sprint report doubles as
specification. This is acceptable for a narrow slice but weakens traceability of
"scope agreed" versus "scope implemented".

### 4.5 Closure recommendation

**REQUEST ADDITIONAL EVIDENCE.**

Rationale:

- The implementation, targeted tests, integration seams and documentation are
  substantively complete.
- The remaining gaps are evidential/governance, not code defects.
- The missing evidence is concrete and obtainable: an approval record, a staging
  deployment record, and a closure decision.
- Approving closure now would violate the project DoD and would allow SPR-017 to rely
  on an unclosed prerequisite.

**Status remains `DEFERRED`.** This report does not change any governance document's
status.

**Minimum evidence package for closure:**

1. A named approver (Product Owner and/or Architecture Team as the owner decides) and
   an approval date, recorded in the SPR-014 report or a signed closure record.
2. A staging deployment record: environment, commit SHA, deployment date, executed
   migration set, smoke-test result, and rollback note.
3. A recorded closure decision that explicitly accepts the scope, the evidence, and
   any residual observations (O-1 to O-4).
4. If staging evidence will not be produced, an explicit, owner-signed waiver that
   states the risk and the compensating controls. A waiver is not the same as a
   passing gate and must be recorded as such.

### 4.6 Interlock consequence

ADR-024 invariant **L4** states that SPR-017 does not mark SPR-014 complete and does
not reopen it; the interlock is contract-only consumption. This review adds the
governance interpretation:

- ADR-024 may be **reviewed and revised** while SPR-014 is deferred.
- SPR-017 **implementation** must not begin until SPR-014's closure decision is
  recorded, or until the project owner records an explicit waiver of that interlock.
- Accepting ADR-024 must not be treated as closing SPR-014, and closing SPR-014 must
  not be treated as accepting ADR-024. They are separate decisions with separate
  owners and evidence.

---

## 5. Part B - ADR-024 Architectural Analysis

### 5.A Domain boundaries

**Proposal in ADR-024.** A dedicated `App\Domains\Archive` bounded context that owns
archive provenance, retention policy configuration/evaluation, archive lifecycle
handling and future package generation. It consumes SPR-014 and SPR-012 through
contracts only, never creates/mutates/deletes source records, and never replaces
`RecordsLifecycle` (Decision 3, L1-L5, LG1-LG3, responsibility matrix).

**Repository evidence.**

| Fact | Evidence |
|---|---|
| No Archive domain exists | `app/Domains/` has 46 contexts; `Archive` is absent |
| Records Lifecycle is a thin envelope | `RecordLifecycleState` has only `registered`, `published`, `archived`; `RecordLifecycleService` exposes `register`, `publish`, `archive`, `findBySource` |
| Source domains own business records | `AcademicCommunication` owns `AcademicDocument`, versions and `GeneratedFile`; `ReportCard` owns runs; `ClassCouncil` owns sessions |
| Certificate/Transcript delegates | `CertificateAndTranscript` composes and delegates to Academic Communication; it does not own a second lifecycle |
| Identity/Verification has a read-only precedent | `AcademicDocumentVerificationContract::stateForDocument()` exposes a narrow projection |
| Archive boundary is already a documented dependency rule | `DEPENDENCY_MAP.md` §13/§15: `Archive -> Business logic` is forbidden |

**Finding A1 - The read seam does not exist.** ADR-024 says Archive consumes
"SPR-014 via the `RecordsLifecycleContract` / read surface". Every
`RecordsLifecycleContract` in the repository is a **write/gateway** interface with
only `register`, `publish` and `archive`. There is no read contract, no list/query
contract, and no DTO projection suitable for Archive. `RecordLifecycleService::findBySource()`
is a concrete service method returning the `RecordLifecycle` model, which Archive
Application code must not import under C1. **This is a specification gap, not a code
gap: the ADR names a contract that does not exist.**

**Finding A2 - No Academic Communication read contract for archive.** The only
Academic Communication read-only contract is `AcademicDocumentVerificationContract`,
whose projection is strictly verification-relevant (state only). Archive needs document
metadata and current file reference (disk, path, size, checksum, mime, document number,
type, version), which no contract exposes. `AcademicCommunicationService` exposes
commands (generate/review/approve/publish/archive/regenerate), not queries. A
read-contract precedent does exist elsewhere (`AcademicPolicyQueryContract`), so the
pattern is established even though the Academic Communication seam is missing.

**Finding A3 - Ownership must be stated as a closed list.** "Archive owns archive
provenance..." is directionally correct but not testable as written. The ADR must
list the concrete artifacts Archive owns and the concrete artifacts it must never
own.

**Recommended boundary (acceptance-ready):**

| Archive owns | Archive may read (via contracts only) | Archive must never own or mutate |
|---|---|---|
| `archive_entries` (provenance + object pointer) | Lifecycle state and source identity | `record_lifecycles` (SPR-014) |
| `archive_retention_policies` (mechanism, not legal values) | Document metadata and current file reference | `academic_documents`, versions, approvals, signatures |
| Archive audit events (its own event catalogue) | Tenant identity and authorization context | `generated_files` (SPR-012 owns storage records) |
| Package request/job metadata (deferred release) | Plan/subscription quota projection | Verification identities, certificates/transcripts |
| Retention evaluation outcomes | | Any source-domain state transition |

Archive must not: generate documents, render PDFs, number documents, decide academic
validity, publish, verify identities, issue certificates, or maintain a second
lifecycle state machine.

### 5.B Storage architecture

**Proposal in ADR-024.** Local-first on the existing tenant-suffixed `local`/`public`
disks; per-school prefix `archive/{school_id}/...`; decoupled storage seam proposed
as a single S3-compatible abstraction; cloud deferred behind a five-item activation
checklist.

**Repository evidence.**

| Fact | Evidence |
|---|---|
| Disks are stock local/public/s3 | `config/filesystems.php`: `local` root `storage_path('app/private')`, `public` root `storage_path('app/public')`, `s3` env-driven |
| Default is local | `'default' => env('FILESYSTEM_DISK', 'local')` |
| Tenant suffixing covers local/public only | `config/tenancy.php` tenant disks list includes `local`, `public`; `// 's3'` is commented at line 142 |
| One document disk switch | `config/nexus-academic-communication.php` line 25: `env('NEXUS_DOCUMENT_DISK', 'local')` |
| Official document path | `documents/{school_id}/{type}/{number}-v{n}.pdf` in `GenerateDocumentAction.php:212`, `RegenerateDocumentAction.php:95`, `PdfExportChannel.php:72` |
| File metadata exists | `generated_files`: `school_id`, `file_path`, `file_name`, `disk`, `mime_type`, `size_bytes`, `checksum` (64), `is_current` |
| Integrity digest exists | SHA-256 written at `GenerateDocumentAction.php:236`, `RegenerateDocumentAction.php:118`, `PdfExportChannel.php:90` |
| No storage abstraction | Domain code calls `Storage::disk(...)` directly; no port/adapter for object storage |
| No download/authorization seam for archive | No archive route/controller exists |

**Findings.**

- **B1 - Path partition is not authorization.** The ADR correctly says this. The
  implementation must enforce it: resolve tenant, authorize policy, then load by
  `school_id` + id; never construct a path from user input.
- **B2 - Metadata is sufficient for a first release, with fixes.** `size_bytes` has a
  default of `0`; `checksum` is required but no verification-on-read exists; `disk` is
  a free string with no allow-list. Archive must treat these as untrusted until
  verified.
- **B3 - "S3-compatible abstraction" is premature as a mandatory seam.** No cloud
  vendor or protocol has been approved (ODC1). Requiring S3 compatibility constrains
  the design before the decision. The seam should be a provider-neutral storage port
  with a local adapter; an S3 adapter can implement it later if approved.
- **B4 - At-rest encryption is not evidenced.** The local disk stores plain files;
  `config/database.php` has `// 'encrypt' => ...` commented out; no application-level
  encryption/key-management service was found. "Follows existing platform defaults"
  is not a control statement. The ADR must either require at-rest encryption before
  production or explicitly record it as an accepted risk/open decision.
- **B5 - Backup coverage is assumed.** `docs/08_Deployment/Backups.md` describes
  daily/weekly/monthly encrypted backups with off-site copy and RTO < 2h / RPO < 15m,
  and `app/Console/Commands/System/SystemBackupCommand.php` exists. There is no
  evidence that archive paths would be included, nor a restore test. Archive must be
  explicitly added to backup scope; archive is not a backup.

**Recommended storage design (acceptance-ready):**

- Private tenant disk only; never the `public` disk for archived official documents.
- Path scheme: `archive/{school_id}/{document_type}/{academic_year}/{document_number}-v{n}.pdf`,
  with `school_id` and `document_number` validated against the resolved metadata, not
  user input.
- Persist `disk`, relative `path`, `size_bytes`, `checksum_sha256`, `mime_type`,
  `source_document_id`, `source_version_id`, `generated_file_id`.
- Verify checksum before every download and in a periodic integrity job; record the
  verification result.
- Download through an authorized controller/action that streams the file with
  `Content-Disposition: attachment`; no public URL, no inline rendering, no
  unauthenticated signed URL. If temporary URLs are used later, authorization must
  precede issuance and the URL must be short-lived and audited.
- Provider-neutral `StoragePort` (`put`, `getStream`, `exists`, `delete`, `checksum`);
  local adapter first. S3 adapter only after ODC1/ODC2 close.
- No physical deletion in the first release; metadata soft-delete only. Physical
  deletion is a separate, gated capability.

### 5.C Retention policies

**Proposal in ADR-024.** Configurable per document type and per school; mechanism
built now, values owned by product/legal; permanent preservation and secure
destruction are distinct; automatic destruction disabled by default; legal values
deferred to ODL/ODD.

**Repository evidence.**

| Fact | Evidence |
|---|---|
| No retention implementation | No archive/retention table, config, service or job exists |
| Audit retention is documented as 7 years | `docs/03_Database/Audit.md` lines 74-79: "Audit logs are immutable. Minimum 7 years. Configurable per institution." |
| Audit cleanup defaults to 90 days | `app/Console/Commands/System/SystemCleanupCommand.php:14,23-24` deletes audit logs older than `--days=90` |
| Backup retention is 30d/12w/12m | `docs/08_Deployment/Backups.md` lines 66-78; this is recovery retention, not records retention |
| No country-specific legal periods | Repository references Burkina Faso/Ghana only as academic-framework examples; no data-protection or records law |
| Two archive semantics | `DocumentStatus::Archived` and `RecordLifecycleState::Archived` both exist |
| `archived_at` exists on both | `academic_documents.archived_at` and `record_lifecycles.archived_at` |

**Findings.**

- **C1 - The mechanism is not specified.** No policy schema, scope precedence, clock
  start, disposition enum, legal-hold concept, jurisdiction field, evaluation
  trigger, dry-run mode, or deletion certificate is defined. "Configurable" is a
  goal, not a design.
- **C2 - Legal values are correctly absent.** ADR-024 does not invent them, which is
  correct. This review also does not invent them. The absence must remain an explicit
  gate, not an implicit default of "keep forever" or "delete after X".
- **C3 - Destruction safety is stated, not enforced by design.** "Disabled by
  default" must be represented as a config default, a permission, and a code path
  that cannot run without an approval record. A comment is not a safeguard.
- **C4 - Audit retention contradiction must be resolved before relying on audit logs
  for retention evidence.** A 90-day cleanup cannot support a 7-year immutable audit
  claim.
- **C5 - Clock start is ambiguous.** Retention could run from publication, document
  generation, archive entry, or school closure. The ADR must choose one and define
  extension/review semantics.

**Recommended retention model (acceptance-ready):**

- `archive_retention_policies`: `id`, `school_id` (nullable = platform default),
  `document_type` (nullable = all types), `jurisdiction` (nullable), `retention_months`
  (nullable = indefinite), `disposition` (`preserve` | `destroy`), `legal_basis`
  (text, required for `destroy`), `is_active`, `effective_from`, `created_by`,
  timestamps.
- Precedence: school+type > school+all > platform+type > platform+all; unconfigured =
  preserve indefinitely, no destruction.
- Clock: retention starts at `archived_at` (explicit), with an optional
  `retention_starts_at` override.
- `archive_entries.legal_hold` boolean + reason; legal hold always wins.
- Evaluation: on archive entry and by a scheduled job; always audit the evaluation,
  the policy matched, the computed due date, and the outcome.
- Destruction: disabled by default; requires ODL values, ODD approval workflow,
  explicit permission, dry-run evidence, and an operator ceremony. Never automatic
  in the first release.
- No hard-coded duration anywhere; no deletion when policy is missing or ambiguous.
- A "retention preview" (dry-run) is mandatory before enabling destruction.

### 5.D Storage quotas

**Proposal in ADR-024.** Reuse `plans.storage_limit` as the single authoritative plan
limit; no second quota authority; metering and enforcement are open (ODQ).

**Repository evidence.**

| Fact | Evidence |
|---|---|
| Column is a string | `database/migrations/2026_07_24_000001_create_plans_table.php:23`: `$table->string('storage_limit')->default('1GB')` |
| Seed values are strings | `database/seeders/PlanSeeder.php:25,40,55,70`: `500MB`, `2GB`, `10GB`, `50GB` |
| No cast/accessor/parser | `app/Models/Plan.php` lists it as fillable only; no `casts()` entry, no helper |
| Only a Filament text input | `app/Filament/Resources/PlanResource.php:96` |
| No consumption service | Repository-wide search finds no metering, aggregation or enforcement of `storage_limit` |
| School is not directly linked to a plan | `app/Domains/School/Models/School.php` has no plan/subscription relation |
| Link is via subscriptions | `subscriptions` has `school_id` -> `tenants`, `plan_id` -> `plans`, `starts_at`, `expires_at`, `status` (`active/expired/suspended/cancelled`) |
| Plan has many subscriptions | `app/Models/Plan.php::subscriptions()` |
| File sizes exist but are not aggregated | `generated_files.size_bytes` is per file only |

**Findings.**

- **D1 - The ADR overstates the current mechanism.** "Quotas reuse `plans.storage_limit`"
  is true only as a data reference. There is no unit parser, no canonical byte value,
  no usage measurement, no threshold evaluation, no enforcement point, and no
  school->plan resolver. The Decision Summary should say "the plan limit column is the
  intended authority; metering/enforcement is not implemented and is out of first
  release until ODQ closes."
- **D2 - Unit ambiguity is a correctness risk.** `'1GB'` could mean 1,000,000,000 or
  1,073,741,824 bytes; values may be lowercase, have spaces, or be invalid. A canonical
  unit and validator are mandatory before any comparison.
- **D3 - Subscription resolution is undefined.** A school may have zero, one or many
  subscriptions; expired/suspended/cancelled semantics are not specified. Which
  subscription supplies the plan on a given date? What happens after expiry? The ADR
  must define this before any quota read.
- **D4 - Scope must stay per school.** Plan rows are global; the limit applies to the
  school's active subscription. Do not create a second quota table in Archive.
- **D5 - Metering belongs to the platform/SaaS layer, not Archive.** Archive should
  consume a quota projection; if usage is computed, it must include at least
  `generated_files.size_bytes` plus archive objects, with deduplication (an archive
  copy of an existing file may or may not double-count; this must be decided).

**Recommended quota design (acceptance-ready):**

- Add an `ArchiveQuotaContract` (or platform `StorageQuotaContract`) that, for a
  school, resolves the active subscription on a date and returns
  `limit_bytes`, `used_bytes` (nullable until metering exists), `enforcement_mode`
  (`report_only` | `soft_limit` | `hard_limit`), and `source` (plan id/name).
- Canonical unit: bytes, stored/derived with a strict parser and validation
  (`B`, `KB`, `MB`, `GB`, `TB`; binary or decimal explicitly chosen and documented).
- First release: `report_only`; record sizes and expose usage; do not block archive
  operations. Hard enforcement only after ODQ and Product approval.
- Do not write quota values into Archive tables; read them through the contract.
- Define double-counting rules for archived copies before metering.

### 5.E Security

**Proposal in ADR-024.** Tenant isolation in authorization and path; every read
re-authorized; no cross-tenant access; integrity digests; audit; encryption follows
existing defaults; cloud key handling open; no unauthorized deletion.

**Repository evidence.**

| Control | Evidence | Assessment |
|---|---|---|
| Tenant scoping | `HasSchoolId` global create + `scopeForSchool`; models implement `SchoolOwned` | Present |
| Lifecycle authorization | `RecordLifecyclePolicy`: `records_lifecycle.view/create/update/publish/archive`; `delete` returns false | Present |
| Document authorization | `HandlesDocumentAuthorization`: `document.view/generate/review/approve/publish/archive/template.manage`; school check | Present |
| Archive authorization | No `archive.*` permission group or policy exists | **Absent** |
| Private storage | `local` root `storage_path('app/private')`; official docs use `local` by default | Present |
| Public exposure | `public` disk exists for public assets; no archive route | No archive exposure yet |
| Integrity | SHA-256 stored; no verification on read or periodic job | Partial |
| Encryption at rest | Local disk plain; DB `encrypt` commented; no key service | **Not evidenced** |
| Audit log | `audit_logs` table with school, actor, action, old/new values, IP, UA | Present, but no Archive integration |
| Audit retention | Docs say 7 years; cleanup defaults to 90 days | **Contradiction** |
| Deletion controls | `RecordLifecyclePolicy::delete` false; no archive deletion path | Partial; archive design must enforce |
| Rate limiting | Verification routes have a limiter; no archive routes | N/A today |
| Super-admin bypass | Platform-wide pattern | Present; must be intentional for archive |

**Findings.**

- **E1 - Archive needs its own permission surface.** Add `archive.view`,
  `archive.manage`, `archive.export` (and possibly `archive.destroy`) before any
  route/UI. Do not reuse `document.*` for archive management; the capabilities differ.
- **E2 - Download authorization must be explicit.** Define the controller/action,
  policy, tenant resolution, headers, rate limit, audit event, and failure behavior
  (uniform 404 for wrong tenant). No direct file serving from the public directory.
- **E3 - Integrity verification is missing.** Store checksum at archive time; verify
  before download and on a schedule; fail closed on mismatch and raise an auditable
  incident.
- **E4 - Encryption at rest needs a truthful decision.** Either implement
  application-level encryption for archived objects (with key management) or record
  it as an accepted risk with the deployment's disk-level controls. Do not claim
  encryption because "Laravel defaults" exist.
- **E5 - Audit retention must be fixed.** Archive audit events must not be deleted by
  a 90-day cleanup if they are needed for retention evidence. Define an audit
  retention policy and exempt archive/retention events from generic cleanup, or write
  them to a dedicated, append-only store.
- **E6 - Destruction requires a real safeguard chain.** Permission + approval record
  + dry-run + two-person rule (if required) + audit + deletion certificate. A config
  flag alone is insufficient.
- **E7 - Package exports are a high-risk surface.** Even though deferred, the boundary
  must define exactly one school per package, requester authorization, manifest
  hashes, expiry, cleanup, and no partial artifacts.

### 5.F Data residency

**Proposal in ADR-024.** Local-first storage; cloud provider/region deferred; no
legal compliance claim; cloud activation gated by a residency decision.

**Repository evidence.**

- `docs/01_Architecture/Academic_Framework_2.md` lists Burkina Faso and Ghana as
  academic-framework examples, not as data-residency law.
- No data-protection impact assessment, DPA template, hosting-region record, or
  country-specific retention/residency requirement was found.
- `docs/08_Deployment/Backups.md` mentions local, cloud and off-site backup locations
  generically; no jurisdiction is attached.
- No cloud account, region, credential or provider is configured; `s3` is defined but
  unused and not tenant-aware.

**Findings.**

- **F1 - The technical and legal questions must stay separate.** Local-first means
  the architecture does not require cross-border transfer. It does **not** prove
  compliance with any country's data-protection or records law.
- **F2 - "Intended African market" is not a legal basis.** Market intent must not be
  converted into a residency or retention claim. Country-specific obligations require
  named legal research (ODL) and, for hosting, a residency decision (ODC1).
- **F3 - Deployment location is unknown.** The repository does not record where the
  production or staging host is. A local-first design on a host outside the school's
  country is still a cross-border deployment.
- **F4 - Controller/processor roles are undefined.** If schools are data controllers
  and the platform is a processor, a data-processing agreement and sub-processor list
  are needed. This is outside the ADR's current scope but blocks a production
  compliance claim.

**Recommended treatment (acceptance-ready):**

- Keep the cloud activation gate. Add a "Residency Decision Record" requirement
  before any cloud provider/region is selected.
- State explicitly: "No legal compliance is claimed; local-first is a technical
  property, not a legal conclusion."
- Record the actual hosting location(s) as deployment evidence at staging/production
  time, not as an architectural assumption.
- Separate legal retention (ODL) from technical residency (ODC1); neither can substitute
  for the other.
- If the product targets specific countries, add a jurisdiction field to retention
  policy and a legal review gate before enabling destruction.

---

## 6. Repository Evidence Ledger

This ledger records the facts used in this review. Line numbers are from the
reviewed working tree at HEAD `d42dbef`.

| ID | Fact | Path / evidence |
|---|---|---|
| EV-01 | No Archive bounded context | `app/Domains/` directory listing; `App\Domains\Archive` absent |
| EV-02 | No archive route | `routes/api.php`, `console.php`, `tenant.php`, `verification.php`, `web.php`; no archive route |
| EV-03 | No archive/retention/quota config | `config/` listing; no matching file |
| EV-04 | Local disk root | `config/filesystems.php:35` -> `storage_path('app/private')` |
| EV-05 | Public disk root | `config/filesystems.php:43` -> `storage_path('app/public')` |
| EV-06 | S3 disk defined but unused | `config/filesystems.php:50`; no domain usage |
| EV-07 | Tenant suffixing local/public only | `config/tenancy.php` lines 139-142; `// 's3'` at line 142 |
| EV-08 | Document disk switch | `config/nexus-academic-communication.php:25` |
| EV-09 | Official document path | `GenerateDocumentAction.php:212`; `RegenerateDocumentAction.php:95`; `PdfExportChannel.php:72` |
| EV-10 | SHA-256 file checksum | `GenerateDocumentAction.php:236`; `RegenerateDocumentAction.php:118`; `PdfExportChannel.php:90` |
| EV-11 | File metadata columns | `database/migrations/2026_08_06_000010_create_generated_files_table.php:15-25` |
| EV-12 | File FK cascade on tenant delete | same migration, line 29 |
| EV-13 | Lifecycle states | `app/Domains/RecordsLifecycle/Domain/Enums/RecordLifecycleState.php` |
| EV-14 | Lifecycle service API | `RecordLifecycleService.php:23-49` |
| EV-15 | Lifecycle contract is write-only | `AcademicCommunication/Domain/Contracts/RecordsLifecycleContract.php:22,36,46` |
| EV-16 | Lifecycle persistence | `database/migrations/2026_08_10_000000_create_record_lifecycles_table.php` |
| EV-17 | Lifecycle policy | `RecordLifecyclePolicy.php`; permissions `records_lifecycle.*` |
| EV-18 | No lifecycle domain events/listeners | `RecordsLifecycleServiceProvider.php`; no Events/Listeners; actions have no `event()`/audit writes |
| EV-19 | Document status includes Archived | `app/Domains/AcademicCommunication/Domain/Enums/DocumentStatus.php` |
| EV-20 | Archive action updates both states | `ArchiveDocumentAction.php` |
| EV-21 | Only Academic Communication read contract is verification-specific | `IdentityAndVerification/Domain/Contracts/AcademicDocumentVerificationContract.php`; read-contract precedent elsewhere: `AcademicPolicy/Domain/Contracts/AcademicPolicyQueryContract.php` |
| EV-22 | `plans.storage_limit` is a string | `database/migrations/2026_07_24_000001_create_plans_table.php:23` |
| EV-23 | Seeded storage values are strings | `database/seeders/PlanSeeder.php:25,40,55,70` |
| EV-24 | No cast/parser for storage limit | `app/Models/Plan.php` fillable only |
| EV-25 | No metering/enforcement service | repository-wide search for `storage_limit` yields model, migration, seeder, Filament field only |
| EV-26 | School not directly linked to plan | `app/Domains/School/Models/School.php`; no plan/subscription relation |
| EV-27 | Subscription link + status | `database/migrations/2026_07_24_000002_create_subscriptions_table.php:13-17` |
| EV-28 | Audit log table | `database/migrations/2026_07_17_000070_create_audit_logs_table.php` |
| EV-29 | Audit retention doc says 7 years | `docs/03_Database/Audit.md:74-79` |
| EV-30 | Audit cleanup defaults to 90 days | `app/Console/Commands/System/SystemCleanupCommand.php:14,23-24` |
| EV-31 | Backup doc: encrypted, off-site, RTO/RPO | `docs/08_Deployment/Backups.md:21,62,70-78,94-98` |
| EV-32 | No staging deployment record | `docs/08_Deployment/Development.md`, `CI_CD.md` describe process only |
| EV-33 | No legal retention/residency evidence | repository search for GDPR/residency/retention law; only generic references |
| EV-34 | Coefficient index is already short-named | `2026_08_01_000011_create_coefficients_table.php:27` uses `coefficients_school_class_subject_unique`; ADR-024's 65-char note is stale |
| EV-35 | No Archive permission group | `app/Domains/Permission/Enums/PermissionGroup.php`; no Archive case |
| EV-36 | `s3` line reference stale | ADR-024 says line 115; actual `// 's3'` is line 142 |
| EV-37 | ADR-024 status is Proposed | `docs/ADR/ADR-024.md:5` |
| EV-38 | ADR-024 product decisions D1-D5 recorded as approved | `ADR-024.md:99-106,687-694` |
| EV-39 | SPR-017 report says decisions pending | `SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md:162-171,296,342-365` |
| EV-40 | SPR-014 report has no approval/staging/closure record | `SPR-014_Records_Lifecycle_Engine_Report.md` (ends at line 475) |

---

## 7. Part C - ADR-024 Decision Board

Each decision below records: current proposal, repository evidence, alternatives,
advantages/disadvantages, risks, dependencies, recommendation, and whether it blocks
implementation. The classification is summarised in section 7.15.

### 7.1 D1 - Scope and phase placement

- **Current proposal:** SPR-017 is a Phase 9 Enterprise Services capability, planned
  now but not an ordinary Phase 3 deliverable.
- **Evidence:** `MASTER_ROADMAP` Phase 9 lists Cloud Archive/Backup/DR; `SESSION_HANDOFF`
  listed SPR-017 as an upcoming major sprint; ADR-024 records the reconciliation.
- **Alternatives:** (a) Phase 3 academic-records deliverable; (b) Phase 9 strategic
  capability; (c) split: Phase 3 local archive, Phase 9 cloud/DR.
- **Advantages/disadvantages:** Phase 9 avoids overloading Phase 3 and matches the
  roadmap; it may delay archive value and complicate near-term planning. A split
  delivers local retention sooner but creates two approval gates.
- **Risks:** planning drift; teams treating Phase 9 as "no urgency"; accidental
  Phase 3 commitment.
- **Dependencies:** Product Owner confirmation of the approved D1 record; roadmap
  consistency.
- **Recommendation:** retain Phase 9 classification; explicitly state that planning
  may happen now and implementation requires separate authorisation.
- **Blocks implementation:** No (already recorded); **MUST DECIDE BEFORE ACCEPTANCE**
  only to confirm the record is authoritative.

### 7.2 D2 - Storage strategy (local-first, cloud deferred)

- **Current proposal:** local-first on tenant-suffixed local/public disks; cloud
  deferred; cloud activation gated by five conditions.
- **Evidence:** EV-04 to EV-07; `s3` defined but unused and not tenant-aware; no cloud
  credentials/config.
- **Alternatives:** (a) local-first, cloud deferred; (b) cloud-first; (c)
  provider-neutral local + optional S3 adapter from day one; (d) managed document
  store.
- **Advantages/disadvantages:** local-first is deployable now, low cost, preserves
  current posture; it is not a resilient/durable archive and creates a future
  migration. Cloud-first adds resilience but introduces provider/residency/key
  decisions and cost.
- **Risks:** local disk failure/loss; archive presented as durable; migration debt;
  no off-site copy if backup is not extended.
- **Dependencies:** backup scope; staging/production hosting decision; ODC1/ODC2 for
  cloud.
- **Recommendation:** keep local-first for first release, but require backup coverage
  and an explicit "not a resilient archive" disclosure. Replace "S3-compatible seam
  is the proposed seam" with "provider-neutral storage port; S3 adapter optional and
  gated."
- **Blocks implementation:** No; **MUST DECIDE BEFORE ACCEPTANCE** for the corrected
  seam wording and disclosure.

### 7.3 D3 - Archive bounded context and read contracts

- **Current proposal:** new `App\Domains\Archive`; consumes source domains through
  contracts only; owns archive provenance/retention; never replaces lifecycle.
- **Evidence:** EV-01, EV-15, EV-16, EV-21; no read contract exists; source-domain
  models are the only query surface today.
- **Alternatives:** (a) thin Archive context + explicit read contracts; (b) extend
  RecordsLifecycle with archive fields; (c) platform service outside Domains; (d)
  generic document repository.
- **Advantages/disadvantages:** (a) preserves DDD boundaries and testability; (b)
  violates thin-lifecycle scope and risks a second engine; (c) weakens dependency
  discipline; (d) becomes a second document engine.
- **Risks:** Archive imports source models (C1 violation); duplicated lifecycle;
  contract churn; unclear ownership.
- **Dependencies:** read contracts for SPR-014 and SPR-012; DTO projections; DI
  bindings; architecture tests.
- **Recommendation:** keep (a), but add to the ADR: exact contract names, method
  semantics, projections, and a prohibition on importing
  `RecordLifecycle`/`AcademicDocument`/`GeneratedFile`.
- **Blocks implementation:** **Yes - MUST DECIDE BEFORE ACCEPTANCE.**

### 7.4 D4 - Cold-storage trigger and lifecycle interpretation

- **Current proposal:** SPR-014 `archived` is the trigger; no `read_only` /
  `cold_storage` states; archive provenance separate; future extension via ODC.
- **Evidence:** EV-13; only three lifecycle states; `DocumentStatus::Archived` also
  exists; no read-only/cold-storage representation.
- **Alternatives:** (a) `archived` trigger + separate archive metadata; (b) add
  lifecycle states now; (c) use document status only; (d) use storage tier only.
- **Advantages/disadvantages:** (a) preserves one lifecycle authority and avoids a
  premature state model; (b) is more expressive but expands SPR-014 and needs an ADR
  amendment; (c) conflates document and lifecycle; (d) loses records semantics.
- **Risks:** semantic divergence between document status and lifecycle; future
  migration; "cold storage" claims without representation.
- **Dependencies:** ODC; an explicit reconciliation rule for the two `archived`
  concepts; a migration path if states are added later.
- **Recommendation:** keep (a); add a reconciliation invariant: Archive triggers on
  `RecordLifecycleState::Archived` and verifies the source document status; divergence
  is an auditable exception, not a silent archive.
- **Blocks implementation:** **Yes - MUST DECIDE BEFORE ACCEPTANCE** for the
  reconciliation rule; the lifecycle-state extension itself may be deferred.

### 7.5 D5 - Retention policy model

- **Current proposal:** configurable per document type and school; mechanism now,
  values by product/legal; preservation vs destruction distinct; auto-destruction off.
- **Evidence:** EV-29/EV-30 contradiction; no retention schema/config/service; no
  legal periods; backup retention is unrelated.
- **Alternatives:** (a) policy table + evaluation engine + gated destruction; (b)
  hard-coded per-type periods; (c) no retention (preserve forever); (d) rely on backup
  retention.
- **Advantages/disadvantages:** (a) is flexible and legally defensible if values are
  supplied; (b) is simple but illegal/inflexible; (c) is safe but may violate
  minimization/erasure duties; (d) confuses recovery with records management.
- **Risks:** CR-002 retention violations; accidental destruction; audit gaps; legal
  non-compliance; policy precedence bugs.
- **Dependencies:** ODL (values), ODD (destruction safeguards), audit retention fix,
  jurisdiction model.
- **Recommendation:** keep (a); specify the schema, precedence, clock start, legal
  hold, dry-run, approval chain, and audit events. Destruction remains off.
- **Blocks implementation:** **Yes - MUST DECIDE BEFORE ACCEPTANCE** for the mechanism;
  **MUST BE DECIDED BEFORE PRODUCTION** for values and destruction.

### 7.6 D6 - Storage quotas and paid extensions

- **Current proposal:** `plans.storage_limit` is the single authority; no second
  quota authority; metering/enforcement open (ODQ).
- **Evidence:** EV-22 to EV-27; string column, no parser, no metering, no school->plan
  relation; only `subscriptions`.
- **Alternatives:** (a) plan column + quota read contract + separate metering; (b) new
  quota store in Archive; (c) no quota (unlimited); (d) hard-code per-plan byte values.
- **Advantages/disadvantages:** (a) preserves plan authority and avoids duplication;
  (b) creates a second authority; (c) risks storage exhaustion; (d) duplicates plan
  data.
- **Risks:** OR-004/SA-001 storage exhaustion; wrong unit; expired subscription
  ambiguity; double counting; false sense of enforcement.
- **Dependencies:** subscription resolution; unit parser; metering design; ODQ;
  Product pricing decisions.
- **Recommendation:** keep (a), but correct the ADR wording to "intended authority,
  not yet a working mechanism"; add `StorageQuotaContract` with `report_only` first
  release; do not block archive operations until ODQ.
- **Blocks implementation:** **Yes - MUST DECIDE BEFORE ACCEPTANCE** for the contract
  and first-release semantics; **MUST BE DECIDED BEFORE PRODUCTION** for enforcement.

### 7.7 D7 - Downloadable school archive packages

- **Current proposal:** in architectural scope, not mandatory for first release;
  boundary/security fixed; implementation deferred (ODP).
- **Evidence:** no package code/routes/jobs; no export capability; ADR boundary text.
- **Alternatives:** (a) defer with fixed boundary; (b) include in first release; (c)
  omit entirely; (d) generic data export.
- **Advantages/disadvantages:** (a) avoids scope creep and keeps security boundary;
  (b) adds delivery value but expands first-release risk; (c) loses a product
  commitment; (d) may leak unrelated data.
- **Risks:** cross-tenant leakage; partial packages; expiry/cleanup failures; format
  lock-in; unauthorised bulk export.
- **Dependencies:** ODP; archive permissions; manifest/integrity; temporary storage;
  audit.
- **Recommendation:** keep (a); require a manifest with hashes, one-school-only,
  expiry/cleanup, and an auditable request/approval.
- **Blocks implementation:** No; **MAY BE DEFERRED.** The boundary must remain in the
  accepted ADR.

### 7.8 D8 - Backup versus archive separation

- **Current proposal:** archive/retention distinct from operational backup; no
  duplication; archived docs remain in backup scope.
- **Evidence:** EV-31; `Backups.md` operational scope; `SystemBackupCommand`.
- **Alternatives:** (a) separate pipelines; (b) use backup as archive; (c) use archive
  as backup.
- **Advantages/disadvantages:** (a) is correct records management; (b) confuses
  retention with recovery; (c) archive is not a recovery mechanism.
- **Risks:** archive not included in backup; restore untested; RTO/RPO claims applied
  to archive without evidence; duplicate storage cost.
- **Dependencies:** backup scope update; restore test; ops ownership.
- **Recommendation:** keep (a); add archive paths to backup scope and a restore test;
  state that backup retention is not archival retention.
- **Blocks implementation:** No; **MUST BE DECIDED BEFORE PRODUCTION** for backup/
  restore coverage.

### 7.9 D9 - Tenant isolation of archived objects

- **Current proposal:** per-school prefix + authorization on every read; path is not
  authorization; shared cloud objects need an approved access layer.
- **Evidence:** EV-04 to EV-07, EV-11/EV-12, `HasSchoolId`, policies; no archive
  permission yet.
- **Alternatives:** (a) per-tenant prefix + policy; (b) per-tenant bucket; (c)
  per-tenant credentials; (d) shared bucket with app-level filtering only.
- **Advantages/disadvantages:** (a) works locally and is testable; (b) is strong but
  operationally heavy; (c) is strong but complex; (d) is unsafe.
- **Risks:** path traversal; IDOR; cross-tenant download; super-admin misuse; package
  leakage.
- **Dependencies:** archive policy/permissions; download controller; tenant
  resolution; security tests; ODP1 for cloud.
- **Recommendation:** keep (a) for local; add mandatory negative tests and a
  path-canonicalization rule; defer cloud topology to ODP1.
- **Blocks implementation:** **Yes - MUST DECIDE BEFORE ACCEPTANCE** for the local
  authorization/download design; cloud topology may be deferred.

### 7.10 D10 - Legacy pipeline exclusion

- **Current proposal:** never use `GeneratedDocument`, `DocumentTemplate`,
  `DocumentGenerationService`, legacy `App\Domains\Document`, DocumentCenter or
  `pdf_enable_qr_code`.
- **Evidence:** legacy classes exist; ADR-021/022 fixed constraints; no SPR-017 code.
- **Alternatives:** (a) exclude; (b) reuse legacy pipeline; (c) migrate legacy data.
- **Advantages/disadvantages:** (a) avoids two document engines; (b) is quick but
  revives deprecated behavior; (c) is a separate data-migration project.
- **Risks:** accidental import; legacy data inconsistency; hidden coupling.
- **Dependencies:** architecture test; code review checklist.
- **Recommendation:** keep (a); add an architecture test that fails on legacy imports.
- **Blocks implementation:** No; already a fixed constraint. **MUST DECIDE BEFORE
  ACCEPTANCE** only to keep the test requirement.

### 7.11 ODC1/ODC2 - Cloud provider, region, key management

- **Current proposal:** open; cloud activation blocked until provider/region and key
  management are approved.
- **Evidence:** no cloud config/credentials; `s3` unused; no key service; no residency
  record.
- **Alternatives:** (a) defer; (b) choose a provider now; (c) multi-provider
  abstraction; (d) on-prem object store.
- **Advantages/disadvantages:** (a) preserves local-first and avoids premature lock-in;
  (b) accelerates cloud but forces unresolved residency/key decisions; (c) reduces
  lock-in but adds complexity; (d) may satisfy residency but adds ops burden.
- **Risks:** residency non-compliance; key loss; vendor lock-in; cost surprises.
- **Dependencies:** ODP1 (tenant isolation), ODP2 (SLO/cost), legal review.
- **Recommendation:** keep open and deferred; do not name a provider or region.
- **Blocks implementation:** No; **MAY BE DEFERRED**; **MUST BE DECIDED BEFORE
  PRODUCTION** for any cloud use.

### 7.12 ODP - Package specifics

- **Current proposal:** format, contents, size, expiry, requesters, delivery mechanism
  open.
- **Evidence:** no package code; product intent only.
- **Alternatives:** ZIP + manifest; PDF bundle; API export; no packages.
- **Advantages/disadvantages:** ZIP is portable but large; API export is flexible but
  complex; no packages is simplest.
- **Risks:** data leakage; unbounded size; format obsolescence; expiry failures.
- **Dependencies:** ODP, permissions, temporary storage, audit, quota.
- **Recommendation:** defer; require a manifest and a size cap when decided.
- **Blocks implementation:** No; **MAY BE DEFERRED.**

### 7.13 ODL/ODD - Legal retention values and destruction safeguards

- **Current proposal:** values by product/legal; destruction disabled; authorization
  and safeguards open.
- **Evidence:** no legal research in repo; no destruction workflow; audit contradiction.
- **Alternatives:** (a) jurisdiction-specific research; (b) one global default; (c)
  preserve forever; (d) delete after fixed period.
- **Advantages/disadvantages:** (a) is defensible but slow; (b) is simple but likely
  wrong; (c) is safe but may violate erasure duties; (d) is risky without law.
- **Risks:** CR-002 critical; unlawful destruction; litigation hold violation; audit
  failure.
- **Dependencies:** legal counsel; jurisdiction model; audit retention; approval
  workflow.
- **Recommendation:** keep destruction off; commission legal research; require ODD
  before enabling.
- **Blocks implementation:** Mechanism **MUST DECIDE BEFORE ACCEPTANCE**; values and
  destruction **MUST BE DECIDED BEFORE PRODUCTION.**

### 7.14 Audit and encryption posture

- **Current proposal:** audit events for archive entry/retention/disposition/packages;
  encryption "follows existing defaults"; cloud key management open.
- **Evidence:** EV-28 to EV-30; no archive audit; no local at-rest encryption; DB
  encrypt commented; no key service.
- **Alternatives:** (a) dedicated append-only archive audit stream; (b) reuse
  `audit_logs` with an exemption; (c) no audit (unacceptable); for encryption: (a)
  disk-level encryption, (b) application-level envelope encryption, (c) accepted risk
  with disclosure.
- **Advantages/disadvantages:** (a) is durable and retention-safe; (b) is simpler but
  conflicts with 90-day cleanup; (c) violates S5/EP; encryption adds key-management
  burden but protects at rest.
- **Risks:** audit evidence deleted; integrity/authenticity confusion; key loss;
  unencrypted archives.
- **Dependencies:** audit retention decision; key management; threat model; security
  review.
- **Recommendation:** add an archive audit event catalogue; exempt archive/retention
  events from generic cleanup or use a dedicated store; state encryption truthfully
  and gate production on an explicit decision.
- **Blocks implementation:** **Yes - MUST DECIDE BEFORE ACCEPTANCE** for the audit
  catalogue and encryption posture statement; **MUST BE DECIDED BEFORE PRODUCTION**
  for implementation.

### 7.15 Decision classification board

#### MUST DECIDE BEFORE ACCEPTANCE

| ID | Decision | Why it blocks acceptance |
|---|---|---|
| A1 | Exact Archive ownership list and prohibited artifacts | Without it, Archive may duplicate lifecycle/document ownership |
| A2 | Read contracts for SPR-014 and SPR-012 (names, projections, semantics) | The contracts named in the ADR do not exist; implementation would invent them |
| A3 | Archive provenance/metadata model (fields, linkage, immutability) | No implementable schema or invariant set |
| A4 | Retention mechanism structure (scope precedence, clock, legal hold, dry-run, disposition) | "Configurable" is not a design |
| A5 | Storage port and download authorization design | ADR currently over-constrains to S3 and omits access control |
| A6 | Quota contract and first-release semantics | Column is not a mechanism; current wording overstates |
| A7 | Archive audit event catalogue and audit-retention resolution | No audit integration; 90-day cleanup contradicts 7-year doc |
| A8 | At-rest encryption posture for local storage | Must state the actual control, not "defaults" |
| A9 | SPR-014 interlock gate wording (closure/waiver required before implementation) | The ADR must make the gate explicit even though SPR-014 may remain deferred during review |
| A10 | Decision-log reconciliation and stale-evidence correction | D-numbering conflicts; coefficient note and line refs stale |
| A11 | Revised acceptance criteria and test requirements | Current criteria are not testable enough |

#### MAY BE DEFERRED

| ID | Decision | Deferral condition |
|---|---|---|
| B1 | Cloud provider and region (ODC1) | Deferred until cloud activation is proposed |
| B2 | Cloud key management and rotation (ODC2) | Deferred until cloud activation |
| B3 | Explicit `read_only` / `cold_storage` lifecycle states (ODC) | Additive; separate ADR amendment |
| B4 | Package format, contents, size, expiry (ODP) | Deferred release |
| B5 | Paid-extension pricing/billing/grace (ODQ commercial) | Deferred until metering exists |
| B6 | SLO/availability/durability/cost baseline (ODP2) | Deferred until cloud/production |
| B7 | DR topology for shared objects (ODP1) | Deferred until cloud |

#### MUST BE DECIDED BEFORE PRODUCTION

| ID | Decision | Why production blocks |
|---|---|---|
| P1 | Legal retention periods per type/jurisdiction (ODL) | Cannot enforce or claim retention without law |
| P2 | Destruction authorization/safeguards (ODD) | Cannot enable deletion safely |
| P3 | Metering and threshold/enforcement policy (ODQ) | Cannot enforce quotas or paid extensions |
| P4 | At-rest encryption and key management for the chosen storage | Protects archived records at rest |
| P5 | Data residency and cloud tenant isolation (ODC1/ODP1) | Prevents unlawful transfer and cross-tenant exposure |
| P6 | SLO/durability/cost/DR (ODP2/ODP1) | Required before claiming a production archive |
| P7 | Audit retention aligned to records obligations | Prevents loss of evidence |
| P8 | Backup/restore validation including archive objects | Prevents unrecoverable archive |
| P9 | Staging deployment evidence and ops runbooks | Proves the pipeline before production |
| P10 | `archive.*` permissions, roles and UI/ops controls | Prevents unauthorized access |

---

## 8. Part D - Risks and Unresolved Questions

### 8.1 Risk register (review-specific)

| ID | Risk | Severity | Likelihood | Evidence | Mitigation |
|---|---|---|---|---|---|
| RR-01 | SPR-014 is implicitly treated as closed because ADR-024 is accepted | High | Medium | Three DoD items missing; no waiver | Keep interlock explicit; separate decisions |
| RR-02 | Archive imports source models because no read contract exists | High | High | EV-15, EV-21 | Contract-first design; architecture test |
| RR-03 | `plans.storage_limit` is mistaken for enforcement | High | High | EV-22 to EV-27 | Correct wording; quota contract; report-only first release |
| RR-04 | Retention values invented or auto-destruction enabled | Critical | Low | No legal values; destruction off by default | ODL/ODD gates; dry-run; approval chain |
| RR-05 | Audit evidence deleted by 90-day cleanup | High | High | EV-29/EV-30 | Dedicated/exempt archive audit stream |
| RR-06 | Local-only archive presented as resilient/durable | High | Medium | Single local disk; backup coverage unproven | Disclosure; backup inclusion; restore test |
| RR-07 | At-rest encryption assumed but absent | High | Medium | Plain local disk; DB encrypt commented | Explicit decision; disk/app encryption |
| RR-08 | Cross-tenant archive read via path/IDOR | Critical | Medium | No archive policy/route yet; path-only risk | Policy + tenant resolution + negative tests |
| RR-09 | Dual `archived` semantics diverge | High | Medium | EV-19/EV-20 | Reconciliation invariant + audit exception |
| RR-10 | Subscription/plan resolution ambiguous | Medium | High | Multiple/expired subscriptions possible | Quota contract with explicit precedence |
| RR-11 | S3-mandatory seam creates premature lock-in | Medium | Medium | No provider decision | Provider-neutral port |
| RR-12 | Documentation drift (stale coefficient note, line refs, D-numbering) | Medium | High | EV-34/EV-36/EV-39 | Revise ADR; single decision log |
| RR-13 | Archive not included in backup/restore | High | Medium | Backup doc generic; no archive scope | Ops change + restore test |
| RR-14 | No archive permissions, so access defaults to super_admin | Medium | High | EV-35 | Add `archive.*` permissions/policy |
| RR-15 | Package export leaks cross-tenant data | High | Low (deferred) | No package design | One-school manifest, auth, expiry, audit |
| RR-16 | Legal hold/litigation hold unsupported | High | Low | No legal-hold concept | Add `legal_hold` field and precedence |
| RR-17 | Tenant deletion cascades destroy archive metadata | High | Medium | EV-12 cascade on `generated_files`; archive design must not repeat | Restrict/soft-delete; retention-aware offboarding |

### 8.2 Unresolved questions

1. Which state is authoritative for archive entry when
   `AcademicDocument.status = archived` and `RecordLifecycleState != archived` (or
   vice versa)?
2. Does retention start at publication, document generation, archive entry, or school
   closure? Can a school override the start?
3. Is the archive a **system of record** or a **derived copy**? The ADR implies
   metadata + object pointer, but does not state whether archived bytes are the
   authoritative copy.
4. Are archived objects immutable at the storage layer (WORM/object lock), or only
   application-immutable? Local disk is mutable today.
5. What happens to archive objects and metadata when a school is deleted, suspended,
   or a subscription expires?
6. Are legal holds supported, and who can place/release them?
7. What is the canonical storage unit (decimal vs binary), and how are invalid plan
   values handled?
8. How are multiple/expired/suspended subscriptions resolved for quota purposes?
9. Does an archive copy double-count against the plan quota? If yes, archiving could
   block a school; if no, usage can exceed the limit.
10. Which audit events are mandatory, and for how long must they be retained?
11. Is local at-rest encryption required for the first release, or accepted as a
    risk? If required, where are keys stored and rotated?
12. What is the production hosting location, and does it satisfy any residency
    obligation?
13. What are the archive-specific RPO/RTO, and has a restore been tested?
14. How are already-archived lifecycle rows backfilled into Archive, and are
    duplicates/idempotency guaranteed?
15. Who may request a package, and what approval/identity proof is required?
16. Are packages subject to retention, or are they strictly temporary?
17. What is the accepted behavior when a checksum verification fails during download?
18. Is `super_admin` allowed to read/export any school's archive without a second
    approval (break-glass)?
19. What is the archive's behavior when the storage disk is unavailable or full?
20. Which decision record is authoritative when ADR-024 and the SPR-017 decision-board
    report disagree on D-numbering and approval status?

---

## 9. Recommended Architecture

The recommendation keeps ADR-024's direction and makes it implementable without
creating a second domain engine.

### 9.1 Component shape

```text
Presentation / Ops
  - Authorized archive list/detail/download (later: package request)
  - No public file URL

Application (App\Domains\Archive)
  - ArchiveEntryService (register archived source -> archive entry)
  - RetentionEvaluationService (policy match, dry-run, disposition)
  - ArchiveDownloadService (authorize, verify checksum, stream, audit)
  - ArchivePackageService (deferred)

Domain (App\Domains\Archive)
  - ArchiveEntry, ArchiveRetentionPolicy, DispositionClass, LegalHold
  - ArchiveEvent catalogue
  - Contracts (interfaces): LifecycleReadContract, DocumentReadContract,
    StoragePort, StorageQuotaContract, ArchiveAuditContract

Infrastructure
  - Eloquent models/repositories for archive tables only
  - LocalStorageAdapter (first release)
  - Optional S3StorageAdapter (gated; not first release)
  - Audit adapter -> dedicated archive audit stream or exempt audit_logs
  - DI bindings in ArchiveServiceProvider

Consumed (read-only, via contracts)
  - SPR-014 lifecycle projection
  - SPR-012 document + current GeneratedFile projection
  - Platform quota projection
```

### 9.2 Contract set (design-level, not implementation)

| Contract | Purpose | Must not expose |
|---|---|---|
| `ArchivedLifecycleReadContract` | Find archived lifecycle records by school/type/date/source; return a projection | `RecordLifecycle` model, cross-school rows |
| `ArchiveDocumentReadContract` | Return document identity, type, number, status, current version, current file pointer | Content, grades, personal data, mutations |
| `ArchiveFileReadContract` (could merge with above) | Return disk/path/size/checksum/mime/current flag | Raw storage handles, public URLs |
| `StoragePort` | Put/getStream/exists/delete/checksum; local adapter first | Provider-specific types in Domain/Application |
| `StorageQuotaContract` | Resolve school -> active subscription -> plan -> `limit_bytes`, `used_bytes`, `mode` | Pricing/billing logic, second quota table |
| `ArchiveAuditContract` | Append archive/retention/package events | Mutable audit rows |

These contracts do not exist today. They are an acceptance prerequisite, not an
implementation detail.

### 9.3 Data model (proposed, not implemented)

**`archive_entries`**

| Column | Notes |
|---|---|
| `id`, `uuid` | Primary key + public identifier |
| `school_id` | Required; FK to `tenants`; indexed |
| `source_domain`, `source_type`, `source_id`, `source_version` | Source identity |
| `record_lifecycle_id` | Link to SPR-014 envelope (no FK mutation) |
| `academic_document_id`, `document_version_id`, `generated_file_id` | Source references |
| `disk`, `path`, `size_bytes`, `checksum_sha256`, `mime_type` | Object pointer + integrity |
| `archived_at`, `archived_by` | Archive provenance |
| `retention_policy_id` | Nullable; matched policy |
| `disposition_class` | `preserve` or `destroy` (value from policy) |
| `retention_due_at` | Computed; nullable |
| `legal_hold`, `legal_hold_reason` | Overrides retention |
| `status` | `active`, `missing`, `quarantined`, `disposed` |
| `metadata` | Small JSON for non-critical context |
| timestamps, soft deletes | No hard delete in first release |

**`archive_retention_policies`**

As described in section 5.C. Platform defaults have `school_id = null`; school
overrides are explicit rows. A `destroy` policy requires `legal_basis`.

**Audit stream**

Either a dedicated `archive_events` append-only table or `audit_logs` with an
exemption from `system:cleanup`. The ADR must choose one and define retention.

### 9.4 Behaviors

- **Archive entry:** triggered by a lifecycle read showing `archived`. Resolve the
  source document/file, verify tenant, compute checksum/size if needed, write an
  archive entry, then evaluate retention. Idempotent by
  `(school_id, source_domain, source_type, source_id, source_version)`.
- **Download:** resolve tenant, authorize `archive.view`, load entry by school+id,
  verify checksum, stream with attachment headers, audit success/failure. Uniform 404
  for wrong tenant.
- **Retention evaluation:** on entry and by scheduler; policy precedence; no policy =
  preserve indefinitely; legal hold wins; audit every evaluation; dry-run before
  enabling destruction.
- **Destruction:** disabled by default; requires ODL/ODD/Product approval, a dedicated
  permission, a dry-run report, an operator ceremony, and an audit/deletion
  certificate. First release does not implement physical destruction.
- **Quota:** first release report-only. Record sizes; expose usage; do not block.
  Hard enforcement only after ODQ.
- **Backup:** archive paths included in the existing backup scope; restore test
  required. Archive is not a backup.
- **Cloud:** no provider/region/credentials. Provider-neutral port; S3 adapter only
  after ODC1/ODC2/ODP1/ODP2 close.

### 9.5 Why this is the right level of complexity

- It preserves one lifecycle authority and one document engine.
- It avoids a second quota authority.
- It makes retention a mechanism with explicit, non-invented values.
- It keeps cloud decisions reversible and provider-neutral.
- It makes security properties testable (tenant isolation, integrity, authorization,
  audit).
- It does not require a new document pipeline, AI, search, or analytics.

---

## 10. Acceptance Criteria for ADR-024 (Revised)

ADR-024 may be recommended for acceptance only when all of the following are true.
These are criteria for the **ADR document**, not for SPR-017 implementation.

1. **Ownership is a closed list.** The ADR enumerates the tables, contracts and
   responsibilities Archive owns, and the artifacts it must never own or mutate.
2. **Read contracts are specified.** The ADR names the SPR-014 and SPR-012 read
   contracts, their projections, their tenant-scoping semantics, and their
   "no model import" rule.
3. **Archive metadata model is specified.** Fields, keys, idempotency, immutability,
   soft-delete behavior and source linkage are defined.
4. **Retention mechanism is specified.** Policy scope, precedence, clock start,
   jurisdiction, legal hold, disposition enum, dry-run, approval chain, and audit
   events are defined. Legal values remain explicitly deferred.
5. **Quota wording is corrected.** The ADR distinguishes the plan limit column from
   metering/enforcement, defines the quota contract and first-release `report_only`
   behavior, and defines subscription resolution and canonical units.
6. **Storage seam is provider-neutral.** A `StoragePort` with a local adapter is the
   first-release seam; S3 compatibility is an optional future adapter, not a
   precondition.
7. **Download/access design is specified.** Authorized streaming, tenant resolution,
   headers, rate limiting, checksum verification, audit, and uniform wrong-tenant
   behavior are defined.
8. **Audit is defined.** An archive event catalogue exists, and audit retention is
   reconciled with the 7-year documentation versus the 90-day cleanup.
9. **Encryption posture is truthful.** The ADR states what exists today, what is
   required for production, and what is open for cloud key management.
10. **Residency is separated from law.** The ADR states that local-first is technical,
    not legal compliance, and requires a residency decision before cloud.
11. **SPR-014 interlock is explicit.** SPR-017 implementation is gated on a recorded
    SPR-014 closure decision or an owner-signed waiver.
12. **Decision log is reconciled.** A single authoritative D-numbering/decision record
    supersedes the conflicting SPR-017 report entries.
13. **Stale evidence is corrected.** The coefficient-index note and line references
    are updated or removed.
14. **Acceptance criteria and tests are testable.** The ADR includes the security,
    tenant, retention, quota, integrity and legacy-exclusion tests it requires.
15. **Approvers are named.** A sign-off block identifies Product, Architecture,
    Security, Ops and Legal (as applicable) and the exact decision each owns.
16. **No cloud commitment.** No provider, region, credential, SDK, IAM policy or SLO
    is introduced by the ADR.

---

## 11. Implementation Dependencies (SPR-017)

| # | Dependency | Owner | Required before | Current status |
|---|---|---|---|---|
| D1 | ADR-024 accepted (revised) | Architecture Team | Any implementation | Not accepted |
| D2 | SPR-014 closure decision or waiver | Product + Architecture + Ops | Any implementation | Missing |
| D3 | SPR-014 read contract | Architecture | Archive entry | Missing |
| D4 | SPR-012 document/file read contract | Architecture | Archive entry/download | Missing |
| D5 | `StoragePort` + local adapter design | Architecture | Storage writes | Missing |
| D6 | Quota contract + unit parser + subscription resolution | Architecture + Product | Quota reads | Missing |
| D7 | Archive permission group + policy | Architecture + Security | Any UI/route | Missing |
| D8 | Audit event catalogue + audit retention decision | Architecture + Security | Archive entry | Missing |
| D9 | Retention policy schema + evaluation semantics | Architecture + Legal | Retention evaluation | Missing |
| D10 | Legal values (ODL) | Product + Legal | Destruction/enforcement | Missing |
| D11 | Destruction safeguards (ODD) | Product + Security + Ops | Destruction | Missing |
| D12 | Encryption-at-rest decision | Security + Architecture | Production | Missing |
| D13 | Backup scope + restore test | Ops | Production | Missing |
| D14 | Staging environment + deployment evidence | Ops | Production | Missing |
| D15 | Migrations for archive tables | Engineering | Implementation | Not created |
| D16 | Tests (unit/feature/security/architecture/integration) | Engineering | Completion | Not created |

---

## 12. Part E - SPR-017 Readiness Assessment

### 12.1 Verdict

**NOT READY FOR IMPLEMENTATION.**

The direction is architecturally plausible, but the implementation prerequisites do
not exist. Starting now would force the team to invent contracts, quota semantics,
retention structure, audit behavior and storage security in code, which is precisely
what the ADR is supposed to prevent.

### 12.2 Readiness matrix

| Readiness domain | Status | Evidence / gap |
|---|---|---|
| ADR-024 accepted | **NO** | Status `Proposed`; this review recommends revision |
| SPR-014 closed or waived | **NO** | Deferred; three governance items missing |
| Archive domain exists | **NO** | `App\Domains\Archive` absent |
| Archive migrations exist | **NO** | No archive/quota/retention table |
| Archive routes exist | **NO** | No archive route |
| Archive config exists | **NO** | No archive/retention config |
| Read contracts exist | **NO** | Only write/gateway lifecycle contract and verification contract |
| Storage abstraction exists | **NO** | Direct `Storage::disk()` calls only |
| Quota mechanism exists | **NO** | String column only; no parser/metering/enforcement |
| Retention mechanism exists | **NO** | No schema/config/service |
| Audit integration exists | **NO** | No archive events; audit cleanup conflicts |
| Archive permissions exist | **NO** | No `archive.*` permission group |
| Security design complete | **PARTIAL** | Tenant patterns exist; archive-specific design missing |
| Test plan exists | **NO** | ADR has testing requirements, but no implementation plan |
| DoD defined for SPR-017 | **PARTIAL** | Generic DoD exists; sprint-specific DoD not written |
| Staging path available | **UNVERIFIED** | Process docs only; no environment evidence |

### 12.3 Architectural prerequisites

1. Revised ADR-024 accepted by the Architecture Team.
2. SPR-014 closure decision recorded (or explicit waiver).
3. Read contracts for SPR-014 and SPR-012 approved.
4. Archive ownership list and prohibited-artifact list approved.
5. Archive metadata model and idempotency rules approved.
6. Retention mechanism structure approved; legal values explicitly deferred.
7. Quota contract, unit semantics and subscription resolution approved.
8. Storage port/download authorization design approved.
9. Audit event catalogue and audit-retention decision approved.
10. Encryption-at-rest posture approved.
11. Data-residency statement and cloud gate approved.
12. `archive.*` permissions and policy approved.
13. Staging deployment plan and evidence requirements approved.

### 12.4 Domain boundaries (implementation constraints)

- Archive reads only; it never writes source state.
- Archive never calls `RecordLifecycleService::archive()` or mutates
  `record_lifecycles`.
- Archive never imports `RecordLifecycle`, `AcademicDocument`,
  `AcademicDocumentVersion`, `GeneratedFile`, verification or certificate models
  into Domain/Application.
- Archive never generates, renders, numbers or publishes documents.
- Archive never owns a second lifecycle state machine.
- Archive never owns backup/recovery.
- Archive never owns academic validity, grades or personal data beyond document
  metadata.

### 12.5 Required contracts

| Contract | Direction | Key semantics |
|---|---|---|
| `ArchivedLifecycleReadContract` | Archive -> RecordsLifecycle | Tenant-scoped; by school/type/date/source; returns projection only; no writes |
| `ArchiveDocumentReadContract` | Archive -> AcademicCommunication | Tenant-scoped; document identity/type/number/status/current version; no content/grades |
| `ArchiveFileReadContract` | Archive -> AcademicCommunication | Current file pointer: disk/path/size/checksum/mime; no public URL |
| `StoragePort` | Archive domain -> infrastructure | Provider-neutral; local first; checksum/exists/stream/delete |
| `StorageQuotaContract` | Archive -> platform/SaaS | Resolve active subscription/plan; limit bytes; usage nullable; mode |
| `ArchiveAuditContract` | Archive -> audit infrastructure | Append-only; retention-aware; actor/scope/time |

### 12.6 Security requirements

- Archive permission group (`archive.view`, `archive.manage`, `archive.export`,
  later `archive.destroy`) and a school-scoped policy.
- Every read/download re-authorizes; path `school_id` is never sufficient.
- Uniform 404 for wrong-tenant or missing objects; no existence leak.
- Private disk only; no public URL; attachment headers; no inline rendering.
- Checksum verification before download and on a schedule; fail closed.
- Path canonicalization to prevent traversal.
- Rate limiting on download/export endpoints.
- Audit every entry/download/retention/export/destruction event.
- Legal hold blocks destruction.
- Super-admin break-glass is explicit and audited.
- At-rest encryption decision recorded; keys managed outside the repository.
- No secrets or cloud credentials in code/config committed by SPR-017.

### 12.7 Storage infrastructure dependencies

- A private, tenant-aware local disk (existing `local` disk with tenant suffix).
- Archive path scheme and disk allow-list.
- Backup scope including archive paths; restore test.
- Capacity monitoring/alerting (distinct from quota enforcement).
- Optional: object storage adapter, only after cloud decisions.
- No S3 dependency in the first release.

### 12.8 Required migrations (proposed, not created)

1. `create_archive_entries_table` - provenance, object pointer, retention fields,
   legal hold, status, indexes on `(school_id, archived_at)`,
   `(school_id, source_domain, source_type, source_id, source_version)`.
2. `create_archive_retention_policies_table` - scope, jurisdiction, duration,
   disposition, legal basis, active/effective dates.
3. `create_archive_events_table` (or an equivalent audit decision) - append-only
   archive audit stream, indexed by school/time/type.
4. Optional/deferred: package request/artifact tables - only after ODP.

No migration may alter `record_lifecycles`, `academic_documents`, `generated_files`
or other source tables.

### 12.9 Required tests

**Unit**
- Archive entry idempotency and source linkage.
- Retention precedence: school+type > school+all > platform+type > platform+all.
- Retention clock and due-date calculation.
- Legal hold overrides destruction.
- Quota unit parsing and subscription resolution edge cases.
- Disposition enum and "no policy = preserve".

**Feature**
- Archive entry from an archived lifecycle record.
- Non-archived records never enter the archive.
- Archive list/detail only returns the current school.
- Download streams the correct file with attachment headers.
- Checksum mismatch fails closed and audits.
- Quota is report-only in first release.

**Security**
- School A cannot list/read/download school B's archive by ID, path or guessed URL.
- Path traversal is rejected.
- Unauthorized permission is denied; super-admin break-glass is audited.
- Cross-tenant package export is impossible (when packages exist).

**Architecture**
- No import of source-domain infrastructure models in Archive Domain/Application.
- No second lifecycle engine/state machine.
- No legacy document pipeline imports.
- No direct `Storage::disk()` in Domain/Application (must use `StoragePort`).

**Integration**
- SPR-014 archived record -> Archive entry.
- SPR-012 document/current file -> Archive pointer.
- Audit event emitted for entry/download/retention.
- Backup inclusion smoke test (ops).

**Operations**
- Staging deployment with migrations, seeded policy, smoke test.
- Restore drill including an archived object.
- Failure modes: disk full, missing object, checksum mismatch, DB unavailable.

### 12.10 Definition of Done for SPR-017 (proposed)

SPR-017 is complete only when:

1. Revised ADR-024 is accepted by the named owners.
2. SPR-014 closure decision (or waiver) is recorded.
3. Archive domain, contracts, migrations, config and permissions are implemented.
4. Archive entry, list, detail, download and retention evaluation work end-to-end.
5. Destruction remains disabled (or is enabled only under an approved ODD/ODL).
6. Quota is report-only or enforced per an approved ODQ decision.
7. Tenant isolation, integrity, audit and authorization tests pass.
8. Architecture tests prove no source-model/legacy imports and no second lifecycle.
9. PHPStan, Pint and the full test suite pass.
10. Backup includes archive objects; a restore drill is recorded.
11. Staging deployment evidence is recorded.
12. Documentation, runbooks and permissions are updated.
13. Code review is approved by the named reviewers.
14. No cloud provider/region/credentials are introduced without approval.

---

## 13. Formal Review Outcome

### 13.1 Outcome

**REVISION REQUIRED.**

ADR-024 is **not rejected**: its core direction (thin Archive context, local-first,
contract-only consumption, one lifecycle authority, no invented legal values,
backup/archive separation, gated cloud) is consistent with the repository and should
be preserved.

ADR-024 is **not acceptance-recommended** because it does not yet specify the
contracts, quota mechanism, retention structure, audit integration, storage access
design and encryption posture needed for a safe implementation, and because the
SPR-014 interlock is unresolved.

### 13.2 Required revisions (blocking acceptance)

| # | Revision | Section |
|---|---|---|
| RV-01 | Replace "RecordsLifecycleContract / read surface" with named read contracts and projections; add the SPR-012 read contract | 5.A, 9.2 |
| RV-02 | Add a closed ownership/prohibition list for `App\Domains\Archive` | 5.A |
| RV-03 | Specify the archive metadata model, idempotency, immutability and source linkage | 9.3 |
| RV-04 | Specify retention policy schema, precedence, clock, jurisdiction, legal hold, dry-run, disposition and audit events | 5.C |
| RV-05 | Correct the quota section: string column vs mechanism; contract; subscription resolution; units; `report_only` first release | 5.D |
| RV-06 | Replace the S3-mandatory seam with a provider-neutral `StoragePort`; keep S3 as an optional gated adapter | 5.B |
| RV-07 | Specify authorized download, tenant resolution, headers, rate limiting, checksum verification and audit | 5.B, 5.E |
| RV-08 | Define the archive audit event catalogue and reconcile 7-year audit retention with 90-day cleanup | 5.E |
| RV-09 | State the at-rest encryption posture truthfully and gate production on a decision | 5.B, 5.E |
| RV-10 | Make the SPR-014 interlock operational: closure decision or explicit waiver before implementation | 4.6 |
| RV-11 | Reconcile the decision log with the SPR-017 decision-board report and fix stale evidence (coefficient note, line refs) | 6, 7 |
| RV-12 | Add testable acceptance criteria and the SPR-017 test/DoD requirements | 10, 12.9-12.10 |

### 13.3 Required owner approvals

This review does not grant any approval. The following approvals are required before
ADR-024 can be accepted or SPR-017 implemented:

| Approval | Owner | Scope |
|---|---|---|
| ADR-024 acceptance | Architecture Team | Revised architectural content, contracts, boundaries |
| SPR-014 closure or waiver | Product Owner + Architecture Team (+ Ops for staging) | DoD evidence, closure decision |
| Product decisions | Product Owner | D1-D7 scope, first-release behavior, quota policy, package scope |
| Security sign-off | Security Lead / Architecture | Tenant isolation, download authorization, encryption, audit, threat model |
| Legal/retention sign-off | Legal/Compliance + Product | ODL values, legal hold, destruction authorization (ODD) |
| Operations sign-off | Ops Lead | Staging evidence, backup/restore, capacity, SLO (ODP2) |
| Data residency decision | Product + Legal + Architecture | Cloud region/provider (ODC1) before any cloud use |
| Cloud activation | Product + Architecture + Security + Ops | All five Decision 2 gate items |

### 13.4 Status statements (explicit)

- **ADR-024:** remains `Proposed`. No status change was made.
- **SPR-014:** remains `DEFERRED`. No closure was recorded.
- **SPR-017:** not implemented, not authorized, not ready.
- **SPR-015:** untouched; ADR-021 remains `Proposed`.
- **SPR-016:** untouched; Phase 6B and QR remain deferred.

---

## 14. Files Created or Modified

### Created by this task

- `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md`

### Modified by this task

- None.

### Preserved pre-existing state (not modified by this task)

- `docs/docs/00_Project_Management/CURRENT_POSITION.md` (prior reconciliation)
- `docs/docs/00_Project_Management/MASTER_ROADMAP.md` (prior reconciliation)
- `docs/docs/00_Project_Management/PROJECT_STATE.md` (prior reconciliation)
- `docs/docs/SESSION_HANDOFF.md` (prior reconciliation)
- `docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md`
  (untracked, prior reconciliation)
- `2_NexusSchool.zip`, `NexusSchool.zip` (untracked, pre-existing)

No application code, migration, contract, route, config, dependency, ADR or accepted
governance document was edited.

---

## 15. Final Git State

Verified after writing this report:

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

`git diff --stat` remains limited to the four pre-existing governance documents
(246 insertions, 75 deletions). This task added one untracked documentation file and
changed no tracked file.

---

## 16. Appendix - Classification Key and Review Checklist

### 16.1 Classification key

| Label | Meaning |
|---|---|
| **SATISFIED** | Direct, current repository evidence confirms the criterion |
| **NOT SATISFIED** | The requirement is unmet, contradicted, or explicitly pending |
| **UNVERIFIED** | No evidence either way; success must not be assumed |
| **NOT APPLICABLE** | The criterion does not apply to this slice |

### 16.2 Decision classifications

| Label | Meaning |
|---|---|
| **MUST DECIDE BEFORE ACCEPTANCE** | ADR-024 cannot be accepted until this is specified |
| **MAY BE DEFERRED** | Can remain open without blocking acceptance or first release |
| **MUST BE DECIDED BEFORE PRODUCTION** | Can remain open for design, but blocks production use |

### 16.3 Review checklist

- [x] Branch, HEAD and working tree recorded before review.
- [x] Pre-existing changes preserved; no destructive Git.
- [x] ADR-024 read in full (Context, Decisions 1-9, invariants, matrix, open decisions,
      approvals).
- [x] SPR-014 report, code, tests, contracts and integrations inspected.
- [x] Storage substrate, tenancy, quota column, file metadata and paths verified.
- [x] Audit, encryption and backup evidence inspected.
- [x] SPR-014 DoD matrix classified.
- [x] ADR-024 architectural analysis completed for A-F.
- [x] Decision board completed and classified.
- [x] Risks and unresolved questions recorded.
- [x] Recommended architecture, acceptance criteria and dependencies recorded.
- [x] SPR-017 readiness assessed.
- [x] No application code, migration, contract, route or config changed.
- [x] No ADR status changed; SPR-014 not closed; SPR-017 not implemented.
- [x] Single new documentation file created.
- [x] Final Git status verified.

---

**End of report.**
