# Academic Module
# Validation Rules
## AcademicYearValidation

Version : 1.0

---

# Objectif

Définir l'ensemble des règles de validation applicables à une année académique afin de garantir la cohérence du système scolaire.

Ces règles doivent être appliquées :

- Interface utilisateur
- API REST
- Services métier
- Importation de données
- Scripts automatisés

Aucune opération ne doit contourner ces validations.

---

# Entité concernée

AcademicYear

Exemple :

| Name | Start | End | Status |
|------|---------|------|---------|
| 2025-2026 | 2025-09-15 | 2026-07-31 | Active |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Name | Oui |
| Start Date | Oui |
| End Date | Oui |
| School | Oui |
| Status | Oui |

---

# Validation du nom

Le nom doit respecter le format :

```
YYYY-YYYY
```

Exemples valides

```
2025-2026
2026-2027
2030-2031
```

Exemples invalides

```
2025

2025/2026

2025_2026

Academic Year 2025

2025-2028
```

---

# Validation chronologique

Start Date

doit être

<

End Date

Toujours.

Interdiction

```
Start Date == End Date
```

ou

```
Start Date > End Date
```

---

# Durée minimale

Une année académique doit durer au minimum

9 mois

---

# Durée maximale

Une année académique ne peut dépasser

15 mois

---

# Unicité

Le nom doit être unique

dans une école.

Autorisé

School A

2025-2026

School B

2025-2026

Interdit

School A

2025-2026

School A

2025-2026

---

# Chevauchement

Deux années académiques d'une même école

ne doivent jamais se chevaucher.

Interdit

2025-09-01 → 2026-07-31

2026-01-01 → 2026-10-01

---

# Statut

Valeurs autorisées

Draft

Active

Archived

Closed

---

# Règles de statut

Draft

Peut être modifiée.

Active

Peut recevoir des données.

Closed

Lecture seule.

Archived

Historique uniquement.

---

# Année active

Une école

ne peut posséder

qu'une seule année active.

Si une nouvelle année devient Active

la précédente doit automatiquement passer à Closed.

---

# Suppression

Impossible de supprimer une année si :

- des Terms existent
- des Classes existent
- des Students existent
- des Grades existent
- des Timetables existent

Dans ce cas :

Soft Delete interdit

Hard Delete interdit

---

# Modification

Impossible de modifier :

Start Date

ou

End Date

si des périodes (Terms) existent déjà.

---

# Validation des dates

Toutes les dates des Terms

doivent être incluses

entre

AcademicYear.StartDate

et

AcademicYear.EndDate

---

# Validation multi-tenant

Toutes les validations

s'effectuent

à l'intérieur d'une seule école.

Aucune validation ne doit comparer les données de deux écoles différentes.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| AY001 | Nom obligatoire |
| AY002 | Format invalide |
| AY003 | Nom déjà utilisé |
| AY004 | Date de début invalide |
| AY005 | Date de fin invalide |
| AY006 | Chevauchement détecté |
| AY007 | Plusieurs années actives |
| AY008 | Suppression interdite |
| AY009 | Modification interdite |
| AY010 | Durée invalide |

---

# Messages utilisateur

AY001

```
Le nom de l'année académique est obligatoire.
```

AY002

```
Le format doit être AAAA-AAAA.
```

AY003

```
Cette année académique existe déjà.
```

AY006

```
Les dates chevauchent une autre année académique.
```

AY007

```
Une seule année académique peut être active.
```

AY008

```
Impossible de supprimer une année académique contenant des données.
```

---

# Cas limites (Edge Cases)

Cas 1

Activation d'une nouvelle année

Résultat attendu

Ancienne année → Closed

Nouvelle année → Active

---

Cas 2

Création d'une année avec une date de fin antérieure

Résultat attendu

Refus

---

Cas 3

Création de deux années identiques

Résultat attendu

Refus

---

Cas 4

Création d'une année couvrant déjà une période existante

Résultat attendu

Refus

---

# Tests obligatoires

✓ Nom obligatoire

✓ Format valide

✓ Dates cohérentes

✓ Durée minimale

✓ Durée maximale

✓ Unicité

✓ Une seule année active

✓ Aucun chevauchement

✓ Suppression protégée

✓ Validation multi-tenant

---

# Résumé

Une année académique est valide uniquement si :

- son nom respecte le format AAAA-AAAA ;
- ses dates sont cohérentes ;
- elle ne chevauche aucune autre année de la même école ;
- elle est unique dans l'établissement ;
- une seule année est active à la fois ;
- aucune suppression n'est autorisée lorsqu'elle contient des données métier.