# Academic Module
# Enums
## TermStatus

Version : 1.0

---

# Objectif

TermStatus définit les différents états possibles d'une période académique (Term).

Chaque période suit un cycle de vie précis permettant de contrôler les activités pédagogiques, les évaluations et la publication des résultats.

Cet Enum garantit une gestion cohérente des périodes académiques dans l'ensemble du système.

---

# Utilisation

TermStatus est utilisé par :

- AcademicTerm
- AcademicYear
- Gradebook
- Examination
- Attendance
- Report Cards
- DTOs
- Events
- Validation Rules

---

# Valeurs

## Draft

Valeur

```
Draft
```

Description

La période est en préparation.

Caractéristiques

- modifiable
- invisible aux enseignants
- aucune activité académique

---

## Active

Valeur

```
Active
```

Description

La période est actuellement en cours.

Caractéristiques

- évaluations autorisées
- présences autorisées
- saisie des notes autorisée

---

## Closed

Valeur

```
Closed
```

Description

La période est terminée.

Caractéristiques

- saisie des notes verrouillée
- présence verrouillée
- calcul des moyennes autorisé

---

## Published

Valeur

```
Published
```

Description

Les résultats sont publiés.

Caractéristiques

- bulletins disponibles
- consultation autorisée
- aucune modification académique

---

## Archived

Valeur

```
Archived
```

Description

La période est archivée.

Caractéristiques

- lecture seule
- historique uniquement
- suppression interdite

---

# Diagramme du cycle de vie

```
Draft
   │
   ▼
Active
   │
   ▼
Closed
   │
   ▼
Published
   │
   ▼
Archived
```

---

# Transitions autorisées

| De | Vers | Autorisé |
|----|------|----------|
| Draft | Active | Oui |
| Active | Closed | Oui |
| Closed | Published | Oui |
| Published | Archived | Oui |
| Draft | Published | Non |
| Active | Archived | Non |
| Archived | Active | Non |

---

# Règles métier

## Draft

- période en préparation
- paramètres modifiables

---

## Active

- présence autorisée
- évaluations autorisées
- devoirs autorisés

---

## Closed

- notes verrouillées
- présence verrouillée
- calcul des moyennes possible

---

## Published

- bulletins accessibles
- consultation autorisée
- modifications interdites

---

## Archived

- historique uniquement
- aucune opération métier

---

# Contraintes

Une seule période peut être Active dans une même année académique.

Une période Archived ne peut jamais redevenir Active.

Une période Published ne peut plus être modifiée.

---

# Exemples

Validation

```php
TermStatus::Active
```

Transition

```
Draft → Active
```

```
Active → Closed
```

```
Closed → Published
```

```
Published → Archived
```

---

# Tests

✓ Toutes les valeurs existent

✓ Les transitions sont valides

✓ Les transitions interdites sont refusées

✓ Une seule période active

✓ Publication impossible avant fermeture

---

# Résumé

TermStatus représente le cycle de vie complet d'une période académique.

Il garantit :

- une gestion cohérente des trimestres et semestres ;
- un contrôle strict des transitions ;
- une meilleure traçabilité des opérations académiques ;
- une suppression des chaînes de caractères magiques dans l'application.