# Academic Module
# Validation Rules
## Enrollment

Version : 1.0

---

# Objectif

Définir les règles de validation des inscriptions académiques.

Une Enrollment représente l'inscription annuelle d'un élève dans une classe.

Un élève possède :

- un dossier Student permanent

et

- une Enrollment par année académique.

Toutes les données pédagogiques sont rattachées à Enrollment.

---

# Entité concernée

Enrollment

Exemple

| Student | Academic Year | Classroom |
|----------|---------------|-----------|
| Ali Traoré | 2025-2026 | Première C1 |

---

# Champs obligatoires

| Champ | Obligatoire |
|--------|-------------|
| Student | Oui |
| Academic Year | Oui |
| Classroom | Oui |
| Enrollment Date | Oui |
| Status | Oui |

Section est obligatoire uniquement si la classe utilise des sections.

---

# Validation de l'élève

Le Student

doit exister.

Le Student

doit appartenir

à la même école.

---

# Validation de l'année académique

Academic Year

doit être

Active.

Aucune inscription

dans une année

Closed

ou

Archived.

---

# Validation de la classe

La Classroom

doit être

Active.

---

# Validation de la section

Si la classe

utilise des sections

Section est obligatoire.

Sinon

Section = NULL.

---

# Unicité

Un Student

ne peut avoir

qu'une seule Enrollment

par Academic Year.

---

# Changement de classe

Le changement de classe

ne modifie pas

l'Enrollment.

La Classroom

est simplement mise à jour

et l'opération

est historisée.

---

# Numéro de classe

Roll Number

doit être unique

dans une Classroom.

---

# Date d'inscription

Enrollment Date

ne peut être

antérieure

au début

de l'année académique.

Elle ne peut pas être

postérieure

à la date du jour.

---

# Statut

Valeurs autorisées

Pending

Active

Transferred

Suspended

Completed

Cancelled

Archived

---

# Règles métier

Pending

Inscription créée.

Non validée.

---

Active

L'élève suit les cours.

---

Transferred

Élève transféré.

---

Suspended

Suspendu.

---

Completed

Année terminée.

---

Cancelled

Inscription annulée.

---

Archived

Historique.

Lecture seule.

---

# Capacité

Avant création

le système

doit vérifier

la capacité restante

de la Classroom.

---

# Suppression

Suppression interdite

si l'Enrollment possède :

- Notes
- Présences
- Bulletins
- Paiements
- Examens
- Sanctions
- Bibliothèque

Une annulation

doit être utilisée

à la place.

---

# Modification

Impossible de modifier

Student

Academic Year

après validation.

Seule la Classroom

peut changer

dans le cadre d'un transfert.

---

# Validation multi-tenant

Toutes les validations

s'effectuent

dans la même école.

---

# Cohérence académique

Les éléments suivants

doivent appartenir

au même contexte académique :

- Academic Year
- Program
- Level
- Series
- Classroom
- Section

---

# Codes d'erreur

| Code | Description |
|-------|-------------|
| EN001 | Élève obligatoire |
| EN002 | Année académique obligatoire |
| EN003 | Classe obligatoire |
| EN004 | Section invalide |
| EN005 | Élève déjà inscrit |
| EN006 | Classe pleine |
| EN007 | Année académique fermée |
| EN008 | Classe inactive |
| EN009 | Suppression interdite |
| EN010 | Cohérence académique invalide |

---

# Messages utilisateur

EN001

L'élève est obligatoire.

EN003

La classe est obligatoire.

EN005

Cet élève est déjà inscrit pour cette année académique.

EN006

La capacité maximale de cette classe est atteinte.

EN007

Les inscriptions sont fermées pour cette année académique.

EN009

Impossible de supprimer cette inscription.

---

# Cas limites (Edge Cases)

Cas 1

Inscription du même élève

deux fois

dans la même année.

Résultat

Refus.

---

Cas 2

Inscription

dans une classe pleine.

Résultat

Refus.

---

Cas 3

Transfert

vers une autre classe.

Résultat

Autorisé.

Historique conservé.

---

Cas 4

Suppression

d'une inscription

ayant déjà des notes.

Résultat

Refus.

---

Cas 5

Inscription

dans une année académique fermée.

Résultat

Refus.

---

# Tests obligatoires

✓ Student obligatoire

✓ AcademicYear obligatoire

✓ Classroom obligatoire

✓ Section conditionnelle

✓ Unicité

✓ Classe active

✓ Capacité disponible

✓ Roll Number unique

✓ Suppression protégée

✓ Validation multi-tenant

✓ Cohérence académique

---

# Résumé

Une Enrollment est valide uniquement si :

- l'élève est inscrit une seule fois par année académique ;
- l'année académique est active ;
- la classe est active et dispose d'une capacité suffisante ;
- la cohérence entre Program, Level, Series, Classroom et Section est respectée ;
- l'inscription ne peut pas être supprimée après utilisation et doit être conservée pour l'historique académique.