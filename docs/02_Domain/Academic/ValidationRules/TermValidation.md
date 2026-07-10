# Academic Module
# Validation Rules
## TermValidation

Version : 1.0

---

# Objectif

Définir les règles de validation des périodes académiques (Terms) afin de garantir la cohérence du calendrier scolaire.

Un Term représente une subdivision d'une année académique.

Selon les pays, il peut s'agir de :

- Trimestre
- Semestre
- Quadrimestre
- Toute autre période officielle

Toutes les validations s'appliquent à l'ensemble de ces cas.

---

# Entité concernée

Term

Exemple

| Name | Academic Year | Start | End |
|------|---------------|-------|-----|
| Premier Trimestre | 2025-2026 | 2025-09-15 | 2025-12-20 |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Academic Year | Oui |
| Name | Oui |
| Order | Oui |
| Start Date | Oui |
| End Date | Oui |
| Status | Oui |

---

# Validation du nom

Le nom est obligatoire.

Exemples valides

Premier Trimestre

Deuxième Trimestre

Semestre 1

Semestre 2

Quadrimestre 1

---

# Longueur

Minimum

3 caractères

Maximum

100 caractères

---

# Validation de l'ordre

Order

doit être

un entier positif.

Exemples valides

```
1
2
3
4
```

Interdit

```
0

-1

1.5

ABC
```

---

# Unicité

Le couple

AcademicYear + Order

doit être unique.

Interdit

Academic Year

2025-2026

Premier Trimestre

Order = 1

Deuxième période

Order = 1

---

# Unicité du nom

Le nom doit être unique

dans une même année académique.

Autorisé

2025-2026

Premier Trimestre

2026-2027

Premier Trimestre

Interdit

2025-2026

Premier Trimestre

2025-2026

Premier Trimestre

---

# Validation chronologique

Start Date

doit être

strictement inférieure

à

End Date

---

# Inclusion dans l'année académique

Le début

ne peut pas être

avant

AcademicYear.StartDate

Le terme

ne peut pas se terminer

après

AcademicYear.EndDate

---

# Chevauchement

Deux périodes

ne doivent jamais se chevaucher.

Interdit

Premier Trimestre

01 Septembre

↓

20 Décembre

Deuxième Trimestre

15 Décembre

↓

20 Mars

---

# Continuité

Les périodes peuvent

être :

continues

ou

séparées par quelques jours

(vacances scolaires)

mais jamais se chevaucher.

---

# Nombre de périodes

Minimum

1

Maximum recommandé

6

Au-delà,

une validation métier

doit demander une confirmation.

---

# Statut

Valeurs autorisées

Draft

Active

Closed

Archived

---

# Règles de statut

Draft

Modification libre

Active

Peut recevoir :

- Notes
- Présences
- Devoirs
- Examens

Closed

Lecture seule

Archived

Historique

---

# Période active

Une seule période

peut être Active

à la fois

dans une même année académique.

---

# Suppression

Suppression interdite

si le Term contient :

- Notes
- Examens
- Présences
- Bulletins
- Emploi du temps
- Évaluations

---

# Modification

Impossible de modifier

Start Date

ou

End Date

si des évaluations existent déjà.

---

# Validation multi-tenant

Toutes les validations

s'effectuent

à l'intérieur

de la même école.

---

# Validation de séquence

L'ordre des périodes

doit être logique.

Autorisé

```
1

2

3
```

Interdit

```
1

3

5
```

sauf si le système autorise explicitement les numéros personnalisés.

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| TR001 | Nom obligatoire |
| TR002 | Academic Year obligatoire |
| TR003 | Dates invalides |
| TR004 | Chevauchement détecté |
| TR005 | Ordre invalide |
| TR006 | Ordre déjà utilisé |
| TR007 | Nom déjà utilisé |
| TR008 | Plusieurs périodes actives |
| TR009 | Suppression interdite |
| TR010 | Modification interdite |

---

# Messages utilisateur

TR001

```
Le nom de la période est obligatoire.
```

TR003

```
Les dates de la période sont invalides.
```

TR004

```
Cette période chevauche une autre période existante.
```

TR006

```
Cet ordre est déjà utilisé.
```

TR008

```
Une seule période peut être active.
```

TR009

```
Impossible de supprimer cette période car elle contient déjà des données académiques.
```

---

# Cas limites (Edge Cases)

Cas 1

Création d'un trimestre dont la fin dépasse l'année académique.

Résultat

Refus.

---

Cas 2

Deux trimestres commencent le même jour.

Résultat

Refus.

---

Cas 3

Activation d'un deuxième trimestre actif.

Résultat

Le système refuse l'opération ou désactive automatiquement l'ancien selon la configuration.

---

Cas 4

Modification des dates après la saisie des notes.

Résultat

Refus.

---

Cas 5

Suppression d'un trimestre contenant des bulletins.

Résultat

Refus.

---

# Tests obligatoires

✓ Nom obligatoire

✓ Academic Year obligatoire

✓ Dates valides

✓ Inclusion dans l'année académique

✓ Aucun chevauchement

✓ Ordre unique

✓ Nom unique

✓ Une seule période active

✓ Suppression protégée

✓ Validation multi-tenant

---

# Résumé

Une période académique est valide uniquement si :

- elle appartient à une année académique existante ;
- ses dates sont entièrement incluses dans cette année académique ;
- elle ne chevauche aucune autre période ;
- son ordre est unique ;
- son nom est unique dans l'année ;
- une seule période est active à la fois ;
- elle ne peut être supprimée ou modifiée lorsqu'elle contient déjà des données académiques.