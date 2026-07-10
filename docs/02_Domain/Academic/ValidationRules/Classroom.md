# Academic Module
# Validation Rules
## Classroom

Version : 1.0

---

# Objectif

Définir les règles de validation des classes académiques.

Une Classroom représente une classe réelle au sein d'un établissement.

Exemples :

- 6ème A
- 6ème B
- Première C1
- Première C2
- Terminale D

Une Classroom est l'entité qui accueillera les élèves durant une année académique.

---

# Entité concernée

Classroom

Exemple

| Name | Level | Series | Academic Year |
|------|-------|--------|---------------|
| Première C1 | Première | C | 2025-2026 |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Academic Year | Oui |
| Program | Oui |
| Level | Oui |
| Name | Oui |
| Capacity | Oui |
| Status | Oui |

Series est obligatoire uniquement si le Level utilise des séries.

---

# Validation du nom

Le nom est obligatoire.

Longueur

Minimum

2 caractères

Maximum

100 caractères

---

# Exemples valides

```
6ème A

6ème B

Première C1

Première C2

Terminale D
```

---

# Exemples invalides

```
#

@

AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
```

---

# Capacité

Capacity

doit être

un entier positif.

Minimum

1

Maximum recommandé

150

Une alerte doit être affichée au-delà de 80 élèves.

---

# Unicité

Le nom

doit être unique

dans la combinaison

AcademicYear + Level + Series.

Exemple

Autorisé

```
2025-2026

Première C1
```

```
2026-2027

Première C1
```

Interdit

```
2025-2026

Première C1

2025-2026

Première C1
```

---

# Validation de la série

Si le Level

n'utilise pas

de Series

alors

Series = NULL

Si le Level

utilise

des Series

alors

Series est obligatoire.

---

# Statut

Valeurs autorisées

Draft

Active

Closed

Archived

---

# Règles métier

Draft

Classe en préparation.

Aucun élève ne peut être inscrit.

---

Active

Autorise :

- Inscriptions
- Notes
- Présences
- Emploi du temps
- Bulletins

---

Closed

Plus aucune inscription.

Consultation uniquement.

---

Archived

Historique.

Lecture seule.

---

# Effectif

Le nombre d'élèves inscrits

ne doit jamais dépasser

Capacity.

Le système doit empêcher toute nouvelle inscription lorsque la capacité est atteinte.

---

# Changement de capacité

La capacité

ne peut pas être réduite

en dessous

du nombre actuel d'élèves.

Exemple

Capacité

40

Élèves inscrits

35

Nouvelle capacité

30

Résultat

Refus.

---

# Suppression

Suppression interdite

si la classe possède :

- des Élèves
- des Inscriptions
- des Notes
- des Bulletins
- des Présences
- des Emplois du temps
- des Examens

---

# Modification

Impossible de modifier

Academic Year

Program

Level

Series

si des inscriptions existent.

Le nom

reste modifiable.

---

# Validation multi-tenant

Toutes les validations

s'effectuent

uniquement

dans la même école.

---

# Cohérence académique

Le Program

du Level

de la Series

doivent être identiques.

Exemple valide

```
Programme Général

↓

Première

↓

Série C

↓

Première C1
```

Exemple invalide

```
Programme Général

↓

Première

↓

Filière Technique

↓

Première C1
```

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| CL001 | Nom obligatoire |
| CL002 | Année académique obligatoire |
| CL003 | Programme obligatoire |
| CL004 | Niveau obligatoire |
| CL005 | Série invalide |
| CL006 | Nom déjà utilisé |
| CL007 | Capacité invalide |
| CL008 | Classe pleine |
| CL009 | Classe utilisée |
| CL010 | Suppression interdite |
| CL011 | Cohérence académique invalide |

---

# Messages utilisateur

CL001

```
Le nom de la classe est obligatoire.
```

CL005

```
La série sélectionnée est invalide.
```

CL006

```
Cette classe existe déjà.
```

CL007

```
La capacité doit être supérieure à zéro.
```

CL008

```
La capacité maximale de la classe est atteinte.
```

CL010

```
Impossible de supprimer une classe contenant déjà des données.
```

---

# Cas limites (Edge Cases)

Cas 1

Inscription du 41ème élève

dans une classe limitée à 40.

Résultat

Refus.

---

Cas 2

Suppression d'une classe

ayant déjà un bulletin.

Résultat

Refus.

---

Cas 3

Réduction de la capacité

en dessous du nombre actuel d'élèves.

Résultat

Refus.

---

Cas 4

Création de deux classes

Première C1

dans la même année.

Résultat

Refus.

---

Cas 5

Création de

Première C1

dans une autre année académique.

Résultat

Autorisé.

---

# Tests obligatoires

✓ Academic Year obligatoire

✓ Program obligatoire

✓ Level obligatoire

✓ Série conditionnelle

✓ Nom obligatoire

✓ Nom unique

✓ Capacité valide

✓ Limite d'effectif

✓ Suppression protégée

✓ Validation multi-tenant

✓ Cohérence Program → Level → Series

---

# Résumé

Une Classroom est valide uniquement si :

- elle appartient à une année académique active ;
- elle est rattachée à un Program et un Level cohérents ;
- une Series est renseignée lorsque le niveau l'exige ;
- son nom est unique dans son contexte académique ;
- sa capacité est respectée ;
- elle ne peut être supprimée ou modifiée lorsqu'elle contient des données académiques.