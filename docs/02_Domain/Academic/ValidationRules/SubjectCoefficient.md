# Academic Module
# Validation Rules
## SubjectCoefficient

Version : 1.0

---

# Objectif

Définir les règles de validation des coefficients des matières.

Un SubjectCoefficient détermine le poids d'une matière pour un niveau, une série et une année académique.

Le système doit permettre qu'une même matière possède des coefficients différents selon :

- le niveau
- la série
- le programme
- l'année académique

---

# Entité concernée

SubjectCoefficient

Exemple

| Subject | Level | Series | Coefficient |
|----------|--------|--------|------------|
| Mathématiques | Première | C | 7 |
| Mathématiques | Première | D | 5 |
| Français | Première | C | 3 |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Academic Year | Oui |
| Program | Oui |
| Level | Oui |
| Subject | Oui |
| Coefficient | Oui |
| Status | Oui |

Series est obligatoire uniquement lorsque le Level utilise des séries.

---

# Validation du coefficient

Le coefficient est obligatoire.

Type

Numérique

Valeur minimale

0.5

Valeur maximale

20

Les décimales sont autorisées.

---

# Exemples valides

```
1

2

2.5

4

5

7

10
```

---

# Exemples invalides

```
0

-1

21

ABC
```

---

# Unicité

Une combinaison

AcademicYear

+

Program

+

Level

+

Series

+

Subject

ne peut exister qu'une seule fois.

---

# Validation de la série

Si le Level

n'utilise pas de Series

Series doit être NULL.

Si le Level

utilise des Series

Series est obligatoire.

---

# Statut

Valeurs autorisées

- Draft
- Active
- Archived

---

# Règles métier

Draft

Le coefficient peut être modifié.

---

Active

Le coefficient est utilisé pour :

- calcul des moyennes
- bulletins
- classements
- statistiques

---

Archived

Historique uniquement.

Lecture seule.

---

# Modification

Le coefficient

ne peut plus être modifié

si :

- des notes existent
- des moyennes existent
- des bulletins existent

Toute modification doit se faire en créant une nouvelle configuration pour une nouvelle année académique.

---

# Suppression

Suppression interdite

si le coefficient est utilisé par :

- Notes
- Bulletins
- Calculs de moyenne
- Résultats
- Classements

---

# Validation métier

La matière

doit être autorisée

pour le Level concerné.

Exemple

Mathématiques

↓

Première C

Autorisé

---

Exemple

Mathématiques

↓

Niveau où la matière n'existe pas

Refus

---

# Validation multi-tenant

Toutes les validations

s'effectuent

uniquement

dans la même école.

---

# Cohérence académique

Les entités suivantes

doivent appartenir

au même contexte académique :

- AcademicYear
- Program
- Level
- Series
- Subject

Aucun mélange entre plusieurs programmes ou écoles n'est autorisé.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| SC001 | Année académique obligatoire |
| SC002 | Programme obligatoire |
| SC003 | Niveau obligatoire |
| SC004 | Matière obligatoire |
| SC005 | Coefficient invalide |
| SC006 | Coefficient déjà défini |
| SC007 | Série invalide |
| SC008 | Coefficient utilisé |
| SC009 | Suppression interdite |
| SC010 | Cohérence académique invalide |

---

# Messages utilisateur

SC001

```
L'année académique est obligatoire.
```

SC004

```
La matière est obligatoire.
```

SC005

```
Le coefficient doit être compris entre 0.5 et 20.
```

SC006

```
Un coefficient existe déjà pour cette combinaison.
```

SC008

```
Ce coefficient est déjà utilisé dans des calculs académiques.
```

SC009

```
Impossible de supprimer un coefficient utilisé.
```

---

# Cas limites (Edge Cases)

Cas 1

Deux coefficients

Mathématiques

Première C

2025-2026

Résultat

Refus.

---

Cas 2

Coefficient négatif.

Résultat

Refus.

---

Cas 3

Modification du coefficient

après publication des bulletins.

Résultat

Refus.

---

Cas 4

Création d'un coefficient

pour une matière inexistante dans le niveau.

Résultat

Refus.

---

Cas 5

Archivage d'un ancien coefficient.

Résultat

Autorisé.

---

# Tests obligatoires

✓ AcademicYear obligatoire

✓ Program obligatoire

✓ Level obligatoire

✓ Subject obligatoire

✓ Série conditionnelle

✓ Coefficient valide

✓ Unicité

✓ Suppression protégée

✓ Validation multi-tenant

✓ Cohérence académique

---

# Résumé

Un SubjectCoefficient est valide uniquement si :

- il appartient à une année académique existante ;
- il référence un Program, un Level et une Subject cohérents ;
- son coefficient est compris entre 0.5 et 20 ;
- une seule configuration existe pour une combinaison donnée ;
- il ne peut être modifié ni supprimé après son utilisation dans des calculs académiques.