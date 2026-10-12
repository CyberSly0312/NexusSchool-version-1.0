# SPR-017 Archive & Cloud Storage Platform — Architecture Decision Board

**Status:** DISCOVERY / DECISION PREPARATION — NO IMPLEMENTATION
**Verdict:** **NEEDS PRODUCT-OWNER DECISIONS**
**Date:** 2026-10-10
**Type:** Documentation-only artifact (no code, schema, routes, config, dependencies)

---

## 1. Baseline

- **Repository:** `C:\xampp\htdocs\NexusSchool`, branch `main`, remote `origin main`.
- **HEAD:** `ac8acc2` ("commit du 06/08/2026"). Working tree is **NOT clean** and was
  **not** modified by this session.
- **Pre-existing uncommitted state (read-only, preserved):**
  `git diff --stat` = **317 files changed, 1892 insertions(+), 9932 deletions(-)**;
  `git status --porcelain` lists **362** entries of which **45** are untracked
  (`??`). This is the known pre-existing refonte + ESP-002 + SPR-009.5/012/013/014/015/016
  work, **UNCOMMITTED / UNCLASSIFIED**. No reset, clean, stash, revert, checkout or
  overwrite was performed.
- **Session actions:** read-only discovery plus the creation of this one report file.
  No migrations, models, jobs, services, contracts, routes, UI, dependencies, cloud
  configuration or secrets were created or changed.
- **Deliverable constraint honored:** this report is the only new file; nothing else
  in the tree was altered.

---

## 2. Evidence

### 2.1 No archive implementation exists

- `app/Domains/` contains **no** `Archive*`, `Storage*`, `Backup*` or `Retention*`
  bounded context. Current domains include `RecordsLifecycle`,
  `AcademicCommunication`, `CertificateAndTranscript`, `IdentityAndVerification`,
  `Payment`, `Library`, `Transport`, `System`, `Analytics`, `Workflow`, etc.
- `config/` contains **no** `archive`, `cloud`, `storage`, `retention` or `backup`
  config file. There is no `config/nexus-archive.php` or equivalent.
- `database/migrations/` contains **no** archive/quota/retention/cold-storage table.
  The only storage-related column found is `plans.storage_limit` (default `'1GB'`),
  `plans.max_schools`, `max_students`, `max_teachers`, `max_users`
  (`database/migrations/2026_07_24_000001_create_plans_table.php`).
- `routes/` has `api.php`, `console.php`, `tenant.php`, `verification.php`, `web.php`
  — no archive route.

### 2.2 Storage substrate today

- `config/filesystems.php` defines stock disks: `local`
  (`storage_path('app/private')`), `public`, and `s3` (env-driven `AWS_*`).
  `default` = env `FILESYSTEM_DISK` → `local`. `s3` is **defined but unused** by any
  domain.
- `config/tenancy.php`: FilesystemTenancyBootstrapper suffixes `local` and `public`;
  **`s3` is commented out** of the tenant disk list (line 115) — S3 is not
  tenant-aware today.
- `config/nexus-academic-communication.php`: `'disk' => env('NEXUS_DOCUMENT_DISK',
  'local')` is the single document storage switch.
- Official immutable document files are written by SPR-012 under
  `documents/{school_id}/{...}-v{n}.pdf` via
  `Storage::disk(config('nexus-academic-communication.disk', 'local'))->put(...)`
  (`GenerateDocumentAction.php:212`, `RegenerateDocumentAction.php:88`,
  `PdfExportChannel.php:72`).

### 2.3 Lifecycle substrate SPR-017 would consume (SPR-014)

- `RecordLifecycleState` = `registered | published | archived` only
  (`app/Domains/RecordsLifecycle/Domain/Enums/RecordLifecycleState.php`). There is
  **no** `read_only` or `cold_storage` state — the AD §12 "Cold Storage" layer does
  **not** exist as a state.
- `RecordLifecycleService` exposes `register`/`publish`/`archive`/`findBySource`;
  persistence is `record_lifecycles` with `school_id`, `source_domain`, `source_type`,
  `source_id`, `source_version`, `lifecycle_state`, `academic_context`, `origin_event`,
  `published_at`, `archived_at` (`2026_08_10_000000_create_record_lifecycles_table.php`).
