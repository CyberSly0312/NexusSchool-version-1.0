# Promotion Engine

## Module

Academic

---

# Purpose

Le `PromotionEngine` est responsable de l'évaluation académique des élèves en fin de période et de la prise de décision concernant leur parcours scolaire.

Il applique les règles pédagogiques définies par l'établissement ou les autorités éducatives afin de déterminer si un élève est promu, redouble, est orienté vers une autre série ou obtient son diplôme.

Toutes les décisions académiques relatives à la progression des élèves reposent sur ce moteur.

---

# Responsibilities

Le moteur est chargé de :

- Évaluer les résultats académiques.
- Calculer les moyennes générales.
- Vérifier les moyennes par matière.
- Appliquer les règles de promotion.
- Déterminer les redoublements.
- Gérer les orientations.
- Détecter les élèves en situation d'échec.
- Préparer la promotion vers l'année suivante.
- Générer les statistiques de promotion.

---

# Inputs

## Academic Year

- année académique

## Academic Term

- trimestre
- semestre

## Student

- dossier scolaire
- statut

## Grades

- notes
- moyennes

## Subject Coefficients

- coefficients officiels

## Promotion Rules

- seuil de réussite
- matières éliminatoires
- règles nationales

---

# Outputs

Le moteur retourne :

- décision académique
- moyenne générale
- moyenne pondérée
- classement
- justification
- rapport de promotion

---

# Core Operations

## Calculate Final Average

Calcule la moyenne générale pondérée de l'élève.

---

## Evaluate Student

Analyse :

- notes
- absences
- comportement
- sanctions
- critères pédagogiques

---

## Apply Promotion Rules

Détermine automatiquement :

- Promoted
- Repeated
- Graduated
- Reoriented
- Pending Validation

---

## Determine Next Level

Identifie automatiquement la prochaine classe ou le prochain niveau.

---

## Generate Promotion Report

Produit le rapport détaillé de décision.

---

## Batch Promotion

Traite automatiquement l'ensemble des élèves d'une classe ou d'un niveau.

---

## Validate Promotion

Permet au directeur ou au conseil de classe de confirmer ou modifier la décision proposée.

---

# Business Rules

## BR-001

La promotion est basée sur la moyenne générale pondérée.

---

## BR-002

Les matières éliminatoires sont prises en compte avant toute promotion.

---

## BR-003

Un élève ne peut être promu que si toutes ses notes sont validées.

---

## BR-004

Une décision de promotion doit être validée avant d'être définitive.

---

## BR-005

Les promotions doivent respecter la structure académique de l'établissement.

---

## BR-006

Une classe terminale ne peut être promue que vers un statut de diplômé.

---

## BR-007

Le moteur doit conserver l'historique complet de toutes les décisions académiques.

---

## BR-008

Les règles de promotion peuvent être différentes selon :

- programme
- niveau
- série
- pays
- établissement

---

# Dependencies

Le moteur utilise :

- Student
- AcademicYear
- AcademicTerm
- SchoolClass
- Subject
- Grade
- SubjectCoefficient
- AcademicSeries
- PromotionRule

---

# Used By

- Enrollment
- Examination
- Report Cards
- Graduation
- Student Transfer
- Analytics

---

# Events Triggered

- StudentPromoted
- StudentRepeated
- StudentGraduated
- StudentReoriented
- PromotionValidated
- PromotionCancelled

---

# Possible Exceptions

- MissingGradesException
- InvalidPromotionRuleException
- PromotionValidationException
- IncompleteAcademicRecordException
- GraduationRequirementException

---

# Performance Considerations

Le moteur doit être capable de traiter plusieurs milliers d'élèves en traitement par lot (Batch Processing) avec une consommation mémoire optimisée.

Les résultats intermédiaires doivent être mis en cache afin de limiter les recalculs lors des validations successives.

---

# Future Extensions

- Promotion basée sur les compétences
- Promotion conditionnelle
- Promotion assistée par Intelligence Artificielle
- Orientation automatique selon les performances
- Détection précoce des risques de redoublement
- Simulation de promotion avant validation officielle