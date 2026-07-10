# AcademicSeries Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-004

---

# Purpose

Le modèle **AcademicSeries** représente une spécialisation académique au sein d'un niveau d'enseignement.

Une série permet de différencier les parcours pédagogiques en définissant les matières enseignées, leurs coefficients, les règles d'évaluation et les critères de promotion.

Elle constitue un élément clé de l'organisation pédagogique des établissements secondaires et techniques.

---

# Direct Relationships

## AcademicSeries → AcademicProgram

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque série appartient à un seul programme académique.

**Eloquent**

```php
AcademicSeries::belongsTo(AcademicProgram::class);

AcademicProgram::hasMany(AcademicSeries::class);
```

---

## AcademicSeries → AcademicLevel

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque série est rattachée à un niveau précis.

Exemple :

Première

↓

Série A

Série C

Série D

---

## AcademicSeries → Classroom

**Relationship Type**

One-to-Many (1:N)

**Description**

Une série peut contenir plusieurs classes.

Exemple :

Première D

↓

1D1

1D2

1D3

---

## AcademicSeries → Subject

**Relationship Type**

Many-to-Many (N:N)

**Description**

Chaque série possède un ensemble spécifique de matières.

Certaines matières sont communes à plusieurs séries.

**Eloquent**

```php
AcademicSeries::belongsToMany(Subject::class);

Subject::belongsToMany(AcademicSeries::class);
```

---

## AcademicSeries → SubjectCoefficient

**Relationship Type**

One-to-Many (1:N)

**Description**

Les coefficients des matières varient selon la série.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| AcademicProgram | 1 → N | AcademicSeries |
| AcademicLevel | 1 → N | AcademicSeries |
| AcademicSeries | 1 → N | Classroom |
| AcademicSeries | N ↔ N | Subject |
| AcademicSeries | 1 → N | SubjectCoefficient |

---

# Database Constraints

## Foreign Keys

AcademicSeries

```text
academic_program_id
academic_level_id
```

Classroom

```text
academic_series_id
```

SubjectCoefficient

```text
academic_series_id
```

Pivot

```text
academic_series_subject
```

---

# Business Rules

## BR-001

Une série appartient à un seul programme.

---

## BR-002

Une série appartient à un seul niveau.

---

## BR-003

Une classe ne peut appartenir qu'à une seule série.

---

## BR-004

Chaque série possède son propre ensemble de matières.

---

## BR-005

Les coefficients peuvent différer d'une série à une autre.

---

## BR-006

Une série inactive ne peut plus recevoir de nouvelles inscriptions.

---

# Referential Integrity

La suppression d'une série est interdite lorsqu'elle possède :

- des classes
- des élèves inscrits
- des matières
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
AcademicSeries
├── Program
├── Level
├── Classrooms
├── Subjects
└── SubjectCoefficients
```

Privilégier l'Eager Loading lors des inscriptions et des calculs académiques.

---

# Index Recommendations

Créer des index sur :

```text
academic_program_id
academic_level_id
code
name
status
```

---

# Events Impacted

- AcademicSeriesCreated
- AcademicSeriesUpdated
- AcademicSeriesArchived

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

Les séries sont systématiquement consultées avec leurs matières et leurs coefficients.

La mise en cache des relations est recommandée afin d'améliorer les performances des calculs académiques.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer une série
- modifier une série
- archiver une série
- supprimer une série

---

# Future Extensions

- Séries internationales
- Double spécialisation
- Parcours personnalisés
- Options facultatives
- Mineures et majeures
- Séries spécifiques aux universités