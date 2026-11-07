# ADR-024 Decision Resolution and Approval Preparation

**Document ID:** ADR-DR-2026-11-06

**Status:** Decision-preparation package. **NOT an approval, acceptance, waiver or closure.**

**Date:** 06 November 2026

**Owner:** Architecture Team (preparation only)

**Subject:** `docs/ADR/ADR-024.md`, version 1.1, status `Proposed`

**Subject SHA-256:** `F16A19E8262E3F33BD9C3919B8819A77E2B67B48E8B5D669B82B925E1FD5045A`

**Subject size:** 1,704 lines, 155,485 bytes (verified at the time of this package)

**Baseline:** Branch `main`, HEAD `d42dbef3e32ad6e7a7a4bc70c68f805be60da480`

**Authoritative inputs:**

1. `docs/ADR/ADR-024.md`
2. `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md`
3. `docs/docs/00_Project_Management/ADR-024_REVISION_BLUEPRINT_2026-11-06.md`
4. `docs/docs/00_Project_Management/ADR-024_REVISION_REPORT_2026-11-06.md`
5. `docs/docs/00_Project_Management/ADR-024_FORMAL_REVIEW_2026-11-06.md`

**Scope:** Resolve, classify and assign ownership to the ten conditions in section 1.3 of the formal review; prepare explicit proposals for AC-06, AC-16, AC-22 and DR-13; assess PA-1 to PA-5 provenance; prepare the consolidated decision register, human approval matrix, SPR-014 closure action plan and SPR-017 readiness assessment.

**Explicit non-scope:** This package does not accept ADR-024, change its status, sign H-01, approve any DR decision on behalf of a stakeholder, modify any PA approval record, close SPR-014, authorize SPR-017, implement Archive, create contracts/interfaces/migrations/routes/permissions/configuration, modify application code, modify unrelated governance documents, or commit changes.

---

## 1. Executive Summary

### 1.1 Recommendation

The formal review recommends:

**ACCEPTANCE RECOMMENDED WITH EXPLICIT DEFERRED ITEMS**, conditional on the Architecture Team recording H-01 with conditions C-01 to C-10.

This package supports that recommendation but does not convert it into an acceptance. It resolves the ten conditions into explicit decision proposals, identifies who must decide each one, and separates:

- decisions the Project Owner can make or confirm;
- decisions reserved to the Architecture Team;
- decisions that require Security, Operations or Legal/Compliance approval;
- decisions that can be deferred to production or cloud activation;
- decisions that are blocked by missing evidence.

### 1.2 What this package found

1. **All ten conditions are still open.** No DR is approved. No H item is approved. H-01 is unsigned.
2. **The three partial acceptance criteria are decision-recording gaps, not architecture gaps:** AC-06 (tenant and source deletion semantics), AC-16 (encryption posture) and AC-22 (PA provenance and DR-27 ownership).
3. **DR-13 is a real contradiction, not a documentation style issue.** `audit_logs` rows are deleted after 90 days by a scheduled command while `docs/03_Database/Audit.md` claims a minimum seven-year immutable retention. The repository does not encode retention classes.
4. **PA-1 to PA-5 are only partially corroborated.** The only approval record is the ADR-024 v1.0 approval block, repeated in `SESSION_HANDOFF.md` on the same date. The SPR-017 board report dated the same day records the corresponding product items as unresolved or pending. There is no independent signature, meeting minute or approval artifact. Product Owner confirmation is mandatory.
5. **The formal review's statement that no `Crypt` usage exists is too broad.** `app/Models/SystemSetting.php` uses `Crypt` for selected setting values. It is not file or archive encryption. No application-level encryption of archived files exists. This correction does not change the AC-16 conclusion.
6. **SPR-014 remains `DEFERRED`.** The report has no formal review approval, staging record or closure decision. A passing test run is not closure.
7. **SPR-017 remains `NOT IMPLEMENTED / NOT AUTHORIZED`.** No Archive domain, migration, route, config or permission exists. All R-01 to R-15 readiness conditions remain `NOT MET`.

### 1.3 Counts

| Item | Count | Status |
|---|---|---|
| Formal-review conditions | 10 (C-01 to C-10) | Open |
| Acceptance-gate DRs | 18 | All `PROPOSED` |
| Deferred DRs | 2 (DR-23, DR-24) | `DEFERRED` |
| Production-gate DRs | 6 | All `PROPOSED` |
| Cloud-gate DRs | 3 | All `PROPOSED` |
| Human-reserved decisions | 18 (H-01 to H-18) | All `PROPOSED - pending` |
| PA items | 5 (PA-1 to PA-5) | Recorded as approved in v1.0; provenance unconfirmed |
| SPR-014 | 1 | `DEFERRED` |
| SPR-017 | 1 | `NOT IMPLEMENTED / NOT AUTHORIZED` |

### 1.4 Bottom line

ADR-024 is architecturally coherent and can be made acceptance-ready by recording owner decisions. It cannot be accepted by this package. The shortest defensible path is:

1. Project Owner confirms PA provenance and makes the product decisions listed in the `PROJECT OWNER DECISIONS REQUIRED` section.
2. Architecture Team records the per-DR decisions and the DR-27 single-authority declaration.
3. Security, Operations and Legal/Compliance record their reserved decisions, especially DR-13, DR-16 and DR-09/DR-10.
4. H-01 is signed only after C-01 to C-10 are recorded with evidence.
5. SPR-014 closure or waiver is handled separately.
6. SPR-017 implementation authorization remains a separate, later decision.

---

## PROJECT OWNER DECISIONS REQUIRED

This section is deliberately concise. It tells the Project Owner what to decide, what to co-sign, and what is not the Project Owner's decision.

### PO-1. Confirm the provenance of PA-1 to PA-5

**What is needed:** A signed confirmation stating whether the five product decisions recorded in ADR-024 v1.0 were actually approved on 2026-10-10, and with what exact scope.

**Options:**

- **A. Confirm the five decisions as recorded.** State that the SPR-017 board report predates the decisions or records a different question, and confirm the narrow scope in section 5 of this package.
- **B. Confirm a narrower scope.** State which parts were approved and which remain open.
- **C. Do not confirm.** The PA items must then be marked "recorded, unverified"; AC-22 cannot be satisfied and ADR-024 cannot be accepted as written.

**Recommended:** A, with the exact scope limits in section 5. Do not expand the historical approval to provider, region, retention values, metering, pricing or package specifics.

**Who else must sign:** Architecture Team (DR-27 co-ownership and single-authority declaration).

**Blocks acceptance?** Yes.

### PO-2. Choose the quota unit base for DR-06

**What is needed:** A binary (1024) or decimal (1000) choice for the canonical storage unit.

**Options:** Binary 1024 (recommended for technical consistency with file sizes) or decimal 1000 (SI/marketing consistency).

**Recommended:** Binary 1024 as the canonical calculation base, with decimal display optional if Product wants it.

**Who else must sign:** Architecture Team.

**Blocks acceptance?** Yes.

### PO-3. Confirm the first-release encryption risk position

**What is needed:** A decision on the DR-16/H-06 posture:

- **A.** Require application-level envelope encryption for archive objects before production.
- **B.** Require evidenced host/volume encryption plus private-disk and access controls for the first release; defer application-level encryption to DR-17 unless Security rejects the residual risk.
- **C.** Accept the risk with documented controls and a written risk acceptance.

**Recommended:** B, with a fallback to A if Operations cannot evidence host/volume encryption. This is a recommendation only; Security Lead and Architecture Team must formally decide.

**Who else must sign:** Security Lead and Architecture Team are mandatory. The Project Owner signs only the residual-risk acceptance, not the security design.

**Blocks acceptance?** Yes, for the chosen posture. Implementation/evidence is a production gate.

### PO-4. Confirm the school-offboarding default and source-deletion rule

**What is needed:** Confirm that:

- school offboarding defaults to **suspend and retain**, not automatic deletion;
- deleting a source record does **not** cascade to archive metadata or archived objects;
- export and permanent disposition are explicit, authorized actions, not automatic;
- legal holds override retention and destruction;
- permanent destruction remains disabled in the first release and requires DR-11.

**Recommended:** Confirm all five points. This is the AC-06 corrective action.

**Who else must sign:** Legal/Compliance and Architecture Team are mandatory. Operations must confirm execution feasibility.

**Blocks acceptance?** Yes, for the semantics. Legal values and destruction execution can be deferred to production.

### PO-5. Confirm DR-07: quota is `report_only` in the first release

**What is needed:** Confirm that quota resolution reports usage but never blocks archive operations, and that `used_bytes` may be null.

**Recommended:** Confirm.

**Who else must sign:** Architecture Team.

**Blocks acceptance?** Yes.

### PO-6. Decide the production direction for DR-08

**What is needed:** Confirm whether metering, enforcement and paid extensions are required for local-first production, or explicitly deferred.

**Recommended:** Keep the first release report-only; decide enforcement and paid extensions before production, with Finance if paid.

**Who else must sign:** Architecture Team, plus Finance if paid.

**Blocks acceptance?** No. Blocks production: Yes.

### PO-7. Confirm DR-24: packages remain deferred

**What is needed:** Confirm that downloadable school archive packages remain in architectural scope but are not first-release mandatory; format, contents, size and expiry remain deferred.

**Recommended:** Confirm.

**Who else must sign:** Architecture Team; Security Lead for the package threat boundary.

**Blocks acceptance?** No. The boundary is part of acceptance; specifics are deferred.

### PO-8. Confirm DR-28 and the SPR-014 path

**What is needed:** Confirm that SPR-017 implementation is gated on a recorded SPR-014 closure decision or an explicit owner-signed waiver, and that ADR-024 acceptance does not close SPR-014.

**Recommended:** Confirm the interlock. Do **not** authorize a waiver now. Choose closure-with-evidence as the default path; a waiver is a separate, scoped decision.

**Who else must sign:** Architecture Team; Operations Lead for staging evidence.

**Blocks acceptance?** Yes, for the interlock wording. Blocks SPR-017: Yes.

### PO-9. Acknowledge C-09

**What is needed:** Acknowledge that acceptance of ADR-024:

- does not close SPR-014;
- does not authorize SPR-017 implementation;
- does not declare production or cloud readiness;
- leaves H-15 and H-16 pending.

**Recommended:** Acknowledge in H-01.

**Who else must sign:** Architecture Team.

**Blocks acceptance?** Yes, as a recorded acknowledgement.

### PO-10. Confirm the responsible role holders

**What is needed:** Confirm who currently holds or is authorized to act as Security Lead, Operations Lead, Legal/Compliance and Privacy/DPO. If a role is vacant, the related decision cannot be validly recorded by an agent or by the Project Owner acting alone.

**Recommended:** Name the role holders in the H-01 record or an attached authority matrix.

**Who else must sign:** Architecture Team.

**Blocks acceptance?** Yes, for decisions reserved to those roles.

### What the Project Owner is not being asked to do

The Project Owner is **not** being asked to:

- approve DR-01 to DR-05, DR-09, DR-12, DR-13, DR-14, DR-15, DR-16, DR-19, DR-26, DR-27 or DR-29 on behalf of the Architecture Team, Security Lead, Operations Lead or Legal/Compliance;
- sign H-01 on behalf of the Architecture Team;
- close SPR-014;
- authorize SPR-017;
- accept production or cloud readiness;
- approve legal retention periods, a DPA, a hosting location or a cloud provider.

---

## 2. Ten Formal-Review Conditions

The ten conditions below are reproduced **verbatim** from section 1.3 of `ADR-024_FORMAL_REVIEW_2026-11-06.md`. No paraphrase is substituted.

### 2.1 C-01 - Per-decision approval of the 18 acceptance-gate DRs

**Exact condition:** "Approve, amend or reject each of the 18 acceptance-gate DRs: DR-01, DR-02, DR-03, DR-04, DR-05, DR-06, DR-07, DR-09, DR-12, DR-13, DR-14, DR-15, DR-16, DR-19, DR-26, DR-27, DR-28, DR-29. A blanket "ADR accepted" is not a substitute for a per-decision record."

**Owner (formal review):** Architecture Team, with the co-owners named in section 3.

**Related DR/H:** DR-01, DR-02, DR-03, DR-04, DR-05, DR-06, DR-07, DR-09, DR-12, DR-13, DR-14, DR-15, DR-16, DR-19, DR-26, DR-27, DR-28, DR-29; H-01.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL. Specific DRs additionally require Security, Legal/Compliance or Product Owner approval.

### 2.2 C-02 - Quota unit base

**Exact condition:** "Record a binary (1024) or decimal (1000) choice for quota units under DR-06. The ADR correctly does not choose on the owners' behalf."

**Owner (formal review):** Architecture Team + Product Owner.

**Related DR/H:** DR-06; H-08.

**Classification:** READY FOR PROJECT OWNER DECISION, with mandatory Architecture Team concurrence.

### 2.3 C-03 - First-release encryption posture

**Exact condition:** "Record the first-release encryption posture under DR-16 and H-06: application-level encryption, evidenced host/volume encryption, or accepted risk with controls. The ADR currently records the options, not the decision. This is the corrective action for AC-16."

**Owner (formal review):** Security Lead + Architecture Team, with Product Owner for risk acceptance.

**Related DR/H:** DR-16, DR-17, H-06, H-07.

**Classification:** REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL. Product Owner risk acceptance is mandatory if residual risk is accepted.

### 2.4 C-04 - Tenant-offboarding and source-deletion semantics

