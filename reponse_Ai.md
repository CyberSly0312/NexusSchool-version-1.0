# Wrote docs\docs\00_Project_Management\SPR-014_Records_Lifecycle_Engine_Report.md
# SPR-014 — Records Lifecycle Management Engine Report

**Status:** Audit / Phase 0-1 — NO IMPLEMENTATION

**Date:** 2026-08-24

**Verdict:** NEEDS REWORK

---

## Repository Reality Check — 2026-08-24

Audit performed directly against the repository, file by file, with `git grep`.
Nothing below is taken from the roadmap narrative; everything is code-proven.

- No `RecordLifecycle`, `RecordsLifecycle` or `provenance` symbol exists anywhere
  in `app/`, `database/`, `tests/`, `config/`, `routes/` (git grep: 0 hits).
- No SPR-014 implementation exists. SPR-014 is referenced only in planning
  documents (`CURRENT_POSITION`, `MASTER_ROADMAP`, `SESSION_HANDOFF`,
  journal 2026-08-06).
- Shared Kernel (`app/Domains/Shared/`) contains exactly 4 files:
  - `Enums/ActiveArchivedStatus.php`
  - `Enums/DraftActiveArchivedStatus.php`
  - `Traits/UniqueValidator.php`
  - `Traits/HasEntityExtraction.php`
- Correction to the previous session note: **20** ADR files exist under
  `docs/ADR/` (ADR-001 … ADR-020), not 19.

---

## ADR Availability

| ADR | Size | Content |
|-----|------|---------|
| ADR-006 | 7 329 bytes | Staff Workforce Architecture — Accepted |
| ADR-012 | 10 133 bytes | Academic Communication Engine Architecture — Accepted |
| ADR-013 | 10 585 bytes | Class Council Engine Architecture — Accepted |
| All others (ADR-001…005, 007…011, 014…020) | 0 bytes | Empty files |

**Only ADR-006, ADR-012 and ADR-013 are currently non-empty.**
No ADR exists for Records Lifecycle; none was created during this audit.

---

## CONFIRMED

### A. ADR findings (verbatim extraction, no deduction)

#### ADR-006 — Staff Workforce Architecture

1. **Status:** Accepted.
2. **Date:** 2026-08-04.
3. **Decision:** Single `Staff` aggregate root, role = specialization enum
   value, append-only employment-history journal, single `StaffService`
   facade over explicit application Actions.
4. **Architecture imposed:** Model holds no transition logic; all invariants
   in Actions invoked through the facade; one active employment per staff per
   school enforced by action + partial unique index; domain events consumed by
   `StaffAuditLogger` → `audit_logs` + immutable history journal;
   soft-only deletion gated to `super_admin`; centralized `StaffPolicy`
   (4 CRUD + 6 workforce permissions); staff numbers `STF-{hex}-{seq}`,
   count-based per school, never re-used.
5. **Bounded contexts concerned:** Staff; read-only consumers announced:
   Payroll, Timetable, Attendance.
6. **Lifecycle / events / authorization / tenancy:** `StaffStatus`
   active ↔ suspended → terminated | retired | resigned (immutable);
   every mutation dispatches a domain event; permission-gated policy;
   tenant isolation applied at query level ("Multi_Tenant_Isolation" listed
   in related documents).
7. **Constraints potentially affecting SPR-014:** append-only journal +
   actor traceability pattern; "history must never be silently overwritten".
   Nothing else lifecycle-generic.

#### ADR-012 — Academic Communication Engine Architecture

1. **Status:** Accepted.
2. **Date:** 2026-08-04.
3. **Decision:** Single `AcademicDocument` aggregate root with immutable
   version rows, policy-driven visibility, notification-based publication,
   contracts instead of foreign-model imports.
4. **Architecture imposed:**
   - Permanent identity `{platform}-{year}-{school}-{type}-{seq}`
     (e.g. `NS-2027-SCH001-RC-000000145`), assigned once, survives corrections.
   - `DocumentStatus`: draft → generated → reviewed → approved → published →
     archived (approval skippable); published/archived immutable.
   - Corrections go through `RegenerateDocumentAction` → new immutable version,
     new official file, `GeneratedFile.is_current` flipped; publication records
     never mutated; re-delivery creates a new `PublicationRecord`.
   - Business data enters only via Domain Contracts (10 listed) + adapters.
   - Visibility via `PublicationPolicy` / `VisibilityPolicy` per `VisibilityScope`.
   - Delivery requires ≥ 1 completed channel or exception; channels declared in
     `config/nexus-academic-communication.php` (5 delivered; email/sms/push
     declared but un-delivered).
   - Authorization: `HandlesDocumentAuthorization`, 7 capabilities
     (`document.view/generate/review/approve/publish/archive/template.manage`),
     capability-based, `super_admin` bypass, school-scoped checks.
   - Deletion disabled on the aggregate; append-only `document_audits` journal.
