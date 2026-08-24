CONTINUATION — NEXUSSCHOOL SPR-014

Nous reprenons exactement l'état actuel du repository.

Ne recommence pas les travaux déjà validés.

SPR-014 — Records Lifecycle Management
Status actuel : IN PROGRESS

Déjà validé :
- RecordsLifecycle core
- state machine
- policy / authorization
- tenant isolation
- factory
- ReportCard integration
- AcademicCommunication integration
- targeted tests
- PHPStan = 0 errors
- Pint = PASS sur les fichiers concernés

Pattern architectural validé :

Source Domain
    ↓
RecordsLifecycleContract
    ↓
RecordsLifecycleAdapter
    ↓
RecordsLifecycleService
    ↓
RecordLifecycle

Les domaines sources ne doivent PAS importer directement le modèle
RecordLifecycle.

==================================================
OBJECTIF IMMÉDIAT
==================================================

Poursuivre SPR-014 avec l'intégration de Class Council.

Mais avant toute modification :

1. Vérifie l'état actuel du repository.
2. Lis le rapport actuel :
   docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md
3. Lis :
   CURRENT_POSITION.md
   PROJECT_STATE.md
   SESSION_HANDOFF.md
4. Inspecte réellement le bounded context ClassCouncil.

NE SUPPOSE AUCUN NOM DE CLASSE OU DE MÉTHODE.

==================================================
PHASE 1 — AUDIT CLASS COUNCIL
==================================================

Identifie précisément :

- ClassCouncilServiceProvider
- modèles
- actions
- services
- contrats
- adapters
- policies
- événements
- transitions d'état
- génération d'artefacts
- publication
- archivage
- version/document number
- academic context
- tenant scoping
- tests existants

Cherche notamment les opérations correspondant à :

Create / Generate
Publish
Archive
Approve
Finalize
Close

Détermine exactement quels événements/actions représentent le cycle de vie
réel d'un artefact de Class Council.

NE CODE RIEN avant d'avoir compris ce flux.

==================================================
PHASE 2 — COMPATIBILITÉ AVEC LE PATTERN EXISTANT
==================================================

Compare avec les intégrations déjà validées :

ReportCard
AcademicCommunication

Détermine :

- source_domain
- source_type
- source_id
- source_version
- academic_context
- origin_event

Le contrat doit rester minimal.

Ne crée pas d'abstraction générique supplémentaire simplement pour
"factoriser".

==================================================
PHASE 3 — IMPLÉMENTATION
==================================================

Si ClassCouncil produit bien un artefact entrant dans le scope SPR-014 :

Créer uniquement les composants nécessaires :

Domain/Contracts/RecordsLifecycleContract.php
Infrastructure/Contracts/RecordsLifecycleAdapter.php

ou réutiliser les structures existantes si elles sont déjà compatibles.

Puis :

- binding dans ClassCouncilServiceProvider
- registration au bon point de création/génération
- publication au vrai point de publication
- archivage au vrai point d'archivage

Le domaine ClassCouncil ne doit jamais dépendre directement de :

RecordLifecycle
RecordLifecycleState
RecordLifecycleService

Utiliser uniquement le Contract.

Respecter :

- tenant isolation
- authorization
- transactions
- provenance
- lifecycle transition rules
- bounded context boundaries

==================================================
PHASE 4 — TESTS CLASS COUNCIL
==================================================

Créer des tests d'intégration ciblés.

Tester au minimum :

1. création/génération → lifecycle Registered
2. publication → Published
3. archivage → Archived
4. tenant isolation
5. cross-school denial
6. provenance correcte
7. source_domain
8. source_type
9. source_id
10. source_version si applicable
11. academic_context
12. invalid lifecycle transition

Ajouter également un test architectural garantissant que ClassCouncil
n'importe pas directement RecordLifecycle si cette vérification est
compatible avec les conventions existantes.

==================================================
PHASE 5 — VALIDATION
==================================================

Exécuter réellement :

php artisan test tests/Unit/Domains/RecordsLifecycle
php artisan test tests/Feature/Domains/RecordsLifecycle

Puis les tests ClassCouncil concernés.

Puis :

php artisan test tests/Feature/Domains/ReportCard
php artisan test tests/Feature/Domains/AcademicCommunication

Puis :

vendor/bin/phpstan analyse

Puis Pint sur les fichiers touchés.

IMPORTANT :

Une commande interrompue, tuée ou timeoutée n'est PAS un succès.

Ne donne jamais un résultat supposé.

==================================================
PHASE 6 — RÉGRESSION COMPLÈTE
==================================================

Si toutes les suites ciblées passent :

php artisan test

Lancer la suite complète.

Si elle échoue :

- identifier exactement le test
- déterminer si la régression vient de SPR-014
- corriger uniquement si nécessaire
- sinon documenter le problème comme préexistant

Ne modifie pas les tests historiques uniquement pour obtenir du vert.

Ne modifie pas phpstan-baseline.neon pour masquer de nouvelles erreurs.

==================================================
PHASE 7 — DOCUMENTATION
==================================================

Après validation seulement, mettre à jour :

docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md
docs/docs/00_Project_Management/CURRENT_POSITION.md
docs/docs/00_Project_Management/PROJECT_STATE.md
docs/docs/00_Project_Management/CHANGELOG.md
docs/docs/SESSION_HANDOFF.md

Ajouter le journal de session selon la convention existante.

Vérifier qu'aucune ancienne information contradictoire ne reste.

==================================================
PHASE 8 — DÉCISION
==================================================

SPR-014 ne devient COMPLETE que si :

- RecordsLifecycle core validé
- ReportCard integration validée
- AcademicCommunication integration validée
- ClassCouncil integration validée si retenue dans le scope
- tests ciblés verts
- PHPStan vert
- Pint vert sur les fichiers touchés
- full suite exécutée
- aucune régression SPR-014
- documentation cohérente
- tenant isolation confirmée
- architecture boundaries respectées

Sinon :

SPR-014 reste IN PROGRESS.

==================================================
CONTRAINTES
==================================================

- Aucun commit.
- Ne pas restaurer le repository.
- Ne pas nettoyer les centaines de changements préexistants.
- Ne pas toucher aux domaines sans nécessité.
- Ne pas élargir le scope vers PDF, API, notifications, analytics,
  dashboard, transcript generation ou archive storage.
- Ne pas modifier le PHPStan baseline pour cacher des erreurs.
- Ne pas inventer de résultats.
- Distinguer clairement :
  CONFIRMED
  NOT VERIFIED
  PRE-EXISTING
  NEW
  OUT OF SCOPE

==================================================
FORMAT FINAL
==================================================

# SPR-014 CURRENT POSITION

Status:
Phase:
Current Slice:

## Class Council Integration
- ...

## Completed
- ...

## Tests
- RecordsLifecycle:
- ReportCard:
- AcademicCommunication:
- ClassCouncil:
- Full suite:

## PHPStan
- ...

## Pint
- ...

## Files Added
- ...

## Files Modified
- ...

## Confirmed
- ...

## Not Yet Verified
- ...

## Known Issues
- ...

## Remaining Work
- ...

## Final Verdict
COMPLETE / IN PROGRESS

COMMENCE PAR LA PHASE 1 :
AUDIT RÉEL DU BOUNDED CONTEXT CLASS COUNCIL.

NE MODIFIE AUCUN FICHIER AVANT CET AUDIT.