**Exact condition:** "Record tenant-offboarding and source-deletion semantics under DR-09/DR-10: suspend, retain, export or dispose; confirm that archive metadata and objects do not cascade silently; state what happens when a source record is deleted after archive entry. This is the corrective action for AC-06."

**Owner (formal review):** Architecture Team + Legal/Compliance, with Product Owner.

**Related DR/H:** DR-09, DR-10, DR-11, H-02, H-03, H-04, H-05.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL. Product Owner approval is mandatory for the product disposition. Operations approval is mandatory for execution.

### 2.5 C-05 - PA-1 to PA-5 provenance

**Exact condition:** "Confirm the provenance of PA-1 to PA-5 with the Product Owner. The revised ADR treats the v1.0 approval block as the only approval record and the board report as historical evidence. The conflict must be confirmed, not silently resolved. Add the Product Owner as a co-owner of DR-27 or record the confirmation under H-01. This is the corrective action for AC-22."

**Owner (formal review):** Product Owner + Architecture Team.

**Related DR/H:** DR-27; PA-1 to PA-5; H-01.

**Classification:** READY FOR PROJECT OWNER DECISION for the provenance confirmation + REQUIRES ARCHITECTURE TEAM APPROVAL for DR-27 co-ownership and authority.

### 2.6 C-06 - Audit-retention contradiction

**Exact condition:** "Resolve the seven-year versus ninety-day audit-retention contradiction under DR-13 before relying on audit evidence. A recorded contradiction is not a resolution."

**Owner (formal review):** Security Lead + Legal/Compliance + Architecture Team.

**Related DR/H:** DR-12, DR-13; H-02; PRD-07.

**Classification:** REQUIRES SECURITY APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL.

### 2.7 C-07 - DR-27 single-authority declaration

**Exact condition:** "Confirm DR-27's single-authority declaration: the revised ADR is the authoritative SPR-017 decision log; the SPR-017 board report remains historical evidence."

**Owner (formal review):** Architecture Team, with Product Owner.

**Related DR/H:** DR-27; H-01.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL, with Product Owner concurrence.

### 2.8 C-08 - Acknowledge deferred items and gates

**Exact condition:** "Acknowledge the deferred items and gates in section 3: DR-23 and DR-24 are deferred with rationale; DR-08, DR-10, DR-11, DR-17, DR-18 and DR-21 block production; DR-20, DR-22 and DR-25 block cloud activation. Acceptance does not satisfy any of these."

**Owner (formal review):** Architecture Team.

**Related DR/H:** DR-23, DR-24, DR-08, DR-10, DR-11, DR-17, DR-18, DR-21, DR-20, DR-22, DR-25; H-01.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL (recorded acknowledgement in H-01).

### 2.9 C-09 - Acceptance does not close SPR-014 or authorize SPR-017

**Exact condition:** "Acknowledge that acceptance does not close SPR-014, does not authorize SPR-017 and does not declare production or cloud readiness. H-15 and H-16 remain pending."

**Owner (formal review):** Architecture Team + Product Owner.

**Related DR/H:** DR-28; H-15, H-16; SPR-014; SPR-017.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES PROJECT OWNER ACKNOWLEDGEMENT.

### 2.10 C-10 - Record the H-01 decision

**Exact condition:** "Record the H-01 decision date, authorized decision-makers, scope, conditions and approval evidence in section 8. The template must remain unsigned until that record exists."

**Owner (formal review):** Architecture Team.

**Related DR/H:** H-01; all C-01 to C-09.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL.

### 2.11 What "recorded" means

A condition is recorded only when the repository contains a decision record with all of the following:

1. the decision ID and the exact decision text or scope;
2. the name and role of each authorized decision-maker;
3. the decision date;
4. the decision outcome: approve, amend, reject, defer or request evidence;
5. the rationale and any conditions or exceptions;
6. the evidence reference (meeting minute, signed record, ticket or repository document);
7. a signature or equivalent verifiable approval artifact.

An agent cannot supply any of items 2, 3, 4, 6 or 7. A checked box without a named signatory, date and evidence reference is not an approval.

---

## 3. Individual Decision Proposals

Each entry below is self-contained. The original condition is repeated verbatim so the proposal can be read without cross-referencing section 2.

### 3.1 C-01 - Per-decision approval of the 18 acceptance-gate DRs

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL; specific DRs require Security, Legal/Compliance or Product Owner co-approval.

**Original condition (verbatim):** "Approve, amend or reject each of the 18 acceptance-gate DRs: DR-01, DR-02, DR-03, DR-04, DR-05, DR-06, DR-07, DR-09, DR-12, DR-13, DR-14, DR-15, DR-16, DR-19, DR-26, DR-27, DR-28, DR-29. A blanket "ADR accepted" is not a substitute for a per-decision record."

**Architectural or product question:** Can the Architecture Team adopt the proposed direction of each acceptance-gate DR, with named co-owners for the decisions that are not architecture-only, without a blanket acceptance that hides unresolved items?

**Existing repository evidence:** ADR-024 section 24.1 lists all 18 as `PROPOSED`; section 24.2 classifies them as acceptance-gate; section 37.3 assigns sign-off roles; the formal review section 3.1 confirms no DR is approved. No decision schedule or signed record exists.

**Current status:** Open. All 18 are `PROPOSED`. No per-decision record exists.

**Responsible owner:** Architecture Team, with the co-owners named in section 7.

**Approval sufficiency:** Project Owner approval is **not sufficient**. The Architecture Team owns the architecture decisions. Product Owner co-approval is mandatory for DR-06 and DR-07. Security co-approval is mandatory for DR-05, DR-13, DR-14, DR-15, DR-16 and DR-26. Legal/Compliance co-approval is mandatory for DR-09 and DR-13. Operations co-approval is mandatory for DR-19.

**Recommended direction:** Adopt a per-decision decision schedule that records, for each DR, one of: `APPROVED AS PROPOSED`, `APPROVED WITH AMENDMENT`, `REJECTED`, `DEFERRED WITH RATIONALE`, or `EVIDENCE REQUIRED`. Record it as an annex to H-01. Use the recommended directions in section 6.2 of this package as the starting proposal. Do not use a blanket "ADR accepted" entry.

**Alternative options:**

- **A. Blanket acceptance of all 18.** Rejected: it is a silent promotion and defeats the formal review's C-01.
- **B. Approve only the uncontested DRs and defer the rest.** Acceptable only if the deferred DRs are explicitly listed and the ADR is not recorded as fully accepted.
- **C. Per-decision schedule (recommended).** Highest evidence quality; more work.
- **D. Request a further revision instead of deciding.** Acceptable if the Architecture Team cannot record C-01 to C-06; the formal review converts to `REVISION REQUIRED`.

**Advantages:** A per-decision schedule makes every acceptance-gate choice visible, testable and auditable. It prevents a blanket acceptance from hiding DR-13, DR-16 or DR-27.

**Disadvantages:** It requires a meeting or written decision cycle and named co-signatories. It may expose disagreements that a blanket acceptance would conceal.

**Consequences of postponing:** H-01 cannot be signed. ADR-024 remains `Proposed`. SPR-017 remains unauthorized. The formal review recommendation expires into `REVISION REQUIRED` if C-01 to C-06 cannot be recorded.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, directly through R-01.

**Deferrable until:** Not deferrable. This is an acceptance gate.

**Evidence required to close:** A dated per-DR decision schedule signed or otherwise verifiably approved by the named owners, attached to or referenced by H-01.

### 3.2 C-02 - Quota unit base (binary or decimal)

**Classification:** READY FOR PROJECT OWNER DECISION, with mandatory Architecture Team concurrence.

**Original condition (verbatim):** "Record a binary (1024) or decimal (1000) choice for quota units under DR-06. The ADR correctly does not choose on the owners' behalf."

**Architectural or product question:** Is the canonical storage unit binary (1 KB = 1024 bytes) or decimal (1 KB = 1000 bytes), and how are existing plan strings such as `500MB`, `2GB`, `10GB` and `50GB` interpreted?

**Existing repository evidence:** `database/migrations/2026_07_24_000001_create_plans_table.php:23` defines `storage_limit` as a string defaulting to `1GB`; `database/seeders/PlanSeeder.php:25,40,55,70` seeds `500MB`, `2GB`, `10GB`, `50GB`; `app/Models/Plan.php` has no parser or cast. ADR-024 section 11.2 specifies canonical bytes and leaves the base choice to DR-06.

**Current status:** Open. No base choice is recorded.

**Responsible owner:** Product Owner + Architecture Team.

**Approval sufficiency:** Project Owner decision is necessary but **not sufficient alone**; Architecture Team concurrence is mandatory because the parser, contract and tests depend on it.

**Recommended direction:** Adopt **binary 1024** as the canonical calculation base, with decimal display optional for product presentation. Parse `KB`, `MB`, `GB`, `TB` case-insensitively; reject invalid or negative values; store canonical bytes. Record the choice in DR-06 and in the quota contract tests.

**Alternative options:**

- **A. Binary 1024 (recommended).** Matches file-size reporting and common storage tooling.
- **B. Decimal 1000.** Matches SI and some marketing conventions; requires clear labelling to avoid confusion.
- **C. Store only bytes and never parse human strings.** Safest technically, but requires a migration/backfill of existing plan values and a product display decision.

**Advantages:** Removes ambiguity from quota reads and tests. Makes `500MB` and `2GB` deterministic.

**Disadvantages:** Binary units can differ from a plan marketed as "2 GB" under decimal expectations. A display convention is still needed.

**Consequences of postponing:** DR-06 cannot be approved; the quota contract cannot be implemented unambiguously; SPR-017 R-11 cannot start.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, for the quota contract and consumer.

**Deferrable until:** Not deferrable for the unit base. The choice of display format can be deferred to UI work.

**Evidence required to close:** A recorded Product Owner choice with Architecture Team concurrence, plus the parser rules in the DR-06 decision record.

### 3.3 C-03 - First-release encryption posture

**Classification:** REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL; Product Owner risk acceptance is mandatory if risk is accepted.

**Original condition (verbatim):** "Record the first-release encryption posture under DR-16 and H-06: application-level encryption, evidenced host/volume encryption, or accepted risk with controls. The ADR currently records the options, not the decision. This is the corrective action for AC-16."

**Architectural or product question:** What protects archived official records at rest in the first release, and what evidence is required before the platform may claim that protection?

**Existing repository evidence:** `config/database.php:113` has the database `encrypt` option commented out. The private local disk is `storage_path('app/private')` (`config/filesystems.php:33-35`). `app/Models/SystemSetting.php:62-75` uses `Crypt` for selected setting values; this is **not** file or archive encryption. No application-level encryption of documents or archive objects was found. Host/volume encryption is unverified. `BackupService` does not encrypt backups.

**Current status:** Open. ADR-024 section 14.5 records options (a), (b) and (c), but no choice. AC-16 is partially satisfied.

**Responsible owner:** Security Lead + Architecture Team, with Operations Lead for host evidence and Product Owner for residual-risk acceptance.

**Approval sufficiency:** Project Owner approval is **not sufficient**. Security Lead and Architecture Team must decide the control. Operations must evidence host/volume encryption. Product Owner signs only the residual-risk acceptance if option (c) is chosen.

**Recommended direction:** For the first release, require **evidenced host/volume encryption** plus the private disk, tenant isolation, access control and audit controls already proposed; do not claim application-level encryption. Defer application-level envelope encryption to DR-17 unless Security determines that host/volume encryption cannot be evidenced or that the residual risk is unacceptable. If host/volume encryption cannot be evidenced, make application-level encryption mandatory before production. Record the posture in DR-16/H-06. See section 4.2 for the full analysis.

**Alternative options:**

- **A. Application-level envelope encryption for archive objects.** Strongest protection against disk, backup and host compromise; supports per-tenant keys and crypto-shredding; highest operational complexity.
- **B. Evidenced host/volume encryption (recommended minimum).** Transparent to the application; protects against physical media theft; does not protect against application or privileged host compromise; requires Operations evidence.
- **C. Accepted risk with documented controls.** Fastest; requires a written risk acceptance by Product Owner and Security; weakest evidence.
- **D. Hybrid: B now, A before production if B cannot be evidenced or risk is rejected.** Defensible and staged.

**Advantages:** B is simple, has no application performance cost and can be evidenced by Operations. A provides the strongest protection but is a larger implementation and key-management burden.

**Disadvantages:** B does not protect archived files from an application-level compromise or a privileged host operator. A introduces key custody, rotation, recovery and backup-encryption complexity.

**Consequences of postponing:** AC-16 remains partially satisfied; H-06 remains pending; the first release could proceed with an unrecorded at-rest risk; DR-17 cannot be implemented.

**Blocks ADR acceptance?** Yes, the posture must be recorded.

**Blocks SPR-017 implementation?** Yes, because the chosen posture determines whether the storage port needs an encryption seam. If host/volume encryption is chosen, the code impact is smaller; if application-level encryption is chosen, the port and object pipeline must be designed for it.

**Deferrable until:** Implementation and evidence can be deferred to production (DR-17). Cloud key management and rotation are deferrable to cloud activation (DR-20/DR-22). The posture choice itself is not deferrable beyond acceptance.

**Evidence required to close:** A signed DR-16/H-06 decision naming the chosen control, the residual-risk position, the key-management owner, the rotation approach and the evidence Operations must produce.

### 3.4 C-04 - Tenant-offboarding and source-deletion semantics

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL; Product Owner approval for the product disposition; Operations approval for execution.

**Original condition (verbatim):** "Record tenant-offboarding and source-deletion semantics under DR-09/DR-10: suspend, retain, export or dispose; confirm that archive metadata and objects do not cascade silently; state what happens when a source record is deleted after archive entry. This is the corrective action for AC-06."

