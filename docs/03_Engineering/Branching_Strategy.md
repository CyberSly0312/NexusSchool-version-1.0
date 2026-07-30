# Branching_Strategy.md

# Nexus Platform
## Branching Strategy

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Ce document définit la stratégie de branches officielle de Nexus Platform.

Son objectif est d'organiser le développement afin de garantir une évolution parallèle des différentes Applications, Engines et composants de la plateforme tout en conservant un historique Git propre et cohérent.

---

# 2. Scope

Ce document couvre :

- Les branches principales
- Les branches de développement
- Les branches de fonctionnalités
- Les branches de correction
- Les branches de release
- Les branches de hotfix

Ce document ne couvre pas :

- Les conventions de commits
- Les revues de code
- Les pipelines CI/CD

---

# 3. Principles

## One Branch, One Purpose

Chaque branche possède un objectif unique.

---

## Short-Lived Branches

Les branches doivent être de courte durée.

---

## Continuous Integration

Les branches sont fusionnées régulièrement afin d'éviter les divergences importantes.

---

## Stable Main

La branche `main` est toujours déployable.

---

## Traceability

Chaque branche est liée à une User Story, une Issue ou une ADR.

---

# 4. Branch Architecture

```text
main
│
├── develop
│
├── feature/
├── bugfix/
├── hotfix/
├── release/
└── spike/
```

---

# 5. Main Branches

## main

Contient uniquement les versions stables.

Chaque commit correspond à une version publiée.

---

## develop

Contient les développements validés avant une Release.

Toutes les nouvelles fonctionnalités sont intégrées ici.

---

# 6. Feature Branches

Les fonctionnalités suivent le format :

```text
feature/{application}/{feature-name}
```

Exemples :

```text
feature/academic/student-admission

feature/academic/report-cards

feature/finance/invoices

feature/hr/employees

feature/library/book-loans

feature/platform/navigation-engine
```

---

# 7. Bugfix Branches

Format :

```text
bugfix/{application}/{bug-name}
```

Exemples :

```text
bugfix/finance/payment-validation

bugfix/academic/grade-calculation
```

---

# 8. Hotfix Branches

Format :

```text
hotfix/{issue-name}
```

Exemples :

```text
hotfix/session-timeout

hotfix/database-lock
```

Les Hotfix sont créés depuis `main`.

---

# 9. Release Branches

Format :

```text
release/v1.2.0
```

Une Release est gelée.

Seules les corrections critiques sont autorisées.

---

# 10. Spike Branches

Les expérimentations utilisent :

```text
spike/{topic}
```

Exemples :

```text
spike/octane

spike/graphql

spike/event-sourcing
```

Les Spikes ne sont jamais fusionnés directement.

---

# 11. Merge Strategy

Les règles suivantes sont obligatoires.

- Les Pull Requests sont obligatoires.
- Les Squash Merge sont privilégiés.
- Les conflits sont résolus avant validation.
- Les branches supprimées après fusion.

---

# 12. Branch Lifecycle

```text
Issue

↓

Feature Branch

↓

Development

↓

Pull Request

↓

Review

↓

Merge into Develop

↓

Release

↓

Main

↓

Delete Branch
```

---

# 13. Naming Rules

Les noms utilisent uniquement :

- minuscules
- tirets
- noms explicites

Exemple :

```text
feature/academic/student-attendance
```

Interdit :

```text
feature/test

feature/new

feature/fix
```

---

# 14. Constraints

Les règles suivantes sont obligatoires.

- Aucun développement direct sur `main`.
- Aucun développement direct sur `release`.
- Les branches obsolètes sont supprimées.
- Les Pull Requests doivent être approuvées.
- Les pipelines CI doivent réussir avant fusion.

---

# 15. Future Evolution

Les prochaines versions pourront intégrer :

- Branches éphémères automatiques
- Preview Environments
- Merge Queue
- Protection avancée des branches
- Releases automatisées

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-27

## Related Documents

- Git_Strategy
- Code_Review_Guidelines
- CI_CD_Pipeline
- Release_Policy