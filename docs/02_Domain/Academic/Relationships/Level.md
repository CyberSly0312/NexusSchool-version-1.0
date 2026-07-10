# AcademicLevel Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-003

---

# Purpose

Le modèle **AcademicLevel** représente un niveau d'enseignement au sein d'un programme académique.

Il constitue le lien entre le programme pédagogique et les classes effectivement utilisées pour accueillir les élèves.

Chaque niveau définit les matières enseignées, les séries disponibles, les règles de promotion et la progression académique.

---

# Direct Relationships

## AcademicLevel → AcademicProgram

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque niveau appartient à un unique programme académique.

**Eloquent**

```php
AcademicLevel::belongsTo(AcademicProgram::class);

AcademicProgram::hasMany(AcademicLevel::class);
```

---

## AcademicLevel → AcademicSeries

**Relationship Type**

One-to-Many (1:N)

**Description**

Un niveau peut proposer plusieurs séries.

Exemple :

Terminale

↓

A

C

D

G

---

## AcademicLevel → Classroom

**Relationship Type**

One-to-Many (1:N)

**Description**

Chaque niveau possède plusieurs classes.

Exemple :

6ème

↓

6A

6B

6C

6D

---

## AcademicLevel → Subject

**Relationship Type**

Many-to-Many (N:N)

**Description**

Les matières enseignées varient selon le niveau.

Certaines matières sont communes à plusieurs niveaux.

**Eloquent**

```php
AcademicLevel::belongsToMany(Subject::class);

Subject::belongsToMany(AcademicLevel::class);
```

---

## AcademicLevel → SubjectCoefficient

**Relationship Type**

One-to-Many (1:N)

**Description**

Les coefficients sont définis en fonction du niveau académique.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| AcademicProgram | 1 → N | AcademicLevel |
| AcademicLevel | 1 → N | AcademicSeries |
| AcademicLevel | 1 → N | Classroom |
| AcademicLevel | N ↔ N | Subject |
| AcademicLevel | 1 → N | SubjectCoefficient |

---

# Database Constraints

## Foreign Keys

AcademicLevel

```text
academic_program_id
```

AcademicSeries

```text
academic_level_id
```

Classroom

```text
academic_level_id
```

SubjectCoefficient

```text
academic_level_id
```

Pivot

```text
academic_level_subject
```

---

# Business Rules

## BR-001

Chaque niveau appartient à un seul programme.

---

## BR-002

Une classe appartient à un seul niveau.

---

## BR-003

Une série ne peut exister que si elle est autorisée pour ce niveau.

---

## BR-004

Les matières doivent être compatibles avec le niveau.

---

## BR-005

Les coefficients sont définis au niveau académique.

---

## BR-006

Le niveau constitue l'unité de base utilisée pour la promotion des élèves.

---

# Referential Integrity

La suppression d'un niveau est interdite lorsqu'il possède :

- des classes
- des séries
- des matières
- des inscriptions
- des coefficients

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
AcademicLevel
├── Program
├── Series
├── Classrooms
├── Subjects
└── SubjectCoefficients
```

Utiliser l'Eager Loading lors des opérations de planification académique.

---

# Index Recommendations

Créer des index sur :

```text
academic_program_id
code
name
status
display_order
```

---

# Events Impacted

- AcademicLevelCreated
- AcademicLevelUpdated
- AcademicLevelArchived

---

# Used By

- Enrollment
- Timetable
- Attendance
- Examination
- Promotion
- Report Cards
- Student Portal
- Analytics

---

# Performance Considerations

Les niveaux sont fréquemment consultés lors des inscriptions, de la génération des emplois du temps et des calculs académiques.

Ils peuvent être mis en cache avec leurs séries et leurs matières afin de limiter les requêtes répétitives.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer un niveau
- modifier un niveau
- archiver un niveau
- supprimer un niveau

---

# Future Extensions

- Niveaux multi-référentiels
- Progression par compétences
- Niveaux universitaires (Licence, Master, Doctorat)
- Niveaux personnalisés par établissement
- Passerelles entre niveaux