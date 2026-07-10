# Transfer Student

## Module

Academic

---

# Use Case ID

UC-ACA-006

---

# Purpose

Permet de transférer un élève d'une classe, d'une section, d'un niveau, d'une série ou d'un établissement vers un autre tout en conservant l'intégralité de son historique académique.

Le transfert garantit la continuité de la scolarité de l'élève sans perte de données.

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Academic Manager
- Admission Officer
- Super Administrator

---

# Trigger

L'utilisateur sélectionne **Transfer Student** depuis le dossier de l'élève.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `student.transfer`.
- L'élève possède une inscription académique active.
- La destination existe.
- La destination possède une capacité disponible.
- Le transfert est autorisé par les règles académiques.

---

# Main Flow

### Step 1

L'utilisateur sélectionne l'élève.

---

### Step 2

Le système affiche :

- Classe actuelle
- Niveau
- Série
- Section
- Programme
- Année académique

---

### Step 3

L'utilisateur sélectionne la destination :

- Nouvelle classe
- Nouvelle section
- Nouveau niveau
- Nouvelle série
- Nouvel établissement (si autorisé)

---

### Step 4

Le `AcademicValidationEngine` vérifie la cohérence académique.

---

### Step 5

Le `ClassCapacityEngine` vérifie les places disponibles.

---

### Step 6

Le système valide les conditions de transfert.

---

### Step 7

Le système met à jour :

- l'inscription académique
- la classe
- la section
- les effectifs

---

### Step 8

Le système conserve l'historique complet du transfert.

---

### Step 9

Les modules suivants sont automatiquement synchronisés :

- Attendance
- Timetable
- Examination
- Report Cards
- Student Portal

---

### Step 10

Le système enregistre un Audit Log.

---

### Step 11

L'événement `StudentTransferred` est déclenché.

---

### Step 12

Le système confirme le transfert.

---

# Alternative Flows

## AF-01

La classe de destination est complète.

Résultat :

Le système propose d'autres classes compatibles.

---

## AF-02

Le transfert est demandé vers un niveau incompatible.

Résultat :

Le transfert est refusé.

---

## AF-03

Le transfert concerne un autre établissement.

Résultat :

Le système applique la procédure de transfert inter-écoles.

---

## AF-04

L'utilisateur annule l'opération.

Résultat :

Aucune modification n'est enregistrée.

---

# Exception Flows

## EX-01

Erreur de validation.

Résultat :

Le transfert est annulé.

---

## EX-02

Erreur de base de données.

Résultat :

Rollback complet.

---

# Business Rules

- Un élève ne peut avoir qu'une seule inscription active.
- Le transfert conserve l'historique académique.
- Les notes déjà publiées restent associées à la classe d'origine.
- Les nouvelles présences utilisent la nouvelle classe.
- Les effectifs des deux classes sont automatiquement mis à jour.
- Les emplois du temps sont recalculés si nécessaire.

---

# Data Used

- Student
- Enrollment
- AcademicYear
- SchoolClass
- Section
- AcademicSeries
- AcademicLevel

---

# Services Used

- StudentTransferService
- EnrollmentService
- AuditLogService

---

# Engines Used

- AcademicValidationEngine
- ClassCapacityEngine
- AcademicStructureEngine

---

# Events Triggered

- StudentTransferred

---

# Permissions Required

- student.transfer

---

# Audit Log

Action enregistrée :

Transfer Student

---

# Success Result

L'élève est transféré avec succès vers sa nouvelle affectation académique tout en conservant son historique.

---

# Postconditions

- La nouvelle inscription est active.
- L'ancienne affectation est archivée.
- Les effectifs sont mis à jour.
- Les emplois du temps sont synchronisés.
- Les futurs examens utilisent la nouvelle affectation.
- L'historique des transferts est disponible.

---

# Related Use Cases

- Enroll Student
- Promote Student
- Assign Subject
- Create Academic Year