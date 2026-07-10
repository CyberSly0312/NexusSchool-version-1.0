# Academic Module
# Enums
## AcademicYearStatus

Version : 1.0

---

# Objectif

AcademicYearStatus définit les différents états possibles d'une année académique tout au long de son cycle de vie.

Cet Enum est utilisé afin d'éviter l'utilisation de chaînes de caractères ("Draft", "Active", etc.) dispersées dans l'application.

---

# Utilisation

AcademicYearStatus est utilisé par :

- AcademicYear
- AcademicYearService
- Validation Rules
- DTOs
- Events
- Policies
- Workflows

---

# Valeurs

## Draft

Valeur

```
Draft
```

Description

Année académique en préparation.

Caractéristiques

- modifiable
- non visible aux utilisateurs
- aucune inscription autorisée

---

## Active

Valeur

```
Active
```

Description

Année académique actuellement en cours.

Caractéristiques

- inscriptions autorisées
- emploi du temps actif
- évaluations autorisées
- présence autorisée

---

## Closed

Valeur

```
Closed
```

Description

Année académique terminée.

Caractéristiques

- lecture uniquement
- aucune modification
- promotions autorisées
- rapports disponibles

---

## Archived

Valeur

```
Archived
```

Description

Année académique archivée définitivement.

Caractéristiques

- totalement en lecture seule
- aucune modification
- utilisée uniquement pour l'historique

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
Archived
```

---

# Transitions autorisées

| De | Vers | Autorisé |
|----|------|----------|
| Draft | Active | Oui |
| Active | Closed | Oui |
| Closed | Archived | Oui |
| Draft | Closed | Non |
| Active | Archived | Non |
| Archived | Active | Non |

---

# Règles métier

Draft

- une seule année peut être préparée.

---

Active

- une seule année active par école.
- toutes les opérations académiques utilisent cette année.

---

Closed

- aucune inscription nouvelle.
- résultats verrouillés.
- promotions possibles.

---

Archived

- consultation uniquement.
- suppression interdite.

---

# Exemples d'utilisation

Validation

```
if ($academicYear->status === AcademicYearStatus::Active)
```

---

Transition

```
Draft → Active
```

```
Active → Closed
```

```
Closed → Archived
```

---

# Contraintes

Une école ne peut posséder qu'une seule année académique Active à un instant donné.

Une année Archived ne peut jamais redevenir Active.

---

# Tests

✓ Toutes les valeurs existent

✓ Les transitions sont valides

✓ Les transitions interdites sont bloquées

✓ Une seule année Active

---

# Résumé

AcademicYearStatus représente le cycle de vie complet d'une année académique.

Il garantit :

- une cohérence métier ;
- un contrôle des transitions ;
- une meilleure lisibilité du code ;
- une suppression des chaînes de caractères magiques.