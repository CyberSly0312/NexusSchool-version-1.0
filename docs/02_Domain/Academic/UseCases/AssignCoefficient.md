# Assign Coefficient

## Module

Academic

---

# Use Case ID

UC-ACA-004

---

# Purpose

Permet à un administrateur académique d'affecter un coefficient à une matière en fonction du programme, du niveau, de la série et de l'année académique.

Cette opération garantit que les calculs de moyennes, de classements, de mentions et de promotions utilisent les règles pédagogiques officielles de l'établissement ou du ministère.

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Academic Manager
- Super Administrator

---

# Trigger

L'utilisateur sélectionne **Assign Coefficient** depuis le module Academic.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `subject-coefficient.assign`.
- Une année académique existe.
- La matière existe.
- Le niveau existe.
- La série existe si applicable.
- La matière est déjà affectée au niveau concerné.

---

# Main Flow

### Step 1

L'utilisateur sélectionne :

- Année académique
- Programme
- Niveau
- Série
- Matière

---

### Step 2

Le système affiche le coefficient actuel, s'il existe.

---

### Step 3

L'utilisateur saisit le nouveau coefficient.

---

### Step 4

Le système exécute le `CoefficientEngine`.

---

### Step 5

Le moteur vérifie :

- validité du coefficient
- cohérence avec le programme
- cohérence avec le niveau
- cohérence avec la série
- absence de doublon

---

### Step 6

Le système enregistre le coefficient.

---

### Step 7

Les calculs académiques sont automatiquement mis à jour.

---

### Step 8

Le cache des calculs est invalidé.

---

### Step 9

L'événement `SubjectCoefficientAssigned` est déclenché.

---

### Step 10

Le système confirme l'opération.

---

# Alternative Flows

## AF-01

Un coefficient existe déjà.

Résultat :

Le système propose de le remplacer.

---

## AF-02

Le coefficient est identique.

Résultat :

Aucune modification n'est effectuée.

---

## AF-03

La matière n'est pas affectée au niveau.

Résultat :

Le système refuse l'opération.

---

## AF-04

L'utilisateur annule.

Résultat :

Aucune donnée n'est enregistrée.

---

# Exception Flows

## EX-01

Coefficient invalide.

Résultat :

Le système refuse l'enregistrement.

---

## EX-02

Erreur de base de données.

Résultat :

Rollback complet.

---

# Business Rules

- Une matière ne possède qu'un seul coefficient actif par programme, niveau, série et année académique.
- Le coefficient doit être supérieur à zéro.
- Les coefficients doivent respecter le référentiel académique.
- Toute modification doit conserver un historique.
- Les nouveaux coefficients s'appliquent uniquement aux évaluations futures, sauf décision explicite de recalcul.

---

# Data Used

- AcademicYear
- AcademicProgram
- AcademicLevel
- AcademicSeries
- Subject
- SubjectCoefficient

---

# Services Used

- SubjectCoefficientService
- AuditLogService

---

# Engines Used

- CoefficientEngine
- AcademicValidationEngine

---

# Events Triggered

- SubjectCoefficientAssigned
- SubjectCoefficientUpdated

---

# Permissions Required

- subject-coefficient.assign

---

# Audit Log

Action enregistrée :

Assign Subject Coefficient

---

# Success Result

Le coefficient est enregistré et immédiatement disponible pour tous les calculs académiques.

---

# Postconditions

- La matière possède un coefficient valide.
- Les calculs de moyennes utilisent automatiquement cette nouvelle valeur.
- Les futurs examens et bulletins appliqueront ce coefficient.

---

# Related Use Cases

- Assign Subject
- Promote Student
- Create Academic Year
- Enroll Student