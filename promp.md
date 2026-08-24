Continue exactement l’audit Phase 0 / Phase 1.

IMPORTANT :
- Aucun code ne doit être créé ou modifié pour l’instant.
- Aucun migration, test, factory ou documentation SPR-014 ne doit être créé.
- Nous sommes toujours en audit architectural.
- Tout doit être basé sur ce repository réel, pas sur les travaux SPR-014 perdus de l’ancien workspace.

Tu as déjà confirmé :
- HEAD ac8acc2
- main
- working tree clean
- aucun RecordLifecycle existant
- aucun provenance existant
- Shared kernel limité notamment à Shared/Enums et Shared/Traits
- SoftDeletes présent dans le repository
- school_id / tenant ownership présent dans les migrations
- ADR-006, ADR-012 et ADR-013 présents sous docs/ADR/

Poursuis maintenant avec les points suivants.

==================================================
1. GOUVERNANCE / ADR
==================================================

Lis intégralement les ADR pertinents, notamment :

- ADR-006
- ADR-012
- ADR-013
- tout autre ADR concernant :
  tenancy
  academic architecture
  lifecycle
  documents
  events
  authorization
  bounded contexts

Pour chaque ADR pertinent, indique :

- décision
- statut
- contraintes imposées
- impact potentiel sur SPR-014

Ne suppose jamais qu'un ADR est applicable simplement parce que son titre semble pertinent.

==================================================
2. SHARED KERNEL
==================================================

Inspecte réellement :

app/Domains/Shared/

notamment :

- Traits
- Enums
- éventuels contracts ou helpers

Identifie les mécanismes existants que SPR-014 devrait réutiliser.

==================================================
3. CLASS COUNCIL
==================================================

Audit détaillé de :

app/Domains/ClassCouncil/

Documente :

- models
- enums / state machine
- actions
- services
- contracts
- adapters
- policies
- events
- listeners
- observers
- migrations
- factories
- unit tests
- feature tests

Construis la lifecycle map réelle.

Exemple de format :

Draft
 ↓
Convened
 ↓
Published
 ↓
Archived

Mais utilise UNIQUEMENT les états réellement présents dans le code.

Identifie également les données qui pourraient constituer une provenance :

- school_id
- academic year
- term
- class
- section
- student
- staff
- session ID
- decision ID
- etc.

==================================================
4. REPORT CARD
==================================================

Même audit pour :

app/Domains/ReportCard/

Identifier précisément :

- création/génération
- publication
- archivage
- version
- document identity
- events
- policies
- authorization
- tenant isolation
- tests

Vérifie particulièrement s'il existe déjà une abstraction de document,
publication ou provenance qui pourrait rendre SPR-014 redondant.

==================================================
5. ACADEMIC COMMUNICATION
==================================================

Même audit pour :

app/Domains/AcademicCommunication/

Identifier :

- AcademicDocument
- DocumentType
- GenerateDocumentAction
- PublishDocumentAction
- ArchiveDocumentAction
- events
- policies
- version/document number
- tenant ownership
- tests

Détermine précisément quelles données peuvent servir de provenance.

==================================================
6. ACADEMIC POLICY
==================================================

Inspecte :

app/Domains/AcademicPolicy/

Détermine si AcademicPolicy doit :

- produire des records
- être référencé par RecordsLifecycle
- rester totalement indépendant

Ne crée aucune dépendance simplement parce qu'elle semble utile.

==================================================
7. CROSS-DOMAIN ANALYSIS
==================================================

À partir des quatre domaines :

ClassCouncil
ReportCard
AcademicCommunication
AcademicPolicy

détermine :

A. Qu'est-ce qu'ils ont réellement en commun ?
B. Qu'est-ce qui est différent ?
C. Quel problème architectural justifie réellement SPR-014 ?
D. Quel problème ne nécessite PAS SPR-014 ?
E. Quelle serait la plus petite abstraction commune viable ?
F. Où placer cette abstraction dans l'architecture existante ?

Très important :

Ne pars PAS de l'idée que RecordsLifecycle doit obligatoirement être une
nouvelle grosse bounded context.

Prouve d'abord que cette abstraction est nécessaire.

==================================================
8. PROPOSITION DE SCOPE
==================================================

À la fin de l'audit, propose :

SPR-014 V1

avec :

- objectif précis
- 1 à 3 use cases maximum
- états nécessaires
- provenance minimale
- contrats nécessaires
- producteurs concernés
- consommateurs concernés
- données persistées
- policies
- événements éventuels
- tests nécessaires

Puis :

NON-GOALS

avec tout ce qui doit explicitement rester hors scope.

==================================================
9. VERDICT
==================================================

Donne obligatoirement l'un des trois verdicts :

READY FOR IMPLEMENTATION

ou

NEEDS REWORK

ou

SPR-014 NOT JUSTIFIED BY CURRENT ARCHITECTURE

Si READY FOR IMPLEMENTATION :

donne l'ordre exact des fichiers à créer/modifier.

Si NEEDS REWORK :

explique précisément ce qui manque.

Si NOT JUSTIFIED :

explique quel mécanisme existant doit être utilisé à la place.

==================================================
10. RAPPORT
==================================================

Tu peux créer/modifier UNIQUEMENT le rapport d'audit :

docs/docs/00_Project_Management/SPR-014_Records_Lifecycle_Engine_Report.md

Si le fichier n'existe pas, crée-le.

Ce document doit commencer par :

SPR-014 STATUS: RECONSTRUCTION FROM SCRATCH

et distinguer clairement :

CONFIRMED
INFERRED
PROPOSED
NOT VERIFIED

Aucune implémentation ne doit être faite dans cette étape.

À la fin, arrête-toi et fournis le rapport Phase 0/1 complet.
