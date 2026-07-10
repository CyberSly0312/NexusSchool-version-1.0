# SubjectCoefficient Relationships

## Module

Academic

---

# Relationship ID

REL-ACA-008

---

# Purpose

Le modèle **SubjectCoefficient** représente le coefficient appliqué à une matière dans un contexte académique précis.

Les coefficients peuvent varier selon :

- l'année académique
- le programme
- le niveau
- la série

Ils sont utilisés pour le calcul des moyennes, des classements, des décisions de promotion et des bulletins scolaires.

---

# Direct Relationships

## SubjectCoefficient → Subject

**Relationship Type**

Many-to-One (N:1)

**Description**

Chaque coefficient appartient à une seule matière.

Une matière peut posséder plusieurs coefficients.

**Eloquent**

```php
SubjectCoefficient::belongsTo(Subject::class);

Subject::hasMany(SubjectCoefficient::class);
```

---

## SubjectCoefficient → AcademicYear

**Relationship Type**

Many-to-One (N:1)

**Description**

Les coefficients sont historisés par année académique.

Ils peuvent évoluer d'une année à l'autre.

---

## SubjectCoefficient → AcademicProgram

**Relationship Type**

Many-to-One (N:1)

**Description**

Le coefficient est défini pour un programme spécifique.

---

## SubjectCoefficient → AcademicLevel

**Relationship Type**

Many-to-One (N:1)

**Description**

Les coefficients varient selon le niveau académique.

---

## SubjectCoefficient → AcademicSeries

**Relationship Type**

Many-to-One (N:1)

**Description**

Les séries peuvent appliquer des coefficients différents à une même matière.

Cette relation est optionnelle lorsque le niveau ne comporte pas de séries.

---

# Indirect Relationships

## Grade

Chaque note utilise le coefficient associé à la matière.

---

## Assessment

Les évaluations pondèrent les résultats grâce au coefficient.

---

## Promotion

Les décisions de promotion utilisent les coefficients officiels.

---

## Report Card

Les moyennes affichées sur les bulletins utilisent ces coefficients.

---

# Cardinality

| Parent | Relation | Child |
|---------|----------|-------|
| Subject | 1 → N | SubjectCoefficient |
| AcademicYear | 1 → N | SubjectCoefficient |
| AcademicProgram | 1 → N | SubjectCoefficient |
| AcademicLevel | 1 → N | SubjectCoefficient |
| AcademicSeries | 1 → N | SubjectCoefficient |

---

# Database Constraints

## Foreign Keys

```text
subject_id
academic_year_id
academic_program_id
academic_level_id
academic_series_id (nullable)
```

---

# Business Rules

## BR-001

Chaque matière possède un seul coefficient actif pour une combinaison donnée :

- Année académique
- Programme
- Niveau
- Série (si applicable)

---

## BR-002

Le coefficient doit être supérieur à zéro.

---

## BR-003

Les coefficients sont figés après la publication des premières notes.

---

## BR-004

Toute modification d'un coefficient doit être historisée.

---

## BR-005

Un coefficient inactif ne peut plus être utilisé pour de nouveaux calculs.

---

## BR-006

Les calculs des moyennes utilisent exclusivement les coefficients actifs.

---

# Referential Integrity

La suppression est interdite si le coefficient est utilisé par :

- des notes
- des bulletins
- des calculs de moyenne
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
SubjectCoefficient
├── Subject
├── AcademicYear
├── AcademicProgram
├── AcademicLevel
└── AcademicSeries
```

Utiliser l'Eager Loading lors des calculs de moyennes et de la génération des bulletins.

---

# Index Recommendations

Créer des index sur :

```text
subject_id
academic_year_id
academic_program_id
academic_level_id
academic_series_id
status
```

Créer également un index unique sur :

```text
subject_id
academic_year_id
academic_program_id
academic_level_id
academic_series_id
```

afin d'empêcher la création de doublons.

---

# Events Impacted

- SubjectCoefficientCreated
- SubjectCoefficientUpdated
- SubjectCoefficientArchived

---

# Used By

- Grading
- Assessment
- Promotion
- Report Cards
- Analytics
- Student Portal
- Parent Portal

---

# Performance Considerations

Les coefficients sont chargés à chaque calcul de moyenne.

Ils doivent être mis en cache pendant une session de calcul afin d'éviter des accès répétés à la base de données.

---

# Security Considerations

Seuls les utilisateurs autorisés peuvent :

- créer un coefficient
- modifier un coefficient
- archiver un coefficient
- supprimer un coefficient

Les modifications après la publication des résultats doivent être soumises à une validation renforcée.

---

# Future Extensions

- Coefficients par semestre
- Coefficients par type d'évaluation
- Coefficients spécifiques à un établissement
- Pondérations par compétence
- Versionnage complet des coefficients