5. **Bounded contexts concerned:** AcademicCommunication as the single
   publication layer for Report Card, Class Council, Attendance, Transcript…
6. **Lifecycle / events / authorization / tenancy:** forward-only status
   machine owned by `DocumentStatus`; audit journal; capability-based
   authorization; school scoping on every check.
7. **Constraints potentially affecting SPR-014:** **this ADR already owns
   document identity, version immutability, publication and archiving.**
   "New document features must preserve the forward-only lifecycle, version
   immutability and the contract boundary." Any RecordsLifecycle that
   re-implements these would violate this decision.

#### ADR-013 — Class Council Engine Architecture

1. **Status:** Accepted.
2. **Date:** 2026-08-06.
3. **Decision:** Single `CouncilSession` aggregate root, forward-only
   lifecycle, application-owned automatic decision rule, contracts instead of
   foreign-model imports for every consumer.
4. **Architecture imposed:**
   - `CouncilSessionStatus`: draft → convened → published → archived;
     published/archived immutable; only convened sessions accept decisions /
     deliberations (`isDeliberating()`).
   - Decision code `{session.reference}-%03d`, unique per school — permanent
     reference usable on bulletins and minutes.
   - Override ⇒ justification + approval + snapshot of overridden automatic
     decision; `OverrideApprovalRequiredException` otherwise.
   - Confidentiality invariant: `CouncilDeliberation` never exposed through a
     contract, can never reach a rendered document.
   - Consumers read only Published/Archived decisions through contracts:
     Report Card via `decisionsForStudent(...)`, Academic Communication via
     `publishedDecisions(...)`.
   - Authorization: `HandlesClassCouncilAuthorization`, 8 capabilities
     (`class-council.view/create/update/convene/decide/publish/archive/delete`),
     capability-based, `super_admin` bypass, school-scoped.
   - Deletion disabled on the aggregate; `council_*` tables + append-only
     audit journal (`ClassCouncilAuditLogger`).
5. **Bounded contexts concerned:** ClassCouncil (owner); ReportCard and
   AcademicCommunication (read-only consumers); future transcript/certificate
   engines will consume through the same contract boundary.
6. **Lifecycle / events / authorization / tenancy:** transition guard in
   `CouncilSessionStatus::allowsTransitionFrom()`; events + audit logger;
   capability-based policy; school scoping everywhere.
7. **Constraints potentially affecting SPR-014:** academic validation is
   exclusively Class Council's; downstream engines must never recompute a
   council outcome; consumers depend only on the contract surface.

### B. Shared Kernel findings

`app/Domains/Shared/` (complete inventory, git grep verified):

| Element | Kind | Real usage |
|---------|------|-----------|
| `ActiveArchivedStatus` | enum (active/archived + label/color) | Delegated to by 12 deprecated per-domain status enums (AcademicClass, Classroom, Department, GradeLevel, Guardian ×2, Program, Section, Subject, SubjectCategory, SubjectCoefficient). Each wrapper carries the comment "Use App\Domains\Shared\Enums\ActiveArchivedStatus in new code." |
| `DraftActiveArchivedStatus` | enum (draft/active/archived + label/color) | Used by `CalendarYearStatus`, `EntityStatus` (Academic), `AcademicTermStatus`, `AcademicYearStatus`. Same delegation comment. |
| `UniqueValidator` | trait | **Zero usages found outside its own file** (`validateUniquePerSchool`, `validateUniquePerParent`). Dead code today. |
| `HasEntityExtraction` | trait (reflection-based entity extraction from events) | Used by listeners of Ai (5), Communication (1), System (5), Workflow (4). |

Reusability assessment for SPR-014:

- The two shared enums cover only trivial active/archived flags. They are
  **too poor** for any of the three real lifecycles audited below
  (council/report-card/document), which each own a dedicated state machine
  with transitions and immutability rules.
- `HasEntityExtraction` is listener plumbing, unrelated to records lifecycle.
- `UniqueValidator` unused — proves the kernel is not currently the place
  where cross-domain invariants actually live.
