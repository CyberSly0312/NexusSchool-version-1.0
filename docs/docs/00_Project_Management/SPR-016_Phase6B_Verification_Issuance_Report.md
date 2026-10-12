# SPR-016 Phase 6B — Verification Issuance Boundary

Status:
DEFERRED — ISSUANCE NOT YET JUSTIFIED

Verdict:
**D. DEFERRED — ISSUANCE NOT YET JUSTIFIED**

Date:
2026-09-17

---

## 1. Decision Summary

Phase 6B examines the **production issuance boundary** of SPR-016 — the point at
which verification identity issuance would be connected to the Academic
Communication lifecycle. The verdict is **D. DEFERRED — ISSUANCE NOT YET
JUSTIFIED**.

No production issuance boundary is approved. No party currently consumes a
verification identity at a production boundary, and SPR-016 Phase 5 already
recorded that the SPR-012 substrate has **no token-bearing consumer boundary** —
so there is no genuine product requirement that would justify wiring issuance into
the Academic Communication lifecycle today. Phase 6B therefore defers all issuance
decisions, rejects nothing permanently, and records a candidate set only.

This report is a **documentation-only artifact**. It authors no ADR (in particular
**no ADR-023**), implements nothing, and changes no application code, tests,
migrations, routes, contracts, services, events, listeners, QR infrastructure or
dependencies. Its sole governing decision remains **ADR-022** (Accepted
2026-09-07).

## 2. Scope and Constraints

- **Documentation only.** No code, no schema, no routes, no dependencies.
- **No ADR authored.** ADR-023 is explicitly **not** created by Phase 6B (see §7).
- **No new architectural decision.** Phase 6B records analysis and a deferred brief
  only; nothing is accepted here.
- **ADR-022 is the governing decision.** All terminology and claims below conform to
  ADR-022 Decisions 1–8 and their invariants (I1–I13, P1–P5, T1–T4, L1–L3, C1–C9).
- **Verdict D preserved.** "Issuance is not yet justified" is the standing Phase 6B
  outcome; the candidate matrix does not lift it.

## 3. Governing Context

SPR-016 (Identity & Verification Engine) is governed by ADR-022, accepted
2026-09-07. The decisions that bound Phase 6B:

| ADR-022 Decision | Binding content for Phase 6B |
|---|---|
| D1 — Public surface | central, public, unauthenticated verification endpoint |
| D2 — Identity / token | single random opaque verification token (≥144-bit, CSPRNG, stored SHA-256-hashed), generated once, globally unique, permanent |
| D3 — Response / privacy | LEVEL 2 default; no grades, personal data, content or internal ids on the public surface |
| D4 — Revocation / replacement | formal REVOKED and REPLACED; forward-only; reason + capability + audit |
| D5 — QR | payload is verification URL + token only; rendering owned by the SPR-012 substrate |
| D6 — Tenancy | central endpoint + opaque token resolves exactly one tenant; no school-code/document-number probing |
| D7 — Lifecycle truth | aggregate lifecycle status is verification truth; PublicationRecord is never truth |
| D8 — Identity stability | verification identity belongs to the **AcademicDocument aggregate** |

Additionally, ADR-021 (Decision 3) reserves verifier capabilities for SPR-016, and
ADR-022 rejects "per-version or per-publication identity" in its Rejected
Alternatives (item 10). Phase 6B preserves all of this.

## 4. Phase 6 Reconciliation

Phase 6 of SPR-016 was scoped as the issuance boundary: the production point where
a verification identity is created for an official document and attached to the
Academic Communication lifecycle. Reconciliation of that phase yields the
following:

- **No real consumer exists.** No external verifier, portal integration, government
  service, or in-platform flow currently consumes verification identities at a
  production boundary.
- **No holder of a token today.** The QR consumer was deferred in Phase 5; the
  `qr_code_enabled` template flag remains unused and no QR library is present in
  `composer.json`. There is no token-bearing consumer boundary in the SPR-012
  substrate.
- **Issuance cannot be justified in isolation.** Wiring issuance into the lifecycle
  without a consumer would produce stored identity state that nothing reads,
  increases attack surface, and adds operational burden without a requirement.
- **ADR-022 does not require a phase-6 boundary.** ADR-022 decides identity
  *semantics and location* (aggregate-bound, stable, permanent); it deliberately
  defers the *production wiring* to the SPR-016 implementation phase. With no
  consumer, that wiring remains unperformed.

Phase 6B therefore closes the phase with **Defers** — issue planted for a future
ADR-023 (see §7), no boundary built, no boundary accepted.

