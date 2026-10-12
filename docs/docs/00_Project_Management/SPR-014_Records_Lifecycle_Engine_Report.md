# SPR-014 Records Lifecycle Management Engine

Status:
IMPLEMENTED — TARGETED GATES PASS

Date:
2026-08-11

---

## 1. Current Audit Status

This report has been re-scoped from a broad architecture vision to a single implementation-ready slice for SPR-014.

- Audit status: Approved for rework, not approved for full implementation.
- Planning status: READY FOR IMPLEMENTATION for the first slice only.
- Sprint status remains PLANNED / NOT STARTED until real implementation begins.
- No application code, migration, model, action, event, or test has been created in this planning session.

---

## 2. Repository Evidence

The following repository facts constrain the SPR-014 design and must remain authoritative:

- The platform is multi-tenant and tenant-scoped through [docs/01_Architecture/Multi_Tenant_Architecture.md](../../01_Architecture/Multi_Tenant_Architecture.md), [app/Traits/HasSchoolId.php](../../../app/Traits/HasSchoolId.php), [app/Traits/ScopedByTenant.php](../../../app/Traits/ScopedByTenant.php), and [app/Contracts/SchoolOwned.php](../../../app/Contracts/SchoolOwned.php).
- Academic rules are owned by the Academic Policies engine, not by other domains; see [app/Domains/AcademicPolicy](../../../app/Domains/AcademicPolicy) and [docs/docs/00_Project_Management/ARCHITECTURE_DECISIONS.md](ARCHITECTURE_DECISIONS.md).
- Official publication is owned by Academic Communication; see [app/Domains/AcademicCommunication/Infrastructure/Models/AcademicDocument.php](../../../app/Domains/AcademicCommunication/Infrastructure/Models/AcademicDocument.php) and [app/Domains/AcademicCommunication/Application/Actions/PublishDocumentAction.php](../../../app/Domains/AcademicCommunication/Application/Actions/PublishDocumentAction.php).
- Report Card already composes bulletin content and delegates official document creation/publishing to Academic Communication through [app/Domains/ReportCard/Domain/Contracts/AcademicCommunicationContract.php](../../../app/Domains/ReportCard/Domain/Contracts/AcademicCommunicationContract.php).
- Class Council already owns its own state machine and immutability rules through [app/Domains/ClassCouncil/Infrastructure/Models/CouncilSession.php](../../../app/Domains/ClassCouncil/Infrastructure/Models/CouncilSession.php) and [app/Domains/ClassCouncil/Domain/Enums/CouncilSessionStatus.php](../../../app/Domains/ClassCouncil/Domain/Enums/CouncilSessionStatus.php).
- Authorization is policy-driven and school-scoped, with super_admin bypass; see [app/Domains/Student/Domain/Traits/HandlesStudentAuthorization.php](../../../app/Domains/Student/Domain/Traits/HandlesStudentAuthorization.php), [app/Domains/ReportCard/Domain/Traits/HandlesReportCardAuthorization.php](../../../app/Domains/ReportCard/Domain/Traits/HandlesReportCardAuthorization.php), and [app/Domains/ClassCouncil/Domain/Traits/HandlesClassCouncilAuthorization.php](../../../app/Domains/ClassCouncil/Domain/Traits/HandlesClassCouncilAuthorization.php).

---

## 3. Record Definition

A “record” in NexusSchool is not a brand-new business entity by default. The repository shows that the existing engines already own real academic objects:

- Academic Communication owns official documents and publication state.
- Report Card owns report-card runs and bulletin composition.
- Class Council owns council sessions and decisions.
- Academic Policy owns rules and scope.

Therefore, SPR-014 should define a record as an envelope of lifecycle metadata and provenance around an existing source object, not as a replacement for that source object.

### What is a record in this repository?

A record is an official academic artifact that has:

- a source domain,
- a source object identifier,
- a school owner,
- a lifecycle state,
- an audit trail,
- and a provenance link to the original engine.

### What is not a record in this slice?

