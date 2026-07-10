# Academic Module
# Services
## EnrollmentService

Version : 1.0

---

# Objectif

EnrollmentService centralise toute la logique métier relative aux inscriptions académiques.

Toutes les opérations concernant les inscriptions des élèves doivent être exécutées exclusivement par ce service.

Enrollment représente le dossier académique annuel d'un élève.

---

# Responsabilités

Le service est responsable de :

- création d'une inscription
- validation d'une inscription
- affectation d'une classe
- affectation d'une section
- changement de classe
- changement de section
- suspension
- réactivation
- annulation
- clôture d'une inscription
- contrôle des capacités
- contrôle de cohérence académique

---

# Dépendances

EnrollmentService utilise :

- StudentService
- AcademicYearService
- ClassroomService
- Validation Rules
- Audit Log Service
- Event Dispatcher

---

# Méthodes publiques

## create()

Créer une nouvelle inscription.

Entrée

```
CreateEnrollmentDTO
```

Retour

```
Enrollment
```

---

## update()

Modifier une inscription.

Entrée

```
Enrollment

UpdateEnrollmentDTO
```

Retour

```
Enrollment
```

---

## approve()

Valider définitivement une inscription.

Retour

```
bool
```

---

## cancel()

Annuler une inscription.

Conserve l'historique.

---

## suspend()

Suspend temporairement une inscription.

---

## reactivate()

Réactive une inscription suspendue.

---

## complete()

Clôture une inscription.

Fin d'année scolaire.

---

## transferClassroom()

Changer la classe.

L'historique doit être conservé.

---

## transferSection()

Changer la section.

---

## assignRollNumber()

Attribuer automatiquement

le numéro de classe.

---

## canEnroll()

Retour

```
bool
```

---

## canTransfer()

Retour

```
bool
```

---

## canCancel()

Retour

```
bool
```

---

## canComplete()

Retour

```
bool
```

---

# Workflow

Création

↓

Validation Student

↓

Validation AcademicYear

↓

Validation Classroom

↓

Validation Capacity

↓

Validation Section

↓

Création Enrollment

↓

Assignation Roll Number

↓

Audit Log

↓

Events

↓

Notification

---

# Validation métier

Le service doit vérifier :

✓ Student actif

✓ année académique active

✓ classe active

✓ capacité disponible

✓ section valide

✓ unicité de l'inscription

✓ cohérence Program

✓ cohérence Level

✓ cohérence Series

---

# Gestion des transferts

Lors d'un transfert

le service doit :

- vérifier la disponibilité
- conserver l'historique
- mettre à jour Enrollment
- recalculer le Roll Number si nécessaire
- notifier les modules concernés

---

# Gestion des capacités

Avant chaque inscription

ou transfert

le service vérifie :

Capacité

↓

Effectif actuel

↓

Places disponibles

↓

Autorisation

---

# Gestion des statuts

Pending

↓

Approved

↓

Active

↓

Completed

ou

Transferred

ou

Suspended

ou

Cancelled

---

# Exceptions

EnrollmentAlreadyExistsException

---

EnrollmentClosedException

---

EnrollmentCapacityExceededException

---

EnrollmentValidationException

---

InvalidClassroomException

---

StudentAlreadyEnrolledException

---

AcademicYearClosedException

---

# Audit Log

Toutes les opérations doivent être historisées.

Exemples

Create Enrollment

Approve Enrollment

Update Enrollment

Transfer Classroom

Transfer Section

Suspend Enrollment

Reactivate Enrollment

Cancel Enrollment

Complete Enrollment

---

# Événements déclenchés

EnrollmentCreated

EnrollmentApproved

EnrollmentUpdated

EnrollmentTransferred

EnrollmentCancelled

EnrollmentSuspended

EnrollmentCompleted

RollNumberAssigned

---

# Transactions

Les opérations suivantes doivent être atomiques :

- create()

- approve()

- transferClassroom()

- transferSection()

- complete()

- cancel()

Rollback complet

en cas d'erreur.

---

# Sécurité

Le service vérifie :

- Tenant courant
- Permissions utilisateur
- Appartenance de l'élève
- Appartenance de la classe
- Cohérence académique

Aucune opération inter-écoles n'est autorisée.

---

# Performance

Optimisations recommandées

- Cache des effectifs

- Cache des numéros de classe

- Index sur

    school_id

    student_id

    academic_year_id

    classroom_id

    status

---

# Intégration avec les autres modules

Enrollment est utilisé par :

- Attendance
- Timetable
- Examination
- Gradebook
- Report Cards
- Finance
- Library
- Transport
- Hostel
- Medical
- Discipline

Tous ces modules doivent référencer Enrollment et non directement Student.

---

# Tests unitaires

✓ Création

✓ Validation

✓ Affectation

✓ Changement de classe

✓ Changement de section

✓ Suspension

✓ Réactivation

✓ Annulation

✓ Clôture

✓ Capacité

✓ Roll Number

✓ Rollback

✓ Audit Log

✓ Events

✓ Validation multi-tenant

---

# Diagramme simplifié

```
Controller
      │
      ▼
EnrollmentService
      │
      ├── Student Validation
      ├── AcademicYear Validation
      ├── Classroom Validation
      ├── Capacity Validation
      ├── Section Validation
      ▼
Repository
      ▼
Database
      ▼
Events
      ▼
Audit Log
```

---

# Résumé

EnrollmentService est le cœur du domaine académique.

Il garantit :

- une seule inscription par élève et par année académique ;
- la cohérence entre Student, AcademicYear, Classroom et Section ;
- la gestion sécurisée des transferts ;
- le respect des capacités des classes ;
- la conservation complète de l'historique académique ;
- une intégration fiable avec tous les autres modules du système.