## 5. Candidate Matrix: Production Issuance Boundary

Three candidate mechanisms for a future issuance boundary were evaluated. **All
three remain candidates; none is accepted by Phase 6B.**

| Dimension | Option A — Event-based | Option B — Orchestration boundary | Option C — Dedicated issuance aggregate |
|---|---|---|---|
| Mechanism | SPR-016 listener/subscriber reacts to `DocumentGenerated` / `DocumentVersionCreated` and mints the identity | explicit issuance facade invoked inside the same transactional boundary as the lifecycle action | SPR-012 writes an issuance-ready enrollment record; SPR-016 mints identities at its own transactional boundary |
| Transactional consistency | dependent on dispatch semantics; only defensible if transactional (outbox/after-commit) dispatch is demonstrably available | strong — one unit of work, explicit ordering | strong within each engine; eventual between engines |
| Dependency direction | inverted: SPR-012 events → SPR-016 side effect | orchestrator-owned seam; lifecycle action calls the issuance contract | aggregate seam; both engines declare the exchange explicitly |
| Identity stability (ADR-022 D8) | unaffected — identity still bound to the AcademicDocument aggregate | unaffected | unaffected |
| Failure modes | partial identity creation; duplicates on retry without the transactional dispatch guarantee | transaction rollback covers the whole boundary; clear failure | split-boundary gaps unless the enrollment record is authoritative |
| Pattern fit in this repository | existing events already exist (SPR-012) | `RecordsLifecycleContract` precedent — `GenerateDocumentAction` already calls `$this->lifecycle->register(...)` | `CompletionRequest` precedent (SPR-015) + `RecordLifecycle` (SPR-014) |
| Phase 6B disposition | candidate only — **reconsidered only if transactional dispatch semantics are demonstrably available** | candidate; preferred direction of current analysis — **NOT accepted** | candidate |

Every candidate preserves the ADR-022 invariant that identity is created for a
genuinely new AcademicDocument aggregate and never rotates with versions, files or
delivery records. The matrix evaluates only *where and how* that creation would be
wired once a real consumer requires it.

## 6. Candidate Analysis

**Option A — Event-based.** SPR-012 already dispatches `DocumentGenerated` and
`DocumentVersionCreated`; a listener in SPR-016 could in principle mint identities
from those events. The analysis shows the constraint is dispatch semantics: without
a demonstrably transactional dispatch guarantee (after-commit / outbox), event
delivery can race or duplicate, and identity creation could be half-applied with no
clean retry story. Because ADR-022 D8 requires identity to exist for the aggregate
and D2 requires it stored hashed with a single global lookup row, untrusted
listener-side creation is riskier than other options. Option A is retained purely as
a candidate and only under the stated precondition.

**Option B — Orchestration boundary.** An explicit issuance facade invoked from the
lifecycle action (generation/publish boundary) keeps identity creation in the same
transactional unit as the action that establishes the document. This mirrors the
established `RecordsLifecycleContract` seam that `GenerateDocumentAction` already
honours today, and it makes the invocation graph legible and testable. On current
analysis this is the strongest candidate and the preferred direction — but it is a
**candidate direction only**; it is not accepted here and does not bind SPR-012 or
SPR-016.

**Option C — Dedicated issuance aggregate.** SPR-012 writes an issuance-ready
enrollment snapshot (the `CompletionRequest` / `RecordLifecycle` pattern), and
SPR-016 consumes it at its own boundary to mint identities. This maximally decouples
the two engines and gives SPR-016 full ownership of its minting transaction. The
cost is a second authoritative record and eventual-consistency between engines,
which must be judged against the real consumer requirement when it arrives.

## 7. Deferred ADR-023 Brief — Future Decision Scope

**Deferred ADR-023 Brief — Future Decision Scope.**

No ADR-023 is authored by Phase 6B. If a genuine production requirement later
requires verification identity issuance to be connected to the Academic
Communication lifecycle, ADR-023 should decide the production issuance boundary and
transaction semantics. The current analysis suggests that a controlled orchestration
boundary (Option B) may be preferable, but this is a candidate direction only and is
**NOT accepted** by SPR-016 Phase 6B.

This §7 is explicitly **non-binding**. It is a deferred brief recording the future
decision scope, not an accepted architectural decision. Specifically:

- **ADR-023 is NOT authored now.** This brief is not an ADR and carries no decision
  weight.
- **No production issuance boundary is approved now.** Phase 6B verdict D stands; no
  wiring is permitted to be added to the lifecycle.