**Architectural or product question:** When a school is suspended, closed or deleted, or when a source document/version is deleted after it has been archived, what happens to the archive metadata, the archived object and the retention/legal-hold obligations?

**Existing repository evidence:** `generated_files` cascades on `school_id`, `academic_document_id` and `document_version_id` (`database/migrations/2026_08_06_000010_create_generated_files_table.php:29-31`). `document_audits` cascades on the same keys (`database/migrations/2026_08_06_000009_create_document_audits_table.php:28-30`). ADR-024 section 8.1 prohibits a blind archive cascade and defers the disposition to DR-09/DR-10; section 15 repeats the prohibition; section 21 states the tenancy invariants. No Archive table exists, so no archive cascade exists yet.

**Current status:** Open. AC-06 is partially satisfied: the prohibition is recorded, but the chosen disposition and post-entry source-deletion behaviour are not.

**Responsible owner:** Architecture Team + Legal/Compliance + Product Owner; Operations Lead for execution design.

**Approval sufficiency:** Project Owner approval is **not sufficient**. Legal/Compliance must decide the retention, erasure and legal-hold obligations. Architecture must decide the data model and no-cascade rule. Operations must confirm the execution path.

**Recommended direction:** Adopt the following default semantics and record them in DR-09/DR-10:

1. **School suspension:** archive becomes read-only; no new entries; metadata and objects are retained; access is restricted to authorized administrators; no deletion.
2. **School offboarding/closure:** default is **suspend and retain**, not delete. A recorded offboarding decision chooses suspend, retain, export or dispose. Export and dispose are explicit, authorized workflows.
3. **Source-record deletion after archive entry:** the archive entry and object are **not** deleted by cascade. The archive records an auditable divergence (`source_missing` or an equivalent event) and remains readable to authorized users. If the deletion is an erasure request, Legal/Compliance decides whether an archival or legal obligation overrides erasure; that decision is recorded under DR-10/H-02/H-03.
4. **Legal hold:** a hold always wins over retention and destruction; only an authorized role may place or release it; both actions are audited.
5. **Permanent destruction:** disabled in the first release; requires DR-11, H-03, H-04 and H-05; dry-run, approval, separation of duties and a deletion certificate.
6. **No silent cascade:** `archive_entries` must not use `cascadeOnDelete` foreign keys to source tables or tenants. Source identifiers are stored as values; archive lifecycle is independent.
7. **Backup is not archive:** deleting a backup must never be presented as destruction of an archived record.

**Alternative options:**

- **A. Cascade delete with the school.** Rejected: it destroys official records and violates the preservation intent.
- **B. Retain everything indefinitely with no offboarding.** Rejected: it ignores erasure, cost and legal obligations.
- **C. Suspend-and-retain default with explicit export/dispose (recommended).** Balances preservation, legal obligations and operability.
- **D. Export-first offboarding, then dispose.** Acceptable if export is mandatory and verified; heavier and depends on DR-24.
- **E. Per-tenant encryption keys with crypto-shredding.** A future option; requires key management and is not a first-release solution.

**Advantages:** C prevents accidental destruction, preserves audit evidence and gives Legal a controlled disposition path. It is consistent with the ADR's no-cascade rule.

**Disadvantages:** C retains data after a school leaves, which may conflict with an erasure request or cost expectations; it requires a legal decision and an operational offboarding process.

**Consequences of postponing:** AC-06 remains partial; the archive schema could be built with a dangerous cascade; offboarding could destroy records or retain them unlawfully; DR-10 and DR-11 remain blocked.

**Blocks ADR acceptance?** Yes, for the no-cascade and offboarding semantics. Legal values and destruction execution can be deferred to production.

**Blocks SPR-017 implementation?** Yes, for the schema and source-deletion behaviour. The offboarding UI, export and destruction execution can be later.

**Deferrable until:** Legal retention values and permanent destruction: production. Cloud-specific deletion and residency semantics: cloud activation.

**Evidence required to close:** A recorded DR-09/DR-10 decision with the no-cascade rule, the offboarding default, the source-deletion behaviour, the legal-hold precedence and the disposition authority. See section 4.1.

### 3.5 C-05 - PA-1 to PA-5 provenance

**Classification:** READY FOR PROJECT OWNER DECISION (provenance confirmation) + REQUIRES ARCHITECTURE TEAM APPROVAL (DR-27 ownership and authority).

**Original condition (verbatim):** "Confirm the provenance of PA-1 to PA-5 with the Product Owner. The revised ADR treats the v1.0 approval block as the only approval record and the board report as historical evidence. The conflict must be confirmed, not silently resolved. Add the Product Owner as a co-owner of DR-27 or record the confirmation under H-01. This is the corrective action for AC-22."

**Architectural or product question:** Did the Product Owner actually approve PA-1 to PA-5 on 2026-10-10, and if so, with what exact scope, given that the SPR-017 board report dated the same day records the corresponding items as unresolved or pending?

**Existing repository evidence:** ADR-024 v1.0 approval block (preserved in v1.1 section 37.1) records PA-1 to PA-5 as approved on 2026-10-10. `SESSION_HANDOFF.md` section "2026-10-10" repeats the approval. The SPR-017 board report dated 2026-10-10 records D1 `Unresolved`, D2 `Pending`, D5 `Partly pending`, D6 `Pending` and D7 `Pending`. There is no independent signature, meeting minute or approval artifact. See section 5 for the full assessment.

**Current status:** Open and contested. AC-22 is partially satisfied.

**Responsible owner:** Product Owner + Architecture Team.

**Approval sufficiency:** Product Owner confirmation is the core evidence. It is **not sufficient alone** for DR-27: the Architecture Team must co-own DR-27 and confirm the single-authority declaration.

**Recommended direction:** The Product Owner issues a signed provenance confirmation that:

- names PA-1 to PA-5 individually;
- states whether each was approved on 2026-10-10;
- states the exact scope of each approval, using the narrow scope in section 5.3;
- states whether the board report predates the decision or records a different question;
- does not expand the approval to provider, region, retention values, metering, pricing or package specifics.

The Architecture Team then records the Product Owner as DR-27 co-owner and confirms the revised ADR as the authoritative decision log. The confirmation is referenced in H-01.

**Alternative options:**

- **A. Confirm the five decisions as recorded (recommended, with exact scope).** Closes AC-22 if the confirmation is signed.
- **B. Treat the board report as authoritative and reopen PA-1 to PA-5.** Safest evidentially, but it reopens scope, storage, retention, quota and package decisions and delays acceptance.
- **C. Mark PA-1 to PA-5 "recorded, unverified" and proceed.** Not acceptable for AC-22; it leaves the only approved items contested.
- **D. Commission an independent governance audit.** Highest assurance; heavier and slower.

**Advantages:** A preserves the historical record, resolves the conflict with the accountable owner and avoids re-litigating settled product strategy.

**Disadvantages:** A relies on a retrospective confirmation of a documentary record; if the Product Owner cannot confirm, the conflict remains and ADR acceptance is blocked.

**Consequences of postponing:** AC-22 remains partial; H-01 rests on an unconfirmed approval record; DR-27 cannot be approved; the only approved items in the ADR remain challengeable.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, indirectly: PA-1 to PA-5 underpin scope, storage strategy, retention direction, quota authority and package scope.

**Deferrable until:** Not deferrable. This is an acceptance gate.

**Evidence required to close:** A dated, signed Product Owner confirmation with the exact scope, plus the Architecture Team's DR-27 co-ownership and single-authority record.

### 3.6 C-06 - Audit-retention contradiction

**Classification:** REQUIRES SECURITY APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL.

**Original condition (verbatim):** "Resolve the seven-year versus ninety-day audit-retention contradiction under DR-13 before relying on audit evidence. A recorded contradiction is not a resolution."

**Architectural or product question:** Which audit records must be retained for which period, who owns each retention decision, and how does the platform prevent the generic 90-day cleanup from deleting records or compliance evidence?

**Existing repository evidence:** `docs/03_Database/Audit.md:74-79` states audit logs are immutable with a minimum seven-year retention. `app/Console/Commands/System/SystemCleanupCommand.php:14,23-29` defaults to 90 days and deletes `AuditLog`, `ActivityLog` and `SystemHealth`. `bootstrap/app.php:47` schedules `system:cleanup --days=90` daily. `audit_logs` has no `retention_class` column (`database/migrations/2026_07_17_000070_create_audit_logs_table.php`). `document_audits` is a separate append-only journal but cascades on school/document delete. No `archive_events` table exists.

**Current status:** Open. The contradiction is recorded in ADR-024 section 13.2 but not resolved. DR-13 is acceptance-gated.

**Responsible owner:** Security Lead + Legal/Compliance + Architecture Team; Operations Lead for implementation.

**Approval sufficiency:** Project Owner approval is **not sufficient**. Security owns security-event retention; Legal/Compliance owns records and compliance retention; Architecture owns the store and class model; Operations owns the cleanup implementation.

**Recommended direction:** Resolve DR-13 by adopting the four-class model and a dedicated archive evidence store:

1. `operational`: health checks, routine activity feed. Owner: Operations. Short retention; the current 90-day default may continue for this class only.
2. `security`: authentication, access denial, permission changes, super-admin break-glass. Owner: Security. Configurable retention; never deleted by the generic cleanup unless the class is explicitly authorized.
3. `records`: archive entry, retention evaluation, hold, disposition, restoration. Owner: Legal/Compliance + Product. Dedicated append-only `archive_events` store; no generic cleanup.
4. `compliance`: destruction certificates, legal-hold placement/release, approval records. Owner: Legal/Compliance. Dedicated append-only store; no generic cleanup.

The generic `audit_logs` stream remains for operational and security events with a configurable retention. Archive records/compliance events go to `archive_events` (DR-12). The seven-year claim in `docs/03_Database/Audit.md` must be corrected or qualified by Legal/Compliance: either it applies to a named class with a recorded legal basis, or it is removed as an unqualified claim. No retention period is invented by this package.

**Alternative options:**

- **A. Keep the 90-day cleanup for all audit rows.** Rejected: it contradicts the documented seven-year claim and can destroy evidence.
- **B. Change all audit retention to seven years.** Rejected unless Legal confirms the legal basis and Operations accepts the cost; the repository does not support the claim today.
- **C. Class-based retention with a dedicated archive store (recommended).** Separates concerns and protects evidence.
- **D. Stop the cleanup job entirely.** Operationally risky and does not solve class ownership.
- **E. Add a `retention_class` column to `audit_logs` and exempt protected classes.** Simpler than a new table but modifies existing cleanup behaviour and risks accidental deletion.

**Advantages:** C gives each class an owner, protects records and compliance evidence and avoids inventing a legal period.

**Disadvantages:** C adds a new store and requires a migration/backfill decision for existing `audit_logs`; it also requires cleanup-job changes and duplicate-command consolidation.

**Consequences of postponing:** Audit evidence can be deleted at 90 days while the platform claims seven years. DR-12/DR-13 remain open; PRD-07 cannot be satisfied; archive audit evidence cannot be relied on.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, for the audit sink and event catalogue (R-10). Other implementation work is not directly blocked, but no audit evidence claim may be made.

**Deferrable until:** The class/ownership resolution is not deferrable. Implementation of class-specific retention and cleanup changes can be a production prerequisite. Cloud audit retention is a cloud consideration.

**Evidence required to close:** A recorded DR-13 decision with the class table, owners, store choice, cleanup behaviour, the correction or legal qualification of the seven-year claim, and the migration/backfill decision. See section 4.4.

### 3.7 C-07 - DR-27 single-authority declaration

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL, with Product Owner concurrence.

**Original condition (verbatim):** "Confirm DR-27's single-authority declaration: the revised ADR is the authoritative SPR-017 decision log; the SPR-017 board report remains historical evidence."

**Architectural or product question:** Which document is authoritative when ADR-024, the SPR-017 board report and `SESSION_HANDOFF.md` disagree on decision numbering, approval status and gate classification?

**Existing repository evidence:** ADR-024 section 36.2 declares the revised ADR the authoritative decision log and maps board D1-D10 to PA/DR items. The board report remains unchanged and records pending items. `SESSION_HANDOFF.md` says open decisions do not block ADR acceptance, which conflicts with the current acceptance-gate classification. DR-27's owner row names only Architecture Team, while section 36.3 requires Product Owner confirmation.

**Current status:** Open. The reconciliation is applied in the ADR, but owner confirmation is pending and the DR-27 ownership is incomplete.

**Responsible owner:** Architecture Team, with Product Owner.

**Approval sufficiency:** Project Owner concurrence is necessary because the PA provenance is a product record. It is **not sufficient alone**: the Architecture Team owns the single-authority declaration.

**Recommended direction:** Confirm that:

- ADR-024 v1.1 is the authoritative SPR-017 architecture decision log;
- the SPR-017 board report is historical evidence, not a competing authority;
- the board's D1-D10 mapping in ADR section 36.2 is accepted;
- the `SESSION_HANDOFF.md` statement that open decisions do not block ADR acceptance is superseded by the current gate classification;
- the Product Owner is added as DR-27 co-owner.

**Alternative options:**

- **A. Confirm the ADR as single authority (recommended).** Clearest and consistent with the revision.
- **B. Treat the board report as authoritative.** Would reopen the decision log and contradict the revision.
- **C. Maintain dual authority with a cross-reference.** Rejected: it leaves readers unable to tell which record governs.
- **D. Create a new consolidated decision log.** Heavier; duplicates the ADR's register.