- A new grading engine
- A new document generation engine
- A new report-card engine
- A replacement for Class Council decisions
- A replacement for Academic Policy rules

### Repository-backed conclusion

SPR-014 should be a thin lifecycle metadata / coordination engine around already-existing academic artifacts.

---

## 4. Candidate Source Domains

The first slice must be narrow. The strongest candidates are Report Card and Academic Communication because both already expose lifecycle and publication concepts through contracts and domain events.

| Domain | Lifecycle maturity | Integration complexity | Value | Risk | Candidate |
|--------|-------------------|------------------------|-------|------|-----------|
| Report Card | High | Medium | High | Medium | Yes |
| Academic Communication | High | Medium | Very High | Medium | Yes |
| Class Council | High | Medium | High | Medium | Yes |

### Why Report Card and Academic Communication are the best first slice

- Report Card already integrates with Academic Communication through a dedicated contract.
- Academic Communication already carries publication, versioning, and immutability semantics.
- Both domains already expose domain events and audit logging patterns.
- The slice can be implemented without creating a new business engine or duplicating content logic.

### Recommended first slice source domain

Use Report Card as the first source domain and Academic Communication as the publication owner.

That gives SPR-014 a concrete, low-risk entry point:

- Report Card creates the academic context and can emit lifecycle registration.
- Academic Communication remains the owner of official document publication.
- SPR-014 only records state and provenance.

---

## 5. Provenance Contract

The provenance contract must be minimal and repository-grounded. The first slice does not need a large metadata model.

### Minimum fields for the first slice

| Field | Purpose | Evidence basis |
|-------|---------|----------------|
| school_id | mandatory tenant ownership | HasSchoolId / SchoolOwned |
| source_domain | identifies the originating engine | Report Card / Academic Communication / Class Council |
| source_type | identifies the originating object type | existing domain models |
| source_id | identifies the originating record | existing domain primary keys |
| source_version | optional version, number, or revision reference | Academic Communication versioning and Report Card generated runs |
| lifecycle_state | current lifecycle envelope state | existing draft/published/archived semantics |
| academic_context | optional year/term/class/section/student context | existing Report Card and document metadata |
| created_by | actor of registration | existing event payloads and audit logs |
| created_at | registration time | standard Eloquent timestamps |
| published_at | publication time when applicable | existing model fields |
| archived_at | archival time when applicable | existing model fields |
| origin_event | source event that triggered the state change | existing domain events |

### Provenance invariants

- The provenance record must never be cross-school.
- The source object and provenance record must belong to the same school.
- Published records must remain immutable from the lifecycle engine’s perspective.
- The lifecycle engine must never duplicate the source domain’s business meaning.

---

## 6. Lifecycle Contract

SPR-014 should not invent a new generic state machine that duplicates the underlying domain states already present in the repository.

### Minimal lifecycle for the first slice

- Registered / Draft
  - initial state after provenance is attached
- Published
  - source has reached a published or externally visible state
- Archived
  - source is removed from active flow but retained for history

### Allowed transitions

- Registered → Published
- Registered → Archived
- Published → Archived

### Invariants

- A published record is immutable for lifecycle updates.
- An archived record is historical and read-only.
- The source domain remains the owner of the source state machine.
- SPR-014 only observes and records lifecycle transitions; it does not decide academic validity.

### Why this is the correct minimum

The repository already shows that more detailed states exist in the source domains:

- Report Card: Draft → Generated → Published → Archived
- Academic Communication: Draft → Generated → Reviewed → Approved → Published → Archived
- Class Council: Draft → Convened → Published → Archived

SPR-014 should not re-encode those internal states. It should only track the lifecycle envelope around the source record.

---

## 7. Contract Boundaries

