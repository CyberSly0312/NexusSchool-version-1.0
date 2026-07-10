# Academic Module
# Enums
## ProgramType

Version : 1.0

---

# Objectif

ProgramType définit les différents types de programmes académiques proposés par un établissement.

Chaque programme possède ses propres niveaux, matières, coefficients, emplois du temps et règles pédagogiques.

Cet Enum garantit une classification cohérente des programmes dans tout le système.

---

# Utilisation

ProgramType est utilisé par :

- Program
- Enrollment
- Classroom
- Subject
- PromotionService
- DTOs
- Events
- Validation Rules

---

# Valeurs

## General

Valeur

```
General
```

Description

Programme d'enseignement général.

Caractéristiques

- enseignement classique
- préparation aux examens nationaux
- accès aux études supérieures

---

## Technical

Valeur

```
Technical
```

Description

Programme d'enseignement technique.

Caractéristiques

- enseignement théorique
- ateliers pratiques
- laboratoires
- compétences professionnelles

---

## Vocational

Valeur

```
Vocational
```

Description

Programme de formation professionnelle.

Caractéristiques

- apprentissage métier
- stages
- forte composante pratique

---

## SpecialEducation

Valeur

```
SpecialEducation
```

Description

Programme destiné aux apprenants ayant des besoins éducatifs particuliers.

Caractéristiques

- pédagogie adaptée
- accompagnement spécifique
- évaluations personnalisées

---

## ContinuingEducation

Valeur

```
ContinuingEducation
```

Description

Programme de formation continue.

Caractéristiques

- cours du soir
- formation des adultes
- certification professionnelle

---

# Diagramme

```
Program
    │
    ▼
ProgramType
    │
 ┌──┼──────────────┬─────────────┬──────────────┐
 ▼  ▼              ▼             ▼              ▼
General Technical Vocational Special Continuing
```

---

# Contraintes

Chaque programme possède :

- ses propres niveaux
- ses propres matières
- ses propres coefficients
- son propre règlement académique

---

# Règles métier

General

- programme par défaut.

---

Technical

- peut nécessiter des laboratoires et ateliers.

---

Vocational

- inclut généralement des périodes de stage.

---

SpecialEducation

- nécessite des adaptations pédagogiques.

---

ContinuingEducation

- calendrier académique spécifique.

---

# Exemples

```php
ProgramType::General
```

```php
ProgramType::Technical
```

```php
ProgramType::Vocational
```

---

# Tests

✓ Toutes les valeurs existent

✓ Chaque programme possède un type valide

✓ Les conversions fonctionnent

✓ Les validations utilisent uniquement les valeurs de l'Enum

---

# Résumé

ProgramType définit les différents types de programmes académiques disponibles dans Nexus School.

Il fournit une classification standardisée permettant d'adapter les règles pédagogiques, les matières, les évaluations et les parcours des élèves.