**Advantages:** A gives implementers and reviewers one authoritative record and preserves history without rewriting it.

**Disadvantages:** A requires explicit correction of the handoff statement and a Product Owner co-ownership change; it does not by itself prove PA provenance.

**Consequences of postponing:** Conflicting authority persists; H-01 could be signed without a clear decision log; DR-27 remains unapproved.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, indirectly: implementers would not know which decision record governs.

**Deferrable until:** Not deferrable. This is an acceptance gate.

**Evidence required to close:** A recorded Architecture Team decision with Product Owner concurrence, the DR-27 owner update and the mapping accepted.

### 3.8 C-08 - Acknowledge deferred items and gates

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL (recorded acknowledgement in H-01).

**Original condition (verbatim):** "Acknowledge the deferred items and gates in section 3: DR-23 and DR-24 are deferred with rationale; DR-08, DR-10, DR-11, DR-17, DR-18 and DR-21 block production; DR-20, DR-22 and DR-25 block cloud activation. Acceptance does not satisfy any of these."

**Architectural or product question:** Does acceptance of ADR-024 satisfy any production or cloud gate, or does it only accept the architecture direction?

**Existing repository evidence:** ADR-024 section 24.2 lists the gate split; section 27.4 lists production prerequisites; section 27.5 lists cloud prerequisites; DR-23 and DR-24 are `DEFERRED`; all production and cloud DRs are `PROPOSED`. No production or cloud evidence exists.

**Current status:** Open as an acknowledgement. The gate split is recorded in the ADR but not acknowledged in an H-01 decision.

**Responsible owner:** Architecture Team.

**Approval sufficiency:** Architecture Team acknowledgement is required. Project Owner acknowledgement is required for the production/cloud consequence (C-09). This is not a Project Owner-only decision.

**Recommended direction:** Record the acknowledgement in H-01 exactly as stated: DR-23/DR-24 deferred; DR-08/10/11/17/18/21 block production; DR-20/22/25 block cloud; acceptance satisfies none of them.

**Alternative options:**

- **A. Record the acknowledgement (recommended).** Keeps the gates explicit.
- **B. Treat acceptance as satisfying the gates.** Rejected: it would be a silent promotion and is contradicted by the repository evidence.
- **C. Reclassify a gate.** Allowed only by a separate recorded decision with owner evidence; not recommended at this stage.

**Advantages:** A prevents acceptance from being misread as production or cloud readiness.

**Disadvantages:** None material; it is a governance acknowledgement.

**Consequences of postponing:** H-01 is incomplete; readers may treat acceptance as satisfying production or cloud gates.

**Blocks ADR acceptance?** Yes, as a required H-01 acknowledgement.

**Blocks SPR-017 implementation?** No direct effect. It records gates that already block implementation/production/cloud.

**Deferrable until:** Not deferrable beyond H-01.

**Evidence required to close:** The H-01 record contains the acknowledgement.

### 3.9 C-09 - Acceptance does not close SPR-014 or authorize SPR-017

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES PROJECT OWNER ACKNOWLEDGEMENT.

**Original condition (verbatim):** "Acknowledge that acceptance does not close SPR-014, does not authorize SPR-017 and does not declare production or cloud readiness. H-15 and H-16 remain pending."

**Architectural or product question:** What does ADR-024 acceptance actually authorize, and which separate decisions remain?

**Existing repository evidence:** ADR-024 section 34.1 records SPR-014 as `DEFERRED` with outstanding review, staging and closure evidence; section 34.2 records SPR-017 as `NOT IMPLEMENTED / NOT AUTHORIZED` with R-01 to R-15 all `NOT MET`; H-15 and H-16 are `PROPOSED - pending`. No Archive domain exists.

**Current status:** Open as an acknowledgement. The statuses are recorded, but H-01 has not acknowledged them.

**Responsible owner:** Architecture Team + Product Owner.

**Approval sufficiency:** Both are required. Project Owner acknowledgement alone is not sufficient; Architecture Team acknowledgement alone is not sufficient for the product consequence.

**Recommended direction:** Record the acknowledgement in H-01: acceptance satisfies R-01 only; SPR-014 closure/waiver (R-02) and all implementation conditions remain; SPR-017 remains unauthorized.

**Alternative options:**

- **A. Record the acknowledgement (recommended).** Prevents acceptance from being treated as closure or authorization.
- **B. Treat acceptance as closing SPR-014.** Rejected: it contradicts the SPR-014 evidence and the interlock.
- **C. Treat acceptance as authorizing SPR-017.** Rejected: R-02 to R-15 are unmet.
- **D. Authorize a waiver now.** Not permitted by this task and not recommended before a scoped waiver decision.

**Advantages:** A preserves the separation between architecture acceptance, sprint closure, implementation authorization, production and cloud.

**Disadvantages:** None material; it is a governance acknowledgement.

**Consequences of postponing:** H-01 is incomplete; SPR-014 could be treated as closed by implication; SPR-017 could start without authorization.

**Blocks ADR acceptance?** Yes, as a required acknowledgement.

**Blocks SPR-017 implementation?** No direct effect; it records the existing implementation gate.

**Deferrable until:** Not deferrable beyond H-01.

**Evidence required to close:** The H-01 record contains the acknowledgement, signed by the Architecture Team and Product Owner.

### 3.10 C-10 - Record the H-01 decision

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL.

**Original condition (verbatim):** "Record the H-01 decision date, authorized decision-makers, scope, conditions and approval evidence in section 8. The template must remain unsigned until that record exists."

**Architectural or product question:** What must the H-01 record contain before ADR-024 can be treated as accepted, rejected or returned for revision?

**Existing repository evidence:** The formal review section 8 contains an unsigned H-01 template. ADR-024 section 37.2 records architecture acceptance as `PENDING`. No decision date, signatory or evidence reference exists.

**Current status:** Open. H-01 is unsigned.

**Responsible owner:** Architecture Team.

**Approval sufficiency:** The Architecture Team is the H-01 decision-maker. The Product Owner signs only the PA provenance confirmation; the Product Owner does not decide the architecture acceptance.

**Recommended direction:** Record H-01 only after C-01 to C-09 are recorded. The record must contain the decision date, named authorized decision-makers, scope, conditions/exceptions, rationale, evidence reference, and the three acknowledgements (SPR-014 interlock, SPR-017 authorization, production/cloud gates). If C-01 to C-06 cannot be recorded, record `REQUEST REVISION` instead of acceptance.

**Alternative options:**

- **A. Sign the template without the conditions.** Rejected: it is inconsistent with the formal review and would be a silent acceptance.
- **B. Record a conditional acceptance with the conditions listed (recommended if C-01 to C-09 are complete).** This is the formal review's intended path.
- **C. Record `REQUEST REVISION` with the targeted edits.** Correct if the conditions cannot be met.
- **D. Defer H-01.** Acceptable procedurally, but it leaves ADR-024 `Proposed` and blocks SPR-017.

**Advantages:** A complete H-01 record makes acceptance auditable and prevents implied approvals.

**Disadvantages:** It requires a real decision meeting and named signatories; it cannot be produced by an agent.

**Consequences of postponing:** ADR-024 remains `Proposed`; SPR-017 remains unauthorized; the formal review recommendation remains provisional.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, directly through R-01.

**Deferrable until:** Not deferrable. This is the acceptance act.

**Evidence required to close:** A signed H-01 record with the fields in the formal review section 8 and the C-01 to C-09 evidence.

---

## 4. Detailed Resolutions

### 4.1 AC-06 - Tenant and source deletion semantics

#### 4.1.1 The question

AC-06 requires ADR-024 to define tenant-deletion and source-deletion behaviour for archive metadata and objects and not to rely on a silent cascade. The ADR currently prohibits a blind cascade but does not record the chosen disposition or the post-entry source-deletion behaviour.

#### 4.1.2 Proposed event-by-event semantics

| Event | Archive metadata | Archived object | Source record | Required authority | Audit |
|---|---|---|---|---|---|
| School suspended | Retained; archive read-only; no new entries | Retained | Unchanged | Product + Operations | `archive.admin_action` |
| School offboarded / closed | Retained by default; explicit decision required to export or dispose | Retained by default; dispose only under DR-11 | Source domain owns its own offboarding | Product + Legal + Operations | `archive.admin_action`, `archive.deletion_requested` if dispose |
| Subscription expires | Retained; quota report may show no plan | Retained | Unchanged | Product | `archive.quota_evaluated` |
| Source document deleted after archive entry | Retained; divergence recorded | Retained | Deleted by source domain | Legal/Compliance + Architecture | `archive.source_missing` or equivalent |
| Source document version deleted | Retained; version pointer remains as provenance | Retained | Deleted by source domain | Legal/Compliance + Architecture | `archive.source_missing` or equivalent |
| Legal hold placed | `legal_hold = true`; reason, actor, timestamp | Retained; no disposition | Unchanged | Legal/Compliance | `archive.hold_placed` (compliance) |
| Retention due | Evaluated; `preserve` or `due` outcome | Retained | Unchanged | Legal/Compliance + Product | `archive.retention_evaluated` |
| Destruction approved | `status = disposed` after certificate | Physically deleted only under DR-11 | Never deleted by Archive | Product + Security + Operations + Legal | `archive.destroyed` (compliance) |

#### 4.1.3 What must be defined before ADR acceptance

1. The no-cascade rule for `archive_entries` and archived objects.
2. The default offboarding disposition: **suspend and retain**.
3. The rule that source-record deletion does not delete the archive entry or object.
4. The legal-hold precedence and the authority to place/release a hold.
5. The statement that permanent destruction is disabled in the first release and requires DR-11.
6. The ownership split: Archive owns archive metadata/objects; source domains own source records; backup is not archive.

#### 4.1.4 What can be implemented later

1. The offboarding workflow UI and approvals.
2. Export package generation and delivery (DR-24).
3. Physical destruction execution and deletion certificates (DR-11).
4. Retention periods and jurisdiction values (DR-10).
5. Legal-hold procedure and authority matrix (H-04).
6. Notifications and operational runbooks.

#### 4.1.5 Options and trade-offs

| Option | Advantages | Disadvantages | Verdict |
|---|---|---|---|
| Cascade delete with school | Simple; matches `generated_files` behaviour | Destroys official records; violates preservation; destroys audit evidence | Rejected |
| Retain everything indefinitely | Maximum preservation; simplest rule | Ignores erasure, cost and legal obligations; no offboarding path | Rejected |
| Suspend and retain by default; explicit export/dispose | Preserves records; gives Legal a controlled path; consistent with no-cascade | Retains data after offboarding; requires legal decision and operations process | **Recommended** |
| Export-first, then dispose | Strong evidence for the school; controlled | Depends on DR-24; heavier; export may fail or be incomplete | Acceptable variant |
| Per-tenant keys and crypto-shredding | Strong deletion semantics; cloud-friendly | Key management complexity; not first-release | Future/cloud |

#### 4.1.6 Explicit non-assumption

Deleting a school must **not** automatically delete every archived document. The archive is a preservation surface; offboarding is an explicit, authorized decision. A school deletion in the source tenancy may cascade to `generated_files` and `document_audits`; that is precisely why archive metadata and objects must be decoupled from those foreign keys.

#### 4.1.7 Owner and classification

**Owner:** Architecture Team + Legal/Compliance + Product Owner; Operations Lead for execution.

**Classification:** REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL. Product Owner approval is mandatory for the disposition policy. Operations approval is mandatory for the execution path.

### 4.2 AC-16 / DR-16 - Encryption posture

#### 4.2.1 Current state, stated precisely

- No application-level encryption of documents or archive objects exists.
- `config/database.php:113` has the database `encrypt` option commented out.
- `app/Models/SystemSetting.php:62-75` uses `Crypt` for selected setting values. This is application-level encryption of **settings**, not of files or archive objects. The formal review's statement that no `Crypt` usage exists is therefore too broad; the conclusion that archive files are not encrypted is unchanged.
- Host/volume encryption is **unverified**. No evidence was found in the repository.
- `BackupService` writes a schema-only placeholder and does not encrypt backups (`app/Domains/System/Services/BackupService.php:46-97`).
- The private local disk is `storage_path('app/private')`; the public disk exists but archive must not use it.

#### 4.2.2 Distinguish the two layers

| Layer | What it protects | What it does not protect | Key management | Typical evidence |
|---|---|---|---|---|
| Storage-medium encryption (OS/volume) | Data at rest if the physical disk or volume is stolen or improperly decommissioned | Application compromise, privileged host access, misconfigured exports, backup leakage | Host/volume key, often managed by the OS or cloud provider | Operations attestation, configuration export, platform certificate |
| Application-level file encryption | File bytes even if the disk, backup or object store is exposed | Compromised application memory or keys, metadata leakage, authorization failures | Application master key + per-object data keys; KMS/HSM or env custody | Code path, key custody procedure, rotation record, encryption tests |
| Accepted risk with controls | Nothing at rest beyond the controls listed | The same risks as above, explicitly accepted | None beyond existing controls | Signed risk acceptance, compensating controls |

#### 4.2.3 Options

