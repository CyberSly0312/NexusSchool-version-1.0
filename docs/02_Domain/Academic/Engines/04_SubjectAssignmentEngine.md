# Subject Assignment Engine

## Module

Academic

---

# Purpose

Le `SubjectAssignmentEngine` est responsable de l'affectation des matières aux programmes, niveaux, séries, classes et enseignants.

Il garantit que chaque matière est enseignée uniquement aux niveaux autorisés, avec les coefficients, volumes horaires et enseignants appropriés.

Toutes les opérations liées aux matières reposent sur ce moteur.

---

# Responsibilities

Le moteur est chargé de :

- Affecter les matières aux programmes.
- Affecter les matières aux niveaux.
- Affecter les matières aux séries.
- Affecter les matières aux classes.
- Affecter les enseignants aux matières.
- Vérifier la cohérence des affectations.
- Contrôler les coefficients.
- Vérifier les volumes horaires.
- Générer les plans d'enseignement.

---

# Inputs

## Academic Year

- année académique

## Program

- collège
- lycée
- technique

## Academic Level

- niveau scolaire

## Series

- A
- C
- D
- F1
- F2
- etc.

## School Class

- classe

## Subject

- matière
- code
- catégorie
- coefficient

## Teacher

- spécialité
- disponibilité
- charge horaire

---

# Outputs

Le moteur retourne :

- affectations validées
- liste des matières par classe
- liste des matières par enseignant
- erreurs d'affectation
- alertes de cohérence

---

# Core Operations

## Assign Subject To Program

Associe une matière à un programme académique.

---

## Assign Subject To Level

Associe une matière à un niveau donné.

---

## Assign Subject To Series

Associe une matière à une série spécifique.

---

## Assign Subject To Class

Associe une matière à une classe.

---

## Assign Teacher To Subject

Associe un enseignant qualifié à une matière.

---

## Validate Assignment

Vérifie :

- compatibilité programme
- compatibilité niveau
- compatibilité série
- qualification de l'enseignant
- disponibilité
- coefficient

---

## Calculate Weekly Hours

Calcule le volume horaire hebdomadaire de chaque matière.

---

## Calculate Teacher Workload

Additionne automatiquement les heures d'enseignement d'un professeur.

---

## Generate Teaching Plan

Construit automatiquement la liste des matières à enseigner pour une classe durant l'année académique.

---

# Business Rules

## BR-001

Une matière doit appartenir à au moins un programme.

---

## BR-002

Une matière ne peut être enseignée que dans les niveaux autorisés.

---

## BR-003

Une série ne peut recevoir que les matières compatibles avec son référentiel pédagogique.

---

## BR-004

Un enseignant ne peut être affecté qu'aux matières correspondant à sa spécialité.

---

## BR-005

Le coefficient d'une matière doit respecter les référentiels académiques.

---

## BR-006

Le volume horaire hebdomadaire doit être supérieur à zéro.

---

## BR-007

Une matière ne peut être affectée deux fois à une même classe durant une même année académique.

---

## BR-008

Toute modification d'une affectation doit conserver l'historique des anciennes affectations.

---

# Dependencies

Le moteur utilise :

- AcademicYear
- AcademicProgram
- AcademicLevel
- AcademicSeries
- SchoolClass
- Subject
- SubjectCoefficient
- Teacher
- TeachingAssignment

---

# Used By

- Timetable
- Attendance
- Examination
- Grading
- Teacher Workload
- Reports

---

# Events Triggered

- SubjectAssigned
- SubjectUnassigned
- TeacherAssignedToSubject
- TeacherUnassignedFromSubject
- TeachingPlanGenerated

---

# Possible Exceptions

- InvalidSubjectException
- InvalidTeacherQualificationException
- DuplicateAssignmentException
- InvalidCoefficientException
- WeeklyHoursExceededException

---

# Performance Considerations

Les affectations doivent être mises en cache afin de permettre une génération rapide des emplois du temps et des bulletins.

Toute modification d'une affectation invalide automatiquement le cache concerné.

---

# Future Extensions

- Affectation automatique basée sur l'intelligence artificielle
- Optimisation automatique des charges horaires
- Affectation multi-campus
- Enseignement partagé entre plusieurs établissements
- Gestion des enseignants vacataires
- Référentiels nationaux synchronisés automatiquement