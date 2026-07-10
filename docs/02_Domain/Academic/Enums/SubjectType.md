# Academic Module
# Enums
## SubjectType

Version : 1.0

---

# Objectif

SubjectType définit les différents types de matières académiques.

Chaque type possède des règles pédagogiques spécifiques concernant l'évaluation, les coefficients, l'emploi du temps et les exigences académiques.

Cet Enum garantit une classification uniforme des matières dans tout le système.

---

# Utilisation

SubjectType est utilisé par :

- Subject
- SubjectService
- Timetable
- Gradebook
- Examination
- Report Cards
- DTOs
- Events
- Validation Rules

---

# Valeurs

## Core

Valeur

```
Core
```

Description

Matière fondamentale obligatoire.

Caractéristiques

- obligatoire
- coefficient principal
- prise en compte dans la moyenne générale
- évaluations obligatoires

---

## Elective

Valeur

```
Elective
```

Description

Matière optionnelle choisie par l'élève.

Caractéristiques

- facultative
- selon le programme
- coefficient variable

---

## Practical

Valeur

```
Practical
```

Description

Matière essentiellement pratique.

Caractéristiques

- travaux pratiques
- ateliers
- laboratoire
- évaluations pratiques

---

## Complementary

Valeur

```
Complementary
```

Description

Matière complémentaire.

Caractéristiques

- complète la formation
- peut être obligatoire selon le programme
- coefficient généralement réduit

---

## Extracurricular

Valeur

```
Extracurricular
```

Description

Activité parascolaire.

Caractéristiques

- hors programme principal
- non prise en compte dans la moyenne générale
- participation facultative

---

# Diagramme

```
Subject
    │
    ▼
SubjectType
    │
 ┌─────┼──────────┬──────────────┬──────────────┐
 ▼     ▼          ▼              ▼              ▼
Core Elective Practical Complementary Extracurricular
```

---

# Contraintes

Chaque matière possède un seul SubjectType.

Le type influence :

- les évaluations
- les coefficients
- les emplois du temps
- les bulletins
- les statistiques

---

# Règles métier

Core

- toujours disponible.
- évaluations obligatoires.

---

Elective

- dépend du choix de l'élève.

---

Practical

- nécessite parfois un laboratoire ou un atelier.

---

Complementary

- complète le programme principal.

---

Extracurricular

- indépendante du cursus principal.

---

# Exemples

```php
SubjectType::Core
```

```php
SubjectType::Elective
```

```php
SubjectType::Practical
```

---

# Tests

✓ Toutes les valeurs existent

✓ Une matière possède un seul type

✓ Les validations utilisent uniquement l'Enum

✓ Les conversions fonctionnent

---

# Résumé

SubjectType définit la nature pédagogique d'une matière.

Il fournit un langage commun entre les modules académiques et permet d'appliquer automatiquement les règles liées aux évaluations, aux coefficients, aux emplois du temps et aux bulletins.