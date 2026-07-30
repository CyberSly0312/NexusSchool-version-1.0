# Architecture_Rules.md

# Nexus Platform
## Architecture Rules

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Ce document définit les règles architecturales officielles de Nexus Platform.

Son objectif est de préserver la cohérence de l'architecture au fil du temps en définissant les dépendances autorisées entre les différentes couches de la plateforme.

Ces règles sont obligatoires pour tous les développements.

---

# 2. Scope

Ce document couvre :

- Les dépendances
- Les couches
- Les Engines
- Les Applications
- Les Domains
- Les Interfaces
- Les Services

Ce document ne couvre pas :

- Les conventions PHP
- Git
- Les tests
- Le déploiement

---

# 3. Principles

## Dependency Direction

Les dépendances suivent toujours la même direction.

---

## Layer Isolation

Chaque couche est indépendante.

---

## Single Responsibility

Chaque composant possède une responsabilité unique.

---

## Low Coupling

Les dépendances sont limitées.

---

## High Cohesion

Les éléments proches travaillent ensemble.

---

# 4. Allowed Dependencies

Les dépendances suivent uniquement ce schéma.

```text
UI

↓

Applications

↓

Domains

↓

Platform

↓

Foundation
```

Toute dépendance inverse est interdite.

---

# 5. Foundation Rules

Foundation représente la couche la plus basse.

Elle ne dépend de personne.

Autorisé :

```text
Foundation

↓

Nothing
```

Interdit :

- Laravel spécifique
- UI
- Applications
- Domains

---

# 6. Platform Rules

Platform peut dépendre de Foundation.

Autorisé

```text
Platform

↓

Foundation
```

Interdit

- Applications
- UI

Platform ne connaît jamais les Applications.

---

# 7. Domain Rules

Les Domains peuvent utiliser :

- Platform
- Foundation

Ils ne connaissent jamais :

- UI
- Filament
- HTTP
- Controllers

Le Domain reste indépendant du framework.

---

# 8. Application Rules

Une Application orchestre plusieurs Domains.

Elle peut dépendre de :

- Domains
- Platform
- Foundation

Elle ne dépend jamais d'une autre Application.

---

# 9. UI Rules

La UI dépend des Applications.

Elle ne contient jamais :

- logique métier
- calculs complexes
- règles métier

Elle présente uniquement les données.

---

# 10. Engine Rules

Les Engines sont indépendants.

Ils communiquent uniquement via le Platform Kernel.

Exemple

```text
Permission Engine

↓

Platform Kernel

↓

Navigation Engine
```

Jamais :

```text
Permission Engine

↓

Navigation Engine
```

---

# 11. Service Rules

Les Services :

✔ encapsulent une logique technique

✔ sont réutilisables

Ils ne retournent jamais :

- Response HTTP
- Vue Filament
- JSON

---

# 12. Action Rules

Chaque Action représente un seul cas d'utilisation.

Exemples

```
CreateStudentAction

PublishReportAction

ApproveInvoiceAction
```

Une Action ne lance jamais une autre Action.

La coordination appartient à l'Application.

---

# 13. Repository Rules

Les Repositories sont responsables :

- lecture
- écriture
- requêtes

Ils ne contiennent jamais :

- logique métier
- permissions
- notifications

---

# 14. Event Rules

Les Events représentent uniquement des faits.

Exemples

```
StudentCreated

PaymentValidated

ExamPublished
```

Ils ne contiennent aucune logique.

---

# 15. Dependency Matrix

| From | Can depend on |
|--------|---------------|
| UI | Applications |
| Applications | Domains Platform Foundation |
| Domains | Platform Foundation |
| Platform | Foundation |
| Foundation | Nothing |

---

# 16. Forbidden Dependencies

Interdit :

Applications

↓

Applications

---

Interdit

Foundation

↓

UI

---

Interdit

Domains

↓

Laravel Controllers

---

Interdit

Platform

↓

Filament

---

Interdit

Engine

↓

Engine

(sans passer par Platform Kernel)

---

# 17. Workflow

Nouvelle fonctionnalité

↓

Identifier l'Application

↓

Identifier le Domain

↓

Créer l'Action

↓

Créer les Services

↓

Créer la UI

↓

Créer les Tests

---

# 18. Constraints

Toutes les règles précédentes sont obligatoires.

Toute exception nécessite :

- une ADR
- une revue d'architecture
- une justification documentée

---

# 19. Future Evolution

Les prochaines versions intégreront :

- Validation automatique des dépendances
- Diagrammes générés automatiquement
- Analyse statique de l'architecture
- Fitness Functions
- Contrôle CI des violations

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-27

## Related Documents

- Engineering_Principles
- Coding_Standards
- Project_Structure
- Architecture_Decisions