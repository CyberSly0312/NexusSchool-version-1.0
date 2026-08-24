# NEXUSSCHOOL — SPR-014 RECOVERY / RESTART PROTOCOL

Tu travailles dans le repository :

C:\xampp\htdocs\NexusSchool-version-1.0

Nous devons reprendre le sprint SPR-014 — Records Lifecycle Management Engine.

IMPORTANT :
Le précédent workspace dans lequel SPR-014 avait été développé est irrécupérable.
Une vérification forensic complète a confirmé que les fichiers SPR-014 annoncés précédemment n'existent PAS dans ce repository et ne sont présents ni dans Git, ni dans les branches distantes, ni dans les worktrees/stashes locaux.

NE SUPPOSE DONC PAS QUE LE CODE SPR-014 EXISTE.

État confirmé :

- HEAD : ac8acc2
- branche : main
- working tree : clean
- remote main : 345153c
- aucun fichier RecordsLifecycle existant
- aucun RecordLifecycle.php
- aucun RecordLifecycleState.php
- aucun RecordLifecycleService.php
- aucune migration RecordsLifecycle
- aucune factory RecordsLifecycle
- aucun test RecordsLifecycle
- aucun rapport SPR-014 existant
- aucun commit contenant l'implémentation SPR-014
- aucune branche locale alternative
- aucun stash
- aucun worktree alternatif récupérable

Le travail SPR-014 précédent est donc considéré comme PERDU et ne doit PAS être présenté comme existant.

==================================================
OBJECTIF
==================================================

Nous allons maintenant RECONSTRUIRE SPR-014 proprement à partir du repository actuel.

Mais avant d'écrire du code, nous devons refaire l'audit architectural.

Le sprint doit rester fidèle à l'architecture NexusSchool existante.

SPR-014 doit être conçu comme :

"Thin provenance + lifecycle metadata envelope for official academic artifacts"

et NON comme :

- document generation engine
- PDF engine
- notification engine
- archive storage system
- transcript generator
- reporting/dashboard engine
- public API
- replacement for ReportCard
- replacement for AcademicCommunication
- replacement for ClassCouncil

==================================================
PHASE 0 — BASELINE ABSOLUE
==================================================

Avant toute modification :

1. Exécute :

git status --short
git log --oneline -10
git branch -a
git remote -v

2. Vérifie réellement l'arborescence :

app/Domains
tests/Unit/Domains
tests/Feature/Domains
database/migrations
database/factories
docs/docs/00_Project_Management
docs/docs/99_Project_Journal

3. Recherche explicitement :

RecordLifecycle
RecordsLifecycle
lifecycle
provenance
published_at
archived_at
SoftDeletes
SchoolOwned
HasSchoolId
ScopedByTenant

4. Lis les documents de gouvernance pertinents AVANT toute conception :

- architecture globale
- DDD / bounded contexts
- tenancy
- authorization
- coding standards
- testing standards
- sprint workflow
- ADRs
- CURRENT_POSITION
- PROJECT_STATE
- SESSION_HANDOFF
- MASTER_ROADMAP

5. Lis particulièrement les implémentations existantes de :

- ReportCard
- AcademicCommunication
- ClassCouncil
- AcademicPolicy
- Student
- Enrollment

Ne crée aucune abstraction avant d'avoir identifié les conventions réellement utilisées.

==================================================
PHASE 1 — AUDIT DES DOMAINES EXISTANTS
==================================================

Nous devons déterminer comment les domaines existants représentent :

- création
- publication
- archivage
- immutabilité
- version
- provenance
- tenant ownership
- authorization
- événements
- policies
- actions
- services
- contracts
- adapters

Commence obligatoirement par ClassCouncil car il existe déjà et constitue un exemple important de lifecycle académique.

Inspecte notamment :

- CouncilSession
- CouncilDecision
- CouncilDeliberation
- CouncilSessionStatus
- actions
- events
- listeners
- policies
- tests

Puis inspecte :

ReportCard
AcademicCommunication
AcademicPolicy

Ne modifie encore aucun fichier.

==================================================
PHASE 2 — GAP ANALYSIS
==================================================

À partir du code réel, produis une analyse :

A. Ce qui existe déjà et peut être réutilisé
B. Ce qui manque pour SPR-014
C. Ce qui serait une duplication dangereuse
D. Les frontières exactes entre RecordsLifecycle et les domaines producteurs
E. Les contrats nécessaires
F. Les transitions réellement nécessaires

Le résultat doit être enregistré dans :

docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md

Créer le fichier s'il n'existe pas.

Le rapport doit explicitement indiquer :

STATUS: RECONSTRUCTION FROM SCRATCH

et non "recovered", "already implemented" ou "previously validated".

==================================================
PHASE 3 — SCOPE INITIAL
==================================================

La première implémentation doit rester minimale.

RecordsLifecycle doit fournir seulement :

- provenance
- source_domain
- source_type
- source_id
- source_version
- school_id
- academic_context
- lifecycle state
- lifecycle transitions
- authorization
- tenant isolation
- immutable terminal states

États initiaux envisagés :

Registered
Published
Archived

Mais NE LES IMPOSE PAS aveuglément.

Vérifie d'abord les lifecycle semantics existantes dans ReportCard,
AcademicCommunication et ClassCouncil.

Si une autre nomenclature est nécessaire pour rester cohérent avec NexusSchool,
documente la décision dans le rapport avant implémentation.

