# Academic Module
# Validation Rules
## Program

Version : 1.0

---

# Objectif

Définir les règles de validation des programmes académiques.

Un Program représente une filière ou un parcours d'études proposé par un établissement.

Exemples :

- Enseignement Général
- Enseignement Technique
- Enseignement Professionnel
- Formation Continue

Un Program constitue le niveau supérieur de la hiérarchie académique.

---

# Entité concernée

Program

Exemple

| Name | Code | Status |
|------|------|--------|
| Enseignement Général | GEN | Active |
| Enseignement Technique | TEC | Active |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| School | Oui |
| Name | Oui |
| Code | Oui |
| Status | Oui |

---

# Validation du nom

Le nom est obligatoire.

Longueur minimale

3 caractères

Longueur maximale

100 caractères

---

# Exemples valides

Enseignement Général

Enseignement Technique

Formation Professionnelle

Formation Continue

---

# Exemples invalides

```
A

##

1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890ABC
```

---

# Validation du code

Le code est obligatoire.

Il doit être :

- unique
- court
- facilement identifiable

Longueur

Minimum

2 caractères

Maximum

10 caractères

---

# Exemples valides

```
GEN

TEC

PRO

CONT

TVET
```

---

# Exemples invalides

```
GENERAL_EDUCATION_PROGRAM

##

A B

GEN-2025
```

---

# Unicité

Le Name

doit être unique

dans une même école.

Le Code

doit également être unique.

Autorisé

School A

GEN

School B

GEN

Interdit

School A

GEN

School A

GEN

---

# Statut

Valeurs autorisées

Draft

Active

Inactive

Archived

---

# Règles métier

Draft

Le programme est en préparation.

Aucune classe ne peut être créée.

---

Active

Le programme peut être utilisé.

Création autorisée de :

- Levels
- Series
- Classes
- Subjects

---

Inactive

Aucun nouvel élément ne peut être ajouté.

Les données existantes restent consultables.

---

Archived

Programme historique.

Lecture seule.

---

# Suppression

Suppression interdite si le programme possède :

- des Levels
- des Series
- des Classes
- des Students
- des Subjects
- des Timetables
- des Results

---

# Modification

Impossible de modifier

Code

si le programme est déjà utilisé.

Le nom reste modifiable.

---

# Validation multi-tenant

Toutes les validations

s'effectuent

uniquement

dans l'école propriétaire.

---

# Dépendances

Un Program

peut contenir

plusieurs

Levels.

Un Level

ne peut appartenir

qu'à un seul Program.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| PR001 | Nom obligatoire |
| PR002 | Code obligatoire |
| PR003 | Nom déjà utilisé |
| PR004 | Code déjà utilisé |
| PR005 | Statut invalide |
| PR006 | Programme utilisé |
| PR007 | Suppression interdite |
| PR008 | Longueur invalide |

---

# Messages utilisateur

PR001

```
Le nom du programme est obligatoire.
```

PR002

```
Le code du programme est obligatoire.
```

PR003

```
Ce nom de programme existe déjà.
```

PR004

```
Ce code est déjà utilisé.
```

PR006

```
Impossible de modifier le code d'un programme déjà utilisé.
```

PR007

```
Impossible de supprimer un programme contenant des données académiques.
```

---

# Cas limites (Edge Cases)

Cas 1

Création de deux programmes portant le même code.

Résultat

Refus.

---

Cas 2

Suppression d'un programme contenant des niveaux.

Résultat

Refus.

---

Cas 3

Archivage d'un programme contenant plusieurs promotions.

Résultat

Autorisé.

Le programme devient uniquement consultable.

---

Cas 4

Passage d'un programme Inactive à Active.

Résultat

Autorisé.

Les nouvelles créations redeviennent possibles.

---

# Tests obligatoires

✓ Nom obligatoire

✓ Code obligatoire

✓ Nom unique

✓ Code unique

✓ Longueur valide

✓ Statut valide

✓ Suppression protégée

✓ Modification du code interdite après utilisation

✓ Validation multi-tenant

---

# Résumé

Un Program est valide uniquement si :

- son nom est renseigné ;
- son code est unique dans l'établissement ;
- il appartient à une seule école ;
- son statut est cohérent avec son utilisation ;
- il ne peut être supprimé lorsqu'il possède des dépendances ;
- son code devient immuable dès qu'il est utilisé par des entités académiques.