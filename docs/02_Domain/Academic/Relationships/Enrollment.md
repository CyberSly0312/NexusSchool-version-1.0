# Enrollment Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-009

---

# Purpose

Le modèle **Enrollment** représente l'inscription académique d'un élève pour une année scolaire donnée.

Il constitue le lien entre l'élève et son affectation académique (année, programme, niveau, série, classe et section).

Toutes les opérations académiques s'appuient sur cette inscription.

---

# Direct Relationships

## Enrollment → Student

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque inscription appartient à un seul élève.

Un élève peut posséder plusieurs inscriptions au cours de sa scolarité, mais une seule inscription active par année académique.

**Eloquent**

```php
Enrollment::belongsTo(Student::class);

Student::hasMany(Enrollment::class);
```

---

## Enrollment → AcademicYear

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque inscription appartient à une année académique.

---

## Enrollment → AcademicProgram

**Relationship Type**

Many-to-One (N:1)

**Description**

L'inscription est associée à un programme académique.

---

## Enrollment → AcademicLevel

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque inscription est réalisée dans un niveau académique.

---

## Enrollment → AcademicSeries

**Relationship Type**

Many-to-One (N:1)

**Description**

La série est renseignée lorsqu'elle est applicable.

Cette relation est optionnelle.

---

## Enrollment → Classroom

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque inscription affecte l'élève à une seule classe.

---

## Enrollment → ClassSection

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque inscription peut affecter l'élève à une section.

Cette relation est optionnelle.

---

# Indirect Relationships

## Attendance

Toutes les présences utilisent l'inscription académique.

---

## Timetable

Les emplois du temps sont déterminés par l'inscription.

---

## Examination

Les examens sont accessibles via l'inscription.

---

## Grade

Les notes appartiennent à une inscription.

---

## Report Card

Les bulletins sont générés à partir de l'inscription.

---

## Promotion

Les décisions de promotion concernent une inscription académique.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| Student | 1 → N | Enrollment |
| AcademicYear | 1 → N | Enrollment |
| AcademicProgram | 1 → N | Enrollment |
| AcademicLevel | 1 → N | Enrollment |
| AcademicSeries | 1 → N | Enrollment |
| Classroom | 1 → N | Enrollment |
| ClassSection | 1 → N | Enrollment |

---

# Database Constraints

## Foreign Keys

```text
student_id
academic_year_id
academic_program_id
academic_level_id
academic_series_id (nullable)
classroom_id
class_section_id (nullable)
```

---

# Business Rules

## BR-001

Un élève ne peut avoir qu'une seule inscription active par année académique.

---

## BR-002

L'année académique doit être active lors de l'inscription.

---

## BR-003

La classe doit disposer de places disponibles.

---

## BR-004

Le niveau doit appartenir au programme sélectionné.

---

## BR-005

La série doit appartenir au niveau sélectionné.

---

## BR-006

La section doit appartenir à la classe sélectionnée.

---

## BR-007

Toute modification d'inscription doit être historisée.

---

# Referential Integrity

Une inscription ne peut être supprimée lorsqu'elle possède :

- des présences
- des notes
- des évaluations
- des bulletins
- des décisions de promotion

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
Enrollment
├── Student
├── AcademicYear
├── AcademicProgram
├── AcademicLevel
├── AcademicSeries
├── Classroom
└── ClassSection
```

---

# Index Recommendations

Créer des index sur :

```text
student_id
academic_year_id
classroom_id
class_section_id
status
```

Créer un index unique sur :

```text
student_id
academic_year_id
```

pour empêcher les doubles inscriptions.

---

# Events Impacted

- StudentEnrolled
- StudentTransferred
- StudentPromoted
- StudentGraduated
- StudentWithdrawn

---

# Used By

- Attendance
- Timetable
- Examination
- Assessment
- Grading
- Promotion
- Report Cards
- Student Portal
- Parent Portal
- Analytics
- Finance

---

# Performance Considerations

L'inscription académique est l'une des entités les plus consultées du système.

Toutes les listes d'élèves devraient être générées à partir de `Enrollment` plutôt que directement depuis `Student`, afin de garantir que seules les inscriptions actives sont prises en compte.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- inscrire un élève
- modifier une inscription
- transférer un élève
- annuler une inscription
- archiver une inscription

Toutes les modifications doivent être tracées dans l'audit.

---

# Future Extensions

- Réinscription automatique
- Double inscription (cours partagés)
- Mobilité inter-campus
- Inscription conditionnelle
- Historique complet du parcours académique
- Intégration avec les plateformes nationales