==================================================
PHASE 4 — CONTRACT BOUNDARIES
==================================================

Le domaine RecordsLifecycle ne doit PAS importer directement :

- ReportCard models
- AcademicCommunication models
- ClassCouncil models

Les domaines producteurs ne doivent PAS manipuler directement le modèle
RecordLifecycle.

Utiliser des contracts/adapters si l'architecture actuelle les justifie.

Définir explicitement :

Producer Domain
    ↓
Domain Contract
    ↓
Adapter
    ↓
RecordsLifecycle Service
    ↓
RecordLifecycle persistence

Mais seulement si cette architecture correspond réellement aux conventions
existantes du repository.

Ne crée pas d'architecture "parfaite" théorique inutile.

==================================================
PHASE 5 — IMPLEMENTATION
==================================================

Une fois les phases 0-4 terminées et documentées, implémente le premier slice.

Structure attendue à adapter aux conventions réelles :

app/Domains/RecordsLifecycle/

    Domain/
        Enums/
        Contracts/

    Application/
        DTOs/
        Actions/
        Services/

    Infrastructure/
        Models/
        Policies/

    RecordsLifecycleServiceProvider.php

Puis :

database/migrations/
database/factories/RecordsLifecycle/

tests/Unit/Domains/RecordsLifecycle/
tests/Feature/Domains/RecordsLifecycle/

NE crée pas les dossiers/fichiers si les conventions du repository indiquent
une autre organisation.

==================================================
PHASE 6 — STATE MACHINE
==================================================

Créer des tests explicites pour TOUTES les transitions.

Si les états retenus sont :

Registered
Published
Archived

tester séparément :

Registered → Published       VALID
Registered → Archived        VALID
Published → Archived        VALID

et toutes les transitions invalides.

Tester particulièrement :

Archived → Registered
Archived → Published
Archived → Archived
Published → Registered
Published → Published
Registered → Registered

Les états terminaux doivent être immuables.

==================================================
PHASE 7 — SECURITY
==================================================

Tenant isolation obligatoire.

Tester :

- même school : autorisé
- autre school : refusé
- super admin : comportement conforme aux policies existantes
- accès direct à un lifecycle d'une autre école
- publication cross-school
- archivage cross-school
- suppression/restore/forceDelete selon les conventions du projet

Ne jamais introduire une nouvelle logique de tenancy si une convention
existante peut être réutilisée.

==================================================
PHASE 8 — INTEGRATION
==================================================

NE commence pas immédiatement par intégrer tous les domaines.

Après le core :

1. intégrer ReportCard
2. tester
3. intégrer AcademicCommunication
4. tester
5. évaluer ClassCouncil

ClassCouncil ne doit être intégré que si le modèle de provenance est réellement
compatible avec ses artifacts.

Ne force aucune intégration simplement pour "terminer le sprint".

==================================================
PHASE 9 — TESTS
==================================================

Après chaque incrément :

- tests unitaires ciblés
- tests feature ciblés
- tests du domaine producteur concerné
- PHPStan
- Pint

Puis seulement à la fin :

php artisan test

Si le full suite est lent, ne le tue pas arbitrairement.
Utilise des commandes ciblées pendant le développement puis exécute le full
suite avec un timeout raisonnable.

Aucun résultat ne doit être déclaré "PASS" sans sortie réelle.

==================================================
PHASE 10 — DOCUMENTATION
==================================================

Mettre à jour uniquement après validation réelle :

- SPR-014_Records_Lifecycle_Engine_Report.md
- CURRENT_POSITION.md
- PROJECT_STATE.md
- CHANGELOG.md
- SESSION_HANDOFF.md
- journal du jour

Chaque document doit refléter les résultats réellement observés.

Aucun chiffre inventé.

==================================================
RÈGLE CRITIQUE : GIT
==================================================

Le repository contient déjà un historique propre.

NE fais PAS de commit automatiquement.

NE fais PAS de reset.
NE fais PAS de rebase.
NE fais PAS de force push.
NE détruis aucune modification utilisateur.

Avant toute modification, afficher :

git status --short

À la fin, afficher :

git status --short

et classifier clairement :

1. fichiers créés pour SPR-014
2. fichiers modifiés pour SPR-014
3. fichiers préexistants modifiés par ailleurs
4. fichiers non liés

==================================================
RÈGLE CRITIQUE : PAS D'INVENTION
==================================================

Tu dois distinguer strictement :

CONFIRMED
INFERRED
PROPOSED
NOT VERIFIED

Si une information n'est pas démontrée par le repository ou par un test exécuté,
elle doit être marquée NOT VERIFIED.

Ne jamais écrire :

"already implemented"
"already validated"
"tests pass"

sans preuve présente dans cette session.

==================================================
POINT DE DÉPART
==================================================

Commence MAINTENANT par PHASE 0 et PHASE 1 uniquement.

NE CODE PAS ENCORE.

À la fin de Phase 1, fournis :

1. état réel du repository
2. architecture des bounded contexts pertinents
3. lifecycle patterns existants
4. ClassCouncil lifecycle détaillé
5. ReportCard lifecycle détaillé
6. AcademicCommunication lifecycle détaillé
7. éléments réutilisables
8. gaps exacts
9. proposition de scope SPR-014
10. fichiers qui seront créés/modifiés
11. risques architecturaux
12. verdict : READY FOR IMPLEMENTATION ou NEEDS REWORK

Puis arrête-toi.

Nous déciderons de la Phase 2 à partir de preuves réelles.