- Per instruction: **no new enum was created**, none proposed as implementation.

Conclusion: there is **no existing shared lifecycle abstraction** in the
kernel, and the kernel shows no evidence it is meant to host state machines.

### C. Tenancy / School ownership findings

The official pattern, proven by grep across all domains:

1. **Trait actually used:** `App\Traits\HasSchoolId`
   (`app/Traits/HasSchoolId.php`) — used by **80 model files**.
   - Boots `school_id` on creating: `tenant()?->id ?? School::first()?->id`.
   - Provides `scopeForSchool($query, $schoolId)` → `where('school_id', …)`.
2. **Interfaces actually used:** none specific to tenancy; scoping is a
   convention (trait + explicit `forSchool()` calls), not an interface.
3. **Migration pattern** (identical in council/report-card/document tables):
   ```php
   $table->string('school_id', 36);
   // ...
   $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
   $table->unique(['school_id', ...]);      // school-scoped uniqueness
   $table->index(['school_id', 'status']);  // school-scoped queries
   ```
4. **Model pattern:** `use HasSchoolId;` (+ `SoftDeletes`), casts of status
   enums, no global scope — isolation is enforced at call sites and policies.
5. **Policy pattern:** `Handles{Domain}Authorization` traits
   (ClassCouncil / ReportCard / AcademicCommunication / …): permission group +
   `$user->school_id === $model->school_id` + `hasRole('super_admin')`
   bypass; delete/restore/forceDelete return false.
6. **Filament query scoping:** `App\Traits\ScopedByTenant` overrides
   `getEloquentQuery()` adding `where('school_id', tenant()?->id)`
   (used by ~70 resources, legacy and domain).
7. **Tenancy runtime:** `stancl/tenancy ^3.10`;
   `InitializeTenancyForPanel` middleware registered in the 7 panel providers;
   `tenant()` helper used in resources.
8. **Test pattern:** `tests/Pest.php::makeTenant()` inserts a row into
   `tenants` and returns its id; tenant-isolation tests assert
   `forSchool($A)->count()==1 && forSchool($B)->count()==0`, cross-school FK
   rejection, per-school independence (e.g. numbering).

Two real examples:

