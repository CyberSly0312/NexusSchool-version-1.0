# Promote Student

## Module

Academic

---

# Use Case ID

UC-ACA-007

---

# Purpose

Permet de promouvoir un élève vers le niveau académique supérieur après validation de ses résultats scolaires.

Le système applique automatiquement les règles pédagogiques de l'établissement afin de déterminer si l'élève est promu, redouble, est réorienté ou obtient son diplôme.

---

# Actors

## Primary Actor

- School Administrator

## Secondary Actors

- Academic Manager
- Examination Officer
- Super Administrator

---

# Trigger

L'utilisateur sélectionne **Promote Student** après la publication des résultats de fin d'année.

---

# Preconditions

- L'utilisateur est authentifié.
- L'utilisateur possède la permission `student.promote`.
- L'année académique est active.
- Toutes les notes sont publiées.
- Les moyennes générales sont calculées.
- Les décisions du conseil de classe sont validées.

---

# Main Flow

### Step 1

L'utilisateur sélectionne un élève ou une classe complète.

---

### Step 2

Le système récupère :

- les notes
- les moyennes
- les coefficients
- les absences
- les sanctions académiques
- les décisions éventuelles du conseil de classe

---

### Step 3

Le `PromotionEngine` analyse les résultats.

---

### Step 4

Le système applique les règles de promotion.

---

### Step 5

Le moteur détermine automatiquement l'une des décisions suivantes :

- Promoted
- Repeated
- Reoriented
- Graduated

---

### Step 6

Le système propose la décision à l'administrateur.

---

### Step 7

L'administrateur valide ou refuse la proposition.

---

### Step 8

Le système met à jour le statut académique de l'élève.

---

### Step 9

L'historique des promotions est enregistré.

---

### Step 10

Les modules dépendants sont synchronisés :

- Enrollment
- Timetable
- Attendance
- Report Cards
- Student Portal

---

### Step 11

L'événement `StudentPromoted` est déclenché.

---

### Step 12

Le système confirme l'opération.

---

# Alternative Flows

## AF-01

L'élève ne satisfait pas aux critères de promotion.

Résultat :

Le système propose un redoublement.

---

## AF-02

Une réorientation est nécessaire.

Résultat :

Le système propose une nouvelle série ou un nouveau programme.

---

## AF-03

L'élève termine son dernier niveau.

Résultat :

Le statut devient **Graduated**.

---

## AF-04

Le conseil de classe décide d'une exception.

Résultat :

La décision manuelle remplace celle du moteur tout en étant historisée.

---

## AF-05

L'utilisateur annule la procédure.

Résultat :

Aucune modification n'est enregistrée.

---

# Exception Flows

## EX-01

Des notes sont manquantes.

Résultat :

La promotion est impossible.

---

## EX-02

Les calculs académiques sont invalides.

Résultat :

Le système demande un recalcul.

---

## EX-03

Erreur de base de données.

Résultat :

Rollback complet.

---

# Business Rules

- Toutes les notes doivent être validées.
- Les calculs utilisent les coefficients officiels.
- Les décisions sont historisées.
- Une promotion ne peut être annulée qu'avec des droits spécifiques.
- Une promotion validée met automatiquement fin à l'inscription académique de l'année en cours.
- Un élève diplômé ne peut plus être promu.

---

# Data Used

- Student
- AcademicYear
- Enrollment
- Grade
- Subject
- SubjectCoefficient
- PromotionRule

---

# Services Used

- PromotionService
- StudentService
- AuditLogService

---

# Engines Used

- PromotionEngine
- CoefficientEngine
- AcademicValidationEngine

---

# Events Triggered

- StudentPromoted
- StudentRepeated
- StudentGraduated
- StudentReoriented

---

# Permissions Required

- student.promote

---

# Audit Log

Action enregistrée :

Promote Student

---

# Success Result

La décision académique est enregistrée et l'élève est prêt pour la prochaine étape de son parcours scolaire.

---

# Postconditions

- Le statut académique est mis à jour.
- L'historique de promotion est conservé.
- La prochaine inscription académique peut être préparée.
- Les statistiques académiques sont recalculées.
- Les bulletins définitifs sont disponibles.

---

# Related Use Cases

- Create Academic Year
- Close Academic Year
- Enroll Student
- Transfer Student
- Assign Coefficient