- Contract seams exist in three source domains:
  `app/Domains/{AcademicCommunication,ReportCard,ClassCouncil}/Domain/Contracts/RecordsLifecycleContract.php`.
  All three register/publish/archive lifecycle metadata through adapters
  (SPR-014 report §18 / 2026-08-20 handoff, 27 integration tests / 97 assertions).
- SPR-014 is **governance-DEFERRED but code-IMPLEMENTED and green** (see §3).

### 2.4 Governance intent already recorded

- `SESSION_HANDOFF.md` §7 lists **SPR-017 Archive & Cloud Storage Platform** as an
  upcoming major sprint (after SPR-016).
- `SESSION_HANDOFF.md` §9 (Active Design Decisions) names: long-term archive
  lifecycle, cloud archive service, downloadable school archive packages, storage
  quotas, paid archive extensions.
- `SESSION_HANDOFF.md` §10 (Pending Decisions) explicitly lists **cloud object
  storage provider**; also digital signature, government verification services,
  public verification portal.
- `ARCHITECTURE_DECISIONS.md` §12 Archive Rule defines the lifecycle:
  **Active → Read Only → Archived → Cold Storage → Retention Policy → Permanent
  Preservation or Secure Destruction**; "Official records are never deleted
  directly".
- `DEPENDENCY_MAP.md` §10 (Records Lifecycle) and §13 (Archive Dependencies): the
  Records Lifecycle engine receives documents from Academic Communication, Finance,
  HR, Library, Inventory, Administration and **"never creates business data."**
  §15 Forbidden: **`Archive → Business logic`** (architectural defect).
