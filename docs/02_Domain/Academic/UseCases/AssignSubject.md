# Assign Subject

## Module

Academic

---

# Use Case ID

UC-ACA-003

---

# Purpose

Permet à un administrateur académique d'affecter une matière à un programme, un niveau, une série ou une classe pour une année académique donnée.

Cette opération définit quelles matières seront enseignées aux élèves et prépare les données nécessaires à la création des emplois du temps, des évaluations et des bulletins.

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Academic Manager
- Super Administrator

---

# Trigger

L'utilisateur sélectionne **Assign Subject** depuis le module Academic.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `subject.assign`.
- Une année académique est active.
- Le programme existe.
- Le niveau existe.
- La matière existe.
- La classe est active.

---

# Main Flow

### Step 1

L'utilisateur sélectionne :

- Année académique
- Programme
- Niveau
- Série (optionnelle)
- Classe

---

### Step 2

Le système affiche la liste des matières disponibles.

---

### Step 3

L'utilisateur sélectionne une ou plusieurs matières.

---

### Step 4

Le système exécute le `SubjectAssignmentEngine`.

---

### Step 5

Le moteur vérifie :

- compatibilité du programme
- compatibilité du niveau
- compatibilité de la série
- disponibilité de la matière
- absence de doublon

---

### Step 6

Le système crée les affectations.

---

### Step 7

Les matières deviennent disponibles pour :

- les emplois du temps
- les évaluations
- les bulletins
- les calculs académiques

---

### Step 8

Le système enregistre un audit.

---

### Step 9

L'événement `SubjectAssigned` est déclenché.

---

### Step 10

Le système confirme l'opération.

---

# Alternative Flows

## AF-01

La matière est déjà affectée.

Résultat :

Le système refuse le doublon.

---

## AF-02

La matière n'est pas autorisée pour ce niveau.

Résultat :

L'affectation est refusée.

---

## AF-03

La série sélectionnée est incompatible.

Résultat :

Le système affiche une erreur.

---

## AF-04

L'utilisateur annule.

Résultat :

Aucune donnée n'est enregistrée.

---

# Exception Flows

## EX-01

Erreur de validation.

Résultat :

L'opération est annulée.

---

## EX-02

Erreur de base de données.

Résultat :

Rollback complet.

---

# Business Rules

- Une matière ne peut être affectée qu'une seule fois à une classe pour une année académique.
- Une matière doit appartenir au programme concerné.
- Une matière doit être compatible avec le niveau.
- Une série ne peut recevoir que les matières autorisées.
- Une matière inactive ne peut pas être affectée.

---

# Data Used

- AcademicYear
- AcademicProgram
- AcademicLevel
- AcademicSeries
- SchoolClass
- Subject

---

# Services Used

- SubjectService
- AcademicYearService
- AuditLogService

---

# Engines Used

- SubjectAssignmentEngine
- AcademicValidationEngine

---

# Events Triggered

- SubjectAssigned

---

# Permissions Required

- subject.assign

---

# Audit Log

Action enregistrée :

Assign Subject

---

# Success Result

Les matières sont affectées avec succès et deviennent disponibles dans les autres modules.

---

# Postconditions

- Les matières sont liées à la classe.
- Les emplois du temps peuvent être créés.
- Les examens peuvent être configurés.
- Les enseignants pourront être affectés aux matières.

---

# Related Use Cases

- Assign Coefficient
- Enroll Student
- Promote Student
- Create Academic Year