| Option | Advantages | Disadvantages | Operational complexity | Recovery implications |
|---|---|---|---|---|
| A. Application-level envelope encryption | Strongest protection; per-tenant keys possible; supports crypto-shredding | Key custody, rotation, performance, streaming and backup complexity; key loss can make data unrecoverable | High | Requires key escrow/recovery; restore needs the right key version |
| B. Evidenced host/volume encryption | Transparent; no code change; protects physical media; simpler recovery | Does not protect against application or privileged host compromise; evidence must be produced | Low-medium | Recovery depends on host/volume keys; backups need separate encryption |
| C. Accepted risk with documented controls | Fastest; no new infrastructure | Weakest at-rest protection; requires written risk acceptance | Low | No new key recovery burden |
| D. Hybrid: B now, A before production if B is unavailable or risk rejected | Staged; defensible | Requires a decision point and may delay production | Medium | Two evidence paths |

#### 4.2.4 Recommended first-release posture

**Recommendation (for Security/Architecture decision, not an approval):**

1. **First release local-first production:** require **evidenced host/volume encryption** as a production gate, combined with the private disk, tenant isolation, access control, download authorization, audit and backup controls. Do not claim application-level encryption.
2. **Application-level envelope encryption:** defer to DR-17 unless Security determines that host/volume encryption cannot be evidenced or that the residual risk is unacceptable. If so, application-level encryption becomes mandatory before production.
3. **Residual-risk acceptance:** if the first release proceeds without application-level encryption, the Product Owner and Security Lead must sign a written residual-risk acceptance that names the control, the risk, the compensating controls and the review date.
4. **Cloud:** key management, rotation and tenant key separation are cloud-activation gates (DR-20/DR-22). No cloud KMS or provider is selected here.
5. **No claim until evidenced:** the ADR and all downstream documents must say "not implemented" or "unverified" until the chosen control is evidenced.

#### 4.2.5 Key management, rotation, backup and recovery

If application-level encryption is chosen, the DR-17 design must cover:

- master key custody outside the repository (KMS, HSM or a documented secret manager);
- per-object data keys and envelope wrapping;
- rotation without re-encrypting every object at once, or a documented re-encryption plan;
- key versioning and the ability to decrypt old objects after rotation;
- backup encryption keys separate from the primary keys;
- key loss recovery and escrow, with separation of duties;
- audit of key use, rotation and access;
- disaster recovery that includes the keys, not only the ciphertext.

If host/volume encryption is chosen, Operations must evidence:

- the encryption mechanism and scope (which volumes, which hosts, which backups);
- key custody and rotation;
- that archive objects and backups are on encrypted volumes;
- that decommissioned media is handled safely;
- that the evidence is current and reproducible.

#### 4.2.6 Owner and classification

**Owner:** Security Lead + Architecture Team; Operations Lead for host/volume evidence; Product Owner for residual-risk acceptance.

**Classification:** REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL. Product Owner risk acceptance is mandatory if residual risk is accepted.

**Blocks ADR acceptance?** Yes, the posture must be recorded.

**Blocks SPR-017 implementation?** Yes, because the posture determines whether the storage port needs an encryption seam.

**Deferrable until:** Implementation/evidence to production (DR-17); cloud key management to cloud activation (DR-20/DR-22).

### 4.3 AC-22 / DR-27 - PA-1 to PA-5 provenance

This is addressed in full in section 5. The decision summary is:

- The 2026-10-10 ADR v1.0 record approves five narrow product directions, not the board's full discovery questions.
- The board report dated the same day records the corresponding items as unresolved or pending. The conflict is real and cannot be resolved by an agent.
- The existing record is sufficient as **historical evidence**, not sufficient to close AC-22 without a Product Owner confirmation.
- Recommended: Product Owner confirms the five items with exact scope; Architecture adds the Product Owner as DR-27 co-owner and confirms the single-authority declaration.
- If the Product Owner cannot confirm, PA-1 to PA-5 must be marked "recorded, unverified" and ADR-024 cannot be accepted as written.

### 4.4 DR-13 - Audit retention

#### 4.4.1 The two mechanisms

| Mechanism | What it does | Scope | Evidence |
|---|---|---|---|
| Documented retention claim | States audit logs are immutable with a minimum seven-year retention | `docs/03_Database/Audit.md:74-79` | Documentation only; not enforced |
| Scheduled cleanup | Deletes `AuditLog`, `ActivityLog` and `SystemHealth` older than 90 days by default | `app/Console/Commands/System/SystemCleanupCommand.php:14,23-29`; `bootstrap/app.php:47` | Implemented and scheduled daily |

#### 4.4.2 Do they concern the same records?

**Yes, for `audit_logs` rows.** The cleanup deletes `AuditLog` rows from `audit_logs`, which is the table the audit document describes. For those rows, the seven-year claim and the 90-day deletion are a direct contradiction.

**Not necessarily for all audit evidence.** The repository has several separate mechanisms:

- `audit_logs`: generic platform audit; deleted at 90 days.
- `document_audits`: SPR-012 append-only journal; no cleanup reference found, but it cascades on school/document delete.
- `activity_logs`: activity feed; deleted at 90 days.
- `system_health`: health snapshots; deleted at 90 days.
- `archive_events`: not implemented.

The repository does not encode a retention class, so the code cannot distinguish operational, security, records and compliance rows. The contradiction is therefore both a documentation problem and a data-model problem.

#### 4.4.3 Proposed retention classes

| Class | Examples | Retention owner | Store | Cleanup rule |
|---|---|---|---|---|
| `operational` | health checks, routine activity feed | Operations Lead | Existing logs / `system_health` | Short configurable retention; 90 days may continue for this class |
| `security` | login, access denial, permission change, super-admin break-glass | Security Lead | `audit_logs` or a dedicated security stream | Configurable; never deleted by generic cleanup unless the class is explicitly authorized |
| `records` | archive entry, retention evaluation, hold, disposition, restoration | Legal/Compliance + Product Owner | Dedicated append-only `archive_events` | No generic cleanup; retention per approved schedule |
| `compliance` | destruction certificate, legal-hold placement/release, approval record | Legal/Compliance | Dedicated append-only `archive_events` | No generic cleanup; retention per approved schedule |

The durations are **not set here**. Security and Legal/Compliance must set them. No legal period is invented.

#### 4.4.4 Resolution steps

1. Adopt the four classes and their owners.
2. Choose the store: dedicated `archive_events` (recommended) versus `audit_logs` with a `retention_class` exemption versus `document_audits` (not recommended because it cascades).
3. Update the cleanup job later to filter by class and never delete `records`/`compliance` rows. Do not modify it in this task.
4. Consolidate the duplicate `system:cleanup` command classes (`App\Console\Commands\SystemCleanupCommand` and `App\Console\Commands\System\SystemCleanupCommand`) so registration order cannot decide what is deleted.
5. Decide the migration/backfill treatment of existing `audit_logs` rows before changing cleanup behaviour.
6. Correct `docs/03_Database/Audit.md`: either qualify the seven-year claim by class and record the legal basis, or remove the unqualified claim.
7. Record the decision in DR-13 and reference it in H-01.

#### 4.4.5 What not to do

- Do not assert that seven years is legally required without a Legal/Compliance record.
- Do not assert that 90 days is acceptable for records or compliance evidence.
- Do not delete existing `audit_logs` rows while changing the policy without an approved migration/backfill decision.
- Do not use `document_audits` as retention evidence without an ownership change, because it cascades with source deletion.

#### 4.4.6 Owner and classification

**Owner:** Security Lead + Legal/Compliance + Architecture Team; Operations Lead for implementation.

**Classification:** REQUIRES SECURITY APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL.

**Blocks ADR acceptance?** Yes.

**Blocks SPR-017 implementation?** Yes, for the audit sink and event catalogue (R-10).

**Deferrable until:** Class/ownership resolution is not deferrable. Implementation of class-specific retention can be a production prerequisite.

---

## 5. PA-1 to PA-5 Provenance Assessment

### 5.1 The records in conflict

| Record | Date | What it says | Nature of evidence |
|---|---|---|---|
| ADR-024 v1.0 approval block | 2026-10-10 | D1-D5 approved by the product owner | Documentary assertion inside the ADR; preserved in v1.1 section 37.1 |
| `SESSION_HANDOFF.md` 2026-10-10 section | 2026-10-10 | Repeats the five approved product decisions | Derivative handoff record, same date |
| SPR-017 board report | 2026-10-10 | D1 `Unresolved`; D2 `Pending`; D5 `Partly pending`; D6 `Pending`; D7 `Pending` | Contemporaneous discovery/decision-preparation report |
| No independent artifact | - | No signature, meeting minute or separate approval record | Absence of evidence |

### 5.2 Mapping the two numbering schemes

The ADR v1.0 D1-D5 numbering does **not** match the board's D1-D5 numbering. This is the source of much of the confusion.

| PA item (ADR v1.1) | ADR v1.0 item | Board item | Board status on 2026-10-10 | What the PA item actually approves |
|---|---|---|---|---|
| PA-1 | D1 | Board D1 | `Unresolved` | SPR-017 is Phase 9 Enterprise Services, prioritizable for planning now |
| PA-2 | D2 | Board D2 | `Pending` | Local-first storage; cloud deferred and gated; no provider selected |
| PA-3 | D3 | Board D5 | `Partly pending` | Configurable retention mechanism; values owned by Product/Legal; destruction off by default |
| PA-4 | D4 | Board D6 | `Pending` | `plans.storage_limit` remains the single quota authority; no second authority |
| PA-5 | D5 | Board D7 | `Pending` | Packages in architectural scope but not first-release mandatory |

### 5.3 What the 2026-10-10 record actually approves

If the record is valid, it approves only the following narrow directions:

1. **PA-1:** Phase 9 placement and permission to plan now. It does not authorize implementation and does not change the roadmap phase order.
2. **PA-2:** local-first storage and cloud deferral. It does not select a provider, region, credential, bucket, SDK, SLO or residency position.
3. **PA-3:** a configurable retention mechanism, with values owned by Product/Legal and destruction disabled by default. It does not supply any retention period, jurisdiction or legal basis.
4. **PA-4:** `plans.storage_limit` as the single plan authority. It does not assert that the string column is an implemented quota, metering system or enforcement mechanism.
5. **PA-5:** packages in architectural scope, not mandatory for the first release. It does not approve a format, contents, size, expiry, recipient or delivery mechanism.

The record does **not** approve: cloud provider/region, cloud key management, retention periods, legal basis for destruction, legal-hold policy, quota metering/enforcement, paid extensions, package specifics, hosting location, DPA, sub-processors or production/cloud readiness.

### 5.4 Evidence assessment

| Test | Finding |
|---|---|
| Is there an explicit approval statement? | Yes, in ADR-024 v1.0 and the 2026-10-10 handoff. |
| Is there an independent artifact (signature, minute, ticket)? | No. The approval statement is inside the ADR; the handoff repeats it. |
| Is there corroboration from another source? | Partial. The handoff repeats the same assertion; the board report contradicts the status of the corresponding items. |
| Is the contradiction explained by chronology? | Possible but unproven. The board report may predate a same-day decision; no timestamp or decision record establishes the order. |
| Is the scope of the approval clear? | The ADR states a narrow scope; the board report asks broader questions. The PA items are narrower than the board's open questions, which reduces (but does not eliminate) the conflict. |
| Is the direct conflict limited? | The clearest direct conflict is PA-1 vs board D1 (`Unresolved` vs Phase 9 decided). PA-2/PA-3/PA-4/PA-5 are narrower than the board's provider/minima/metering/format questions and can be read as compatible with "pending" on the broader question. |
| Is the record sufficient to close AC-22? | **No.** It is sufficient to preserve as historical evidence, not to declare provenance confirmed. |

**Assessment:** PA-1 to PA-5 are **partially corroborated**. The approval is recorded, but it is not independently evidenced, and the contemporaneous board report conflicts with the status of the corresponding items. The correct conclusion is "recorded, provenance unconfirmed", not "approved" and not "fabricated".

### 5.5 Which parts of DR-27 remain unresolved

1. Whether the five PA decisions were actually made on 2026-10-10.
2. Whether the board report predates the decisions or records a different question.
3. Whether the exact scope of each PA item is the narrow scope in section 5.3.
4. Whether the Product Owner is a co-owner of DR-27.
5. Whether the revised ADR is the single authoritative decision log.
6. Whether the `SESSION_HANDOFF.md` statement that open decisions do not block ADR acceptance is superseded.
7. Whether the board's D1-D10 mapping in ADR section 36.2 is accepted.
8. Whether any historical document must be corrected or only annotated as historical.

### 5.6 Is the existing approval record sufficient for the intended scope?

**For preserving history: yes.** The ADR v1.0 record and the handoff are legitimate historical evidence of what was recorded on 2026-10-10.

**For closing AC-22: no.** The record is an assertion inside the document that is being accepted. It is not an independent approval artifact, and the board report contradicts it. A reviewer cannot verify the provenance from the repository alone.

**For expanding scope: no.** The record does not support provider, region, retention values, metering, pricing, package specifics or any production/cloud decision.

### 5.7 Recommended resolution

1. Product Owner signs a provenance confirmation naming PA-1 to PA-5 individually and stating the exact scope in section 5.3.
2. The confirmation states whether the board report predates the decision or records a different question. If the order is unknown, say so; do not invent a timestamp.
3. Architecture Team records the Product Owner as DR-27 co-owner and confirms the single-authority declaration.
4. H-01 references the confirmation and the DR-27 record.
5. If the Product Owner cannot confirm, mark PA-1 to PA-5 "recorded, unverified", record `REQUEST REVISION` or an explicit exception, and do not treat AC-22 as satisfied.

### 5.8 What must not be done

- Do not invent a signature, meeting minute or approval date.
- Do not expand PA-1 to PA-5 beyond their narrow scope.
- Do not treat the board report as if it did not exist.
- Do not rewrite the board report to match the ADR.
- Do not treat the handoff's repetition as independent corroboration; it is derivative of the same record.

