
# AcademicYear Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-001

---

# Purpose

Le modèle **AcademicYear** représente la période officielle durant laquelle se déroulent les activités académiques d'un établissement.

Il constitue l'entité racine du domaine **Academic** et sert de référence temporelle pour les inscriptions, les périodes académiques, les classes, les évaluations et les promotions.

Toutes les données académiques doivent être rattachées à une année académique.

---

# Direct Relationships

## AcademicYear → AcademicTerm

**Relationship Type**

One-to-Many (1:N)

**Description**

Une année académique est composée d'une ou plusieurs périodes académiques (trimestres, semestres ou quadrimestres).

**Eloquent**

```php
AcademicYear::hasMany(AcademicTerm::class)

AcademicTerm::belongsTo(AcademicYear::class)
```

---

## AcademicYear → Enrollment

**Relationship Type**

One-to-Many (1:N)

**Description**

Chaque inscription est réalisée dans une année académique donnée.

**Eloquent**

```php
AcademicYear::hasMany(Enrollment::class)

Enrollment::belongsTo(AcademicYear::class)
```

---

## AcademicYear → SubjectCoefficient

**Relationship Type**

One-to-Many (1:N)

**Description**

Les coefficients peuvent évoluer d'une année académique à l'autre.

Chaque coefficient appartient donc à une année académique précise.

---

## AcademicYear → Promotion

**Relationship Type**

One-to-Many (1:N)

**Description**

Toutes les décisions de promotion sont historisées par année académique.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| AcademicYear | 1 → N | AcademicTerm |
| AcademicYear | 1 → N | Enrollment |
| AcademicYear | 1 → N | SubjectCoefficient |
| AcademicYear | 1 → N | Promotion |

---

# Database Constraints

## Foreign Keys

AcademicTerm

```text
academic_year_id
```

Enrollment

```text
academic_year_id
```

SubjectCoefficient

```text
academic_year_id
```

Promotion

```text
academic_year_id
```

---

# Business Rules

## BR-001

Une seule année académique peut être active simultanément.

---

## BR-002

Toutes les périodes doivent appartenir à une année académique.

---

## BR-003

Une inscription ne peut exister sans année académique.

---

## BR-004

Les coefficients sont propres à une année académique.

---

## BR-005

Les promotions sont toujours enregistrées dans une année académique.

---

## BR-006

Une année clôturée devient accessible uniquement en lecture.

---

# Referential Integrity

La suppression d'une année académique est interdite si elle possède :

- des périodes
- des inscriptions
- des coefficients
- des promotions

Suppression logique (Soft Delete) recommandée.

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
AcademicYear
 ├── AcademicTerms
 ├── Enrollments
 ├── SubjectCoefficients
 └── Promotions
```

Utiliser l'Eager Loading pour éviter le problème N+1.

---

# Index Recommendations

Créer des index sur :

```text
academic_year_id
status
start_date
end_date
```

---

# Events Impacted

- AcademicYearCreated
- AcademicYearOpened
- AcademicYearClosed
- AcademicYearArchived

---

# Used By

- Enrollment
- Timetable
- Attendance
- Examination
- Assessment
- Promotion
- Report Cards
- Student Portal
- Parent Portal
- Analytics
- Finance

---

# Performance Considerations

Les années académiques sont fréquemment utilisées comme filtre principal.

Les requêtes doivent systématiquement être optimisées par indexation et mises en cache lorsque l'année active est consultée.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer une année académique
- modifier une année académique
- ouvrir une année académique
- clôturer une année académique
- archiver une année académique

---

# Future Extensions

- Multi-campus
- Multi-calendar
- Calendriers nationaux
- Calendriers universitaires
- Gestion des années académiques parallèles
- Synchronisation avec les calendriers ministériels