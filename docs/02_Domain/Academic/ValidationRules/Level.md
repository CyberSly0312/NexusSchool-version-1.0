# Academic Module
# Validation Rules
## Level

Version : 1.0

---

# Objectif

Définir les règles de validation des niveaux académiques.

Un Level représente un niveau d'étude dans un programme académique.

Exemples :

- CP1
- CP2
- CE1
- CE2
- CM1
- CM2
- 6ème
- 5ème
- 4ème
- 3ème
- Seconde
- Première
- Terminale

Le système doit rester suffisamment flexible pour supporter les systèmes éducatifs de différents pays.

---

# Entité concernée

Level

Exemple

| Name | Code | Order | Program |
|------|------|-------|---------|
| Sixième | 6EME | 1 | Général |
| Cinquième | 5EME | 2 | Général |
| Seconde | 2ND | 5 | Général |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Program | Oui |
| Name | Oui |
| Code | Oui |
| Order | Oui |
| Status | Oui |

---

# Validation du nom

Le nom est obligatoire.

Longueur minimale

2 caractères

Longueur maximale

100 caractères

---

# Exemples valides

```
CP1
CM2
Sixième
Cinquième
Troisième
Seconde
Première
Terminale
```

---

# Validation du code

Le code est obligatoire.

Longueur

Minimum

2 caractères

Maximum

15 caractères

---

# Exemples valides

```
CP1

6EME

5EME

3EME

2ND

1ERE

TLE
```

---

# Exemples invalides

```
##

A B

LEVEL_2025_2026_001
```

---

# Validation de l'ordre

Order

doit être :

- numérique
- positif
- supérieur à zéro

---

# Exemple valide

```
1
2
3
4
5
6
```

---

# Exemple invalide

```
0

-1

1.5

ABC
```

---

# Unicité

Le nom

doit être unique

dans un même Program.

Le code

doit également être unique

dans un même Program.

L'ordre

doit être unique

dans un même Program.

---

# Statut

Valeurs autorisées

- Draft
- Active
- Inactive
- Archived

---

# Règles métier

Draft

Le niveau est en préparation.

Aucune classe ne peut être créée.

---

Active

Création autorisée de :

- Series
- Classrooms
- Subjects
- Enrollments

---

Inactive

Le niveau reste consultable.

Aucune nouvelle classe ne peut être créée.

---

Archived

Lecture seule.

Historique uniquement.

---

# Dépendances

Un Program

possède

plusieurs Levels.

Un Level

appartient

à un seul Program.

---

# Suppression

Suppression interdite

si le niveau possède :

- des Series
- des Classes
- des Élèves
- des Inscriptions
- des Bulletins
- des Notes
- des Emplois du temps

---

# Modification

Impossible de modifier

le Code

ou

l'Order

si des classes existent déjà.

Le nom reste modifiable.

---

# Validation hiérarchique

L'ordre doit représenter une progression logique.

Exemple

```
CP1

↓

CP2

↓

CE1

↓

CE2

↓

CM1

↓

CM2
```

ou

```
Sixième

↓

Cinquième

↓

Quatrième

↓

Troisième

↓

Seconde

↓

Première

↓

Terminale
```

---

# Validation multi-tenant

Toutes les validations

s'effectuent

uniquement

dans l'école propriétaire.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| LV001 | Nom obligatoire |
| LV002 | Code obligatoire |
| LV003 | Programme obligatoire |
| LV004 | Ordre invalide |
| LV005 | Nom déjà utilisé |
| LV006 | Code déjà utilisé |
| LV007 | Ordre déjà utilisé |
| LV008 | Niveau utilisé |
| LV009 | Suppression interdite |
| LV010 | Statut invalide |

---

# Messages utilisateur

LV001

```
Le nom du niveau est obligatoire.
```

LV002

```
Le code du niveau est obligatoire.
```

LV005

```
Ce niveau existe déjà dans ce programme.
```

LV006

```
Ce code est déjà utilisé.
```

LV007

```
Cet ordre est déjà utilisé.
```

LV009

```
Impossible de supprimer un niveau déjà utilisé.
```

---

# Cas limites (Edge Cases)

Cas 1

Deux niveaux possèdent le même ordre.

Résultat

Refus.

---

Cas 2

Suppression d'un niveau contenant plusieurs classes.

Résultat

Refus.

---

Cas 3

Modification du code après création des classes.

Résultat

Refus.

---

Cas 4

Archivage d'un niveau historique.

Résultat

Autorisé.

---

Cas 5

Réactivation d'un niveau Inactive.

Résultat

Les nouvelles classes peuvent être créées.

---

# Tests obligatoires

✓ Programme obligatoire

✓ Nom obligatoire

✓ Code obligatoire

✓ Ordre obligatoire

✓ Nom unique

✓ Code unique

✓ Ordre unique

✓ Suppression protégée

✓ Validation multi-tenant

✓ Modification protégée

---

# Résumé

Un Level est valide uniquement si :

- il appartient à un Program existant ;
- son nom, son code et son ordre sont uniques dans ce Program ;
- son statut est cohérent avec son cycle de vie ;
- il ne peut être supprimé lorsqu'il possède des dépendances ;
- son code et son ordre deviennent immuables dès que des classes sont créées.