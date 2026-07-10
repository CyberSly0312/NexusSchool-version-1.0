# Class Capacity Engine

## Module

Academic

---

# Purpose

Le `ClassCapacityEngine` est responsable de la gestion des capacités d'accueil des classes et des sections.

Il garantit qu'aucune classe ou section ne dépasse sa capacité maximale tout en optimisant la répartition des élèves selon les règles définies par l'établissement.

Toutes les opérations d'inscription, de transfert et de promotion utilisant une classe passent par ce moteur.

---

# Responsibilities

Le moteur est chargé de :

- Vérifier la capacité disponible d'une classe.
- Contrôler les inscriptions.
- Empêcher les dépassements de capacité.
- Répartir automatiquement les élèves entre plusieurs sections.
- Calculer les taux d'occupation.
- Identifier les classes sous-occupées ou surchargées.
- Générer des recommandations d'affectation.

---

# Inputs

## School

- établissement

## Academic Year

- année académique

## Class

- capacité maximale
- capacité minimale
- niveau
- série

## Section

- capacité
- effectif actuel

## Students

- liste des élèves
- statut
- niveau

---

# Outputs

Le moteur retourne :

- capacité restante
- taux d'occupation
- disponibilité
- proposition de classe
- proposition de section
- erreurs de capacité

---

# Core Operations

## Check Capacity

Vérifie si une classe possède suffisamment de places disponibles.

Retour :

- Available
- Full
- Over Capacity

---

## Calculate Remaining Seats

Calcule :

Capacité maximale

-

Nombre d'élèves inscrits

=

Places restantes

---

## Calculate Occupancy Rate

Calcule :

(Nombre d'élèves / Capacité maximale)

×

100

Retourne le pourcentage d'occupation.

---

## Suggest Available Class

Recherche automatiquement une classe pouvant accueillir un nouvel élève selon :

- programme
- niveau
- série
- section
- capacité disponible

---

## Auto Assign Section

Lorsque plusieurs sections existent, le moteur répartit automatiquement les élèves afin d'équilibrer les effectifs.

---

## Detect Overloaded Classes

Retourne la liste des classes dépassant leur capacité.

---

## Detect Underutilized Classes

Retourne la liste des classes dont le taux d'occupation est inférieur au seuil défini.

---

## Predict Capacity

Estime les besoins futurs en fonction :

- des nouvelles admissions
- des promotions
- des redoublements
- des transferts

---

# Business Rules

## BR-001

Une classe ne peut jamais dépasser sa capacité maximale.

---

## BR-002

Chaque élève ne peut appartenir qu'à une seule classe active.

---

## BR-003

Une section ne peut recevoir un élève que si elle dispose d'une place disponible.

---

## BR-004

Le moteur privilégie les sections ayant le taux d'occupation le plus faible.

---

## BR-005

Les classes suspendues ne peuvent recevoir aucun élève.

---

## BR-006

Les élèves archivés ne sont pas pris en compte dans les calculs de capacité.

---

## BR-007

Les réservations de places doivent être temporaires et automatiquement libérées en cas d'abandon du processus d'inscription.

---

## BR-008

Les capacités peuvent être différentes selon les niveaux ou les programmes.

---

# Dependencies

Le moteur utilise :

- School
- AcademicYear
- AcademicLevel
- AcademicSeries
- SchoolClass
- Section
- Student
- Enrollment

---

# Used By

- Enrollment
- Student Transfer
- Student Promotion
- Timetable
- Reports
- Dashboard

---

# Events Triggered

- ClassCapacityChecked
- ClassCapacityExceeded
- StudentAssignedToClass
- StudentAssignedToSection
- ClassOccupancyUpdated

---

# Possible Exceptions

- ClassFullException
- SectionFullException
- InvalidCapacityException
- DuplicateEnrollmentException
- CapacityPredictionException

---

# Performance Considerations

Le moteur doit maintenir un compteur des effectifs en temps réel afin d'éviter de recalculer l'ensemble des inscriptions à chaque opération.

Les statistiques de capacité doivent être mises en cache et rafraîchies automatiquement après toute modification des inscriptions.

---

# Future Extensions

- Capacités par campus
- Capacités par salle de classe
- Capacités selon les normes ministérielles
- Prévision basée sur l'intelligence artificielle
- Optimisation automatique des répartitions
- Simulation des effectifs futurs