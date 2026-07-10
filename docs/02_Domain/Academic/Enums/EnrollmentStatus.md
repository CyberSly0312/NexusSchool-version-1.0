# Academic Module
# Enums
## EnrollmentStatus

Version : 1.0

---

# Objectif

EnrollmentStatus définit les différents états possibles d'une inscription académique.

Une inscription (Enrollment) évolue tout au long de l'année scolaire. Cet Enum garantit que toutes les transitions respectent les règles métier du système.

---

# Utilisation

EnrollmentStatus est utilisé par :

- Enrollment
- EnrollmentService
- PromotionService
- Validation Rules
- DTOs
- Events
- Policies
- Workflows

---

# Valeurs

## Pending

Valeur

```
Pending
```

Description

Inscription créée mais en attente de validation.

Caractéristiques

- non active
- aucune présence
- aucune note
- aucun accès aux services scolaires

---

## Active

Valeur

```
Active
```

Description

L'élève est officiellement inscrit.

Caractéristiques

- présence autorisée
- évaluations autorisées
- accès à tous les services

---

## Suspended

Valeur

```
Suspended
```

Description

Inscription temporairement suspendue.

Caractéristiques

- présence bloquée
- évaluations bloquées
- historique conservé

---

## Transferred

Valeur

```
Transferred
```

Description

L'élève a été transféré.

Caractéristiques

- ancienne inscription clôturée
- nouvelle inscription créée
- historique conservé

---

## Graduated

Valeur

```
Graduated
```

Description

L'élève a terminé son cursus.

Caractéristiques

- dossier clôturé
- lecture seule
- historique conservé

---

## Withdrawn

Valeur

```
Withdrawn
```

Description

L'élève a quitté définitivement l'établissement.

Caractéristiques

- inscription inactive
- aucune nouvelle activité
- historique disponible

---

## Cancelled

Valeur

```
Cancelled
```

Description

Inscription annulée avant son activation.

Caractéristiques

- aucune activité académique
- aucune présence
- aucune note

---

# Diagramme du cycle de vie

```
Pending
    │
    ▼
Active
    │
 ┌──┼─────────────┬─────────────┐
 ▼  ▼             ▼             ▼
Suspended  Transferred  Graduated  Withdrawn
    │
    ▼
Active
```

---

# Transitions autorisées

| De | Vers | Autorisé |
|----|------|----------|
| Pending | Active | Oui |
| Pending | Cancelled | Oui |
| Active | Suspended | Oui |
| Suspended | Active | Oui |
| Active | Transferred | Oui |
| Active | Graduated | Oui |
| Active | Withdrawn | Oui |
| Graduated | Active | Non |
| Withdrawn | Active | Non |
| Cancelled | Active | Non |

---

# Règles métier

Pending

- aucune activité académique.

---

Active

- toutes les fonctionnalités sont disponibles.

---

Suspended

- suspension temporaire.
- conservation complète de l'historique.

---

Transferred

- création d'une nouvelle inscription.
- clôture de l'ancienne.

---

Graduated

- inscription définitivement clôturée.

---

Withdrawn

- sortie définitive de l'établissement.

---

Cancelled

- inscription abandonnée avant validation.

---

# Contraintes

Une seule inscription Active par élève et par année académique.

Une inscription Graduated ou Withdrawn ne peut jamais redevenir Active.

---

# Exemples

Validation

```php
EnrollmentStatus::Active
```

Transition

```
Pending → Active
```

```
Active → Suspended
```

```
Suspended → Active
```

```
Active → Graduated
```

---

# Tests

✓ Toutes les valeurs existent

✓ Toutes les transitions autorisées fonctionnent

✓ Les transitions interdites sont refusées

✓ Une seule inscription active par année

---

# Résumé

EnrollmentStatus représente le cycle de vie complet d'une inscription académique.

Il garantit :

- une cohérence fonctionnelle ;
- un contrôle des transitions ;
- une meilleure lisibilité du code ;
- une suppression des chaînes de caractères dispersées dans l'application.