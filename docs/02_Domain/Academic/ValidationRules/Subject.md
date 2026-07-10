# Academic Module
# Validation Rules
## Subject

Version : 1.0

---

# Objectif

Définir les règles de validation des matières académiques.

Une Subject représente une discipline enseignée dans un ou plusieurs niveaux académiques.

Exemples :

- Mathématiques
- Français
- Physique
- Chimie
- Histoire-Géographie
- SVT
- Informatique

Une matière pourra ensuite être associée :

- aux niveaux
- aux séries
- aux enseignants
- aux emplois du temps
- aux examens
- aux coefficients

---

# Entité concernée

Subject

Exemple

| Name | Code | Status |
|------|------|--------|
| Mathématiques | MATH | Active |
| Français | FR | Active |
| SVT | SVT | Active |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Program | Oui |
| Name | Oui |
| Code | Oui |
| Status | Oui |

---

# Validation du nom

Le nom est obligatoire.

Minimum

2 caractères

Maximum

100 caractères.

---

# Exemples valides

Mathématiques

Français

Sciences Physiques

Histoire-Géographie

Technologie

---

# Exemples invalides

#

@

AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA

---

# Validation du code

Le code est obligatoire.

Longueur

Minimum

2 caractères

Maximum

15 caractères.

Le code doit être :

- lisible
- unique
- stable

---

# Exemples valides

MATH

FR

SVT

HG

PHY

INFO

ANG

---

# Exemples invalides

##

A B

MATHEMATIQUES_GENERALES_2025

---

# Unicité

Le nom

doit être unique

dans un même Program.

Le code

doit également être unique

dans un même Program.

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

La matière est en préparation.

Elle ne peut être utilisée.

---

Active

La matière peut être :

- enseignée
- planifiée
- évaluée
- notée

---

Inactive

La matière reste consultable.

Aucune nouvelle utilisation n'est autorisée.

---

Archived

Historique uniquement.

Lecture seule.

---

# Affectation

Une matière

peut être associée

à plusieurs :

- Levels
- Series
- Classrooms

selon les besoins de l'établissement.

---

# Enseignants

Une matière

peut être enseignée

par plusieurs enseignants.

Un enseignant

peut enseigner

plusieurs matières.

---

# Suppression

Suppression interdite

si la matière possède :

- des emplois du temps
- des notes
- des examens
- des coefficients
- des devoirs
- des bulletins
- des enseignants associés

---

# Modification

Le Code

ne peut plus être modifié

si la matière est utilisée.

Le nom reste modifiable.

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
| SB001 | Nom obligatoire |
| SB002 | Code obligatoire |
| SB003 | Programme obligatoire |
| SB004 | Nom déjà utilisé |
| SB005 | Code déjà utilisé |
| SB006 | Statut invalide |
| SB007 | Matière utilisée |
| SB008 | Suppression interdite |

---

# Messages utilisateur

SB001

Le nom de la matière est obligatoire.

SB002

Le code de la matière est obligatoire.

SB004

Cette matière existe déjà.

SB005

Ce code est déjà utilisé.

SB007

Impossible de modifier une matière déjà utilisée.

SB008

Impossible de supprimer une matière utilisée dans le système.

---

# Cas limites (Edge Cases)

Cas 1

Création de deux matières

Mathématiques

dans le même programme.

Résultat

Refus.

---

Cas 2

Création de Mathématiques

dans deux écoles différentes.

Résultat

Autorisé.

---

Cas 3

Suppression d'une matière

ayant déjà des notes.

Résultat

Refus.

---

Cas 4

Archivage d'une matière.

Résultat

Autorisé.

---

Cas 5

Modification du code

après création des emplois du temps.

Résultat

Refus.

---

# Tests obligatoires

✓ Programme obligatoire

✓ Nom obligatoire

✓ Code obligatoire

✓ Nom unique

✓ Code unique

✓ Statut valide

✓ Suppression protégée

✓ Validation multi-tenant

✓ Modification protégée

---

# Résumé

Une Subject est valide uniquement si :

- elle appartient à un Program existant ;
- son nom et son code sont uniques dans ce Program ;
- son statut autorise son utilisation ;
- elle ne peut être supprimée lorsqu'elle possède des dépendances ;
- son code devient immuable dès sa première utilisation.