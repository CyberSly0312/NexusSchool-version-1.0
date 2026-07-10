# Classroom Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-005

---

# Purpose

Le modèle **Classroom** représente une classe académique dans laquelle les élèves sont regroupés durant une année scolaire.

Une classe est associée à un niveau, éventuellement à une série, et sert de point central pour l'organisation des activités pédagogiques.

Elle constitue le principal point de rattachement des élèves, enseignants, emplois du temps, présences, évaluations et bulletins.

---

# Direct Relationships

## Classroom → AcademicLevel

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque classe appartient à un seul niveau académique.

**Eloquent**

```php
Classroom::belongsTo(AcademicLevel::class);

AcademicLevel::hasMany(Classroom::class);
```

---

## Classroom → AcademicSeries

**Relationship Type**

Many-to-One (N:1)

**Description**

Une classe peut appartenir à une série.

Certaines classes (ex. Primaire) peuvent ne pas être associées à une série.

**Eloquent**

```php
Classroom::belongsTo(AcademicSeries::class);
```

---

## Classroom → ClassSection

**Relationship Type**

One-to-Many (1:N)

**Description**

Une classe peut être divisée en plusieurs sections.

Exemple :

6A

↓

Section Rouge

Section Bleue

Section Verte

**Eloquent**

```php
Classroom::hasMany(ClassSection::class);

ClassSection::belongsTo(Classroom::class);
```

---

## Classroom → Enrollment

**Relationship Type**

One-to-Many (1:N)

**Description**

Les inscriptions académiques sont rattachées à une classe.

---

## Classroom → Subject

**Relationship Type**

Many-to-Many (N:N)

**Description**

Une classe suit plusieurs matières.

Une matière peut être enseignée dans plusieurs classes.

**Eloquent**

```php
Classroom::belongsToMany(Subject::class);

Subject::belongsToMany(Classroom::class);
```

---

# Indirect Relationships

La classe est utilisée par plusieurs domaines du système.

## Attendance

Une classe possède plusieurs feuilles de présence.

---

## Timetable

Une classe possède plusieurs emplois du temps.

---

## Examination

Une classe possède plusieurs examens.

---

## Assessment

Une classe possède plusieurs évaluations.

---

## Report Card

Une classe génère plusieurs bulletins.

---

## Promotion

Les décisions de promotion sont calculées par classe.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| AcademicLevel | 1 → N | Classroom |
| AcademicSeries | 1 → N | Classroom |
| Classroom | 1 → N | ClassSection |
| Classroom | 1 → N | Enrollment |
| Classroom | N ↔ N | Subject |

---

# Database Constraints

## Foreign Keys

Classroom

```text
academic_level_id
academic_series_id (nullable)
```

ClassSection

```text
classroom_id
```

Enrollment

```text
classroom_id
```

Pivot

```text
classroom_subject
```

---

# Business Rules

## BR-001

Chaque classe appartient obligatoirement à un niveau académique.

---

## BR-002

Une classe peut appartenir à une série.

---

## BR-003

Une classe possède une capacité maximale.

---

## BR-004

Une classe ne peut dépasser cette capacité.

---

## BR-005

Une classe peut être divisée en plusieurs sections.

---

## BR-006

Les élèves actifs appartiennent à une seule classe par année académique.

---

## BR-007

Une classe ne peut être supprimée si elle possède des élèves actifs.

---

# Referential Integrity

La suppression est interdite si la classe possède :

- des élèves
- des sections
- des emplois du temps
- des examens
- des présences
- des bulletins

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
Classroom
├── AcademicLevel
├── AcademicSeries
├── ClassSections
├── Enrollments
└── Subjects
```

Les modules Attendance, Timetable et Examination doivent utiliser l'Eager Loading.

---

# Index Recommendations

Créer des index sur :

```text
academic_level_id
academic_series_id
code
status
capacity
```

---

# Events Impacted

- ClassroomCreated
- ClassroomUpdated
- ClassroomArchived
- StudentEnrolled
- StudentTransferred
- StudentPromoted

---

# Used By

- Enrollment
- Attendance
- Timetable
- Examination
- Assessment
- Promotion
- Report Cards
- Student Portal
- Parent Portal
- Teacher Portal
- Analytics
- Finance
- Library
- Transport

---

# Performance Considerations

La classe est l'entité la plus consultée du domaine Academic.

Les relations vers les élèves, les matières et les sections doivent être optimisées afin d'éviter les requêtes répétitives.

Les effectifs peuvent être mis en cache et recalculés automatiquement lors des inscriptions, transferts et promotions.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer une classe
- modifier une classe
- archiver une classe
- supprimer une classe

---

# Future Extensions

- Classes virtuelles
- Classes multi-campus
- Classes hybrides
- Groupes pédagogiques temporaires
- Gestion automatique des capacités
- Répartition intelligente des élèves