| Source Domain | SPR-014 may read | SPR-014 may write | Source remains owner of | Events | Forbidden operations |
|---------------|------------------|-------------------|--------------------------|--------|----------------------|
| Report Card | run status, generated/published/archived state, class/term/year context | a lifecycle provenance record linked to the run | report-card content, grading logic, run generation, and state transitions | ReportCardGenerated, ReportCardPublished, ReportCardArchived | changing report-card content or replacing Report Card business rules |
| Academic Communication | document status, publication state, version info, publication policy context | a lifecycle provenance record linked to the document | document content, numbering, publication policy, delivery, and versioning | DocumentGenerated, DocumentPublished, DocumentArchived, DocumentVersionCreated | modifying document content, policy, or publication delivery |
| Class Council | session status, publication state, decision context | a lifecycle provenance record linked to the session | council decisions, validation logic, and session state transitions | CouncilSessionCreated, CouncilSessionConvened, CouncilSessionPublished, CouncilSessionArchived | altering council decisions or overriding council outcomes |

### Contract rule

SPR-014 must consume only contracts and domain events. It must not reach directly into source-domain models.

---

## 8. Tenant / Security Invariants

SPR-014 must respect the existing multi-tenant and authorization model exactly.

- No record may exist without a school owner.
- No provenance record may reference a source object from another school.
- No cross-school visibility is allowed.
- No central platform-wide access path may bypass school scoping.
- The super_admin bypass remains unchanged.
- Authorization stays policy-driven and school-scoped.
- The lifecycle engine must never become a second ownership model.

### Mandatory invariants

1. A provenance record is always school-owned.
2. A provenance record must validate that its source object belongs to the same school.
3. Publication and archive actions must require the appropriate capability.
4. Published records remain immutable.
5. No destructive delete is allowed for official records in this slice.

---

## 9. First Implementation Slice

### Slice name

Record provenance and lifecycle envelope for official report-card documents.

### Bounded-context boundary

Create a new bounded context under App\Domains, for example RecordsLifecycle, with the standard layers:

- Domain: enums, contracts, value objects, events, exceptions
- Application: DTOs, actions, service
- Infrastructure: model, policy, repository, adapter, observer
- Presentation: deferred for the first slice; not required to start implementation

### Scope of the first slice

1. A school-owned lifecycle provenance record.
2. A minimal lifecycle state machine: Registered → Published → Archived.
3. One source integration seam: Report Card → Academic Communication.
4. One contract-based registration event from the source domain.
5. Policy-based authorization for create / publish / archive.
6. Tenant isolation and school-scoping checks.
7. Unit, feature, security, integration, and architecture tests.

### Explicitly excluded from the first slice

- Filament UI
- Public API
- PDF generation
- Notification delivery
- Bulk processing
- Archive storage integration
- Full cross-domain coverage beyond Report Card / Academic Communication

---

## 10. Explicit Non-Goals

The following remain explicitly out of scope for this planning phase and for the first implementation slice:

- Certificate or transcript generation
- Identity verification
- Full archive platform
- Public API
- Mobile experience
- Document generation engine
- PDF engine
- Notification system
- Bulk processing
- Analytics or reporting engine

---

## 11. Test Strategy

The first slice must be test-driven and should follow the repository’s existing conventions.

### Unit tests

- provenance contract validation
- lifecycle transitions
- immutability invariants
- invalid transition rejection

### Feature tests

- create a lifecycle record
- read a lifecycle record
- publish a lifecycle record
- archive a lifecycle record
- tenant isolation on retrieval

### Security tests

- same-school access succeeds
- cross-school access is denied
- super_admin bypass remains intact
- unauthorized transition is denied
- no bypass via central context

### Integration tests

- Report Card → SPR-014 registration
- Academic Communication → SPR-014 publication state sync
- SPR-014 → source domain contract consumption

### Architecture tests

- namespaces remain within the correct bounded context
- dependency direction remains from source domains to the contract seam and not vice versa
- no forbidden coupling to source-domain models

---

## 12. Implementation Order

1. Define the contract seam between Report Card and Academic Communication.
2. Define the minimal provenance contract and lifecycle state machine.
3. Add the school-owned persistence model and the minimum migration.
4. Add application actions and service facade for register / publish / archive.
5. Add policy enforcement and tenant-safety checks.
6. Add domain events and audit hooks.
7. Add unit, feature, security, integration, and architecture tests.
8. Only then decide whether to widen the scope to other source domains.

