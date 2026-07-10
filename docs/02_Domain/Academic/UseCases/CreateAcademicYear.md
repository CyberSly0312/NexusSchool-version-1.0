# Create Academic Year

## Module

Academic

---

# Use Case ID

UC-ACA-001

---

# Purpose

Permet à un administrateur autorisé de créer une nouvelle année académique afin de préparer une nouvelle session scolaire.

Cette opération constitue le point de départ de toutes les activités académiques (inscriptions, emplois du temps, notes, examens, promotions, etc.).

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Super Administrator
- Academic Manager

---

# Trigger

L'utilisateur sélectionne **Create Academic Year** depuis le panneau d'administration.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `academic-year.create`.
- L'établissement existe.
- Aucune année active ne chevauche les dates proposées.

---

# Main Flow

### Step 1

L'utilisateur ouvre le formulaire de création.

---

### Step 2

Le système affiche les champs suivants :

- Name
- Code
- Start Date
- End Date
- Description
- Status

---

### Step 3

L'utilisateur renseigne les informations.

---

### Step 4

Le système valide les données.

---

### Step 5

Le `AcademicValidationEngine` vérifie :

- les dates
- les chevauchements
- les contraintes métier

---

### Step 6

Le système crée la nouvelle année académique.

---

### Step 7

Le système génère automatiquement :

- UUID
- timestamps
- audit log

---

### Step 8

L'événement `AcademicYearCreated` est déclenché.

---

### Step 9

Le système confirme la création.

---

# Alternative Flows

## AF-01

Une année académique existe déjà avec le même code.

Résultat :

Création refusée.

---

## AF-02

Les dates se chevauchent avec une autre année.

Résultat :

Le système demande de corriger les dates.

---

## AF-03

L'utilisateur annule l'opération.

Résultat :

Aucune donnée n'est enregistrée.

---

# Exception Flows

## EX-01

Erreur de base de données.

Résultat :

Rollback complet de la transaction.

---

## EX-02

Erreur de validation.

Résultat :

Les messages d'erreur sont affichés.

---

# Business Rules

- Une seule année peut être active simultanément.
- Le code doit être unique.
- La date de début doit être antérieure à la date de fin.
- Les périodes ne peuvent pas se chevaucher.

---

# Data Used

- School
- AcademicYear

---

# Services Used

- AcademicYearService
- AuditLogService

---

# Engines Used

- AcademicCalendarEngine
- AcademicValidationEngine

---

# Events Triggered

- AcademicYearCreated

---

# Permissions Required

- academic-year.create

---

# Audit Log

Action enregistrée :

Create Academic Year

---

# Success Result

Le système crée une nouvelle année académique valide.

---

# Postconditions

- L'année académique existe.
- Elle est disponible pour la création des périodes.
- Les autres modules peuvent l'utiliser.

---

# Related Use Cases

- Close Academic Year
- Create Academic Term
- Validate Academic Structure