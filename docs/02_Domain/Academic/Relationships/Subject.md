# Subject Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-007

---

# Purpose

Le modèle **Subject** représente une discipline académique enseignée dans un ou plusieurs programmes d'études.

Une matière peut être partagée entre plusieurs programmes, niveaux, séries et classes, tout en conservant ses propres coefficients, enseignants et évaluations.

Elle constitue le point central de l'enseignement et de l'évaluation académique.

---

# Direct Relationships

## Subject → AcademicProgram

**Relationship Type**

Many-to-Many (N:N)

**Description**

Une matière peut appartenir à plusieurs programmes.

Un programme comprend plusieurs matières.

**Eloquent**

```php
Subject::belongsToMany(AcademicProgram::class);

AcademicProgram::belongsToMany(Subject::class);
```

---

## Subject → AcademicLevel

**Relationship Type**

Many-to-Many (N:N)

**Description**

Une matière peut être enseignée dans plusieurs niveaux.

Les niveaux possèdent plusieurs matières.

---

## Subject → AcademicSeries

**Relationship Type**

Many-to-Many (N:N)

**Description**

Les matières disponibles varient selon la série académique.

Certaines sont communes, d'autres spécifiques.

---

## Subject → Classroom

**Relationship Type**

Many-to-Many (N:N)

**Description**

Une matière peut être enseignée dans plusieurs classes.

Une classe suit plusieurs matières.

---

## Subject → SubjectCoefficient

**Relationship Type**

One-to-Many (1:N)

**Description**

Chaque matière possède différents coefficients selon :

- l'année académique
- le niveau
- la série

---

## Subject → Teacher

**Relationship Type**

Many-to-Many (N:N)

**Description**

Une matière peut être enseignée par plusieurs enseignants.

Un enseignant peut enseigner plusieurs matières.

---

# Indirect Relationships

## Examination

Une matière possède plusieurs examens.

---

## Assessment

Une matière possède plusieurs évaluations.

---

## Grade

Chaque note est associée à une matière.

---

## Report Card

Les bulletins affichent les résultats par matière.

---

## Timetable

Les emplois du temps sont construits autour des matières.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| Subject | N ↔ N | AcademicProgram |
| Subject | N ↔ N | AcademicLevel |
| Subject | N ↔ N | AcademicSeries |
| Subject | N ↔ N | Classroom |
| Subject | 1 → N | SubjectCoefficient |
| Subject | N ↔ N | Teacher |

---

# Database Constraints

## Pivot Tables

```text
academic_program_subject

academic_level_subject

academic_series_subject

classroom_subject

subject_teacher
```

## Foreign Keys

SubjectCoefficient

```text
subject_id
```

---

# Business Rules

## BR-001

Chaque matière possède un code unique.

---

## BR-002

Une matière peut appartenir à plusieurs programmes.

---

## BR-003

Une matière peut avoir plusieurs coefficients.

---

## BR-004

Une matière inactive ne peut plus être affectée.

---

## BR-005

Une matière doit posséder au moins un coefficient actif avant d'être utilisée pour les évaluations.

---

## BR-006

Les enseignants doivent être qualifiés pour enseigner la matière.

---

# Referential Integrity

La suppression est interdite lorsqu'une matière possède :

- des coefficients
- des notes
- des examens
- des emplois du temps
- des enseignants affectés

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
Subject
├── Programs
├── Levels
├── Series
├── Classrooms
├── Teachers
└── SubjectCoefficients
```

---

# Index Recommendations

Créer des index sur :

```text
code
name
status
category
```

---

# Events Impacted

- SubjectCreated
- SubjectUpdated
- SubjectArchived
- SubjectAssigned
- SubjectCoefficientAssigned

---

# Used By

- Timetable
- Attendance
- Examination
- Assessment
- Grading
- Promotion
- Report Cards
- Teacher Portal
- Student Portal
- Parent Portal
- Analytics

---

# Performance Considerations

Les matières sont utilisées dans presque tous les modules académiques.

Le chargement des coefficients et des enseignants doit privilégier l'Eager Loading afin de limiter les requêtes répétitives.

Les listes de matières peuvent être mises en cache car elles évoluent rarement.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer une matière
- modifier une matière
- archiver une matière
- supprimer une matière

---

# Future Extensions

- Matières optionnelles
- Matières électives
- Matières interdisciplinaires
- Enseignement par compétences
- Équivalences entre matières
- Catalogue national des matières