# Academic Structure Engine

## Module

Academic

---

# Purpose

Le `AcademicStructureEngine` est responsable de la création, de la validation et du maintien de la structure académique d'un établissement.

Il garantit que tous les éléments académiques (programmes, niveaux, séries, classes, sections et matières) sont organisés selon une hiérarchie cohérente et conforme aux règles de l'établissement ainsi qu'au système éducatif national.

Toutes les opérations impliquant l'organisation académique reposent sur ce moteur.

---

# Responsibilities

Le moteur est chargé de :

- Construire la structure académique complète.
- Vérifier l'intégrité des relations entre les entités.
- Contrôler la cohérence des niveaux scolaires.
- Valider les séries disponibles.
- Vérifier les dépendances entre programmes et classes.
- Empêcher les incohérences structurelles.
- Générer une vue hiérarchique de l'organisation académique.

---

# Inputs

## Academic Year

- active academic year

## Programs

- collège
- lycée
- enseignement technique
- autres programmes

## Grade Levels

- 6ème
- 5ème
- 4ème
- 3ème
- Seconde
- Première
- Terminale

## Series

- A
- C
- D
- F1
- F2
- G2
- etc.

## Classes

- nom
- capacité
- niveau
- série

## Sections

- A
- B
- C
- etc.

## Subjects

- matières
- coefficients
- programme concerné

---

# Outputs

Le moteur retourne notamment :

- structure académique validée
- arbre hiérarchique
- erreurs de structure
- conflits détectés
- recommandations d'organisation

---

# Core Operations

## Build Academic Structure

Construit automatiquement la hiérarchie complète :

Programme

↓

Niveau

↓

Série

↓

Classe

↓

Section

↓

Matières

---

## Validate Structure

Contrôle :

- relations invalides
- éléments manquants
- doublons
- dépendances

---

## Generate Academic Tree

Produit une représentation hiérarchique exploitable par :

- Dashboard
- API
- Rapports
- Interface Filament

---

## Verify Dependencies

S'assure que :

- une classe appartient à un niveau
- un niveau appartient à un programme
- une série est compatible avec le niveau
- une matière appartient au bon programme

---

## Detect Structural Conflicts

Détecte automatiquement :

- classes orphelines
- sections invalides
- matières sans niveau
- séries incompatibles

---

# Business Rules

## BR-001

Chaque programme possède au moins un niveau académique.

---

## BR-002

Chaque niveau appartient à un seul programme.

---

## BR-003

Une classe appartient obligatoirement à un niveau.

---

## BR-004

Une section appartient obligatoirement à une classe.

---

## BR-005

Une matière ne peut être associée qu'aux niveaux compatibles.

---

## BR-006

Les séries ne peuvent exister que dans les niveaux autorisés.

---

## BR-007

Aucune boucle hiérarchique n'est autorisée.

---

## BR-008

Les codes des classes doivent être uniques dans une même année académique.

---

# Dependencies

Le moteur utilise :

- AcademicYear
- AcademicProgram
- AcademicLevel
- AcademicSeries
- SchoolClass
- Section
- Subject

---

# Used By

- Enrollment
- Timetable
- Attendance
- Examination
- Promotion
- Reports

---

# Events Triggered

- AcademicStructureBuilt
- AcademicStructureValidated
- AcademicStructureUpdated

---

# Possible Exceptions

- InvalidAcademicStructureException
- DuplicateClassException
- InvalidSeriesException
- MissingAcademicLevelException
- InvalidProgramHierarchyException

---

# Performance Considerations

Le moteur doit mettre en cache la structure académique afin d'éviter de reconstruire l'arborescence à chaque requête.

Toute modification d'un élément de la structure doit invalider automatiquement ce cache.

---

# Future Extensions

- Structures multi-campus
- Structures multi-pays
- Programmes universitaires
- Programmes techniques avancés
- Gestion des passerelles entre programmes
- Import automatique depuis les référentiels nationaux