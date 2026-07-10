# Academic Module
# Validation Rules
## Series

Version : 1.0

---

# Objectif

Définir les règles de validation des séries académiques.

Une Series représente une spécialisation ou une filière appartenant à un niveau académique.

Exemples :

- A
- A4
- C
- D
- F1
- F2
- F3
- G1
- G2

Toutes les écoles n'utilisent pas des séries. Le système doit donc permettre leur activation ou leur désactivation selon le programme.

---

# Entité concernée

Series

Exemple

| Name | Code | Level | Program |
|------|------|-------|---------|
| Série A | A | Première | Général |
| Série C | C | Première | Général |
| Série D | D | Terminale | Général |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Program | Oui |
| Level | Oui |
| Name | Oui |
| Code | Oui |
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
Série A

Série C

Série D

Filière F3

Gestion G2
```

---

# Validation du code

Le code est obligatoire.

Longueur

Minimum

1 caractère

Maximum

10 caractères

---

# Exemples valides

```
A

C

D

F3

G2

TI
```

---

# Exemples invalides

```
##

A B

SERIE_GENERALE_A
```

---

# Unicité

Le Code

doit être unique

dans un même Level.

Le Name

doit également être unique

dans un même Level.

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

La série est en préparation.

Aucune classe ne peut être créée.

---

Active

Création autorisée de :

- Classrooms
- Subjects
- Subject Coefficients
- Enrollments

---

Inactive

Les nouvelles classes sont interdites.

Les données existantes restent consultables.

---

Archived

Lecture seule.

Historique uniquement.

---

# Dépendances

Un Level

possède

plusieurs Series.

Une Series

appartient

à un seul Level.

---

# Suppression

Suppression interdite

si la série possède :

- des Classes
- des Élèves
- des Inscriptions
- des Matières
- des Coefficients
- des Bulletins
- des Notes
- des Emplois du temps

---

# Modification

Impossible de modifier

le Code

si des classes existent déjà.

Le nom reste modifiable.

---

# Compatibilité avec les programmes

Une Series

doit appartenir

au même Program

que son Level.

Exemple valide

Programme Général

↓

Première

↓

Série C

Exemple invalide

Programme Technique

↓

Première

↓

Série C (Général)

---

# Validation multi-tenant

Toutes les validations

s'effectuent

uniquement

dans la même école.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| SR001 | Nom obligatoire |
| SR002 | Code obligatoire |
| SR003 | Programme obligatoire |
| SR004 | Niveau obligatoire |
| SR005 | Nom déjà utilisé |
| SR006 | Code déjà utilisé |
| SR007 | Série utilisée |
| SR008 | Suppression interdite |
| SR009 | Statut invalide |
| SR010 | Programme incompatible |

---

# Messages utilisateur

SR001

```
Le nom de la série est obligatoire.
```

SR002

```
Le code de la série est obligatoire.
```

SR005

```
Cette série existe déjà dans ce niveau.
```

SR006

```
Ce code est déjà utilisé.
```

SR008

```
Impossible de supprimer une série déjà utilisée.
```

SR010

```
Cette série n'est pas compatible avec le programme sélectionné.
```

---

# Cas limites (Edge Cases)

Cas 1

Création de deux séries "C"

dans le même niveau.

Résultat

Refus.

---

Cas 2

Création d'une série identique

dans un autre niveau.

Résultat

Autorisé.

---

Cas 3

Suppression d'une série

contenant plusieurs classes.

Résultat

Refus.

---

Cas 4

Archivage d'une série historique.

Résultat

Autorisé.

---

Cas 5

Modification du code

après création des classes.

Résultat

Refus.

---

# Tests obligatoires

✓ Programme obligatoire

✓ Niveau obligatoire

✓ Nom obligatoire

✓ Code obligatoire

✓ Nom unique

✓ Code unique

✓ Compatibilité Program ↔ Level

✓ Suppression protégée

✓ Validation multi-tenant

✓ Modification protégée

---

# Résumé

Une Series est valide uniquement si :

- elle appartient à un Level existant ;
- elle appartient au même Program que ce Level ;
- son nom et son code sont uniques dans ce Level ;
- son statut est cohérent avec son utilisation ;
- elle ne peut être supprimée lorsqu'elle possède des dépendances ;
- son code devient immuable dès que des classes sont créées.