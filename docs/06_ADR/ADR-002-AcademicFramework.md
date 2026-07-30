# ADR-002
# Academic Framework Architecture

**Status:** Accepted

**Date:** 2026-07-30

**Decision Makers:**

- Solution Architect
- Academic Domain Expert
- Product Owner

---

# Context

Les établissements scolaires suivent une organisation académique complexe.

Chaque pays possède son propre découpage académique :

- Années académiques
- Semestres
- Trimestres
- Cycles
- Niveaux
- Départements
- Filières
- Classes
- Matières

Nexus Platform est destiné à être utilisé dans plusieurs pays africains francophones, avec la possibilité d'évoluer vers d'autres systèmes éducatifs.

Une architecture académique rigide aurait nécessité de modifier le code source pour chaque nouveau pays ou chaque nouvelle organisation scolaire.

---

# Problem Statement

Comment construire un modèle académique suffisamment générique pour représenter différents systèmes éducatifs tout en conservant une architecture simple, cohérente et maintenable ?

Le modèle devait permettre :

- plusieurs pays
- plusieurs calendriers académiques
- plusieurs structures pédagogiques
- plusieurs organisations administratives

sans modifier le cœur de l'application.

---

# Decision

Nexus adopte un **Academic Framework configurable**.

Toutes les fonctionnalités académiques reposent sur un modèle commun composé d'entités indépendantes mais reliées entre elles.

Le Framework académique constitue le cœur fonctionnel de la plateforme.

```text
Academic Year
        │
        ▼
Academic Term
        │
        ▼
Campus
        │
        ▼
Department
        │
        ▼
Program
        │
        ▼
Grade Level
        │
        ▼
Classroom
        │
        ▼
Subject
        │
        ▼
Student
        │
        ▼
Assessment
        │
        ▼
Report Card
```

Chaque établissement peut activer uniquement les composants dont il a besoin.

---

# Alternatives Considered

## Option 1

Créer une architecture spécifique pour chaque pays.

### Avantages

- Très adaptée localement.

### Inconvénients

- Duplication importante du code.
- Maintenance difficile.
- Faible évolutivité.

---

## Option 2

Créer un modèle académique fixe.

### Avantages

- Simplicité.

### Inconvénients

- Peu flexible.
- Incompatible avec plusieurs systèmes scolaires.

---

## Option 3 (Choisie)

Créer un Framework académique configurable.

### Avantages

- Flexible.
- Réutilisable.
- Compatible Multi-Pays.
- Compatible Multi-Tenant.
- Facilement extensible.

---

# Consequences

Toutes les Applications académiques utilisent désormais ce Framework.

Cela concerne notamment :

- Admissions
- Attendance
- Timetable
- Assessment
- Report Cards
- Promotions
- Graduation
- Finance
- Library
- Student Portal
- Parent Portal

Toute nouvelle fonctionnalité académique doit s'intégrer à ce modèle.

---

# Positive Impacts

- Architecture cohérente.
- Forte réutilisation.
- Très faible duplication.
- Adaptation à différents pays.
- Maintenance simplifiée.
- Évolutivité importante.

---

# Negative Impacts

- Paramétrage initial plus riche.
- Courbe d'apprentissage plus importante.
- Relations métier plus nombreuses.

---

# Implementation Notes

Le Framework est implémenté à travers les Domain Models.

Les règles métier sont réparties entre :

- Domain Services
- Application Services
- Policies
- Domain Events
- Validation Rules

Les Applications ne doivent jamais contourner le Framework académique.

Toute évolution doit être compatible avec ce modèle.

---

# Related Documents

- 02_Domain/Academic
- 02_Architecture_V1
- 03_Database
- 04_Platform
- 06_SaaS
- AcademicYear
- AcademicTerm
- GradeLevel
- Classroom
- Subject
- Student