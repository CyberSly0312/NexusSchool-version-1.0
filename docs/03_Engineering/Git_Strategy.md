# Git_Strategy.md

# Nexus Platform
## Git Strategy

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Ce document définit la stratégie Git officielle de Nexus Platform.

Son objectif est de garantir un historique clair, des livraisons maîtrisées et une collaboration efficace entre tous les contributeurs.

Git constitue la source unique de vérité du code source.

---

# 2. Scope

Ce document couvre :

- La stratégie de branches
- Les conventions de nommage
- Les commits
- Les Pull Requests
- Les Releases
- Les Hotfixes

Ce document ne couvre pas :

- Les standards de codage
- Les tests
- Le déploiement

---

# 3. Principles

## Small Changes

Une branche = une responsabilité.

---

## Atomic Commits

Chaque commit représente une modification logique unique.

---

## Traceability

Toute modification est traçable.

---

## Review Before Merge

Aucune branche n'est fusionnée sans revue.

---

## Stable Main

La branche principale est toujours déployable.

---

# 4. Branch Model

```text
main

│

├── develop

│

├── feature/...

├── bugfix/...

├── hotfix/...

├── release/...

└── spike/...
```

---

# 5. Branch Naming

## Feature

```text
feature/student-admission
feature/report-card-engine
feature/mobile-money
```

---

## Bugfix

```text
bugfix/payment-validation

bugfix/login-timeout
```

---

## Release

```text
release/v1.2.0
```

---

## Hotfix

```text
hotfix/session-security
```

---

## Spike

Branche expérimentale.

Jamais fusionnée directement.

```text
spike/redis-cache
spike/graphql
```

---

# 6. Commit Convention

Format

```text
type(scope): description
```

Exemples

```text
feat(student): add admission workflow

fix(payment): validate invoice status

docs(api): update authentication guide

test(finance): improve payment tests

refactor(identity): simplify login flow

perf(report): optimize report generation

chore(deps): update Laravel
```

---

## Types autorisés

| Type | Description |
|-------|-------------|
| feat | Nouvelle fonctionnalité |
| fix | Correction |
| docs | Documentation |
| refactor | Refactoring |
| test | Tests |
| perf | Performance |
| style | Mise en forme |
| build | Build |
| ci | CI/CD |
| chore | Maintenance |

---

# 7. Pull Requests

Chaque Pull Request doit contenir :

- Objectif
- Description
- Documents impactés
- Tests exécutés
- Captures si UI
- ADR concerné si applicable

---

# 8. Merge Policy

Autorisés :

✅ Merge via Pull Request

✅ Squash Merge

Interdits :

❌ Push direct sur main

❌ Push direct sur release

❌ Force Push sur branches protégées

---

# 9. Release Workflow

```text
develop

↓

release

↓

tests

↓

validation

↓

main

↓

tag

↓

deployment
```

---

# 10. Hotfix Workflow

```text
main

↓

hotfix

↓

review

↓

main

↓

develop
```

---

# 11. Versioning

Le projet suit Semantic Versioning.

```text
MAJOR.MINOR.PATCH
```

Exemple

```text
1.0.0

1.1.0

1.1.1
```

---

# 12. Constraints

Les règles suivantes sont obligatoires.

- Pas de commit "test".
- Pas de commit "update".
- Pas de commit "final".
- Pas de commit "fix".

Chaque message doit être explicite.

---

# 13. Future Evolution

Les prochaines versions intégreront :

- Signature obligatoire des commits
- Release automatique
- Changelog automatique
- Versionnement automatique
- Protection avancée des branches

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-27

## Related Documents

- Engineering_Principles
- Development_Workflow
- Release_Policy
- CI_CD_Pipeline