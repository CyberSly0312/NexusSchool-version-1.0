# AcademicYear ↔ AcademicTerm Relationship

## Module

Academic

---

# Relationship ID

REL-ACA-001

---

# Purpose

Décrit la relation entre une année académique et ses périodes académiques.

Une année académique constitue le conteneur principal de plusieurs périodes (semestres, trimestres ou quadrimestres).

Cette relation garantit que toutes les activités académiques sont correctement rattachées à une année scolaire.

---

# Relationship Type

One-To-Many (1:N)

AcademicYear

↓

AcademicTerm

---

# Cardinality

Un AcademicYear

↓

possède

↓

1..N AcademicTerms

Chaque AcademicTerm

↓

appartient à

↓

1 AcademicYear

---

# Entity Diagram

AcademicYear

│

├── Term 1

├── Term 2

└── Term 3

---

# Foreign Keys

AcademicTerm

academic_year_id

↓

AcademicYear.id

---

# Database Constraints

- academic_year_id est obligatoire.
- Une période ne peut exister sans année académique.
- Une année peut posséder plusieurs périodes.

---

# Business Rules

## BR-001

Une période appartient à une seule année académique.

---

## BR-002

Toutes les périodes doivent être incluses dans les dates de l'année académique.

---

## BR-003

Une année académique doit posséder au moins une période.

---

## BR-004

Les périodes d'une même année ne peuvent pas se chevaucher.

---

## BR-005

La suppression d'une année académique est interdite si des périodes existent.

---

# Eloquent Relationships

AcademicYear

hasMany(AcademicTerm::class)

AcademicTerm

belongsTo(AcademicYear::class)

---

# Loading Strategy

Eager Loading recommandé.

Exemple :

AcademicYear

↓

AcademicTerms

↓

Classes

↓

Subjects

---

# Cascade Rules

Delete

Restricted

Update

Cascade

---

# Index Recommendations

Index :

academic_year_id

---

# Used By

- Enrollment
- Timetable
- Attendance
- Examination
- Promotion

---

# Events Impacted

- AcademicYearCreated
- AcademicYearClosed
- AcademicTermCreated

---

# Validation Rules

- academic_year_id obligatoire.
- Les dates doivent être incluses dans l'année.
- Les périodes doivent être ordonnées.

---

# Performance Considerations

Les périodes doivent être chargées en une seule requête afin d'éviter le problème N+1.

---

# Future Extensions

- Multi-campus
- Multi-calendar
- Universités