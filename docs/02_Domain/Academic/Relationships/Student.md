# Student Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-011

---

# Purpose

Le modèle **Student** représente un apprenant inscrit dans l'établissement.

Bien que l'entité appartienne au domaine **Student Management**, elle constitue l'acteur principal du domaine Academic.

Toutes les activités académiques sont réalisées à travers ses inscriptions.

---

# Direct Relationships

## Student → Enrollment

One-to-Many (1:N)

Un élève peut posséder plusieurs inscriptions académiques durant son parcours.

Une seule inscription active est autorisée par année académique.

---

## Student → Attendance

One-to-Many (1:N)

Toutes les présences sont enregistrées pour un élève.

---

## Student → Grade

One-to-Many (1:N)

Toutes les notes appartiennent à un élève.

---

## Student → Assessment

One-to-Many (1:N)

Toutes les évaluations concernent un élève.

---

## Student → ReportCard

One-to-Many (1:N)

Un élève possède plusieurs bulletins.

---

## Student → Promotion

One-to-Many (1:N)

Les décisions de promotion sont historisées.

---

# Indirect Relationships

Via Enrollment :

- AcademicYear
- AcademicProgram
- AcademicLevel
- AcademicSeries
- Classroom
- ClassSection

---

# Business Rules

- Un élève peut posséder plusieurs inscriptions historiques.
- Une seule inscription active par année académique.
- Les notes ne peuvent exister sans inscription.
- Les présences sont liées à l'inscription active.
- Les promotions sont historisées.

---

# Referential Integrity

Suppression physique interdite.

Soft Delete recommandé.

---

# Loading Strategy

```text
Student
├── Enrollments
├── Grades
├── Attendances
├── ReportCards
└── Promotions
```

---

# Used By

- Enrollment
- Attendance
- Examination
- Assessment
- Grading
- Promotion
- Report Cards
- Student Portal
- Parent Portal
- Finance
- Library
- Transport
- Hostel

---

# Security Considerations

- Consultation selon les permissions.
- Toutes les modifications sont auditées.
- Les données personnelles sont protégées.

---

# Future Extensions

- Alumni
- Student Mobility
- Exchange Programs
- Multi-campus
- National Student Registry