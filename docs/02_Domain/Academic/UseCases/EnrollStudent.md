# Enroll Student

## Module

Academic

---

# Use Case ID

UC-ACA-005

---

# Purpose

Permet d'affecter un élève admis à une année académique, un programme, un niveau, une série, une classe et une section.

Ce cas d'utilisation intervient après la validation de l'admission de l'élève et prépare toutes les informations académiques nécessaires au déroulement de sa scolarité.

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Admission Officer
- Academic Manager
- Super Administrator

---

# Trigger

L'utilisateur sélectionne **Enroll Student** depuis le module Enrollment ou Academic.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `student.enroll`.
- L'élève existe.
- L'admission de l'élève est validée.
- Une année académique est active.
- La classe dispose de places disponibles.
- Les informations académiques sont correctement configurées.

---

# Main Flow

### Step 1

L'utilisateur sélectionne l'élève.

---

### Step 2

Le système affiche :

- Programme
- Niveau
- Série
- Classe
- Section
- Année académique

---

### Step 3

L'utilisateur choisit l'affectation académique.

---

### Step 4

Le `ClassCapacityEngine` vérifie les places disponibles.

---

### Step 5

Le `AcademicValidationEngine` vérifie la cohérence de la structure académique.

---

### Step 6

Le système crée l'inscription académique.

---

### Step 7

Le compteur d'effectif de la classe est mis à jour.

---

### Step 8

Les modules suivants sont automatiquement préparés :

- Attendance
- Timetable
- Examination
- Report Cards
- Student Portal

---

### Step 9

Le système enregistre un Audit Log.

---

### Step 10

L'événement `StudentEnrolled` est déclenché.

---

### Step 11

Le système confirme l'inscription.

---

# Alternative Flows

## AF-01

La classe est complète.

Résultat :

Le système propose une autre classe ou une autre section.

---

## AF-02

L'élève est déjà inscrit.

Résultat :

Le système refuse l'opération.

---

## AF-03

Aucune année académique active.

Résultat :

L'inscription est impossible.

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

L'inscription est annulée.

---

## EX-02

Erreur de base de données.

Résultat :

Rollback complet.

---

# Business Rules

- Un élève ne peut posséder qu'une seule inscription active par année académique.
- Une classe ne peut dépasser sa capacité maximale.
- L'année académique doit être active.
- Les informations académiques doivent être valides.
- L'inscription doit être historisée.

---

# Data Used

- Student
- AcademicYear
- AcademicProgram
- AcademicLevel
- AcademicSeries
- SchoolClass
- Section
- Enrollment

---

# Services Used

- EnrollmentService
- StudentService
- AuditLogService

---

# Engines Used

- AcademicValidationEngine
- ClassCapacityEngine
- AcademicStructureEngine

---

# Events Triggered

- StudentEnrolled

---

# Permissions Required

- student.enroll

---

# Audit Log

Action enregistrée :

Enroll Student

---

# Success Result

L'élève est officiellement inscrit dans sa nouvelle classe et peut participer à toutes les activités académiques.

---

# Postconditions

- L'élève appartient à une classe.
- Il apparaît dans les listes d'appel.
- Il apparaît dans les emplois du temps.
- Il peut recevoir des notes.
- Son bulletin pourra être généré.
- Son portail étudiant est activé.

---

# Related Use Cases

- Transfer Student
- Promote Student
- Assign Subject
- Assign Coefficient
- Create Academic Year