- `RISK_REGISTER.md`: **OR-004** storage exhaustion (mitigation "Archive lifecycle
  policies", Planned), **SA-001** excessive storage consumption (mitigation "Storage
  quotas and paid plans", Product, Planned), **CR-002** document retention
  violations (Records Lifecycle Engine, Planned).
- `CHANGELOG.md` v0.4.0-alpha expected deliverables include **Academic Archive**;
  MASTER_ROADMAP Phase 9 (Enterprise Services) lists **Cloud Archive / Backup /
  Disaster Recovery**, while SPR-017 appears in the near-term sprint handoff — a
  **scope/phase placement conflict** (see §4/§6).
- `docs/06_SaaS/13_Usage_Limits.md` and `04_Plans_and_Features.md` describe quotas
  tied to the active plan; `docs/08_Deployment/Backups.md` describes backup scope,
  schedule, encryption, retention (30d/12w/12m), RTO <2h, RPO <15m. (Note:
  Backups.md names "PostgreSQL", whereas PROJECT_STATE declares MySQL — a separate
  documentation inconsistency, out of scope here.)

### 2.5 ADR numbering

- `docs/ADR/` holds `ADR-001.md` … `ADR-022.md`. Highest = **ADR-022** (Accepted
  2026-09-07, governs SPR-016). Many ADRs are empty 0-byte files (001–005, 007–011,
  014–020) — a pre-existing governance anomaly, not fixed here.
- A separate, older scheme exists at `docs/06_ADR/` (`ADR-001-MultiSchool.md` …
  `ADR-004-EnrollmentLifecycle.md`). It is a different convention and is **not** the
  sequence to extend.
- **ADR-023 is effectively reserved:** `SPR-016_Phase6B_Verification_Issuance_Report.md`
  §7 "plants" the future issuance-boundary decision for a future **ADR-023** ("No
  ADR-023 is authored by Phase 6B"). SPR-016's deferral therefore does **not**
  authorize an archive ADR under the same number. Next collision-free number for a
  new decision = **ADR-024** (see §7).

---

## 3. SPR-014 Interlock Verdict

**Verdict: SPR-017 can be designed against the existing SPR-014 substrate while
SPR-014 remains governance-DEFERRED. No reactivation or new SPR-014 implementation
is required to author the archive architecture.**

Rationale (evidence-based):

- SPR-014 is *code-implemented*: bounded context, migration, factory, three source
  domain contract/adapter seams, 12 tests / 38 assertions core + 27 integration
  tests / 97 assertions; PHPStan 0 errors, Pint clean (SPR-014 report §18,
  2026-08-20 handoff).
- Its **governance** status is "DEFERRED — validated implementation awaiting final
  closure decision" (MASTER_ROADMAP, CURRENT_POSITION, PROJECT_STATE, SESSION_HANDOFF
  2026-09-07). This is a *status* gap, not a missing capability.
- SPR-017 needs from SPR-014 only **read/consume** of `lifecycle_state = archived`
  and the `RecordsLifecycleContract` pattern — both already exist. None of the
  SPR-017 non-goals require SPR-014 to be marked COMPLETE.
- **Therefore:** the SPR-014↔SPR-017 interlock is a *governance closure* issue for
  the eventual ADR, **not** a technical blocker for decision authoring. The archive
  ADR must explicitly (a) consume SPR-014 through contracts, (b) never duplicate its
  lifecycle responsibilities, and (c) record that SPR-014's "Archived" state is the
  only existing lifecycle signal — the AD §12 "Read Only" and "Cold Storage" stages
  have **no** representation today.

**Hard boundary restated:** SPR-017 must **not** consume SPR-014's internals
directly, must **not** create/replace business records, and must **not** be
implemented inside the RecordsLifecycle bounded context.

---

## 4. Decisions Required (Architecture ↔ Product)

| # | Decision | Owner | Current state |
| - | -------- | ----- | ------------- |
| D1 | **Scope placement**: is SPR-017 a *Phase 3 Academic Records* deliverable or the *Phase 9 Enterprise Services* "Cloud Archive"? (MASTER_ROADMAP vs SESSION_HANDOFF §7 conflict). | Product + Architecture | **Unresolved** |
| D2 | **Cloud object storage provider** (and whether S3-compatible abstraction is mandated to avoid lock-in). | Product + Architecture | **Pending** (`SESSION_HANDOFF §10`) |
| D3 | **Archive domain boundary**: new bounded context (`App\Domains\Archive`) vs a platform service; contract-only consumption of SPR-014/SPR-012. | Architecture | To be decided in ADR |
| D4 | **Cold-storage trigger & state model**: does the platform add explicit `Read Only` / `Cold Storage` stages, or treat SPR-014 `archived` as the trigger with archive provenance held separately? | Architecture | To be decided in ADR |
| D5 | **Retention policy model**: configurable per-school/per-document-type retention windows and permanent-preservation vs secure-destruction rules. | Product (minima) + Architecture (mechanism) | **Partly pending** (CR-002) |
| D6 | **Storage quotas & paid extensions**: enforce `plans.storage_limit`, metering, 80/90/100% thresholds, paid expansions. | Product + Architecture | **Pending** (SA-001) |
| D7 | **Downloadable school archive packages**: format, contents, generation triggers, who may request. | Product + Architecture | **Pending** (§9) |
| D8 | **Backup vs Archive separation**: archive/retention is distinct from operational backups (Backups.md); no duplication of the backup pipeline. | Architecture | To be decided in ADR |
| D9 | **Tenant isolation of cloud objects**: how `school_id` isolation holds on a shared S3 bucket (key prefixing, per-tenant credentials, or per-tenant bucket). | Architecture | To be decided in ADR |
| D10 | **Legacy pipeline exclusion**: no revival of the legacy `app/Models/GeneratedDocument` / `DocumentGenerationService` / legacy `app/Domains/Document` pipeline. | Architecture | Fixed constraint |

---

## 5. Options & Trade-offs

### D2 — Cloud storage provider

| Option | Pros | Cons |
| ------ | ---- | ---- |
| A. AWS S3 (stock `s3` disk) | Zero new dependency (`league/flysystem-aws-s3-v3` only); existing env keys | Lock-in; data residency |
| B. S3-compatible abstraction (generic disk + provider-agnostic adapter) | Avoids vendor lock-in; can point at AWS/MinIO/Wasabi/OVH | Slightly more config surface |
| C. Provider-specific SDKs per vendor | Max control | Duplication; violates low-coupling |

**Consulted direction (not decided):** a **single abstraction over an
S3-compatible object store**, with the vendor chosen by product — this satisfies
the "avoid vendor lock-in" constraint and the "no new dependencies beyond the
already-supported driver" principle.

### D3/D4 — Archive boundary & state

| Option | Pros | Cons |
| ------ | ---- | ---- |
| A. New `App\Domains\Archive` consuming contracts | Clean DDD boundary; respects DEPENDENCY_MAP §13; tenant-scoped | New context to build |
| B. Extend `RecordsLifecycle` in place | No new context | Violates SPR-014 thin-envelope scope; risks duplicating lifecycle |
| C. Platform service outside Domains | Strong infra fit | Weakens DDD/contract discipline |

**Consulted direction (not decided):** **Option A**, consuming
`RecordsLifecycleContract` and SPR-012 read contracts only; archive never mutates
source records.

### D6 — Quotas

| Option | Pros | Cons |
| ------ | ---- | ---- |
| A. Reuse `plans.storage_limit` + Usage Limits docs | Consistent with SaaS design | Metering not implemented |
| B. New dedicated quota store | Fine control | Duplicates plan authority |

**Consulted direction (not decided):** **Option A** — consume the plan authority;
SPR-017 must not become a second quota authority.

---

## 6. Product-Owner Decisions Required (blocking the ADR)

1. **Phase placement (D1):** confirm whether SPR-017 is Phase 3 or Phase 9. This
   determines whether the ADR is "near-term" or strategic.
2. **Cloud provider + lock-in policy (D2):** choose provider and whether the
   abstraction layer is mandatory.
3. **Retention minima (D5):** define the compliance floors (legal/regulatory) —
   architecture can build the mechanism, product must set the values.
4. **Quotas & paid archive extensions (D6):** pricing/metering policy, ties to
   `plans.storage_limit`.
5. **Downloadable archive packages (D7):** whether in scope and who may request.

Until 1–5 are answered, the ADR cannot be finalized without inventing product
values (explicitly forbidden by AD §6 / documentation-first governance).

---

## 7. Proposed ADR Number & Structure

- **Proposed number: ADR-024.** ADR-022 is the latest authored; **ADR-023 is
  reserved** by SPR-016 Phase 6B for the deferred verification-issuance boundary.
  Using ADR-023 for archive would collide with that planted decision.
  *Do not auto-create ADR-024 in this session.*
- **Proposed file:** `docs/ADR/ADR-024.md`.
- **Proposed title:** `ADR-024: Archive & Cloud Storage Platform Architecture (SPR-017)`.
- **Proposed structure** (mirroring ADR-021/022 conventions):
  1. Status / Date
  2. Context (SPR-014 substrate, SPR-012 files, AD §12 Archive Rule, DEPENDENCY_MAP §13)
  3. Problem Statement (D1–D10)
  4. Decision Summary
  5. Decision 1 — Scope & phase placement
  6. Decision 2 — Archive bounded context / boundary
  7. Decision 3 — Cold-storage trigger & lifecycle stages
  8. Decision 4 — Storage abstraction & provider policy
  9. Decision 5 — Tenant isolation of archived objects
  10. Decision 6 — Retention policy model
  11. Decision 7 — Quotas & paid extensions integration
  12. Decision 8 — Downloadable archive packages
  13. Decision 9 — Backup vs Archive separation
  14. Cryptographic / encryption scope
  15. Security, Tenancy, Legacy invariants
  16. Responsibility Matrix
  17. Consequences
  18. Rejected Alternatives
  19. Implementation Constraints
  20. Testing Requirements
  21. Related ADRs (ADR-012/013/021/022, AD §12, DEPENDENCY_MAP §10/13)
  22. Approvals

**Additional note:** the `docs/06_ADR/` set must **not** be extended; it is a
separate legacy convention.

---

## 8. Minimal Governance Reconciliation (proposed — NOT applied)

Because the decisions above are not yet resolved, **no governance documents were
edited in this session** (avoiding silent changes to current-sprint status fields).
The following minimal edits are proposed for the session that authorizes ADR-024:

1. `MASTER_ROADMAP.md §5 Current Position`: reconcile stale sprint fields (still
   name SPR-016 initialization while later handoffs show SPR-016 Phases 1–5
   implemented). Add SPR-017 as the next decision target **only after** D1 is
   answered.
2. `CURRENT_POSITION.md` (GOV-005): append a short SPR-017 block (~6 words) —
   e.g. "Next: SPR-017 Archive & Cloud Storage — decision board" — **without**
   changing the "Current Sprint" value while SPR-016 remains formally the active
   sprint.
3. `SESSION_HANDOFF.md §7`: annotate SPR-017 status as "decision board — pending
   product decisions" and link this report.
4. `ADR` numbering: record ADR-023 as reserved (SPR-016 Phase 6B) and point
   SPR-017 to ADR-024.
5. `CHANGELOG.md`: add a non-behavioral entry noting this decision-board report
   (documentation only).

All five are *reconciliation/citation* edits; none asserts completion of SPR-017 or
SPR-014.

---

## 9. Unresolved Blockers & Risks

- **B1 (blocking decision):** Product decisions D1/D2/D5/D6/D7 unresolved (§6).
- **B2 (governance):** SPR-014 remains DEFERRED though implemented; the SPR-017 ADR
  must not silently promote it to COMPLETE. Interlock is otherwise technically
  clear (§3).
- **B3 (pre-existing, unrelated):** MariaDB `migrate` blocker — see §10.
- **R1:** Scope/phase conflict (Phase 3 vs Phase 9) — D1.
- **R2:** Vendor lock-in if a single provider is hard-coded — mitigated by DX/D2.
- **R3:** Tenant isolation of shared cloud objects — D9; must be an invariant.
- **R4:** Duplicating the backup pipeline (`Backups.md`) — D8.
- **R5:** Accidentally duplicating SPR-014 lifecycle responsibilities — fixed
  constraint (contract-only, no new lifecycle engine).
- **R6:** Reviving the legacy document pipeline — fixed constraint (no-go, mirrors
  ADR-021/022 L1–L3).

---

## 10. Validation Performed (read-only)

| Check | Method | Result |
| ----- | ------ | ------ |
| Working tree protected | `git status --porcelain`, `git diff --stat` | 362 entries / 317 files; **unmodified by session** |
| No archive domain/config | directory + glob scans (`app/Domains`, `config`, `migrations`, `routes`) | **none found** |
| Storage substrate | `config/filesystems.php`, `config/tenancy.php`, `config/nexus-academic-communication.php` | `local`/`public`/stock `s3`; `s3` unused & not tenant-suffixed |
| SPR-014 contracts | `app/Domains/*/Domain/Contracts/RecordsLifecycleContract.php` | present in 3 domains |
| ADR sequence | listing + Phase 6B §7 | highest = ADR-022; ADR-023 reserved |
| MariaDB blocker (B3) | inspect `2026_08_01_000011_create_coefficients_table.php` | unique index `coefficients_school_id_school_class_id_academic_subject_id_unique` = **65 chars > MySQL/MariaDB 64-char identifier limit** — original ">64 chars" claim **confirmed** |

### MariaDB blocker discrepancy (recorded, unresolved)

- Phase 6B §14 attributes the MariaDB failure to a "context constraint on
  `academic_documents`" introduced by
  `2026_08_01_000011_create_coefficients_table.php`. **Repository inspection shows
  that migration only creates the `coefficients` table** and contains no
  `academic_documents` constraint; the reproducible defect is the 65-char unique
  index name. The `academic_documents` attribution is therefore **not confirmed**.
- This remains a **pre-existing, unrelated** defect. This session performed **no**
  rollback, edit or workaround. Recorded as an unresolved discrepancy, not a SPR-017
  blocker.

No application tests, PHPStan or Pint were run: this is a documentation-only
decision-board pass and no code changed.

---

## 11. Exact Next Action

1. **Product owner** resolves D1 (phase placement), D2 (provider/lock-in), D5
   (retention minima), D6 (quotas/paid extensions), D7 (archive packages).
2. On those answers, the Architecture Board authors **ADR-024** (structure in §7)
   and applies the minimal governance edits (§8) **in a separate, authorized
   session**.
3. Only after ADR-024 is accepted may SPR-017 implementation planning begin — a new
   `App\Domains\Archive` consuming `RecordsLifecycleContract` + SPR-012 read
   contracts, adding explicit Cold-Storage/Retention semantics, under strict
   tenant-isolation and no-duplicate-lifecycle/no-legacy constraints.
4. The MariaDB blocker (§10) is tracked separately and fixed only in its own
   authorized task; it is **not** part of SPR-017.

---

## Final Verdict

**NEEDS PRODUCT-OWNER DECISIONS.**

The archive architecture is **not** blocked by any unresolved prerequisite
(SPR-014 is implemented and consumable via contracts; the MariaDB defect is
pre-existing and unrelated), so `BLOCKED — PREREQUISITE UNRESOLVED` does not apply.
It is also **not** `READY FOR ADR AUTHORING` because D1/D2/D5/D6/D7 are explicit,
still-pending product-owner decisions (`SESSION_HANDOFF §9/§10`, RISK SA-001/OR-004/
CR-002) that must not be invented by the architecture team. Once those decisions are
provided, the path to ADR-024 is clear.
