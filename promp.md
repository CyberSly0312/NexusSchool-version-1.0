Continue l'audit exactement à partir de cette découverte.

IMPORTANT :
- Toujours aucune implémentation SPR-014.
- Ne crée aucun ADR.
- Ne remplis aucun ADR vide.
- Ne modifie pas les ADR existants.
- Nous sommes encore en Phase 0/1.
- Documente uniquement les conclusions dans le rapport SPR-014.

Tu as confirmé :

CONFIRMED
- 19 fichiers ADR existent sous docs/ADR/
- seuls ADR-006, ADR-012 et ADR-013 contiennent réellement du contenu
- les autres ADR sont des fichiers vides
- Shared Kernel contient actuellement :
  - HasEntityExtraction.php
  - UniqueValidator.php
  - ActiveArchivedStatus.php
  - DraftActiveArchivedStatus.php
- aucun RecordLifecycle n'existe dans le repository
- aucun provenance mechanism explicite n'a été trouvé jusqu'ici

Continue maintenant avec les étapes suivantes.

==================================================
A. ADR-006 / ADR-012 / ADR-013
==================================================

Lis intégralement :

docs/ADR/ADR-006.md
docs/ADR/ADR-012.md
docs/ADR/ADR-013.md

Pour chacun, extrais uniquement ce qui est réellement écrit :

1. Status
2. Date
3. Décision
4. Architecture imposée
5. Bounded contexts concernés
6. Lifecycle / events / authorization / tenancy concernés
7. Contraintes qui pourraient affecter SPR-014

Ne complète pas les informations manquantes par déduction.

==================================================
B. SHARED KERNEL
==================================================

Inspecte complètement :

app/Domains/Shared/

Pour :

ActiveArchivedStatus
DraftActiveArchivedStatus
HasEntityExtraction
UniqueValidator

détermine :

- où ils sont utilisés
- quels domaines les utilisent
- s'ils peuvent être réutilisés par SPR-014
- ou s'ils sont trop spécifiques

Cherche leurs références avec :

git grep

Ne crée aucune nouvelle enum.

==================================================
C. TENANCY / SCHOOL OWNERSHIP
==================================================

Fais maintenant une recherche réelle dans le repository pour identifier le
pattern officiel de tenant ownership.

Cherche notamment :

- school_id
- tenant_id
- SchoolOwned
- HasSchoolId
- ScopedByTenant
- BelongsToSchool
- InitializeTenancy
- tenant()

Puis identifie :

1. le trait réellement utilisé
2. les interfaces réellement utilisées
3. le pattern des migrations
4. le pattern des modèles
5. le pattern des policies
6. le pattern des tests cross-school

Donne au moins 2 exemples réels provenant de domaines existants.

==================================================
D. CLASS COUNCIL
==================================================

Commence maintenant l'audit détaillé de ClassCouncil.

Inspecte :

app/Domains/ClassCouncil/

et les tests correspondants.

Tu dois établir une lifecycle map basée uniquement sur le code réel.

Documente :

MODEL
- CouncilSession
- CouncilDecision
- CouncilDeliberation

STATE
- enum / status

ACTIONS
- Create
- Convene
- RecordDecision
- RecordDeliberation
- Publish
- Archive
- autres éventuelles

EVENTS
- Created
- Convened
- DecisionRecorded
- Published
- Archived
- autres

POLICIES / AUTHORIZATION
- permissions
- school isolation
- roles

PERSISTENCE
- migrations
- foreign keys
- version fields
- timestamps
- soft deletes éventuels

TESTS
- unit
- feature
- tenant isolation
- transitions
- authorization

Puis réponds à cette question :

"Quel est exactement l'artefact officiel produit par ClassCouncil que
RecordsLifecycle pourrait éventuellement envelopper ?"

Ne suppose pas que CouncilSession lui-même est cet artefact.

==================================================
E. REPORT CARD
==================================================

Ensuite inspecte ReportCard.

Cherche notamment :

- ReportCard model
- generation
- publication
- archive
- version
- document identity
- events
- policies
- authorization
- tenant ownership
- tests

Réponds :

Quel est l'artefact officiel ?

Quel est son identifiant ?

Comment est-il versionné ?

Comment sait-on qu'il est publié ?

Comment sait-on qu'il est archivé ?

==================================================
F. ACADEMIC COMMUNICATION
==================================================

Inspecte AcademicCommunication.

Cherche notamment :

- AcademicDocument
- DocumentType
- document number
- publication
- archive
- events
- policies
- tenant ownership

Réponds aux mêmes questions que pour ReportCard.

==================================================
G. CROSS-DOMAIN COMPARISON
==================================================

Construis une comparaison :

| Domaine | Artefact | Identité | Version | Publication | Archivage | Tenant | Events |
|---------|----------|----------|---------|-------------|-----------|--------|--------|

Remplis uniquement les informations prouvées par le repository.

Puis identifie les véritables invariants communs.

==================================================
H. QUESTION ARCHITECTURALE CRITIQUE
==================================================

Avant de proposer RecordsLifecycle, réponds :

"Quel problème concret n'est PAS correctement résolu par les domaines existants
et justifie l'existence d'un RecordsLifecycle bounded context ?"

Si la réponse est faible ou si les domaines existants couvrent déjà le besoin,
le verdict peut parfaitement être :

SPR-014 NOT JUSTIFIED BY CURRENT ARCHITECTURE

Nous ne sommes pas obligés de créer un nouveau bounded context simplement
parce qu'un sprint existe dans la roadmap.

==================================================
I. RAPPORT
==================================================

Continue/complète :

docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md

Le rapport doit garder ces catégories :

CONFIRMED
INFERRED
PROPOSED
NOT VERIFIED

Ajoute une section :

"Repository Reality Check — 2026-08-24"

et une section :

"ADR Availability"

qui précise que seuls ADR-006, ADR-012 et ADR-013 sont actuellement
non-vides.

NE PAS modifier :
- CURRENT_POSITION
- PROJECT_STATE
- CHANGELOG
- SESSION_HANDOFF

tant que le verdict architectural final n'est pas établi.

==================================================
FIN DE L'ÉTAPE
==================================================

À la fin, ne code toujours pas.

Retourne uniquement :

1. ADR findings
2. Shared Kernel findings
3. Tenancy findings
4. ClassCouncil lifecycle
5. ReportCard lifecycle
6. AcademicCommunication lifecycle
7. Cross-domain comparison
8. Actual architectural gap
9. SPR-014 proposed minimum scope
10. Final verdict

Verdict obligatoire :

READY FOR IMPLEMENTATION
ou
NEEDS REWORK
ou
SPR-014 NOT JUSTIFIED BY CURRENT ARCHITECTURE
