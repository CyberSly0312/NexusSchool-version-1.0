# Academic Module
# Enums
## PromotionStatus

Version : 1.0

---

# Objectif

PromotionStatus définit les différents états du processus de promotion académique.

Une promotion ne correspond pas uniquement au passage d'un élève dans une classe supérieure. C'est un processus métier composé de plusieurs étapes.

Cet Enum garantit le suivi cohérent de ce processus dans l'ensemble du système.

---

# Utilisation

PromotionStatus est utilisé par :

- Promotion
- PromotionService
- EnrollmentService
- DTOs
- Events
- Validation Rules
- Workflows

---

# Valeurs

## Pending

Valeur

```
Pending
```

Description

La promotion est en attente de traitement.

Caractéristiques

- résultats disponibles
- décision non validée
- aucune modification de l'inscription

---

## Approved

Valeur

```
Approved
```

Description

La promotion est validée.

Caractéristiques

- décision pédagogique approuvée
- prête à être exécutée

---

## Completed

Valeur

```
Completed
```

Description

La promotion est entièrement exécutée.

Caractéristiques

- nouvelle inscription créée
- ancienne inscription clôturée
- historique mis à jour

---

## Cancelled

Valeur

```
Cancelled
```

Description

La promotion a été annulée.

Caractéristiques

- aucune modification appliquée
- historique conservé

---

## Failed

Valeur

```
Failed
```

Description

La promotion a échoué.

Caractéristiques

- rollback exécuté
- journalisation obligatoire
- intervention administrative requise

---

# Diagramme du cycle de vie

```
Pending
    │
    ▼
Approved
    │
 ┌──┴────────────┐
 ▼               ▼
Completed     Failed
                   │
                   ▼
             Cancelled
```

---

# Transitions autorisées

| De | Vers | Autorisé |
|----|------|----------|
| Pending | Approved | Oui |
| Pending | Cancelled | Oui |
| Approved | Completed | Oui |
| Approved | Failed | Oui |
| Failed | Cancelled | Oui |
| Completed | Pending | Non |
| Cancelled | Approved | Non |

---

# Règles métier

Pending

- promotion en attente de validation.

---

Approved

- toutes les vérifications sont terminées.
- prête pour exécution.

---

Completed

- promotion irréversible.
- historique conservé.

---

Failed

- rollback obligatoire.
- erreur enregistrée.

---

Cancelled

- processus arrêté.
- aucune modification académique.

---

# Contraintes

Une promotion Completed ne peut jamais être réouverte.

Une promotion Failed doit toujours être journalisée.

Une promotion Cancelled ne peut pas être exécutée.

---

# Exemples

```php
PromotionStatus::Pending
```

```php
PromotionStatus::Approved
```

```php
PromotionStatus::Completed
```

---

# Tests

✓ Toutes les valeurs existent

✓ Les transitions sont respectées

✓ Rollback correctement exécuté

✓ Les états terminaux sont protégés

---

# Résumé

PromotionStatus représente le cycle de vie complet d'un processus de promotion académique.

Il garantit :

- un suivi précis des promotions ;
- une gestion sécurisée des erreurs ;
- des transitions contrôlées ;
- une meilleure traçabilité des opérations de fin d'année.