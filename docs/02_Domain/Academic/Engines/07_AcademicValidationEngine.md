# Academic Validation Engine

## Module

Academic

---

# Purpose

Le `AcademicValidationEngine` est responsable de la validation de l'ensemble des données académiques avant leur utilisation par les différents modules du système.

Il garantit l'intégrité, la cohérence et la conformité de la structure académique en appliquant les règles métier définies par l'établissement et les autorités éducatives.

Toutes les validations critiques du domaine académique passent par ce moteur.

---

# Responsibilities

Le moteur est chargé de :

- Valider les années académiques.
- Vérifier les périodes académiques.
- Contrôler les programmes.
- Vérifier les niveaux scolaires.
- Valider les séries.
- Vérifier les classes.
- Contrôler les sections.
- Vérifier les matières.
- Contrôler les coefficients.
- Détecter les incohérences.
- Vérifier les dépendances entre les entités.

---

# Inputs

## Academic Year

- année académique

## Academic Term

- trimestre
- semestre

## Academic Program

- programme

## Academic Level

- niveau

## Academic Series

- série

## School Class

- classe

## Section

- section

## Subject

- matière

## Subject Coefficient

- coefficient

---

# Outputs

Le moteur retourne :

- validation réussie
- liste des erreurs
- liste des avertissements
- recommandations
- rapport de validation

---

# Core Operations

## Validate Academic Year

Vérifie :

- dates
- chevauchements
- unicité
- statut

---

## Validate Academic Terms

Contrôle :

- continuité
- ordre chronologique
- appartenance à une année académique

---

## Validate Programs

Vérifie :

- programmes actifs
- cohérence
- doublons

---

## Validate Levels

Contrôle :

- rattachement à un programme
- ordre logique
- cohérence des niveaux

---

## Validate Series

Vérifie :

- compatibilité avec les niveaux
- compatibilité avec les programmes

---

## Validate Classes

Contrôle :

- niveau
- série
- capacité
- année académique

---

## Validate Sections

Vérifie :

- rattachement à une classe
- unicité
- capacité

---

## Validate Subjects

Contrôle :

- programme
- niveau
- coefficient
- statut

---

## Validate Subject Coefficients

Vérifie :

- coefficient positif
- conformité au référentiel
- absence de doublons

---

## Run Full Academic Audit

Exécute l'ensemble des validations afin de produire un rapport global sur l'état de la structure académique.

---

# Business Rules

## BR-001

Une seule année académique peut être active à la fois.

---

## BR-002

Chaque trimestre ou semestre doit appartenir à une seule année académique.

---

## BR-003

Chaque niveau doit appartenir à un programme existant.

---

## BR-004

Une série ne peut être créée que pour les niveaux autorisés.

---

## BR-005

Une classe doit appartenir à un niveau valide.

---

## BR-006

Chaque section doit appartenir à une classe existante.

---

## BR-007

Une matière doit posséder un coefficient valide.

---

## BR-008

Aucun code académique ne peut être dupliqué dans une même année académique.

---

## BR-009

Les entités archivées ne peuvent plus être modifiées.

---

## BR-010

Toutes les relations doivent respecter les contraintes de la structure académique.

---

# Validation Levels

## Critical

Empêche totalement l'opération.

Exemples :

- année inexistante
- classe invalide
- série incompatible

---

## Warning

Autorise l'opération mais signale un risque.

Exemples :

- coefficient inhabituel
- classe proche de sa capacité maximale

---

## Information

Simple message destiné aux administrateurs.

---

# Dependencies

Le moteur utilise :

- AcademicYear
- AcademicTerm
- AcademicProgram
- AcademicLevel
- AcademicSeries
- SchoolClass
- Section
- Subject
- SubjectCoefficient
- AcademicSettings

---

# Used By

- Enrollment
- Timetable
- Attendance
- Examination
- Promotion
- Report Cards
- Analytics
- Dashboard
- API

---

# Events Triggered

- AcademicValidationStarted
- AcademicValidationCompleted
- AcademicValidationFailed
- AcademicAuditGenerated

---

# Possible Exceptions

- AcademicValidationException
- InvalidAcademicYearException
- InvalidProgramException
- InvalidClassException
- InvalidSubjectException
- DuplicateAcademicCodeException
- InvalidRelationshipException

---

# Validation Report Structure

Le rapport comprend :

- nombre de contrôles exécutés
- validations réussies
- avertissements
- erreurs critiques
- recommandations
- score global de conformité

---

# Performance Considerations

Le moteur doit permettre une validation incrémentale afin de ne contrôler que les éléments modifiés lorsque cela est possible.

Les validations complètes doivent être exécutées en arrière-plan pour les établissements possédant un volume important de données.

---

# Future Extensions

- Validation multi-campus
- Validation multi-pays
- Validation des référentiels ministériels
- Validation assistée par Intelligence Artificielle
- Détection automatique des anomalies
- Audit académique planifié
- Score qualité des données académiques