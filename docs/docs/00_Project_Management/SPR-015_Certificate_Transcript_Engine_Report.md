# SPR-015 Certificate & Transcript Engine

Status:
IMPLEMENTED — TARGETED GATES PASS

Date:
2026-09-07

---

## 1. Objective

Implement the Certificate & Transcript Engine as a dedicated business engine in `app/Domains/CertificateAndTranscript/`, exactly per [ADR-021](../../ADR/ADR-021.md). The engine composes end-of-study certificates and academic transcripts from the already-published school evidence and delegates the official document lifecycle to the Academic Communication engine (SPR-012).

## 2. Scope of the Implemented Slice

- A `completion_request` aggregate (one student × one document kind) with a forward-only lifecycle **Draft → Generated → Published → Archived**, plus its immutable `completion_request_entry` snapshots.
- Transcript composition: verbatim serialization of the **published** report-card bulletin snapshots (never recomputed).
- Certificate issuance: an eligibility **verdict** orchestrated from published report-card snapshots, the latest *published* council decision, graduation closure, and policy-driven thresholds.
- Evidence handling: the eligibility verdict and its evidence citations are persisted for audit **even when the student is ineligible**.
- Official document delivery through the Academic Communication engine with the additive `Transcript` / `Certificate` document origins.
- School-scoped, policy-driven authorization through a dedicated `certificate-and-transcript` permission group.

## 3. ADR-021 Decision Trace

| ADR-021 Decision | Implementation |
|---|---|
| Decision 1 — dedicated domain, document orchestration in SPR-012 | `AcademicCommunicationAdapter` is the only seam to SPR-012; snapshots never see an `AcademicDocument` model |
| Decision 2 — verbatim published snapshots | `TranscriptContentComposer` writes `report_card_entries.content` verbatim for Published/Archived runs |
| Decision 3 — no QR / no verification | Not implemented; only verification-ready metadata (`document_number`, `academic_document_id`) is kept |
| Decision 4 — SPR-015 owns the eligibility verdict | `CertificateEligibilityService` + persisted verdict/citations; verdict committed **before** the ineligibility exception is raised |
| Decision 5 — SPR-015-owned read-only `EnrollmentContract` | `EnrollmentAdapter` exposes enrollment history + graduation closure (school-scoped reads only) |
| Decision 6 — new `certificate-and-transcript` permission group | Added to `PermissionGroup`, `PermissionService` (NO_CRUD_GROUPS + EXTRA_ACTIONS) and `PermissionSeeder` |

## 4. Architecture

```
CreateCompletionRequest / GenerateTranscript / GenerateCertificate / Publish / Archive
   → CompletionRequestService (Application)
   → Domain Contracts (ReportCard, ClassCouncil, Enrollment, StudentIdentity,
                        AcademicPolicies, AcademicCommunication)
   → Infrastructure Adapters (each implements exactly one domain contract)
   → CompletionRequest / CompletionRequestEntry (models, school-scoped)
```

- Domain and Application layers never import foreign-domain classes (enforced by architecture test).
- Registration in `bootstrap/providers.php` (`CertificateAndTranscriptServiceProvider`); test directories registered in `tests/Pest.php`.
- The engine ships no UI, no PDF rendering, and no duplicate source of truth.

## 5. Key Fixes During Validation

1. **Audit-loss bug (real design bug).** `GenerateCertificateAction` originally persisted the ineligible verdict inside the issuance `DB::transaction`; the `CertificateIneligibilityException` rolled it back, losing the audit verdict. The action now persists verdict + citations and dispatches `CertificateEligibilityDetermined` in its own transaction before throwing; issuance runs in a second transaction.
2. **Lint fix.** `CreateCompletionRequestData` used an invalid `(int|string)` cast on `student_id`.
3. **PHPStan typing.** Fixed 64 errors at the root cause (no baseline additions, no `@phpstan-ignore`): `@property` cast annotations on both models, larastan relation generics (`HasMany<..., $this>`), `getAttribute()` for foreign-model attributes and enums, and removal of stale nullsafe/mis-cast reads in the adapters.

## 6. Validation Evidence (2026-09-07)

| Gate | Command | Result |
|---|---|---|
| Unit tests (5 files) | `./vendor/bin/pest tests/Unit/Domains/CertificateAndTranscript` | PASS — 21 tests |
| Feature tests (4 files) | `./vendor/bin/pest tests/Feature/Domains/CertificateAndTranscript` | PASS — 24 tests |
| Domain tests combined | `./vendor/bin/pest tests/Unit/Domains/CertificateAndTranscript tests/Feature/Domains/CertificateAndTranscript` | PASS — 45 tests, 218 assertions |
| PHPStan, repository configuration | `./vendor/bin/phpstan analyse --no-progress --memory-limit=2G` | PASS — 0 errors |
| Pint, scoped | `./vendor/bin/pint --test <new domain + migrations + config + edited files>` | PASS |
| Full suite | `./vendor/bin/pest` | PASS — 1,934 tests, 4,726 assertions |

## 7. Files Created

- `app/Domains/CertificateAndTranscript/` — domain (enums, contracts, references, events, exceptions, trait), application (service, actions, composers, eligibility service, DTO), infrastructure (models, factory, observer, policy, listener, provider, 6 adapters).
- `database/migrations/2026_09_06_000001_create_completion_requests_table.php`, `2026_09_06_000002_create_completion_request_entries_table.php`.
- `database/factories/CertificateAndTranscript/` (2 factories).
- `config/nexus-certificate-and-transcript.php`.
- `tests/Unit/Domains/CertificateAndTranscript/` (5 files), `tests/Feature/Domains/CertificateAndTranscript/` (4 files).

## 8. Files Modified (additive)

- `bootstrap/providers.php`, `tests/Pest.php` (registration).
- `app/Domains/AcademicCommunication/Domain/Enums/DocumentOrigin.php` (`Transcript`, `Certificate` origins).
- `app/Domains/Permission/Enums/PermissionGroup.php`, `app/Domains/Permission/Services/PermissionService.php`, `database/seeders/PermissionSeeder.php` (new permission group).

## 9. Git State

Working tree contains the SPR-015 additions on top of substantial pre-existing uncommitted changes (SPR-009.5/SPR-012/SPR-013/SPR-014 work, Filament resource cleanups, factories, docs). No reset, revert, or destructive operation was performed. New SPR-015 files are untracked; register/add them when committing (committing was not requested).