- **CouncilSession** (`app/Domains/ClassCouncil/Infrastructure/Models/CouncilSession.php`):
  `use HasSchoolId` + migration `2026_08_06_000015` (FK → `tenants`,
  unique `(school_id, reference)`, index `(school_id, status)`);
  tests `CouncilSessionTenantIsolationTest` (3 tests incl. "a session refuses
  an academic frame owned by another tenant").
- **AcademicDocument** (`app/Domains/AcademicCommunication/Infrastructure/Models/AcademicDocument.php`):
  same trait; AC migrations FK → `tenants`;
  tests `AcademicCommunicationTenantIsolationTest` (6 tests incl. numbering
  independent per school, approval scoped to school A cannot act on school B).

(`ReportCardTenantIsolationTest` provides a third example: 4 tests.)

### D. ClassCouncil lifecycle map (code-proven)

**MODEL**

- `CouncilSession` — aggregate root (`council_sessions`)
- `CouncilDecision` — child (`council_decisions`)
- `CouncilDeliberation` — confidential child (`council_deliberations`)

**STATE**

- `CouncilSessionStatus` enum: `draft → convened → published → archived`
  - `allowsTransitionFrom()`: Convened←Draft, Published←Convened,
    Archived←Published; Draft terminal-false.
  - `isImmutable()` = Published | Archived.
  - `isDeliberating()` = Convened only.
- Catalogs: `CouncilDecisionType`, `CouncilDecisionCategory`.

**ACTIONS** (all via `ClassCouncilService` facade only)

- Create (`CreateCouncilSessionAction`) — draft over validated academic frame
- Convene (`ConveneCouncilSessionAction`) — builds review list from published standing
- RecordDecision (`RecordCouncilDecisionAction`) — convened only; sequential
  code `{reference}-%03d`; override ⇒ justification + approval + snapshot
- RecordDeliberation (`RecordCouncilDeliberationAction`) — convened only, confidential
- Publish (`PublishCouncilSessionAction`) — freezes session + decisions
- Archive (`ArchiveCouncilSessionAction`) — from published

**EVENTS** (`Domain/Events/*`, consumed by `ClassCouncilAuditLogger` → AuditLog)

- CouncilSessionCreated, CouncilSessionConvened, CouncilDecisionRecorded,
  CouncilSessionPublished (carries decisionsCount), CouncilSessionArchived

**POLICIES / AUTHORIZATION**

- `CouncilSessionPolicy` + `HandlesClassCouncilAuthorization`
  (group `class-council`): view/viewAny/create/update + convene/decide/
  publish/archive; delete/restore/forceDelete = false; school check +
  super_admin bypass.

**PERSISTENCE**

- 3 migrations `2026_08_06_000015..000017`.
- `council_sessions`: ulid uuid nullable, school_id(36) FK tenants cascade,
  year/term/class FK cascade, section nullable, reference(40)
  **unique(school_id, reference)**, status default draft, held_at/published_at/
  archived_at, created_by FK users nullOnDelete, timestamps + softDeletes.
- `council_decisions`: **unique(school_id, code)**; override snapshot columns
  (automatic_decision_type/label, justification, approved_by/at, recorded_by).
- `council_deliberations`: member/topic/summary/noted_at, recorded_by.
- **No version field anywhere** in the three tables.

**TESTS**

- Unit: architecture test, `CouncilSessionStatusTest` (transitions),
  `AutomaticDecisionServiceTest`.
- Feature: `CouncilSessionLifecycleTest` (14 tests: frame validation,
  cross-school refusal, review list, decision code sequence, override rules,
  deliberation confidentiality, publish freeze, archive, invalid transitions,
  audit trail), `CouncilSessionPolicyTest`, `CouncilSessionTenantIsolationTest`
  (3 tests), `CouncilContractsIntegrationTest`.

**ARTEFACT OFFICIEL PRODUIT PAR CLASSCOUNCIL**

> L'artefact officiel n'est **pas** le `CouncilSession` lui-même : la session
> est l'enveloppe process (aggregate). L'artefact académique officiel est le
> **`CouncilDecision` publié** — porteur du code permanent
> `{session.reference}-%03d` unique par école, exposé vers l'extérieur
> uniquement après publication de la session, et uniquement via les contrats
> (`decisionsForStudent`, `publishedDecisions`). La délibération ne sort
> jamais du domaine (invariant ADR-013). C'est donc le couple
> *session publiée → décisions publiées* qui serait enveloppable ; la seule
> unité adressable et référençable est la décision publiée (code permanent),
> pas la session.

### E. ReportCard lifecycle map (code-proven)

**MODEL**: `ReportCard` (run, aggregate root) + `ReportCardEntry` (one line
per student of the run).

**STATE**

- `ReportCardStatus`: `draft → generated → published → archived`
  - Generated reachable from Draft **or** Generated **or** Published
    (regeneration); Published ← Generated; Archived ← Generated | Published.
  - `isImmutable()` = Published | Archived; `isRegenerable()` =
    Generated | Published.
- `ReportCardEntryStatus`: pending → included | excluded.

**ACTIONS** (via `ReportCardService` only)

Create, Generate (compose snapshots + delegate documents to AC),
Regenerate (audited re-compose), Publish (delivers every bulletin through the
communication engine), Archive.

**EVENTS**: Created, Generated, Published, Regenerated, Archived
(+ `ReportCardAuditLogger`).

**POLICIES / AUTHORIZATION**: `ReportCardPolicy` +
`HandlesReportCardAuthorization` (group `report-card`), same shape as council.

**PERSISTENCE**

- 2 migrations `2026_08_06_000013..000014`.
- `report_cards`: uuid ulid nullable, school FK tenants cascade,
  year/term/class/section, report_card_type, status, created_by,
  generated_at/published_at/archived_at, timestamps + softDeletes;
  **unique scope (school_id, year_id, term_id, class_id, section_id)**.
- `report_card_entries`: content JSON snapshot,
  `academic_document_id` + `academic_document_number` (nullable, FK
  academic_documents nullOnDelete), unique(run, enrollment).
- **No version field on the run itself.**

**TESTS**: unit (architecture, `ReportCardStatusTest`,
`ReportCardContentComposerTest`); feature lifecycle (9 tests incl.
scope uniqueness, delegation to communication engine, regeneration of a
published run), policy test, tenant isolation (4 tests).

**RÉPONSES**

- Artefact officiel : le **run de bulletins** (`ReportCard`) comme agrégat ;
  mais l'artefact documentaire officiel par élève est l'`AcademicDocument`
  créé via `AcademicCommunicationContract::generateReportCardDocument`
  (le moteur ne rend jamais son propre PDF).
- Identifiant : run = uuid ULID + portée unique (école, année, terme,
  classe, section) ; pas de numéro permanent propre au run. Le numéro
  permanent du bulletin est le `document_number` d'Academic Communication,
  recopié sur chaque entry (`academic_document_number`).
- Versionnement : **le run n'est pas versionné**. Regenerate recompose les
  entries/documents sous la même identité de run. Le vrai versionnage vit
  dans AC (`version_number` + `content_hash`).
- Publication : `status = 'published'` + `published_at` non nul ; côté
  document officiel, publication réelle par AC (canaux + versions).
- Archivage : `status = 'archived'` + `archived_at` non nul.

### F. AcademicCommunication lifecycle map (code-proven)

**MODEL**: `AcademicDocument` (aggregate root) + `AcademicDocumentVersion`
(immutables) + GeneratedFile, DocumentApproval, DocumentSignature,
DocumentRecipient, PublicationRecord, PublicationDeliveryAttempt,
DocumentAudit.

**STATE**: `DocumentStatus`:
`draft → generated → reviewed → approved → published → archived`
(approval skippable; Published reachable from Draft/Generated/Reviewed/
Approved; Archived from tout sauf Draft/Archived).
`ApprovalStatus` (pending → approved | rejected), `PublicationStatus`,
`DocumentType`, `DocumentChannel`, `VisibilityScope`, `DocumentOrigin`.

**ACTIONS** (12, via `AcademicCommunicationService` only):
generate + 6 générateurs typés (report card, council minutes, parent/teacher/
student summary, chronicle), review, approve, publish, archive, regenerate.

**IDENTITY**: `DocumentNumber` VO —
`{platform}-{year}-{school}-{type}-{seq}` (ex. `NS-2027-SCH001-RC-000000145`),
assigné une fois, jamais modifié ; séquence comptée par école et par type.

**VERSIONING**: `AcademicDocumentVersion.version_number` (ordre desc pour
current), `content_hash` (anti-mutation silencieuse), `is_published` +
`published_at`. Regenerate ⇒ nouvelle version sous la même identité ;
`GeneratedFile.is_current` basculé pour tous les fichiers du document.

**EVENTS**: DocumentGenerated, DocumentPublished (document + version + actor),
DocumentVersionCreated, DocumentApproved, DocumentArchived,
PublicationCompleted, PublicationCancelled (+ `…AuditLogger` →
`document_audits` append-only).

**POLICIES / AUTHORIZATION**: 3 policies (document, template, publication
policy) + `HandlesDocumentAuthorization` — 7 capacités
(view/generate/review/approve/publish/archive/template.manage), même forme.

**TENANT**: `HasSchoolId`, FK tenants, numérotation indépendante par école
(testé).

**TESTS**: unit (architecture, DocumentStatus, PublicationDispatcher,
DocumentNumber VO, VisibilityPolicy VO) ; feature (lifecycle 18 tests :
identité permanente, PDF première version, séquence par type, review,
publication directe ou avec approbation, refus si aucune livraison complétée,
refus canal désactivé, rejet d'approbation hors rôle, archivage ; regenerate ;
visibility redaction ; adapters ; tenant isolation 6 tests).

**RÉPONSES**

- Artefact officiel : **`AcademicDocument`** (+ ses versions immuables).
- Identifiant : `document_number` permanent (format ci-dessus), assigné une
  fois à la génération, conservé lors des régénérations.
- Versionnement : lignes `AcademicDocumentVersion` immuables, `version_number`
  croissant, `content_hash`, une version publiée active ; correction = nouvelle
  version, jamais overwrite (testé dans `RegenerateDocumentTest`).
- Publié : `DocumentStatus::Published` + `published_at` +
  `AcademicDocumentVersion.is_published=true` + au moins une
  `PublicationRecord` complétée.
- Archivé : `DocumentStatus::Archived` + `archived_at` ; immutable.

---

## INFERRED

### G. Cross-domain comparison (only repository-proven values)

| Domaine | Artefact | Identité | Version | Publication | Archivage | Tenant | Events |
|---|---|---|---|---|---|---|---|
| ClassCouncil | Décision publiée (dans session publiée) | `decision.code` = `{reference}-%03d`, unique/école ; `session.reference` unique/école | Aucune (snapshot override figé ; pas de version rows) | `status=Published` (session) + `published_at` | `status=Archived` + `archived_at` | HasSchoolId + FK tenants + unique/école + policy school-check | Created, Convened, DecisionRecorded, Published, Archived (+audit logger) |
| ReportCard | Run de bulletins ; bulletin officiel = AcademicDocument délégué | Run : uuid ULID + portée unique (école, année, terme, classe, section) ; n° officiel = `academic_document_number` (AC) | Non versionné côté run ; regeneration recompose ; versions réelles chez AC | `status=Published` + `published_at` ; livraison effective par AC | `status=Archived` + `archived_at` | Idem + entries scoped au run school | Created, Generated, Published, Regenerated, Archived (+audit logger) |
| AcademicCommunication | AcademicDocument + versions immuables | `document_number` permanent `NS-{année}-{école}-{type}-{seq}` | `AcademicDocumentVersion.version_number` + `content_hash` + `is_published` | Status Published + version.is_published + PublicationRecord complétée | Status Archived + archived_at | Idem + numérotation indépendante/école | Generated, Published, VersionCreated, Approved, Archived, PublicationCompleted/Cancelled |

**Invariants communs réellement partagés (proven in all 3 domains):**

1. Enum de statut string, forward-only, avec `allowsTransitionFrom()` et
   `InvalidStatusTransitionException`.
2. `isImmutable()` = Published | Archived — identique dans les 3 moteurs.
3. Timestamps dédiés `published_at` / `archived_at` (+ generated/held/reviewed).
4. `HasSchoolId` + `school_id` FK → `tenants` + unicité contrainte par école.
5. Événements de domaine → logger d'audit → journal (audit_logs / council_* /
   document_audits).
