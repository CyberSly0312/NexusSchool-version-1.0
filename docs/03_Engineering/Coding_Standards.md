# Coding_Standards.md

# Nexus Platform
## Coding Standards

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Ce document définit les standards de développement de Nexus Platform.

Son objectif est de garantir un code cohérent, lisible, maintenable et évolutif à travers l'ensemble de la plateforme.

Ces standards s'appliquent à tous les composants de Nexus Platform.

---

# 2. Scope

Ce document couvre :

- Les conventions PHP
- Laravel
- Filament
- Les conventions de nommage
- L'organisation des classes
- Les méthodes
- Les tests
- Les conventions SQL

Ce document ne couvre pas :

- Git
- CI/CD
- Déploiement

---

# 3. Principles

Les standards de développement reposent sur les principes suivants.

## Readability First

Le code est principalement écrit pour être lu.

---

## Consistency

Une même règle s'applique partout.

---

## Explicitness

Le comportement du code doit être évident.

---

## Small Components

Les composants doivent rester simples.

---

## SOLID

Toutes les classes respectent les principes SOLID.

---

## Convention over Configuration

Les conventions sont préférées aux configurations multiples.

---

# 4. Architecture

Les dépendances suivent toujours cette hiérarchie.

```text
UI

↓

Application

↓

Domain

↓

Platform

↓

Foundation
```

Les dépendances inverses sont interdites.

---

# 5. Components

## Classes

Une classe = une responsabilité.

---

## Interfaces

Les interfaces définissent les contrats.

---

## Traits

Les Traits doivent rester rares.

Ils servent uniquement à partager un comportement simple.

---

## Enums

Toutes les constantes métier doivent être représentées par des Enums.

---

## Value Objects

Les valeurs métier doivent être immuables.

Exemple :

- Money
- Email
- PhoneNumber
- SchoolYear

---

## Services

Les Services encapsulent les traitements complexes.

Ils ne doivent jamais contenir de logique d'affichage.

---

## Actions

Les traitements unitaires sont implémentés sous forme d'Actions.

Exemple :

CreateStudentAction

PublishReportCardAction

ApprovePaymentAction

---

## Policies

Les Policies contrôlent l'autorisation.

Elles ne contiennent aucune logique métier.

---

# 6. Responsibilities

| Élément | Responsabilité |
|----------|----------------|
| Controller | Entrée HTTP |
| Action | Exécution d'un cas d'utilisation |
| Service | Logique technique |
| Repository | Accès aux données |
| Policy | Autorisation |
| Event | Notification |
| Listener | Réaction à un événement |
| Job | Traitement asynchrone |
| DTO | Transport de données |
| Resource | Transformation des réponses |

Chaque composant possède une responsabilité unique.

---

# 7. Workflow

Le développement suit le processus suivant.

Requirement

↓

Action

↓

Validation

↓

Domain

↓

Persistence

↓

Event

↓

Response

Chaque étape est indépendante.

---

# 8. Integration

Les Coding Standards s'intègrent avec :

- Engineering Principles
- Git Strategy
- Testing Strategy
- Code Review
- CI/CD Pipeline

Ils constituent la référence pour toutes les revues de code.

---

# 9. Constraints

Les règles suivantes sont obligatoires.

## PHP

- PSR-12 obligatoire.
- `declare(strict_types=1);` dans tous les nouveaux fichiers PHP.
- Types explicites pour les paramètres, propriétés et valeurs de retour.
- Pas de méthodes de plus de 40 lignes (objectif, pas règle absolue).
- Pas de classe dépassant environ 300 lignes sans justification documentée.

---

## Laravel

- Utiliser l'Injection de Dépendances.
- Éviter les Facades dans la logique métier lorsque l'abstraction est préférable.
- Utiliser les Form Requests pour la validation HTTP.
- Les Models ne doivent pas contenir de logique métier complexe.

---

## Base de données

- UUID/ULID comme identifiants principaux.
- Nommage en `snake_case`.
- Clés étrangères explicites.
- Soft Deletes lorsque pertinent.
- Index pour les colonnes fréquemment recherchées.

---

## Nommage

Classes

```text
StudentService
PublishReportAction
AcademicYear
```

Méthodes

```php
createStudent()
publishReportCard()
calculateAverage()
```

Variables

```php
$student
$academicYear
$reportCard
```

Constantes

```php
MAX_STUDENTS
DEFAULT_LANGUAGE
```

---

## Commentaires

Les commentaires expliquent **pourquoi**, jamais **quoi**.

❌ Mauvais

```php
// Increment counter
$counter++;
```

✅ Bon

```php
// The ministry requires sequential numbering per academic year.
$counter++;
```

---

## Exceptions

- Ne jamais capturer une exception sans la traiter.
- Utiliser des exceptions spécifiques.
- Les messages doivent être utiles aux développeurs.
- Les informations sensibles ne doivent jamais être exposées.

---

## Logging

- Journaliser les événements importants.
- Ne jamais enregistrer les mots de passe, tokens ou données sensibles.
- Utiliser des niveaux de journalisation appropriés.

---

# 10. Future Evolution

Les futures versions pourront intégrer :

- PHPStan niveau maximal
- Larastan
- Rector
- Analyse d'architecture automatisée
- Détection automatique des violations SOLID
- Mesure continue de la complexité cyclomatique
- Vérification automatique des conventions Nexus

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-27

## Related Documents

- Engineering_Principles
- Git_Strategy
- Testing_Strategy
- Code_Review_Guidelines
- Dependency_Guidelines