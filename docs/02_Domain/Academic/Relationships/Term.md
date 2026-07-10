# AcademicTerm Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-010

---

# Purpose

Le modèle **AcademicTerm** représente une période académique appartenant à une année scolaire.

Selon le système éducatif de l'établissement, une année académique peut être divisée en :

- Semestres
- Trimestres
- Quadrimestres
- Sessions

Les périodes servent de référence pour l'organisation des cours, des évaluations, des examens, des présences et des bulletins.

---

# Direct Relationships

## AcademicTerm → AcademicYear

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque période appartient à une seule année académique.

Une année académique possède une ou plusieurs périodes.

**Eloquent**

```php
AcademicTerm::belongsTo(AcademicYear::class);

AcademicYear::hasMany(AcademicTerm::class);
```

---

## AcademicTerm → Examination

**Relationship Type**

One-to-Many (1:N)

**Description**

Les examens sont organisés dans une période académique.

---

## AcademicTerm → Assessment

**Relationship Type**

One-to-Many (1:N)

**Description**

Les évaluations sont rattachées à une période.

---

## AcademicTerm → Report Card

**Relationship Type**

One-to-Many (1:N)

**Description**

Chaque bulletin est généré pour une période donnée.

---

## AcademicTerm → Attendance

**Relationship Type**

Indirect

**Description**

Les statistiques de présence sont calculées par période.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| AcademicYear | 1 → N | AcademicTerm |
| AcademicTerm | 1 → N | Examination |
| AcademicTerm | 1 → N | Assessment |
| AcademicTerm | 1 → N | ReportCard |

---

# Database Constraints

## Foreign Keys

```text
academic_year_id
```

---

# Business Rules

## BR-001

Une période appartient obligatoirement à une seule année académique.

---

## BR-002

Les dates d'une période doivent être incluses dans celles de l'année académique.

---

## BR-003

Deux périodes d'une même année ne peuvent pas se chevaucher.

---

## BR-004

Une seule période peut être active à un instant donné.

---

## BR-005

Une période clôturée devient accessible uniquement en lecture.

---

## BR-006

Les bulletins ne peuvent être générés qu'après la clôture des évaluations de la période.

---

# Referential Integrity

Une période ne peut être supprimée si elle possède :

- des évaluations
- des examens
- des bulletins
- des statistiques de présence

Soft Delete recommandé.

---

# Cascade Rules

| Operation | Rule |
|-----------|------|
| Delete | Restrict |
| Update | Cascade |

---

# Loading Strategy

Chargement recommandé :

```text
AcademicTerm
├── AcademicYear
├── Examinations
├── Assessments
└── ReportCards
```

Utiliser l'Eager Loading lors de la génération des bulletins et des rapports académiques.

---

# Index Recommendations

Créer des index sur :

```text
academic_year_id
start_date
end_date
status
display_order
```

---

# Events Impacted

- AcademicTermCreated
- AcademicTermOpened
- AcademicTermClosed
- AcademicTermArchived

---

# Used By

- Attendance
- Timetable
- Examination
- Assessment
- Grading
- Report Cards
- Promotion
- Student Portal
- Parent Portal
- Analytics

---

# Performance Considerations

Les périodes sont souvent utilisées comme filtre principal pour les évaluations, examens et bulletins.

La période active peut être mise en cache afin de réduire les accès répétés à la base de données.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer une période
- modifier une période
- ouvrir une période
- clôturer une période
- archiver une période

---

# Future Extensions

- Sessions de rattrapage
- Sessions d'été
- Calendriers universitaires
- Calendriers multi-campus
- Périodes personnalisables
- Synchronisation avec les calendriers nationaux