---

## 6. Consolidated Decision Register

### 6.1 The ten formal-review conditions

| Decision | Proposed direction | Required approver | Blocks ADR acceptance? | Blocks SPR-017? | Deferrable until |
|---|---|---|---|---|---|
| **C-01** Per-decision approval of 18 DRs | Adopt a per-DR decision schedule; no blanket acceptance | Architecture Team + co-owners (Security, Legal, Product, Operations) | Yes | Yes (R-01) | No |
| **C-02** Quota unit base | Binary 1024 canonical; decimal display optional | Product Owner + Architecture Team | Yes | Yes (R-11) | No |
| **C-03** Encryption posture | Evidenced host/volume encryption + controls; app-level deferred unless risk rejected; written residual-risk acceptance | Security Lead + Architecture Team; Product Owner for risk | Yes | Yes (storage-port design) | Implementation/evidence: production; cloud KMS: cloud |
| **C-04** Offboarding/source deletion | Suspend-and-retain default; no cascade; source deletion preserves archive; legal hold wins; destruction off | Architecture + Legal/Compliance + Product Owner; Operations for execution | Yes | Yes (schema/behaviour) | Legal values/destruction: production; cloud semantics: cloud |
| **C-05** PA-1 to PA-5 provenance | Product Owner confirms exact scope; Architecture adds PO as DR-27 co-owner | Product Owner + Architecture Team | Yes | Yes (indirect) | No |
| **C-06** Audit retention | Four classes; dedicated `archive_events`; generic cleanup only for authorized classes; correct/qualify 7-year claim | Security + Legal/Compliance + Architecture | Yes | Yes (R-10) | Implementation: production; class resolution: no |
| **C-07** DR-27 single authority | ADR is authoritative; board report historical; mapping accepted; PO co-owner | Architecture Team + Product Owner | Yes | Yes (indirect) | No |
| **C-08** Acknowledge deferrals/gates | Record the gate split in H-01; acceptance satisfies none | Architecture Team | Yes (H-01 acknowledgement) | No direct | No |
| **C-09** Non-closure/non-authorization | Record that acceptance does not close SPR-014 or authorize SPR-017; H-15/H-16 pending | Architecture Team + Product Owner | Yes (H-01 acknowledgement) | No direct | No |
| **C-10** Record H-01 | Sign only after C-01 to C-09; include date, names, scope, conditions, evidence | Architecture Team | Yes | Yes (R-01) | No |

### 6.2 Acceptance-gate DRs (18)

| DR | Decision | Owner | Classification | Blocks acceptance? | Blocks SPR-017? | Deferrable until |
|---|---|---|---|---|---|---|
| DR-01 | Lifecycle read contract | Architecture Team | REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-03) | No |
| DR-02 | Document/file read contracts | Architecture Team | REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-04) | No |
| DR-03 | Ownership and prohibited imports | Architecture Team | REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-12) | No |
| DR-04 | Archive metadata model | Architecture Team | REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-06) | No |
| DR-05 | Storage port and adapter | Architecture Team + Security Lead | REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES SECURITY APPROVAL | Yes | Yes (R-05) | No |
| DR-06 | Quota contract/units/resolution | Architecture Team + Product Owner | READY FOR PROJECT OWNER DECISION + ARCHITECTURE | Yes | Yes (R-11) | No |
| DR-07 | Quota first-release behaviour | Product Owner + Architecture Team | READY FOR PROJECT OWNER DECISION + ARCHITECTURE | Yes | Yes (R-11) | No |
| DR-09 | Retention mechanism | Architecture Team + Legal/Compliance | REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL | Yes | Yes (R-09) | No |
| DR-12 | Audit catalogue and store | Architecture Team + Security Lead | REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES SECURITY APPROVAL | Yes | Yes (R-10) | No |
| DR-13 | Audit retention classes | Security + Legal/Compliance + Architecture | REQUIRES SECURITY APPROVAL + REQUIRES LEGAL/COMPLIANCE APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-10) | No |
| DR-14 | Archive permissions/policies | Security Lead + Architecture Team | REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-07) | No |
| DR-15 | Local storage security | Security Lead + Architecture Team | REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-08) | No |
| DR-16 | Encryption posture | Security Lead + Architecture Team | REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (storage-port design) | Implementation: production; cloud KMS: cloud |
| DR-19 | Backup/archive separation | Architecture Team + Operations Lead | REQUIRES ARCHITECTURE TEAM APPROVAL + REQUIRES OPERATIONS APPROVAL | Yes | Yes (design invariant) | No |
| DR-26 | Super-admin break-glass | Security Lead + Architecture Team | REQUIRES SECURITY APPROVAL + REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (security design) | No |
| DR-27 | Decision log reconciliation | Architecture Team (+ Product Owner co-owner) | REQUIRES ARCHITECTURE TEAM APPROVAL + PROJECT OWNER CONFIRMATION | Yes | Yes (indirect) | No |
| DR-28 | SPR-014 interlock | Product Owner + Architecture Team (+ Operations for staging) | READY FOR PROJECT OWNER DECISION + ARCHITECTURE | Yes | Yes (R-02) | No |
| DR-29 | Testable acceptance criteria/test plan | Architecture Team | REQUIRES ARCHITECTURE TEAM APPROVAL | Yes | Yes (R-13) | No |

### 6.3 Production, cloud and deferred DRs

| DR | Decision | Owner | Gate | Classification | Deferrable until |
|---|---|---|---|---|---|
| DR-08 | Quota metering/enforcement/paid extensions | Product Owner + Architecture (+ Finance if paid) | Production | READY FOR PROJECT OWNER DECISION + ARCHITECTURE | Production |
| DR-10 | Retention values and jurisdiction | Product Owner + Legal/Compliance | Production | REQUIRES LEGAL/COMPLIANCE APPROVAL + PRODUCT OWNER | Production |
| DR-11 | Destruction authorization/safeguards | Product + Security + Operations + Legal | Production | REQUIRES SECURITY + OPERATIONS + LEGAL + PRODUCT APPROVAL | Production |
| DR-17 | Encryption implementation/key management | Security Lead + Operations Lead | Production | REQUIRES SECURITY APPROVAL + REQUIRES OPERATIONS APPROVAL | Production |
| DR-18 | Backup/restore including archive | Operations Lead | Production | REQUIRES OPERATIONS APPROVAL | Production |
| DR-21 | Hosting location/residency record | Product Owner + Legal/Compliance + Operations | Production | REQUIRES LEGAL/COMPLIANCE APPROVAL + OPERATIONS + PRODUCT | Production |
| DR-20 | Cloud activation gate | Product + Architecture + Security + Operations | Cloud | REQUIRES MULTI-DISCIPLINARY APPROVAL | Cloud |
| DR-22 | Cloud residency/DPA/controller-processor | Product + Legal/Compliance + Privacy/DPO + Architecture | Cloud | REQUIRES LEGAL/COMPLIANCE + PRIVACY + PRODUCT APPROVAL | Cloud |
| DR-25 | Cloud SLO/durability/cost baseline | Product + Operations + Architecture | Cloud | REQUIRES OPERATIONS + PRODUCT APPROVAL | Cloud |
| DR-23 | `read_only`/`cold_storage` states | Architecture Team + Product Owner | Deferred | CAN BE DEFERRED | Future lifecycle amendment |
| DR-24 | Package specifics | Product Owner + Architecture Team | Deferred | CAN BE DEFERRED | Future package release |

### 6.4 Human approval references

| H | Decision | Reserved to | Status | Related conditions |
|---|---|---|---|---|
| H-01 | Accept/reject/revise ADR-024 | Architecture Team | `PROPOSED - pending` | C-01 to C-10 |
| H-02 | Retention periods/preservation categories | Product Owner + Legal/Compliance | `PROPOSED - pending` | C-04, C-06, DR-10 |
| H-03 | Legal basis for destruction | Legal/Compliance | `PROPOSED - pending` | C-04, DR-11 |
| H-04 | Legal-hold policy/authority | Legal/Compliance | `PROPOSED - pending` | C-04, DR-09/DR-11 |
| H-05 | Destruction approval matrix/two-person rule | Product + Security + Operations + Legal | `PROPOSED - pending` | C-04, DR-11 |
| H-06 | Encryption posture/residual risk | Security Lead (+ Product Owner) | `PROPOSED - pending` | C-03, DR-16 |
| H-07 | Key management/rotation | Security Lead + Operations Lead | `PROPOSED - pending` | C-03, DR-17 |
| H-08 | Quota semantics/over-quota/paid extensions | Product Owner (+ Finance) | `PROPOSED - pending` | C-02, DR-06/07/08 |
| H-09 | Cloud provider/region/residency | Product + Legal + Privacy/DPO | `PROPOSED - pending` | Cloud, DR-20/22 |
| H-10 | DPA/sub-processors/controller-processor | Legal/Compliance + Privacy/DPO | `PROPOSED - pending` | Cloud, DR-22 |
| H-11 | Production hosting location | Product + Operations + Legal | `PROPOSED - pending` | DR-21 |
| H-12 | Backup scope/encryption/RTO-RPO/restore | Operations Lead (+ Security) | `PROPOSED - pending` | DR-18 |
| H-13 | Super-admin break-glass | Security Lead + Product Owner | `PROPOSED - pending` | DR-26 |
| H-14 | Package export policy/recipients/expiry | Product Owner + Security Lead | `PROPOSED - pending` | DR-24 |
| H-15 | SPR-014 closure or waiver | Product + Architecture (+ Operations) | `PROPOSED - pending` | C-09, DR-28 |
| H-16 | Staging evidence sufficiency | Operations Lead + Product Owner | `PROPOSED - pending` | C-09, SPR-014 |
| H-17 | Production readiness | Product + Architecture + Security + Operations (+ Legal) | `PROPOSED - pending` | Production |
| H-18 | Cloud activation | Product + Architecture + Security + Operations + Legal | `PROPOSED - pending` | Cloud |

---

## 7. Human Approval Matrix

### 7.1 Role matrix

| Role | Decisions they own or must sign | Can decide alone? | Must co-approve with |
|---|---|---|---|
| **Project Owner** | PA provenance confirmation; DR-07; DR-08 (with Finance if paid); DR-10 (with Legal); DR-11 (with Security/Ops/Legal); DR-20/21/22 (with Legal/Privacy/Ops); DR-24; DR-28; H-02/08/14/15/16 | No, except the PA provenance confirmation and product-policy choices within the stated scope | Architecture, Security, Operations, Legal/Compliance, Privacy/DPO, Finance as applicable |
| **Architecture Team** | H-01; DR-01 to DR-05; DR-06 (with Product); DR-09 (with Legal); DR-12; DR-13 (with Security/Legal); DR-14/15/16 (with Security); DR-19; DR-23; DR-26 (with Security); DR-27; DR-29 | No for co-owned decisions; yes for architecture-only decisions | Product, Security, Legal, Operations as applicable |
| **Security Lead** | DR-05, DR-11, DR-12, DR-13, DR-14, DR-15, DR-16, DR-17, DR-20, DR-26; H-06, H-07, H-13 | No; security decisions are co-owned with Architecture and sometimes Product/Operations | Architecture, Product, Operations, Legal |
| **Operations Lead** | DR-11, DR-17, DR-18, DR-20, DR-21, DR-25; H-12, H-16 | No; operations evidence is co-owned with Product/Security/Architecture | Product, Security, Architecture, Legal |
| **Legal/Compliance** | DR-09 (mechanism review), DR-10, DR-11, DR-13, DR-21, DR-22; H-02, H-03, H-04, H-05, H-09, H-10, H-11 | No; legal decisions are reserved and cannot be delegated to Architecture or Product | Product, Security, Operations, Privacy |
| **Privacy/DPO (if appointed)** | DR-10, DR-21, DR-22; H-09, H-10 | No | Legal, Product, Architecture |
| **Finance (if paid)** | DR-08 commercial rules | No | Product, Architecture |
| **SPR-014 owner** | Formal review approval; staging evidence; closure decision; H-15/H-16 inputs | No; closure is a joint Product/Architecture decision | Product, Architecture, Operations |
| **SPR-017 owner** | No approval authority; must not start implementation | Not applicable | Must wait for R-01/R-02 |

### 7.2 Project Owner sufficiency table

| Decision | Is Project Owner approval sufficient? | Mandatory additional approver | Reason |
|---|---|---|---|
| PA-1 to PA-5 provenance confirmation | Yes for the provenance statement, but not for DR-27 as a whole | Architecture Team | The Product Owner owns the product record; Architecture owns the decision log |
| DR-06 unit base | No | Architecture Team | Parser/contract design |
| DR-07 report-only quota | No | Architecture Team | Contract and implementation semantics |
| DR-08 metering/enforcement | No | Architecture Team; Finance if paid | Product policy plus technical/commercial design |
| DR-09 retention mechanism | No | Architecture Team + Legal/Compliance | Mechanism plus legal review |
| DR-10 retention values | No | Legal/Compliance | Legal basis and jurisdiction |
| DR-11 destruction safeguards | No | Security + Operations + Legal | Safeguards and legal basis |
| DR-13 audit retention | No | Security + Legal/Compliance + Architecture | Security, legal and store design |
| DR-16 encryption posture | No | Security + Architecture | Security control design |
| DR-17 key management | No | Security + Operations | Custody and rotation |
| DR-18 backup/restore | No | Operations (+ Security) | Operational evidence |
| DR-20 cloud gate | No | Architecture + Security + Operations + Legal | Multi-disciplinary gate |
| DR-21 hosting/residency | No | Legal + Operations | Legal and operational record |
| DR-22 cloud residency/DPA | No | Legal + Privacy/DPO + Architecture | Legal/privacy decision |
| DR-24 package specifics | Yes for product policy, within the security boundary | Security + Architecture | Product policy plus security boundary |
| DR-26 break-glass | No | Security + Architecture | Security control |
| DR-27 single authority | No | Architecture Team | Decision-log ownership |
| DR-28 SPR-014 interlock | No | Architecture Team (+ Operations for staging) | Governance interlock |
| H-01 | No | Architecture Team | H-01 is reserved to the Architecture Team |

