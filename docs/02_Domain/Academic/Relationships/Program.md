# Program Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-002

---

# Purpose

Le modèle **AcademicProgram** représente un programme d'enseignement proposé par l'établissement.

Il sert de conteneur pédagogique pour organiser les niveaux, les séries, les matières et les parcours académiques.

Chaque programme définit un cursus spécifique auquel les élèves sont rattachés durant leur scolarité.

---

# Direct Relationships

## AcademicProgram → AcademicLevel

**Relationship Type**

One-to-Many (1:N)

**Description**

Un programme regroupe plusieurs niveaux académiques.

Exemple :

- Primaire
- Collège
- Lycée
- Université

**Eloquent**

```php
AcademicProgram::hasMany(AcademicLevel::class);

AcademicLevel::belongsTo(AcademicProgram::class);
```

---

## AcademicProgram → AcademicSeries

**Relationship Type**

One-to-Many (1:N)

**Description**

Un programme peut contenir plusieurs séries.

Exemple :

- Série A
- Série C
- Série D
- Série G

---

## AcademicProgram → Subject

**Relationship Type**

Many-to-Many (N:N)

**Description**

Un programme est composé d'un ensemble de matières.

Certaines matières peuvent être communes à plusieurs programmes.

**Eloquent**

```php
AcademicProgram::belongsToMany(Subject::class);

Subject::belongsToMany(AcademicProgram::class);
```

---

## AcademicProgram → Classroom

**Relationship Type**

One-to-Many (Indirect)

**Description**

Les classes sont créées à partir des niveaux appartenant au programme.

La relation est indirecte via **AcademicLevel**.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| AcademicProgram | 1 → N | AcademicLevel |
| AcademicProgram | 1 → N | AcademicSeries |
| AcademicProgram | N ↔ N | Subject |
| AcademicProgram | 1 → N (Indirect) | Classroom |

---

# Database Constraints

## Foreign Keys

AcademicLevel

```text
academic_program_id
```

AcademicSeries

```text
academic_program_id
```

Pivot

```text
academic_program_subject
```

- academic_program_id
- subject_id

---

# Business Rules

## BR-001

Chaque niveau appartient à un seul programme.

---

## BR-002

Une série appartient à un seul programme.

---

## BR-003

Une matière peut être partagée entre plusieurs programmes.

---

## BR-004

Un programme doit posséder au moins un niveau.

---

## BR-005

Un programme inactif ne peut plus recevoir de nouvelles classes.

---

## BR-006

La suppression d'un programme est interdite s'il possède des niveaux ou des élèves actifs.

---

# Referential Integrity

La suppression est interdite lorsqu'il existe :

- niveaux
- séries
- classes
- inscriptions
- matières liées

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
AcademicProgram
├── Levels
├── Series
├── Subjects
└── Classes
```

Utiliser l'Eager Loading pour les écrans de configuration académique.

---

# Index Recommendations

Créer des index sur :

```text
code
name
status
academic_program_id
```

---

# Events Impacted

- AcademicProgramCreated
- AcademicProgramUpdated
- AcademicProgramArchived

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

Les programmes sont relativement peu nombreux.

Ils peuvent être mis en cache afin de réduire les accès répétés à la base de données lors des inscriptions et de la création des emplois du temps.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer un programme
- modifier un programme
- archiver un programme
- supprimer un programme

---

# Future Extensions

- Programmes internationaux
- Programmes multi-langues
- Programmes à crédits
- Programmes hybrides
- Double diplôme
- Compatibilité avec plusieurs référentiels nationaux