- **A future ADR-023 must evaluate the real consumer requirement.** The boundary
  choice is to be made against an actual consumer (a verifier audience, a portal, a
  government service), not against current analysis in a vacuum.
- **Candidate options A, B and C remain candidates.** None is promoted, demoted or
  eliminated by Phase 6B.
- **Event-based issuance (Option A) should only be reconsidered if transactional
  dispatch semantics are demonstrably available** (after-commit / outbox), because
  identity creation must not rely on non-transactional event delivery.
- **The final decision belongs to the future ADR, not Phase 6B.** Phase 6B defers
  the question; it does not decide it.

## 8. Invocation Graph

Current, implemented invocation graph (nothing new is added by Phase 6B):

```
GenerateDocumentAction / RegenerateDocumentAction / PublishDocumentAction / ArchiveDocumentAction
        │
        ├─► DocumentRendererContract          (SPR-012 renders GeneratedFile)
        ├─► RecordsLifecycleContract           (SPR-014: register / publish / archive metadata)
        ├─► events: DocumentGenerated,
        │         DocumentVersionCreated,
        │         DocumentPublished, DocumentArchived
        └─► (no issuance seam — nothing mints a verification identity)
```

The verification side exists today only as read-side and lifecycle-state machinery:

```
VerificationIdentityService (create / revoke / replaceWith / verificationUrlForToken)
        │
        ├─► AcademicDocumentVerificationContract (read-only, SPR-012 substrate)
        └─► VerificationStatus: Active → Revoked | Replaced (forward-only, ADR-022 D4)

PublicVerificationController → PublicVerificationService → contract reads → verdict
Central endpoint, token → SHA-256(token) → single tenant (ADR-022 D6)
```

The dotted edge — a future issuance seam from a lifecycle action to the
`VerificationIdentityService` — **is not present**. Options A/B/C in §5 describe
alternative shapes for that edge. It is intentionally absent while issuance remains
deferred.

## 9. Security Analysis

Phase 6B confirms the security posture is unaffected by the deferral and fully
consistent with ADR-022:

- **Token at rest.** Only the SHA-256 digest is persisted; the plaintext token
  appears only at generation and inside document representations (I11). No new token
  storage was added by this phase.
- **Identity is single and permanent.** One verification identity per
  AcademicDocument aggregate; the token is generated once, is globally unique, and
  never rotates across versions, regenerated files, replacement or archival (I8,
  D2, D8).
- **No verifier capability proliferation.** Because issuance is deferred, no
  mechanism attaches successive identities to successive versions. Lifecycle change
  is expressed through the stable ACTIVE / REVOKED / REPLACED status on the single
  identity, never through new identity creation for the same aggregate (D4, D8).
- **Non-enumerability.** High-entropy tokens, uniform LEVEL 0 responses for invalid
  and unpublished lookups, and rate limiting (I2, I4, P3, C2) remain the boundary
  defenses. Document numbers are never capabilities (I5).
- **Revocation discipline.** Revocation requires capability, recorded reason
  category and audit; it is terminal (I13). The public surface never reveals free
  text or the existence of unpublished records (P1, P5).
- **No expiration semantics.** ADR-022 introduces no token expiration and Phase 6B
  neither adds nor authors any. Identity is permanent at the aggregate level;
  issuance timestamps in the identity record are issuance-lifecycle metadata, not
  validity bounds.

## 10. Tenancy Analysis

- **Resolution remains central + opaque token** (D6, T1). A verification request
  resolves the token to exactly one tenant; the read executes against that tenant's
  data store through the read-only contract (T2).
- **No cross-tenant lookup.** There is no school-code + document-number probing path
  on the public surface (T4); school A can never retrieve school B's record (I3).
- **Identity records are tenant-scoped.** The verification identity row carries the
  school id and the academic document id, with a global uniqueness constraint on the
  token hash (§3 context; D2 global uniqueness).
- **A future issuance boundary must be tenant-safe by construction.** Any candidate
  in §5 creates identities inside the issuing school's execution path and never
  introduces a tenant-hopping side effect. This is a requirement the future ADR-023
  must verify, not a decision made here.

## 11. Idempotency & Regeneration

Regeneration creates a new version under the same permanent AcademicDocument
identity. Under ADR-022, verification identity is bound to the **AcademicDocument
aggregate**, not to a GeneratedFile, document version, or PublicationRecord.
Therefore regeneration or version creation **must not** create a new verification
identity. A genuinely new AcademicDocument receives a new verification identity.

