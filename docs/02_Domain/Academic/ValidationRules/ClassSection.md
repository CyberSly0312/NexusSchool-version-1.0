# Academic Module
# Validation Rules
## ClassSection

Version : 1.0

---

# Objectif

Définir les règles de validation des subdivisions d'une classe.

Une ClassSection représente une subdivision interne d'une Classroom.

Elle permet de répartir les élèves en plusieurs groupes tout en conservant une même classe académique.

---

# Entité concernée

ClassSection

Exemple

| Classroom | Section |
|-----------|---------|
| Première C1 | Groupe A |
| Première C1 | Groupe B |
| Première C1 | Groupe C |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Classroom | Oui |
| Name | Oui |
| Code | Oui |
| Capacity | Oui |
| Status | Oui |

---

# Validation du nom

Le nom est obligatoire.

Minimum

2 caractères

Maximum

50 caractères.

---

# Exemples valides

```
A

B

Groupe A

Groupe B

Rouge

Bleue
```

---

# Validation du code

Le code est obligatoire.

Minimum

1 caractère

Maximum

10 caractères.

---

# Exemples valides

```
A

B

GA

GB

LAB1
```

---

# Unicité

Le Name

doit être unique

dans une Classroom.

Le Code

doit également être unique

dans une Classroom.

---

# Capacité

Capacity

doit être

un entier positif.

Minimum

1

Maximum

égal à la capacité de la Classroom.

---

# Validation des capacités

La somme

des capacités

de toutes les sections

ne doit jamais dépasser

la capacité totale

de la Classroom.

Exemple

Classroom

40 élèves

Sections

A = 20

B = 20

Résultat

Valide

---

Exemple

A = 25

B = 25

Total = 50

Résultat

Refus

---

# Statut

Valeurs autorisées

- Draft
- Active
- Closed
- Archived

---

# Règles métier

Draft

Section en préparation.

Aucun élève.

---

Active

Autorise :

- Affectation des élèves
- Emploi du temps
- Présences
- Évaluations

---

Closed

Plus aucune affectation.

---

Archived

Lecture seule.

---

# Affectation des élèves

Un élève

ne peut appartenir

qu'à une seule section

dans une même Classroom.

---

# Suppression

Suppression interdite

si la section possède :

- des Élèves
- des Présences
- des Notes
- des Bulletins
- des Emplois du temps

---

# Modification

Impossible de modifier

la Classroom

si des élèves sont déjà affectés.

Le nom reste modifiable.

---

# Validation multi-tenant

Toutes les validations

s'effectuent

dans la même école.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| CS001 | Classroom obligatoire |
| CS002 | Nom obligatoire |
| CS003 | Code obligatoire |
| CS004 | Nom déjà utilisé |
| CS005 | Code déjà utilisé |
| CS006 | Capacité invalide |
| CS007 | Capacité dépassée |
| CS008 | Élève déjà affecté |
| CS009 | Section utilisée |
| CS010 | Suppression interdite |

---

# Messages utilisateur

CS001

```
La classe est obligatoire.
```

CS004

```
Cette section existe déjà dans cette classe.
```

CS006

```
La capacité est invalide.
```

CS007

```
La capacité totale des sections dépasse celle de la classe.
```

CS008

```
Cet élève appartient déjà à une autre section.
```

CS010

```
Impossible de supprimer cette section.
```

---

# Cas limites (Edge Cases)

Cas 1

Création d'une troisième section

dépassant la capacité totale.

Résultat

Refus.

---

Cas 2

Affectation d'un élève

dans deux sections.

Résultat

Refus.

---

Cas 3

Suppression d'une section

ayant déjà des élèves.

Résultat

Refus.

---

Cas 4

Archivage d'une ancienne section.

Résultat

Autorisé.

---

# Tests obligatoires

✓ Classroom obligatoire

✓ Nom obligatoire

✓ Code obligatoire

✓ Nom unique

✓ Code unique

✓ Capacité valide

✓ Somme des capacités

✓ Affectation unique

✓ Suppression protégée

✓ Validation multi-tenant

---

# Résumé

Une ClassSection est valide uniquement si :

- elle appartient à une Classroom existante ;
- son nom et son code sont uniques dans cette classe ;
- sa capacité n'entraîne pas un dépassement de la capacité de la classe ;
- un élève ne peut appartenir qu'à une seule section de cette classe ;
- elle ne peut être supprimée lorsqu'elle contient des données académiques.