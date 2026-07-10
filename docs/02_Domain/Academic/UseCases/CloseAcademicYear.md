# Close Academic Year

## Module

Academic

---

# Use Case ID

UC-ACA-002

---

# Purpose

Permet à un administrateur autorisé de clôturer officiellement une année académique.

Cette opération marque la fin de toutes les activités académiques liées à cette année scolaire et empêche toute nouvelle modification des données associées.

La clôture prépare également le système pour l'ouverture de la prochaine année académique.

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Super Administrator
- Academic Manager

---

# Trigger

L'utilisateur sélectionne **Close Academic Year** depuis le panneau d'administration.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `academic-year.close`.
- L'année académique existe.
- L'année académique est active.
- Toutes les périodes académiques sont terminées.
- Tous les examens sont finalisés.
- Toutes les notes sont publiées.
- Toutes les décisions de promotion sont validées.

---

# Main Flow

### Step 1

L'utilisateur sélectionne l'année académique active.

---

### Step 2

Le système affiche un résumé comprenant :

- périodes académiques
- nombre d'élèves
- promotions
- examens
- statistiques

---

### Step 3

L'utilisateur confirme la clôture.

---

### Step 4

Le `AcademicValidationEngine` vérifie :

- examens terminés
- promotions validées
- aucune période encore ouverte
- aucune incohérence détectée

---

### Step 5

Le système verrouille l'année académique.

---

### Step 6

Toutes les modifications deviennent interdites.

---

### Step 7

Le statut de l'année devient :

Closed

---

### Step 8

Le système prépare automatiquement les données nécessaires à la prochaine année académique.

---

### Step 9

L'événement `AcademicYearClosed` est déclenché.

---

### Step 10

Le système confirme la clôture.

---

# Alternative Flows

## AF-01

Des périodes académiques sont encore ouvertes.

Résultat :

La clôture est refusée.

---

## AF-02

Des examens ne sont pas finalisés.

Résultat :

Le système affiche la liste des examens concernés.

---

## AF-03

Certaines promotions ne sont pas validées.

Résultat :

La clôture est bloquée jusqu'à validation.

---

## AF-04

L'utilisateur annule la procédure.

Résultat :

Aucune modification n'est effectuée.

---

# Exception Flows

## EX-01

Erreur de base de données.

Résultat :

Rollback complet.

---

## EX-02

Erreur de validation.

Résultat :

La clôture est annulée.

---

# Business Rules

- Une année clôturée devient en lecture seule.
- Les notes publiées ne peuvent plus être modifiées.
- Les promotions doivent être définitivement validées.
- Toutes les périodes doivent être fermées.
- Les données historiques doivent rester consultables.

---

# Data Used

- AcademicYear
- AcademicTerm
- Student
- Enrollment
- Examination
- Grade
- Promotion

---

# Services Used

- AcademicYearService
- PromotionService
- AuditLogService

---

# Engines Used

- AcademicValidationEngine
- PromotionEngine
- AcademicCalendarEngine

---

# Events Triggered

- AcademicYearClosed
- AcademicArchiveCreated

---

# Permissions Required

- academic-year.close

---

# Audit Log

Action enregistrée :

Close Academic Year

---

# Success Result

L'année académique est officiellement clôturée et verrouillée.

---

# Postconditions

- L'année académique est en statut **Closed**.
- Toutes les données deviennent historiques.
- Les nouvelles inscriptions utilisent la prochaine année académique.
- Les rapports restent disponibles en consultation.

---

# Related Use Cases

- Create Academic Year
- Promote Student
- Enroll Student
- Transfer Student