PHASE 1 — ROADMAP / GOVERNANCE RECONCILIATION
Sources lus intégralement : MASTER_ROADMAP.md, CURRENT_POSITION.md, PROJECT_STATE.md, SESSION_HANDOFF.md, ARCHITECTURE_DECISIONS.md (§9-13), journaux 2026-08-05/06, et le code.
A. Objectif business original de SPR-014 (4 sources concordantes) :
MASTER_ROADMAP §4 Phase 3 : "Create a permanent academic memory for every learner".
SESSION_HANDOFF §3/§5/§6 : permanent academic record of every learner + retention policies + archive lifecycle + cloud archive integration.
Journal 2026-08-05 §Cloud Archive : archive packages téléchargeables, gestion du cycle d'archive par tenant, retention policies configurables, object storage cloud, expansion payante — "planned as a dedicated platform service rather than a simple file repository".
ARCHITECTURE_DECISIONS §12 Archive Rule : Active → Read Only → Archived → Cold Storage → Retention Policy → Permanent Preservation or Secure Destruction — règle gouvernante qui décrit un comportement des records, pas un bounded context.
B. Déjà résolu par les BC existants : identité documentaire permanente, versionnage immuable, publication multi-canaux, archivage, audit append-only, isolation tenant, contrats cross-domain — le tout prouvé dans AcademicCommunication (+ ClassCouncil, ReportCard). En prime : Enrollment possède déjà une histoire immuable par apprenant (EnrollmentHistory append-only, EnrollmentPromotion, EnrollmentTransfer, EnrollmentGraduation avec certificate_reference) et AC a déjà les types Transcript/Certificate.
C. Réellement non résolu : retention policy académique (le seul "retention" du repo = logs/backups dans config/system.php), cold storage / préservation long terme, export/paquet d'archive, intégration object storage.
D. Prérequis réels de SPR-015 : aucun de ces items. Voir Phase 3.
E. Décisions encore pendantes (HANDOFF §10) : provider object storage, email/SMS/push, signature numérique, services de vérification gouvernementale. S'y ajoute un fait technique : l'adaptateur S3 n'est pas installé (league/flysystem-aws-s3-v3 absent de composer.json).
F. SPR-014 doit-il rester un bounded context ? Non. Rien dans les ADR acceptés ni dans le code ne justifie un nouveau BC ; l'Archive Rule est une règle transversale déjà satisfaite en aval par AC.
PHASE 2 — REAL GAP ANALYSIS
Responsabilité candidate	Classification	Preuve
Document identity	OWNED BY EXISTING DOMAIN	AC DocumentNumber VO permanent
Document versioning	OWNED BY EXISTING DOMAIN	AC AcademicDocumentVersion.version_number + content_hash
Publication	OWNED BY EXISTING DOMAIN	AC publication pipeline, 5 canaux
Archival	OWNED BY EXISTING DOMAIN	DocumentStatus::Archived + archived_at dans les 3 moteurs
Audit	OWNED BY EXISTING DOMAIN	audit loggers → audit_logs / document_audits
Tenant isolation	OWNED BY EXISTING DOMAIN	HasSchoolId (80 modèles) + FK tenants + tests
Retention policy	UNSOLVED GAP (mais sans consommateur actuel)	0 hit académique ; seulement logs/backup
Legal retention / purge	FUTURE / DEFERRED	exigences légales non définies ; deletion interdite partout ; "Secure Destruction" sans cadre juridique
Long-term preservation	FUTURE / DEFERRED	storage provider non choisi (HANDOFF §10)
Archive export/package	FUTURE / DEFERRED	journal 2026-08-05 : "planned as a dedicated platform service" → roadmap Phase 9 Enterprise Services ("Cloud Archive")
Object storage	FUTURE / DEFERRED	disk configurable (NEXUS_DOCUMENT_DISK) mais adaptateur S3 non installé
Learner academic history	PARTIELLEMENT OWNED (Enrollment)	EnrollmentHistory append-only + promotions/transfers/graduations
Transcript projection	SPR-015 RESPONSIBILITY	agrégation multi-années possible via contrats existants
Certificate projection	SPR-015 RESPONSIBILITY	DocumentType::Certificate existe ; EnrollmentGraduation.certificate_reference existe
Cross-year academic record	UNSOLVED GAP mais SPR-015 RESPONSIBILITY en pratique	composables à la lecture via contrats
Record discovery/search	NOT JUSTIFIED aujourd'hui	aucun cas d'usage demandé
PHASE 3 — SPR-015 INVESTIGATION
Docs : aucune documentation dédiée SPR-015 n'existe (seulement MASTER_ROADMAP + HANDOFF). Pas d'ADR transcript/certificate.
Code existant prêt pour SPR-015 :
AC : DocumentType::Transcript (code TR) et Certificate (CE), pattern générateur éprouvé (generate + 6 générateurs typés), publication canaux.
ReportCard : snapshots par élève (report_card_entries.content JSON) — matière première du relevé multi-termes.
ClassCouncil : decisionsForStudent(school, student, year, term) via contrat — décisions publiées.
Gradebook/Policies : contrats standing + règles.
Enrollment : EnrollmentGraduation (décision graduation + référence certificat).
Legacy (à ne PAS réutiliser tel quel) : App\Services\DocumentGenerationService génère déjà transcripts/certificats dans l'ancienne architecture via GeneratedDocument — c'est précisément ce que la Communication Rule interdit désormais hors AC.
Dependency statement :
"SPR-015 does NOT require SPR-014. SPR-015 requires only (1) the existing read-only contracts of Gradebook/ClassCouncil/ReportCard/AcademicPolicies to aggregate a learner's published history, and (2) new generator actions inside AcademicCommunication following its own established generator pattern. Retention and preservation are not inputs to transcript/certificate composition."
Le seul besoin "cross-year" se calcule à la lecture depuis les artefacts publiés existants ; aucune projection persistante n'est exigée par quoi que ce soit dans le repo.
PHASE 4 — STORAGE / PRESERVATION DECISION AUDIT
Capacité	État réel
Abstraction fichiers	Laravel Storage disks — existe
Disks déclarés	local, public, s3 (skeleton Laravel, env-driven)
Adaptateur S3 installé	NON (league/flysystem-aws-s3-v3 absent de composer.json)
Disk utilisé par AC	config('nexus-academic-communication.disk', env NEXUS_DOCUMENT_DISK, 'local') ; chaque GeneratedFile enregistre son disk + chemin
Provider externe choisi	NON — HANDOFF §10 "Cloud object storage provider" listé en Pending Decision
Backup	config/system.php backup disk local, schedulable
Export d'archive école	inexistant
Rétention académique	inexistante (rétention logs/activité uniquement)
Conclusion : la capacité de stockage passe déjà par l'abstraction Laravel ; changer de fournisseur = variable d'env + package composer + config, sans toucher aux domaines. Le choix du provider reste une décision dépendance non levée.
PHASE 5 — ARCHITECTURAL OPTIONS
Critère	A. Nouveau BC "Retention & Preservation"	B. Capacités dans les BC existants (AC surtout)	C. Reporter SPR-014 ; SPR-015 consomme les contrats existants
DDD fit	Moyen : domaine sans langage métier propre, agrégat artificiel	Bon : la rétention des documents appartient au propriétaire des documents	Bon : ne construit rien sans besoin prouvé
Compatibilité ADR	Risque : duplique ADR-012 (identity/publication/archive)	Compatible : prolonge ADR-012, respecte Archive Rule §12	Compatible (Contract Rule §4, Communication Rule §10)
Duplication	Élevé (2e couche lifecycle)	Faible	Nulle
Coupling	Élevé (contrats vers tous les moteurs)	Faible (interne AC)	Nul
Complexité	Élevée	Moyenne	Nulle
Utilité SPR-015	Faible (SPR-015 n'en dépend pas)	Faible à court terme	Maximale (démarre immédiatement)
Dépendance stockage	Bloquée (provider non choisi, package absent)	Partiellement bloquée (purge/export)	Aucune
Extensibilité future	Correcte	Bonne (Phase 9 Cloud Archive pourra envelopper AC)	Bonne (réévaluation à besoin prouvé)
RECOMMANDATION : OPTION C
Justification : aucune exigence actuelle ne demande la rétention/préservation ; la décision storage est pendante et l'adaptateur S3 n'est même pas installé ; SPR-015 démarre sans SPR-014 ; le roadmap place lui-même le Cloud Archive en Phase 9 Enterprise Services. Si plus tard une exigence de préservation devient prouvée (conformité, quotas payants, verification portal de SPR-016), la forme probable sera alors l'Option B (capacité interne AC + service plateforme Phase 9), consignée dans un vrai ADR — pas avant.
PHASE 6 — ADR REQUIREMENT
Un ADR existant peut-il couvrir ? Non — ADR-006/012/013 ne traitent ni rétention ni scoping de sprint.
Un nouvel ADR est-il requis aujourd'hui ? NON. Ne pas construire n'est pas une décision architecturale nouvelle : elle découle des règles existantes (§4 Contract, §10 Communication, §12 Archive, §13 Document). Un ADR ne devient nécessaire que si/d quand l'Option B sera implémentée (propriété rétention/préservation).
Décision que cet ADR futur enregistrait : ownership de la rétention et de la préservation par AcademicCommunication + service d'archive plateforme (Phase 9), déclenchées par exigence prouvée.
DRAFT (inline uniquement — aucun fichier créé, aucun numéro attribué) :
ADR-DRAFT — Retention & Preservation Ownership (conditional)
- Context: Phase 3 targets a permanent academic memory. Repository audit shows identity, versioning, publication, archival, audit and tenancy are owned by AcademicCommunication (ADR-012); council/report-card lifecycles by their domains (ADR-013). No academic retention mechanism exists; only operational log/backup retention (config/system.php). Storage provider undecided; no S3 adapter installed.
- Problem: Who owns retention policy, legal purge and long-term preservation of official records, without duplicating the publication layer?
- Existing architecture: Single publication layer (ADR-012); forward-only domain lifecycles (ADR-013); Archive Rule §12 defines record behavior, not ownership; Cloud Archive planned as platform service (roadmap Phase 9).
- Decision (when triggered): Retention & preservation capabilities live INSIDE AcademicCommunication (policy evaluation over documents it already owns), exposed to schools via configuration; physical cold-storage/archive packages delegated to a future platform service (Phase 9). No new RecordsLifecycle bounded context.
- Non-goals: any generic lifecycle engine; redefining statuses/publication/immutability; importing foreign models; deleting published records outside a legally-defined destruction process.
- Boundaries: consumers access artifacts only through existing contracts; retention acts on Published/Archived artifacts only.
- Consequences: one owner for document end-of-life; engines untouched; destruction requires a compliance framework first.
- Dependencies: storage provider decision; legal retention requirements; SPR-016 verification needs.
- Relation to ADR-012: extends it (end-of-life of documents it owns). Relation to ADR-013: untouched. Relation to SPR-015: none required — SPR-015 consumes existing contracts directly.
- Trigger condition: proven requirement (compliance obligation, paid archive feature, or SPR-016 verification guarantee) AND selected storage provider.
PHASE 7 — GOVERNANCE UPDATE PLAN (proposals only — nothing modified)
FILE	SECTION	CHANGE	REASON
SPR-014_Records_Lifecycle_Engine_Report.md	Append "Architectural Decision Brief — 2026-08-24 (Phase 1)"	Add gap classification, SPR-015 dependency statement, storage audit, Option C recommendation, conditional ADR draft reference	Keep single source of truth for the sprint rework
CURRENT_POSITION.md	§2 Current Sprint / §5 Next Planned Phase / §9 Immediate Priorities	Sprint: "SPR-014 — Deferred pending architectural need"; next sprint: SPR-015; Priority 1 → Certificate & Transcript Engine	Verdict NEEDS REWORK → deferred; repository is source of truth
PROJECT_STATE.md	§5 Domain Status row "Records Lifecycle 🟡 In Progress"	Change to "⏳ Deferred — gap covered by existing domains; revisit with ADR"	Domain Status currently claims work that does not exist
PROJECT_STATE.md	§8 Current Engineering Priorities	Reorder: Priority 1 = Certificate & Transcript Engine; Records Lifecycle moved to "Deferred"	Align priorities with decision
SESSION_HANDOFF.md	§3 Current Sprint Objective / §5 WIP / §6 Next Sprint	Replace SPR-014 objective with SPR-015 kickoff context; note SPR-014 deferral rationale + report link	Handoff must reflect reality before next session
CHANGELOG.md	New dated entry under current version	"SPR-014 deferred after architecture audit (report ref); no code produced; SPR-015 becomes active sprint"	Changelog captures architectural evolution
MASTER_ROADMAP.md	§4 Phase 3 sprint list	Annotate SPR-014 as "Deferred — absorbed into SPR-015 prerequisites & Phase 9 Enterprise Services (Cloud Archive)"	Roadmap maintenance clause §8
New ADR	—	None created now; conditional draft stored in SPR-014 report	No architectural change made yet
SPR-014 ARCHITECTURAL DECISION BRIEF
1. Original intent — Permanent academic memory per learner: retention policies, archive lifecycle, cloud preservation (ROADMAP Phase 3; HANDOFF §3; Journal 2026-08-05).
2. What already exists — AC owns identity/versioning/publication/archival/audit/tenancy (ADR-012, code-proven); ClassCouncil & ReportCard own their forward-only lifecycles; Enrollment owns immutable learner timeline + graduations; DocumentType::Transcript/Certificate ready; storage behind Laravel disks (configurable).
3. Real architectural gap — Narrow and unsponsored: academic retention policy, legal purge framework, long-term preservation, archive export. All blocked on pending decisions (storage provider, compliance rules); zero current consumer.
4. SPR-015 dependency — None. "SPR-015 requires nothing from SPR-014 because transcripts/certificates are composed from existing published artifacts through existing contracts and rendered by AC's own generator pattern."
5. Storage dependency — Capability exists (Laravel disks; NEXUS_DOCUMENT_DISK); provider selection pending (HANDOFF §10) and S3 adapter not installed — hard prerequisite for any preservation work.
6. Options — A: new Retention & Preservation BC; B: retention inside existing boundaries (AC); C: defer SPR-014, SPR-015 consumes contracts directly.
7. Recommended option — Option C, with Option B as the expected shape when a proven trigger exists (compliance, paid archive, SPR-016 verification), recorded then in a real ADR.
8. New ADR required? — Not now. Deferral follows existing rules (§4/§10/§12/§13). Conditional draft prepared inline; to be filed only when preservation work is approved.
9. Proposed new SPR-014 scope — None as a bounded context. Sprint closed as Deferred: prerequisites absorbed into SPR-015 (learner projections via contracts) and Phase 9 (Cloud Archive service).
10. Explicit non-goals — No generic lifecycle abstraction; no RecordLifecycle models/state machines/migrations/factories/contracts/adapters/services/tests; no duplication of ADR-012 responsibilities; no deletion path for published records; no external provider choice in this phase.
11. Documentation files needing updates — SPR-014 report (append brief), CURRENT_POSITION, PROJECT_STATE (×2 sections), SESSION_HANDOFF, CHANGELOG, MASTER_ROADMAP annotation; no ADR file.
12. Exact next implementation step (NOT implemented) — Open SPR-015 discovery: define the transcript/certificate business rules and their contract reads (ReportCardContentComposer snapshots, ClassCouncilContract::decisionsForStudent, GradebookContract::studentStanding, AcademicPoliciesContract, EnrollmentGraduation), then add GenerateTranscriptAction / GenerateCertificateAction inside AcademicCommunication following its six-generator pattern — gated on governance docs being updated first.
STOP respected : aucun code, aucune migration, aucun modèle, aucun ADR fichier, aucun commit.
