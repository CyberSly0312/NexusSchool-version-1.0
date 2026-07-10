# Academic Module
# Services
## ClassroomService

Version : 1.0

---

# Objectif

ClassroomService centralise toute la logique métier liée aux classes académiques.

Toutes les opérations concernant une classe doivent être exécutées via ce service.

Aucun Controller, Filament Resource ou API ne doit manipuler directement le modèle Classroom.

---

# Responsabilités

Le service est responsable de :

- création d'une classe
- modification
- activation
- fermeture
- archivage
- suppression
- affectation des élèves
- gestion des capacités
- gestion des sections
- contrôle de cohérence académique
- transfert d'élèves entre classes

---

# Dépendances

ClassroomService utilise :

- Classroom Repository
- AcademicYearService
- EnrollmentService
- Validation Rules
- Audit Log Service
- Event Dispatcher

---

# Méthodes publiques

## create()

Créer une nouvelle classe.

Entrée

```
CreateClassroomDTO
```

Sortie

```
Classroom
```

---

## update()

Modifier une classe.

Entrée

```
Classroom

UpdateClassroomDTO
```

Retour

```
Classroom
```

---

## activate()

Active une classe.

Retour

```
bool
```

---

## close()

Ferme une classe.

Aucune nouvelle inscription n'est autorisée.

---

## archive()

Archive une classe.

Lecture seule.

---

## delete()

Supprime une classe.

Seulement si aucune dépendance n'existe.

---

## assignStudent()

Affecte un élève à une classe.

Entrée

```
Student

Classroom
```

---

## transferStudent()

Transfert d'un élève vers une autre classe.

Conserve l'historique.

---

## removeStudent()

Retire un élève d'une classe.

Selon les règles métier.

---

## hasAvailableCapacity()

Retour

```
bool
```

---

## getAvailableSeats()

Retour

```
int
```

---

## getCurrentEnrollmentCount()

Retour

```
int
```

---

## validateCapacity()

Vérifie que la capacité maximale n'est pas dépassée.

---

## canDelete()

Retour

```
bool
```

---

## canClose()

Retour

```
bool
```

---

## canArchive()

Retour

```
bool
```

---

# Workflow

Création

↓

Validation

↓

Vérification AcademicYear

↓

Vérification Program

↓

Vérification Level

↓

Vérification Series

↓

Sauvegarde

↓

Audit Log

↓

Events

↓

Notifications

---

# Validation métier

Le service doit vérifier :

✓ année académique active

✓ programme valide

✓ niveau valide

✓ série valide

✓ unicité du nom

✓ capacité

✓ cohérence Program → Level → Series

✓ disponibilité de la classe

---

# Gestion des capacités

Avant chaque inscription

le service vérifie :

Capacité maximale

↓

Nombre actuel d'élèves

↓

Places restantes

↓

Autorisation ou refus

---

# Gestion des transferts

Lors d'un transfert

le service doit :

- vérifier la capacité de destination
- mettre à jour Enrollment
- historiser l'opération
- générer un Audit Log
- déclencher un événement

---

# Exceptions

ClassroomAlreadyExistsException

---

ClassroomCapacityExceededException

---

ClassroomAlreadyClosedException

---

ClassroomHasDependenciesException

---

InvalidAcademicStructureException

---

ClassroomValidationException

---

# Audit Log

Toutes les opérations doivent être historisées.

Exemples

Create Classroom

Update Classroom

Activate Classroom

Close Classroom

Archive Classroom

Delete Classroom

Assign Student

Transfer Student

Remove Student

---

# Événements déclenchés

ClassroomCreated

ClassroomUpdated

ClassroomActivated

ClassroomClosed

ClassroomArchived

ClassroomDeleted

StudentAssignedToClassroom

StudentTransferred

StudentRemovedFromClassroom

---

# Transactions

Les opérations suivantes doivent être atomiques :

- create()

- assignStudent()

- transferStudent()

- close()

- delete()

En cas d'échec

Rollback complet.

---

# Sécurité

Le service doit vérifier :

- Tenant courant
- Permissions utilisateur
- Accès à l'école
- Cohérence académique

Aucune opération cross-tenant n'est autorisée.

---

# Performance

Optimisations recommandées

- Cache des effectifs

- Cache des places disponibles

- Index sur

    school_id

    academic_year_id

    level_id

    series_id

    status

---

# Tests unitaires

✓ Création

✓ Modification

✓ Activation

✓ Fermeture

✓ Archivage

✓ Suppression

✓ Affectation d'élève

✓ Transfert

✓ Contrôle des capacités

✓ Validation académique

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
ClassroomService
      │
      ├── Validation
      ├── Capacity Check
      ├── Academic Validation
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

ClassroomService est le point central de gestion des classes académiques.

Il garantit :

- l'intégrité de la structure académique ;
- le respect des capacités des classes ;
- la gestion sécurisée des affectations et transferts d'élèves ;
- la cohérence des données dans un environnement multi-tenant ;
- la traçabilité complète de toutes les opérations via les journaux d'audit et les événements.