6. Policy par groupe de permissions + school check + bypass `super_admin` ;
   delete/restore/forceDelete interdits ; SoftDeletes partout.
7. Façade service unique ; toute la logique de transition dans les Actions.
8. Consommation inter-domaines exclusivement par contrats/adapters
   (jamais d'import de modèle étranger).

**Répétition structurelle observée** : trois machines à états quasi
identiques écrites à la main (mêmes méthodes, mêmes formes), plus celles de
Gradebook/Assessment/etc. C'est la seule "généricité" que le repository
montre — une répétition de *forme*, pas un besoin partagé de *moteur*.

---

## NOT VERIFIED

- Toute notion de **retention policy** : aucun modèle, migration, config,
  enum ou action n'évoque la rétention (grep: 0 hit pertinent).
- Toute notion de **provenance mechanism** explicite : 0 hit.
- Le "permanent academic record of every learner" consolidé multi-années :
  aucun agrégat existant ne consolide la mémoire académique d'un apprenant
  (les bulletins sont des runs classe/terme ; les décisions sont par session ;
  les documents sont par type). Rien ne prouve qu'un consommateur actif en
  ait besoin aujourd'hui.
- Archive packages téléchargeables, quotas, archive cloud payante :
  listés comme *active design decisions / pending decisions* dans
  SESSION_HANDOFF §9-10 — décisions non prises (stockage objet non choisi).

---

## H. Actual architectural gap (question critique)

> « Quel problème concret n'est PAS correctement résolu par les domaines
> existants et justifie l'existence d'un RecordsLifecycle bounded context ? »

Réponse factuelle :

1. **Ce que les domaines existants couvrent déjà (et bien)** :
   identité permanente des documents, versionnage immuable, publication,
   archivage, immutabilité post-publication, audit, autorisation,
   isolation tenant. ADR-012 en fait même une règle : Academic Communication
   est LA couche de publication unique, et les documents publiés sont
   immutables. Un "RecordsLifecycle" qui redéfinirait ces invariants
   violerait frontalement ADR-012/ADR-013 et la doctrine
   "Business Engines never generate PDFs / single publication layer".

2. **Ce qui n'est PAS résolu aujourd'hui (gap réel mais étroit)** :
   - politiques de rétention (durées, purge légale) — absent du code ;
   - préservation long terme / export d'archive école — absent du code et,
     de surcroît, dépendant de décisions pendantes (provider de stockage
     objet non choisi) ;
   - consolidation optionnelle par apprenant — utile surtout à SPR-015
     (Certificate & Transcript), premier consommateur naturel.

3. **Ce qu'un "moteur de cycle de vie générique" apporterait de négatif** :
   centraliser les machines à états que trois ADR ont délibérément placées
   dans leurs domaines, casser la frontière contrat/adapter, dupliquer
   l'immutabilité déjà garantie par AC, et créer un 4e état-machine à
   synchroniser avec les 3 existants — sans un seul cas d'usage prouvé
   nécessitant ce couplage.

Le gap existe donc, mais il est beaucoup plus étroit qu'un bounded context
"Records Lifecycle Engine". Le périmètre roadmap ("permanent academic memory
for every learner", retention, archive cloud) est un objectif Phase 3 légitime,
pas un moteur de lifecycle transversal.

---

## PROPOSED

### I. SPR-014 proposed minimum scope (si le sprint est maintenu)

Uniquement si une décision d'architecture (ADR réel) valide le besoin :

1. **RetentionPolicy** : configuration par école (durées par type d'artefact)
   appliquée aux artefacts déjà publiés/archivés des moteurs existants —
   lecture via les contrats existants, jamais en important leurs modèles.
2. **Archive/preservation** : export/paquet d'archive construit à partir des
   artefacts publiés via contrats — bloqué tant que le provider de stockage
   (SESSION_HANDOFF §10) n'est pas décidé.
3. **Record consolidé par apprenant** : à évaluer avec SPR-015
   (transcripts/certificates) comme premier consommateur — potentiellement
   simple vue/projection, pas un nouvel aggregate d'état.
4. **Interdit dans ce périmètre** : redéfinir statuts/publication/immutabilité
   (déjà propriété de ClassCouncil/ReportCard/AcademicCommunication), créer un
   état-machine parallèle, importer un modèle étranger.
5. Prérequis non négociables avant implémentation : contenu réel d'un ADR
   SPR-014, décision storage provider, définition du premier consommateur.

---

## FINAL VERDICT

## NEEDS REWORK

Justification :

- **Pas READY FOR IMPLEMENTATION** : aucun artefact cible défini, aucun ADR
  avec contenu, chevauchement non résolu avec ADR-012 (identité/version/
  publication/archive déjà possédés par AcademicCommunication), décisions
  amont pendantes (storage provider).
- **Pas "NOT JUSTIFIED BY CURRENT ARCHITECTURE"** stricto sensu : un gap réel
  et prouvé existe (rétention, préservation long terme, consolidation
  apprenant) — il n'est couvert par aucun domaine actuel.
- Mais le sprint tel qu'imaginé (un moteur de cycle de vie générique
  enveloppant les lifecycles existants) n'est pas soutenable : il dupliquerait
  trois machines à états conformes aux ADR et violerait la frontière contrat.
  **SPR-014 doit être re-spécifié sur le gap étroit (rétention + préservation,
  consommateur SPR-015), avec un vrai ADR, avant toute implémentation.**

Aucun code n'a été écrit ; aucun ADR créé/modifié ; CURRENT_POSITION,
PROJECT_STATE, CHANGELOG et SESSION_HANDOFF restent intacts.

# Todos
[✓] A. Lire ADR-006/012/013 intégralement
[✓] B. Auditer Shared Kernel + références git grep
[✓] C. Rechercher pattern tenancy/school ownership
[✓] D. Audit ClassCouncil (lifecycle map)
[✓] E. Audit ReportCard
[✓] F. Audit AcademicCommunication
[✓] G. Comparaison cross-domain
[✓] H. Gap architecturale critique
[✓] I. Compléter le rapport SPR-014
+ Thought: 1.4s
Audit terminé. Rapport écrit : docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md (aucun code, aucun ADR touché, autres docs gouvernance intacts).
1. ADR findings
- ADR-006 (Accepted, 2026-08-04) : Staff aggregate unique, journal append-only, StaffService facade, transitions hors modèle. Rien de générique réutilisable pour un lifecycle engine.
- ADR-012 (Accepted, 2026-08-04) : AcademicCommunication possède déjà identité permanente (NS-{year}-{school}-{type}-{seq}), versions immuables (content_hash), publication, archivage, contrats obligatoires. Contrainte bloquante : "single publication layer", documents publiés immuables.
- ADR-013 (Accepted, 2026-08-06) : CouncilSession forward-only, décisions publiées = vérité académique, délibérations confidentielles, consommation par contrats uniquement.
2. Shared Kernel findings
- ActiveArchivedStatus : délégué par 12 enums domaines (wrappers dépréciés). DraftActiveArchivedStatus : 4 enums Academic.
- UniqueValidator : 0 usage réel (code mort). HasEntityExtraction : plumbing listeners Ai/System/Workflow/Communication.
- Trop pauvres/spécifiques pour SPR-014 ; aucune abstraction lifecycle n'existe dans le kernel. Aucune nouvelle enum créée.
3. Tenancy findings
Pattern officiel : trait App\Traits\HasSchoolId (80 modèles, boot tenant()?->id, scopeForSchool) + migrations school_id string(36) FK → tenants cascade + unique(['school_id',…]) + policies Handles{Domain}Authorization ($user->school_id === $model->school_id, bypass super_admin) + Filament ScopedByTenant + stancl/tenancy v3 (InitializeTenancyForPanel). Tests : makeTenant() dans tests/Pest.php:42. Exemples réels : CouncilSession (+ CouncilSessionTenantIsolationTest), AcademicDocument (+ 6 tests isolation), ReportCard (4 tests).
4. ClassCouncil lifecycle
- State : draft → convened → published → archived (CouncilSessionStatus::allowsTransitionFrom, immutable Published|Archived).
- Actions (via ClassCouncilService) : Create, Convene, RecordDecision, RecordDeliberation, Publish, Archive.
- Events : Created, Convened, DecisionRecorded, Published, Archived → ClassCouncilAuditLogger.
- Policy : groupe class-council (8 caps), delete interdit. Migrations 000015-17 : FK tenants, unique (school_id, reference) et (school_id, code), softDeletes, aucun champ version.
- Tests : 14 lifecycle + policy + tenant isolation + contracts integration.
- Artefact officiel enveloppable : le CouncilDecision publié (code permanent {reference}-%03d), pas la session — la session est l'enveloppe process, la délibération ne sort jamais.
5. ReportCard lifecycle
- Artefact : run ReportCard (uuid ULID + portée unique école/année/terme/classe/section) ; bulletin officiel par élève = AcademicDocument délégué à AC via contrat (entry porte academic_document_number).
- State : draft → generated → published → archived + Regenerate auditable. Identifiant du run non permanent ; versionnement absent côté run (réel chez AC).
- Publié = status+published_at (livraison effective par AC) ; archivé = status+archived_at.
6. AcademicCommunication lifecycle
- Artefact : AcademicDocument ; identité = document_number permanent assigné une fois.
- Versionné par AcademicDocumentVersion.version_number + content_hash + is_published ; correction = nouvelle version (RegenerateDocumentAction).
- Publié = DocumentStatus::Published + version.is_published + PublicationRecord complétée ; archivé = Archived + archived_at. 12 actions, 7 caps, 12 migrations, tests exhaustifs.
7. Cross-domain comparison
Domaine	Artefact	Identité	Version	Publication	Archivage
ClassCouncil	Décision publiée	code {ref}-%03d /école	aucune	session Published + published_at	Archived + archived_at
ReportCard	Run (+docs délégués AC)	uuid + portée unique	non (run)	status+published_at via AC	status+archived_at
AcademicComm.	AcademicDocument+versions	document_number permanent	version_number+content_hash	Published+version.is_published+delivery	Archived+archived_at
Invariants communs prouvés : enum forward-only + allowsTransitionFrom, isImmutable()=Published|Archived, published_at/archived_at, HasSchoolId/FK tenants/unicité école, events→audit logger, policy par groupe + school check, delete interdit + softDeletes, façade service, contrats entre domaines. Seule répétition : la forme des 3 state machines, pas un besoin de moteur partagé.
8. Actual architectural gap
Déjà résolu ailleurs : identité, versionnage immuable, publication, archivage, audit, autorisation, tenancy (ADR-012 en fait une règle). Gap réel mais étroit et non couvert : retention policies (0 hit), préservation long terme/archive cloud (décisions pendantes §10 HANDOFF, storage provider non choisi), record consolidé par apprenant (utile surtout à SPR-015).
9. SPR-014 proposed minimum scope
Re-spécifié sur le gap uniquement : RetentionPolicy par école appliquée aux artefacts publiés via contrats existants ; export/préservation (bloqué tant que provider storage non décidé) ; projection apprenant à évaluer avec SPR-015 comme premier consommateur. Interdit : redéfinir statuts/publication/immutabilité, état-machine parallèle, import de modèle étranger. Prérequis : vrai ADR avec contenu + décision storage + consommateur identifié.
10. Final verdict
NEEDS REWORK
Pas READY (aucun artefact cible, chevauchement direct avec ADR-012, décisions amont pendantes) ; pas NOT JUSTIFIED stricto sensu (gap rétention/préservation prouvé et non couvert) — mais le sprint tel qu'envisagé (moteur générique enveloppant les lifecycles) dupliquerait trois machines conformes aux ADR et doit être re-spécifié avant toute implémentation.