---

## 13. Risks

- The first slice could accidentally absorb business logic from the source domains.
- The provenance contract could become too generic and lose usefulness.
- The implementation could drift toward a full document engine instead of a thin lifecycle envelope.
- The persistence model could be over-designed before the contract seam is validated.

### Mitigation

- Keep the slice limited to metadata and coordination.
- Keep the source-domain contract seam narrow.
- Use only the minimum required lifecycle states.
- Treat the source engines as authoritative owners of business meaning.

---

## 14. Approval Gate

Implementation may begin only if the following are true:

- the first slice remains metadata-only,
- the source-domain ownership boundaries are explicit,
- the provenance contract is minimal and tenant-safe,
- the lifecycle states are limited to the agreed envelope,
- the tests cover lifecycle, tenant isolation, and authorization.

---

## 15. Implementation Readiness Decision

READY FOR IMPLEMENTATION

Reason: the repository clearly supports a narrow, contract-based lifecycle metadata slice anchored to existing Report Card and Academic Communication patterns, with explicit tenant-scoping, policy-based authorization, and a minimal lifecycle envelope. The scope is now specific enough that implementation can begin without ambiguity.

---

## 16. Files Modified

- [docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md](SPR-014_Records_Lifecycle_Engine_Report.md)

## 17. Git State

No code or application files were modified in this planning session. The repository state remains unchanged except for the update to the SPR-014 planning document.

---

## 18. Validation Evidence (2026-08-11)

The implemented scope is a school-owned provenance and lifecycle metadata envelope with the three states `registered`, `published`, and `archived`. It is provided by `RecordsLifecycleServiceProvider`, persisted in `record_lifecycles`, and exposes register, publish, and archive application actions protected by `RecordLifecyclePolicy`.

| Gate | Command | Result |
|---|---|---|
| Lifecycle unit test | `php artisan test tests/Unit/Domains/RecordsLifecycle/RecordLifecycleStateTest.php --no-coverage` | PASS — 2 tests, 4 assertions |
| Architecture test | `php artisan test tests/Unit/Domains/RecordsLifecycle/RecordLifecycleArchitectureTest.php --no-coverage` | PASS — 1 test, 10 assertions |
| Feature/security test | `php artisan test tests/Feature/Domains/RecordsLifecycle/RecordLifecycleFeatureTest.php --no-coverage` | PASS — 5 tests, 17 assertions |
| All SPR-014 tests | `php artisan test tests/Unit/Domains/RecordsLifecycle tests/Feature/Domains/RecordsLifecycle --stop-on-failure --no-coverage` | PASS — 8 tests, 31 assertions |
| PHPStan, scoped | `vendor\\bin\\phpstan analyse app\\Domains\\RecordsLifecycle --memory-limit=1G` | PASS — 0 errors |
| PHPStan, repository configuration | `vendor\\bin\\phpstan analyse --memory-limit=1G` | PASS — 0 errors |
| Pint, scoped | `vendor\\bin\\pint --test app\\Domains\\RecordsLifecycle tests\\Unit\\Domains\\RecordsLifecycle tests\\Feature\\Domains\\RecordsLifecycle database\\migrations\\2026_08_10_000000_create_record_lifecycles_table.php` | PASS |
| Full suite | `php artisan test` | PASS — 1,870 tests, 4,429 assertions, 771.88 s |

Report Card regression tests passed (36 tests, 247 assertions) and Academic Communication regression tests passed (65 tests, 289 assertions), both with `--stop-on-failure --no-coverage`.

### Validation limitation / remaining integration work

No executable contract seam or registration/state-sync call from `ReportCard` or `AcademicCommunication` to `RecordsLifecycle` was found during validation. The two domains therefore remain unaffected by the new metadata layer, and their successful regression tests demonstrate non-regression rather than SPR-014 integration. This is outside the implemented envelope and prevents declaring the broader first-slice integration described in section 9 complete. No source-domain business logic was changed during validation.