Concretely:

- Verification identity is bound to the **AcademicDocument aggregate** (ADR-022
  D8). That aggregate is the verification identity boundary.
- It is **not** bound to a document version — `RegenerateDocumentAction` snapshots
  corrected content as a new `AcademicDocumentVersion` under the same aggregate and
  the same permanent identity.
- It is **not** bound to `GeneratedFile` — re-rendering marks previous files
  historical (`is_current = false`) without touching the identity.
- It is **not** bound to `PublicationRecord` — delivery records are soft-deletable
  and are explicitly not authoritative for verification truth (ADR-022 D7).
- The verification token and identity are **permanent at the identity level**.
  Lifecycle changes are represented through the stable ACTIVE / REVOKED / REPLACED
  status on that single identity (ADR-022 D4, I8) — never through re-issuance of a
  new identity for the same aggregate.
- ADR-022 defines no token validity bound, and none is introduced here: identity is
  permanent at the aggregate level, and an issuance timestamp records when the
  identity was created, not a bound on its validity.

While issuance remains deferred, **no production deduplication policy is required**:
with no issuance boundary wired, there is nothing that could be duplicated in
production. Any future issuance boundary must therefore design for what the deferral
makes trivially true today — issuance keyed to the genuinely new AcademicDocument
aggregate, unique per aggregate — and must preserve aggregate-level identity
stability and ADR-022 lifecycle semantics (D2, D4, D7, D8, I8, C8).

## 12. Legacy No-Go

The legacy generated-document system remains excluded (ADR-022 L1): Phase 6B does
not touch, read or revive `app/Models/GeneratedDocument`,
`app/Models/DocumentTemplate`, `app/Services/DocumentGenerationService`, the legacy
`app/Domains/Document` context, or legacy DocumentCenter views. SPR-016 consumes the
current AcademicCommunication substrate exclusively through its read-only contract
(L2). No duplicate document-generation pipeline and no duplicate document-number
authority exist or are created anywhere under SPR-016 (L3). The deferred issuance
boundary, when it is eventually decided by ADR-023, must continue to respect L1–L3.

## 13. QR Deferral

Phase 5's QR deferral stands: the QR is **not** implemented. The verification URL
capability exists (`VerificationUrl` value object, `verificationUrlForToken`), and
the QR payload contract is fixed by ADR-022 D5 (verification URL + token only,
rendering owned by the SPR-012 substrate), but no QR is rendered, no QR library is
in `composer.json`, and `qr_code_enabled` remains an unused template flag. Phase 6B
does not reintroduce QR work and does not treat the QR as a trigger for issuance.

## 14. MariaDB Blocker Reference

The pre-existing MariaDB migration blocker remains **untouched**:
`database/migrations/2026_08_01_000011_create_coefficients_table.php` (SPR-009.5)
still triggers a failed `migrate` on MariaDB because of the context constraint it
introduces on `academic_documents`. This phase performs no rollback, no edit and no
workaround on that migration. Verification changes remain validated on sqlite only,
as in Phases 4 and 5, and the blocker continues to be carried forward as a
pre-existing defect, not a Phase 6B defect.

## 15. Working-Tree Classification & Governance Status

- **All SPR-016 work to date** lives under already-untracked paths — the
  `IdentityAndVerification`, `RecordsLifecycle` directories, their tests, factories,
  migrations and `routes/verification.php` — plus the authorized `document.revoke`
  extension of `PermissionService`. No pre-existing tracked production file was
  modified by SPR-016 beyond that authorization.
- **This corrective pass / report** adds exactly one new, untracked documentation
  file (`SPR-016_Phase6B_Verification_Issuance_Report.md`). It modifies no
  application code, no contracts, no services, no events, no listeners, no routes,
  no migrations, no factories, no tests, no dependencies, and no config.
- **No implementation appeared.** No QR implementation, no token storage, no
  issuance wiring, no new route, and no ADR (in particular no ADR-023) was created.
- **ADR-022 is not weakened.** Every statement above conforms to ADR-022 Decisions
  1–8 and invariants I1–I13, P1–P5, T1–T4, L1–L3 and constraints C1–C9.
- **Verdict.** **D. DEFERRED — ISSUANCE NOT YET JUSTIFIED.** The production issuance
  boundary decision is planted for the future ADR-023; Options A/B/C remain
  candidates; Option B is a preferred but non-binding direction only.

---

**SPR-016 Phase 6B is now architecturally consistent with ADR-022 and ready to
close.**