### 7.3 Human approval status

All 18 H items remain `PROPOSED - pending`. No approval record, signature, meeting minute or waiver exists. A row in this matrix is a responsibility, not a signature.

---

## 8. SPR-014 Closure Action Plan

### 8.1 Status

**SPR-014 remains `DEFERRED`.** This package does not close, promote, re-scope or waive it.

Evidence:

- The SPR-014 report is unchanged; SHA-256 `5196197BA325A6CBB25824CA89E8F5906AD6074FCD39DA7133FF0338FF1A6BF8`.
- The report records implementation and targeted test evidence, but no formal review approval, staging deployment record or closure decision.
- The report's status line says "IMPLEMENTED - TARGETED GATES PASS"; its audit section says "Approved for rework, not approved for full implementation". Neither is a closure.
- Targeted tests are not a full-suite result and are not a closure decision.

### 8.2 The four tracks

| Track | Requirement | Evidence required | Owner | Current status | Can it be waived? |
|---|---|---|---|---|---|
| A. Formal review approval | A named approver, approval date and scope | Signed review approval record | Product Owner + Architecture Team | `NOT SATISFIED` | Only by an explicit, scoped waiver |
| B. Staging deployment evidence | Successful staging deployment | Environment, commit SHA, deployment date, executed migration set, smoke-test result, rollback note | Operations Lead | `UNVERIFIED / NOT SATISFIED` | Only by an explicit, scoped waiver |
| C. Waiver (if used) | A valid waiver of a named requirement | Waived requirement, reason, risk, compensating controls, expiry/review date, owner signature | Product Owner + Architecture Team (+ Legal/Ops as applicable) | `NOT AUTHORIZED / NOT RECORDED` | Not applicable; the waiver is the mechanism |
| D. Closure decision | A recorded decision accepting scope, evidence and residual observations | Closure record with date, decision-makers, scope, evidence references and residual observations | Product Owner + Architecture Team | `NOT RECORDED` | No; closure is the decision |

### 8.3 Recommended sequence

1. **Freeze the scope:** confirm the SPR-014 slice and the evidence already available. Do not add new features to close the sprint.
2. **Formal review:** assign a named reviewer and record the approval date, scope and any conditions.
3. **Staging:** deploy the exact commit to staging; record the environment, commit SHA, migration set, smoke-test result and rollback note.
4. **Evidence package:** assemble the review approval, staging record, targeted test evidence, residual observations and known limitations.
5. **Decide closure versus waiver:** prefer closure with evidence. If staging evidence cannot be produced, decide whether a scoped waiver is justified. Do not assume a waiver.
6. **Record the closure decision:** date, decision-makers, scope, evidence, residual observations and the interlock consequence.
7. **Update status:** only after the closure record exists may SPR-014 be marked `CLOSED` (or the agreed status). This package does not do that.
8. **Notify SPR-017:** the interlock gate is satisfied only by the recorded closure decision or a signed waiver.

### 8.4 Waiver rules

A waiver is valid only if it is:

- explicit (names the requirement being waived);
- owner-signed (Product Owner and Architecture Team; Legal/Operations where the requirement is legal or operational);
- scoped (not a generic "proceed" waiver);
- dated;
- risk-bearing (states the risk and compensating controls);
- time-bound (expiry or review date).

A waiver of staging evidence does **not** waive security, retention, legal, production or cloud gates. A waiver of SPR-014 closure does **not** authorize SPR-017 implementation beyond the interlock; R-01 and R-03 to R-15 still apply.

### 8.5 Closure record fields (template, unsigned)

If the owners decide to close SPR-014, the closure record should contain:

- sprint ID and scope;
- decision date;
- decision-makers and roles;
- review approval reference;
- staging evidence reference;
- test evidence reference;
- residual observations and accepted limitations;
- interlock acknowledgment: closure does not accept ADR-024 and does not authorize SPR-017;
- signature and date.

This package does not sign or populate this template.

### 8.6 What this package did not do

- It did not close SPR-014.
- It did not authorize a waiver.
- It did not mark SPR-014 `COMPLETE`.
- It did not modify the SPR-014 report.
- It did not treat ADR-024 acceptance as closure.

---

## 9. SPR-017 Readiness Assessment

### 9.1 Verdict

**SPR-017 is NOT READY, NOT IMPLEMENTED and NOT AUTHORIZED.** The direction is sound; the prerequisites are absent.

### 9.2 Prerequisites for ADR-024 acceptance

| ID | Prerequisite | Owner | Current state |
|---|---|---|---|
| ACC-01 | Closed ownership list and prohibited imports | Architecture Team | Addressed; approval pending |
| ACC-02 | Lifecycle read contract specified | Architecture Team | Specified; not implemented |
| ACC-03 | Document/file read contracts specified | Architecture Team | Specified; not implemented |
| ACC-04 | Metadata model and idempotency | Architecture Team | Specified; not implemented |
| ACC-05 | Provider-neutral storage port | Architecture + Security | Specified; not implemented |
| ACC-06 | Quota contract/units/resolution | Architecture + Product | Specified; unit choice pending |
| ACC-07 | Quota first-release semantics | Product + Architecture | Specified; approval pending |
| ACC-08 | Retention mechanism | Architecture + Legal | Specified; legal review pending |
| ACC-09 | Audit catalogue and store | Architecture + Security | Specified; store choice pending |
| ACC-10 | Audit retention classes/contradiction | Security + Legal + Architecture | Recorded, not resolved |
| ACC-11 | Permission surface/policies | Security + Architecture | Specified; not implemented |
| ACC-12 | Download/access security | Security + Architecture | Specified; not implemented |
| ACC-13 | Truthful encryption posture | Security + Architecture | Posture recorded; choice pending |
| ACC-14 | Break-glass rule | Security + Architecture | Specified; approval pending |
| ACC-15 | Backup/archive separation | Architecture + Operations | Addressed; evidence absent |
| ACC-16 | SPR-014 interlock wording | Product + Architecture + Operations | Addressed; closure/waiver absent |
| ACC-17 | Testable acceptance criteria | Architecture | Addressed |
| ACC-18 | Single authoritative decision log | Architecture | Reconciled; owner confirmation pending |
| ACC-19 | Formal architecture review and approval | Architecture Team | Pending |

### 9.3 Prerequisites for SPR-017 implementation

| ID | Prerequisite | Owner | Current state |
|---|---|---|---|
| R-01 | ADR-024 accepted | Architecture Team | `NOT MET` |
| R-02 | SPR-014 closure or signed interlock waiver | Product + Architecture (+ Ops) | `NOT MET` |
| R-03 | `ArchivedLifecycleReadContract` designed, approved, implemented | Architecture + Engineering | `NOT MET` |
| R-04 | Document/file read contracts implemented | Engineering | `NOT MET` |
| R-05 | `StoragePort` + local adapter + conformance tests | Engineering + Security | `NOT MET` |
| R-06 | Archive schema migration, reversible | Engineering | `NOT MET` |
| R-07 | `archive.*` permissions, roles, policies, seed | Security + Engineering | `NOT MET` |
| R-08 | Authorized download path with canonicalization, 404, rate limit, checksum | Engineering + Security | `NOT MET` |
| R-09 | Retention mechanism + dry-run; destruction off | Engineering + Legal | `NOT MET` |
| R-10 | Archive audit sink + event catalogue | Engineering + Security | `NOT MET` |
| R-11 | Quota consumer in `report_only` mode | Engineering | `NOT MET` |
| R-12 | Architecture tests for forbidden imports/mutations | Engineering | `NOT MET` |
| R-13 | Tenant isolation/integrity/failure/idempotency tests | Engineering + Security | `NOT MET` |
| R-14 | Staging deployment, seed, smoke test, rollback note | Operations | `NOT MET` |
| R-15 | Runbook, permissions docs, incident procedure | Engineering + Operations | `NOT MET` |

### 9.4 Prerequisites for local-first production

| ID | Prerequisite | Owner | Current state |
|---|---|---|---|
| PRD-01 | Retention values and jurisdiction approved | Product + Legal | `NOT APPROVED` |
| PRD-02 | Destruction safeguards approved/implemented or explicitly off | Product + Security + Ops + Legal | `NOT IMPLEMENTED` |
| PRD-03 | Quota metering/enforcement decision implemented | Product + Engineering | `NOT IMPLEMENTED` |
| PRD-04 | At-rest encryption implemented/evidenced or risk accepted | Security + Operations | `NOT IMPLEMENTED / UNVERIFIED` |
| PRD-05 | Backup includes archive; encryption; restore drill | Operations | `NOT IMPLEMENTED / UNVERIFIED` |
| PRD-06 | Hosting location/residency record | Product + Legal + Operations | `UNVERIFIED` |
| PRD-07 | Audit retention classes implemented; evidence preserved | Security + Legal | `CONTRADICTION OPEN` |
| PRD-08 | Staging evidence and smoke tests | Operations | `UNVERIFIED` |
| PRD-09 | Capacity monitoring/alerting | Operations | `NOT IMPLEMENTED` |
| PRD-10 | Incident response, rollback, runbooks | Operations | `NOT IMPLEMENTED` |
| PRD-11 | Security review sign-off | Security Lead | `PENDING` |
| PRD-12 | Legal/compliance sign-off on retention/residency | Legal/Compliance | `PENDING` |

### 9.5 Prerequisites for cloud activation

| ID | Prerequisite | Owner | Current state |
|---|---|---|---|
| CLD-01 | Provider and region approved | Product + Architecture | `NOT DECIDED` |
| CLD-02 | Tenant isolation for shared objects approved | Architecture + Security | `NOT DECIDED` |
| CLD-03 | Key management/rotation approved | Security + Architecture | `NOT DECIDED` |
| CLD-04 | SLO/availability/durability/cost baseline | Operations + Product | `NOT DECIDED` |
| CLD-05 | Tenant-aware cloud disk configuration | Operations | `NOT IMPLEMENTED` |
| CLD-06 | Residency, DPA, sub-processors, controller/processor roles | Product + Legal + Privacy | `NOT DECIDED` |
| CLD-07 | Adapter conformance tests | Engineering | `NOT IMPLEMENTED` |
| CLD-08 | Migration/rollback preserving checksums/tenancy | Engineering + Operations | `NOT IMPLEMENTED` |
| CLD-09 | Cloud incident/DR plan and runbooks | Operations | `NOT IMPLEMENTED` |
| CLD-10 | Credential hygiene/secret-management evidence | Security + Operations | `UNVERIFIED` |

### 9.6 What can safely begin before production and cloud decisions

Only non-implementation work is safe:

1. Decision records and meeting preparation.
2. Evidence gathering: hosting location, host/volume encryption attestation, backup/restore drill plan, audit inventory, legal retention schedule inputs.
3. Threat model and data-flow review.
4. Contract shape review and projection field review (as design documents, not code).
5. Test plan and architecture-test specification.
6. Operational runbook outline and staging plan.
7. Repository read-only analysis and risk review.

### 9.7 What cannot begin

The following are **not authorized** until R-01 and R-02 are satisfied:

- creating `app/Domains/Archive`;
- creating contracts, interfaces, DTOs or projections;
- creating migrations, models, routes, controllers, policies or permissions;
- creating configuration or seed data;
- modifying audit tables or the cleanup job;
- writing application tests that assume the Archive domain exists;
- staging an Archive deployment;
- claiming production or cloud readiness.

This package does not authorize or implement any of these.

---

## 10. Recommended Path to ADR-024 Acceptance

### 10.1 Phase plan

| Phase | Action | Owner | Output | Gate |
|---|---|---|---|---|
| 0 | Project Owner responds to PO-1 to PO-10 | Project Owner | Signed responses | PA provenance and product choices |
| 1 | Architecture Team reviews the 18 DRs and records per-DR decisions | Architecture Team | DR decision schedule | C-01 |
| 2 | Security records DR-05/13/14/15/16/26 and the encryption posture | Security Lead | Security decision record | C-03, C-06 |
| 3 | Legal/Compliance records DR-09/10/13 and the offboarding/retention position | Legal/Compliance | Legal decision record | C-04, C-06 |
| 4 | Operations records DR-19 and the staging/host evidence plan | Operations Lead | Operations record | C-08, DR-19 |
| 5 | Architecture records DR-27 single authority and adds Product Owner co-owner | Architecture Team | DR-27 record | C-05, C-07 |
| 6 | Narrow controlled ADR edit if needed (section 8.1/15, 13.2/13.3, 14.5, 24.1, 36.3) | Architecture Team | Revised ADR text | Must be separately authorized; not this task |
| 7 | H-01 decision with C-01 to C-10 evidence | Architecture Team | Signed H-01 | ADR accepted or revision requested |
| 8 | SPR-014 closure or waiver | Product + Architecture (+ Ops) | Closure/waiver record | R-02 |
| 9 | SPR-017 implementation authorization | Architecture + Security + Operations | R-01 to R-15 satisfied/waived | Implementation may start |
| 10 | Local-first production gates | Product + Security + Ops + Legal | PRD-01 to PRD-12 | Production |
| 11 | Cloud activation gates | Product + Architecture + Security + Ops + Legal | CLD-01 to CLD-10 | Cloud |