### Validation continuation evidence (2026-08-12)

The exact first requested unit command, `php artisan test tests/Unit/Domains/RecordsLifecycle/RecordLifecycleStateTest.php`, reached the 120 s command limit after reporting 2 passed tests and 4 assertions, so its result is **TIMEOUT**, not PASS. Its completed retest, `php artisan test tests/Unit/Domains/RecordsLifecycle/RecordLifecycleStateTest.php --no-coverage`, passed with 2 tests and 4 assertions.

All other gates completed successfully: architecture passed (1 test, 10 assertions); feature/security passed (5 tests, 17 assertions); combined SPR-014 tests passed (8 tests, 31 assertions); Report Card regression passed (36 tests, 247 assertions); Academic Communication regression passed (65 tests, 289 assertions); scoped and repository PHPStan passed with 0 errors; scoped Pint passed; and `php artisan test` passed with 1,870 tests, 4,429 assertions, in 511.29 s.

### Stabilization evidence (2026-08-19)

Stabilization pass added a `RecordLifecycleFactory` and expanded state-transition test coverage to the full transition matrix. The previous 8-test / 31-assertion baseline is superseded by the figures below.

| Gate | Command | Result |
|---|---|---|
| Unit: state transitions | `php vendor/bin/pest tests/Unit/Domains/RecordsLifecycle/RecordLifecycleStateTest.php --no-coverage` | PASS — 5 tests, 9 assertions |
| Unit: architecture | `php vendor/bin/pest tests/Unit/Domains/RecordsLifecycle/RecordLifecycleArchitectureTest.php --no-coverage` | PASS — 1 test, 10 assertions |
| Feature / security | `php vendor/bin/pest tests/Feature/Domains/RecordsLifecycle/RecordLifecycleFeatureTest.php --no-coverage` | PASS — 6 tests, 19 assertions |
| All SPR-014 tests | (combined) | PASS — 12 tests, 38 assertions |
| PHPStan, full repository | `vendor/bin/phpstan analyse --memory-limit=1G` | PASS — 0 errors |
| Pint, scoped | `vendor/bin/pint --test app/Domains/RecordsLifecycle tests/Unit/Domains/RecordsLifecycle tests/Feature/Domains/RecordsLifecycle database/migrations/2026_08_10_000000_create_record_lifecycles_table.php database/factories/RecordsLifecycle` | PASS |

**New transition coverage**: `Published → Archived` (positive), `Archived → *` (all negative), `Registered → Registered` (self-transition negative). All 3 transitions previously untested are now covered.

### Source-domain integration evidence (2026-08-19)

Both Report Card and Academic Communication domains now register lifecycle metadata through the contract/adapter pattern.

**Report Card integration** (pre-existing from first slice stabilization):
- `RecordsLifecycleContract` in `ReportCard/Domain/Contracts/`
- `RecordsLifecycleAdapter` in `ReportCard/Infrastructure/Contracts/`
- `GenerateReportCardAction` registers lifecycle on generation
- `PublishReportCardAction` transitions lifecycle to Published
- `ArchiveReportCardAction` transitions lifecycle to Archived
- 5 integration tests covering provenance, transitions, publish-then-archive, and tenant isolation

**Academic Communication integration** (implemented this session):
- `RecordsLifecycleContract` in `AcademicCommunication/Domain/Contracts/`
- `RecordsLifecycleAdapter` in `AcademicCommunication/Infrastructure/Contracts/`
- `GenerateDocumentAction` registers lifecycle on generation (source_type = DocumentType value)
- `PublishDocumentAction` transitions lifecycle to Published
- `ArchiveDocumentAction` transitions lifecycle to Archived
- 5 integration tests covering provenance, transitions, publish-then-archive, and tenant isolation

**Architecture pattern**:

```
Source Domain Action
    → Source Domain Contract (RecordsLifecycleContract)
    → Source Domain Adapter (RecordsLifecycleAdapter)
    → RecordLifecycleService
    → RecordLifecycle (model)
```

Source domains never touch the `RecordLifecycle` model directly.

| Gate | Command | Result |
|---|---|---|
| RecordsLifecycle tests | `php artisan test tests/Unit/Domains/RecordsLifecycle tests/Feature/Domains/RecordsLifecycle` | PASS — 12 tests, 38 assertions |
| ReportCard integration | `php artisan test tests/Feature/Domains/ReportCard/ReportCardRecordsLifecycleIntegrationTest.php` | PASS — 5 tests, 20 assertions |
| AcademicCommunication integration | `php artisan test tests/Feature/Domains/AcademicCommunication/AcademicCommunicationRecordsLifecycleIntegrationTest.php` | PASS — 5 tests, 19 assertions |
| All three combined | (above three together) | PASS — 22 tests, 77 assertions |
| ReportCard full suite | `php artisan test tests/Feature/Domains/ReportCard` | PASS — 32 tests |
| AcademicCommunication full suite | `php artisan test tests/Feature/Domains/AcademicCommunication` | PASS — 47 tests |
| PHPStan, full repository | `vendor/bin/phpstan analyse --memory-limit=2G` | PASS — 0 errors |
| Pint, scoped | `vendor/bin/pint --test <changed files>` | PASS |

### Class Council integration (2026-08-20)

Class Council now registers lifecycle metadata through the same contract/adapter pattern as Report Card and Academic Communication.

**Class Council integration**:
- `RecordsLifecycleContract` in `ClassCouncil/Domain/Contracts/`
- `RecordsLifecycleAdapter` in `ClassCouncil/Infrastructure/Contracts/`
- `CreateCouncilSessionAction` registers lifecycle on creation (source_type = `council_session`)
- `PublishCouncilSessionAction` transitions lifecycle to Published
- `ArchiveCouncilSessionAction` transitions lifecycle to Archived
- 5 integration tests covering provenance, transitions, publish-then-archive, and tenant isolation
- Architecture tests updated to include `RecordsLifecycleContract` in all three domains

**Provenance mapping**:
- `source_domain` = `class_council`
- `source_type` = `council_session`
- `source_id` = CouncilSession primary key
- `source_version` = CouncilSession UUID
- `academic_context` = `{year_id, term_id, class_id, section_id}`
- `origin_event` = `CouncilSessionCreated`, `CouncilSessionPublished`, `CouncilSessionArchived`

| Gate | Command | Result |
|---|---|---|
| ClassCouncil integration | `php artisan test tests/Feature/Domains/ClassCouncil/ClassCouncilRecordsLifecycleIntegrationTest.php` | PASS — 5 tests, 20 assertions |
| ClassCouncil full suite | `php artisan test tests/Feature/Domains/ClassCouncil` | PASS — 36 tests, 124 assertions |
| RecordsLifecycle tests | `php artisan test tests/Unit/Domains/RecordsLifecycle tests/Feature/Domains/RecordsLifecycle` | PASS — 12 tests, 38 assertions |
| ReportCard full suite | `php artisan test tests/Feature/Domains/ReportCard` | PASS — 26 tests, 98 assertions |
| AcademicCommunication full suite | `php artisan test tests/Feature/Domains/AcademicCommunication` | PASS — 41 tests, 144 assertions |
| All integration tests combined | (above three together) | PASS — 27 tests, 97 assertions |
| Architecture tests (all 3 domains) | `php artisan test tests/Unit/Domains/ClassCouncil/Architecture tests/Unit/Domains/ReportCard/Architecture tests/Unit/Domains/AcademicCommunication/Architecture` | PASS — 12 tests, 236 assertions |
| PHPStan, full repository | `vendor/bin/phpstan analyse --no-progress` | PASS — 0 errors |
| Pint, scoped | `vendor/bin/pint --test <changed files>` | PASS |
| Full suite | `php artisan test` | PASS — 1,889 tests, 4,507 assertions |