### 10.2 Decision sequence within acceptance

The shortest defensible sequence is:

1. PA provenance confirmation (PO-1).
2. DR-27 single authority and co-ownership (C-05, C-07).
3. DR-06 unit base (C-02).
4. DR-16 posture (C-03).
5. DR-09/DR-10 offboarding and source-deletion semantics (C-04).
6. DR-13 audit retention (C-06).
7. Remaining acceptance-gate DRs (C-01).
8. Acknowledgements (C-08, C-09).
9. H-01 (C-10).

### 10.3 Contingency

If C-01 to C-06 cannot be recorded, the Architecture Team should record `REQUEST REVISION` rather than a conditional acceptance. The targeted revision items are:

- record the DR-06 unit choice;
- record the DR-16 posture;
- record the DR-09/DR-10 offboarding and source-deletion semantics;
- resolve DR-13;
- confirm PA provenance and correct DR-27 ownership.

These are targeted edits, not a re-architecture.

### 10.4 Gate separation

The following are separate decisions and must not be merged:

- ADR acceptance (H-01);
- SPR-014 closure or waiver (H-15/H-16);
- SPR-017 implementation authorization (R-01 to R-15);
- local-first production readiness (PRD-01 to PRD-12);
- cloud activation (CLD-01 to CLD-10, H-18).

Acceptance of ADR-024 satisfies R-01 only. It does not satisfy R-02 or any implementation, production or cloud condition.

---

## 11. Unresolved Issues and Limitations

| ID | Issue | Why it remains unresolved | Impact | Owner | Required action |
|---|---|---|---|---|---|
| U-01 | PA-1 to PA-5 provenance | No independent approval artifact; board report conflicts | AC-22 cannot close; H-01 rests on a contested record | Product Owner + Architecture | PO-1 confirmation |
| U-02 | DR-13 audit retention | No class model or legal basis; cleanup deletes at 90 days | Evidence loss; PRD-07 blocked | Security + Legal + Architecture | C-06 decision |
| U-03 | DR-16 encryption posture | Host encryption unverified; no app-level file encryption | AC-16 partial; at-rest risk unrecorded | Security + Architecture + Product | C-03 decision |
| U-04 | AC-06 offboarding/source deletion | Legal values and disposition not recorded | Schema risk; erasure/preservation ambiguity | Architecture + Legal + Product | C-04 decision |
| U-05 | DR-27 ownership | Product Owner not a co-owner | Decision-log authority incomplete | Architecture + Product | C-05/C-07 |
| U-06 | O-2 SPR-014 hardening | `ArchiveRecordLifecycleAction` unscoped/null-actor policy skip | Source-domain risk if called | SPR-014 owner | Separate hardening task |
| U-07 | Backup capability overstated | `BackupService` writes placeholders; no file copy/encryption/restore | False recovery confidence | Operations | DR-18/H-12 evidence or doc correction |
| U-08 | Duplicate cleanup commands | Two `system:cleanup` classes; registration order unverified | Wrong cleanup may run | Engineering | Consolidate in a separate task |
| U-09 | S3 wording tension | AC-07 says no provider type; ADR names S3 as gated adapter | Reader confusion | Architecture | Clarify wording in a controlled edit |
| U-10 | Role holders not confirmed | Security/Legal/Operations/Privacy roles may be vacant | Decisions cannot be validly recorded | Project Owner + Architecture | PO-10 confirmation |
| U-11 | No staging evidence | No environment/commit/smoke/rollback record | SPR-014 closure unsupported | Operations | H-16 evidence |
| U-12 | No full-suite run in the formal review | Targeted tests only | Residual regression risk | Engineering | Run full suite if required |
| U-13 | SystemSetting `Crypt` nuance | Formal review's "no Crypt usage" is too broad | Evidence precision | Architecture | Correct the statement; no functional claim |
| U-14 | Cloud provider/region/DPA absent | No business or legal decision | Cloud cannot activate | Product + Legal + Privacy | DR-20/22/25 |
| U-15 | Legal retention periods absent | No legal source in the repository | Retention cannot be enforced or claimed | Legal/Compliance + Product | DR-10/H-02 |
| U-16 | No H-01 decision date/signatories | Acceptance not recorded | ADR remains `Proposed` | Architecture Team | C-10 |
| U-17 | `SESSION_HANDOFF` gate statement conflict | Says open decisions do not block acceptance | Governance ambiguity | Architecture + Product | C-07/C-08 |
| U-18 | SPR-014 report status inconsistency | "IMPLEMENTED - TARGETED GATES PASS" vs governance `DEFERRED` | Misreading as closure | Product + Architecture | Explicit status statement in closure record |

### 11.1 Limitations of this package

- It is a decision-preparation document, not an approval.
- It does not have access to any approval record outside the repository.
- It does not interpret legal requirements; it only identifies where legal decisions are required.
- It does not verify host/volume encryption; no evidence exists in the repository.
- It does not re-run the full test suite.
- It does not resolve the PA provenance; it proposes how the Product Owner can.
- It does not modify ADR-024 or any other governance document.
- It does not create contracts, interfaces, migrations, routes, permissions, configuration or code.

---

## 12. Exact Git Accounting

### 12.1 Baseline

| Item | Value |
|---|---|
| Branch | `main` |
| HEAD | `d42dbef3e32ad6e7a7a4bc70c68f805be60da480` |
| HEAD subject | `d42dbef 2026-11-03 12:28:46 +0000 correction du login` |
| Preflight status | 5 modified tracked files; 7 untracked files |
| Staged changes | None |
| Commits made by this task | None |
| Destructive Git operations | None |

### 12.2 Pre-existing modified tracked files (preserved, not changed by this task)

| File | Note |
|---|---|
| `docs/ADR/ADR-024.md` | Modified before this task; status remains `Proposed`; SHA-256 `F16A19E8262E3F33BD9C3919B8819A77E2B67B48E8B5D669B82B925E1FD5045A`; 155,485 bytes; 1,704 lines. |
| `docs/docs/00_Project_Management/CURRENT_POSITION.md` | Pre-existing modification. |
| `docs/docs/00_Project_Management/MASTER_ROADMAP.md` | Pre-existing modification. |
| `docs/docs/00_Project_Management/PROJECT_STATE.md` | Pre-existing modification. |
| `docs/docs/SESSION_HANDOFF.md` | Pre-existing modification. |

### 12.3 Pre-existing untracked files (preserved, not changed by this task)

| File | Note |
|---|---|
| `2_NexusSchool.zip` | Pre-existing. |
| `NexusSchool.zip` | Pre-existing. |
| `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md` | TASK 02. |
| `docs/docs/00_Project_Management/ADR-024_REVISION_BLUEPRINT_2026-11-06.md` | TASK 03. |
| `docs/docs/00_Project_Management/ADR-024_REVISION_REPORT_2026-11-06.md` | TASK 04. |
| `docs/docs/00_Project_Management/ADR-024_FORMAL_REVIEW_2026-11-06.md` | TASK 05. |
| `docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md` | Prior governance record. |

### 12.4 File created by this task

| File | Purpose | Status |
|---|---|---|
| `docs/docs/00_Project_Management/ADR-024_DECISION_RESOLUTION_2026-11-06.md` | This decision-resolution package. | New, untracked. |

The SHA-256 of this new file is reported in the delivery response rather than embedded here, because embedding a hash of a file inside itself is self-referential.

### 12.5 Files explicitly not changed by this task

- `docs/ADR/ADR-024.md` (status remains `Proposed`; no text edited).
- `docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md` (hash `5196197BA325A6CBB25824CA89E8F5906AD6074FCD39DA7133FF0338FF1A6BF8`; no closure record added).
- `docs/docs/00_Project_Management/SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md`.
- `docs/docs/00_Project_Management/ADR-024_ARCHITECTURE_REVIEW_2026-11-06.md`.
- `docs/docs/00_Project_Management/ADR-024_REVISION_BLUEPRINT_2026-11-06.md`.
- `docs/docs/00_Project_Management/ADR-024_REVISION_REPORT_2026-11-06.md`.
- `docs/docs/00_Project_Management/ADR-024_FORMAL_REVIEW_2026-11-06.md`.
- `docs/docs/00_Project_Management/GOVERNANCE_RECONCILIATION_2026-11-05.md`.
- `CURRENT_POSITION.md`, `MASTER_ROADMAP.md`, `PROJECT_STATE.md`, `SESSION_HANDOFF.md`.
- Application code, contracts, interfaces, migrations, routes, permissions, configuration and tests.
- All other ADRs.

### 12.6 Final repository state

The final `git status --short` and `git rev-parse HEAD` are recorded in the delivery response for this task. The expected state is:

- the same 5 modified tracked files;
- the same 7 pre-existing untracked files;
- plus this new decision-resolution package (8 untracked files total);
- HEAD remains `d42dbef3e32ad6e7a7a4bc70c68f805be60da480`;
- nothing is staged or committed.

### 12.7 Preservation statement

No existing modification or untracked file was reverted, overwritten, staged, committed or deleted. No destructive Git operation was performed. The pre-existing work is preserved.

---

## Appendix A - Decision Classification Key

| Classification | Meaning |
|---|---|
| **READY FOR PROJECT OWNER DECISION** | The Project Owner can make or confirm the decision within the stated scope; other approvers may still be required for co-owned decisions. |
| **REQUIRES ARCHITECTURE TEAM APPROVAL** | The Architecture Team must record the decision; an agent or the Project Owner cannot substitute. |
| **REQUIRES SECURITY APPROVAL** | The Security Lead must record the decision or sign the security position. |
| **REQUIRES OPERATIONS APPROVAL** | The Operations Lead must record the operational evidence or accept the operational risk. |
| **REQUIRES LEGAL/COMPLIANCE APPROVAL** | Legal/Compliance must record the legal position, retention basis, hold or residency decision. |
| **CAN BE DEFERRED** | The decision can remain open without blocking the stated gate, provided the deferral is explicit and recorded. |
| **BLOCKED BY MISSING EVIDENCE** | The decision cannot be validly recorded until the named evidence exists. |

A decision may have multiple required approvers.

## Appendix B - Evidence Index

| Area | Evidence |
|---|---|
| ADR status and registers | `docs/ADR/ADR-024.md` sections 1.3, 8.1, 12-16, 24, 25, 27, 33, 34, 36, 37 |
| Formal review conditions | `docs/docs/00_Project_Management/ADR-024_FORMAL_REVIEW_2026-11-06.md` section 1.3 |
| Blueprint decisions | `docs/docs/00_Project_Management/ADR-024_REVISION_BLUEPRINT_2026-11-06.md` sections 4-6 |
| Revision report | `docs/docs/00_Project_Management/ADR-024_REVISION_REPORT_2026-11-06.md` |
| PA provenance | ADR-024 v1.0 approval block; `SESSION_HANDOFF.md` 2026-10-10 section; `SPR-017_Archive_Cloud_Storage_Decision_Board_Report.md` section 4 |
| Cascade evidence | `database/migrations/2026_08_06_000010_create_generated_files_table.php:29-31`; `database/migrations/2026_08_06_000009_create_document_audits_table.php:28-30` |
| Audit contradiction | `docs/03_Database/Audit.md:74-79`; `app/Console/Commands/System/SystemCleanupCommand.php:14,23-29`; `bootstrap/app.php:47` |
| Encryption state | `config/database.php:113`; `app/Models/SystemSetting.php:62-75`; `config/filesystems.php:33-35` |
| Backup state | `app/Domains/System/Services/BackupService.php:46-107`; `docs/08_Deployment/Backups.md` |
| Quota data | `database/migrations/2026_07_24_000001_create_plans_table.php:23`; `database/seeders/PlanSeeder.php:25,40,55,70` |
| Lifecycle state | `app/Domains/RecordsLifecycle/Domain/Enums/RecordLifecycleState.php:9-11`; `RecordLifecycleService.php:23-38` |
| No Archive domain | `app/Domains/Archive` absent; `app/Domains/Permission/Enums/PermissionGroup.php` has no `Archive` case |
| SPR-014 report | `docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md`; SHA-256 `5196197BA325A6CBB25824CA89E8F5906AD6074FCD39DA7133FF0338FF1A6BF8` |

## Appendix C - Status Declarations

- **ADR-024 remains `Proposed`.** This package does not accept it or change its status.
- **SPR-014 remains `DEFERRED`.** This package does not close it, promote it or waive it.
- **SPR-017 remains `NOT IMPLEMENTED / NOT AUTHORIZED`.** This package does not authorize or implement it.
- **No DR is approved.** All 18 acceptance-gate DRs remain `PROPOSED`; the production and cloud DRs remain `PROPOSED`; DR-23 and DR-24 remain `DEFERRED`.
- **No H item is approved.** All 18 remain `PROPOSED - pending`.
- **No PA record was modified.** PA-1 to PA-5 remain as recorded in ADR-024 v1.0, with the provenance caveat.
- **No waiver, closure, staging record, production readiness or cloud authorization exists.**
- **No application code, contract, interface, migration, route, permission, configuration or test was created or modified.**
- **No commit was made.**

**End of decision-resolution package. ADR-024 remains Proposed. SPR-014 remains Deferred. SPR-017 remains Not Implemented / Not Authorized. H-01 remains unsigned.**
